<?php

namespace Tests\Feature\Question;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemAnalysisTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::create([
            'name' => ucfirst($roleName),
            'email' => str_replace('_', '.', $roleName).'@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    private function setupExam(): array
    {
        $teacher = $this->makeUser('guru');
        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK-IA']);

        $q = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_PG,
            'question_text' => '2 + 2 = ...',
            'difficulty' => 'easy',
            'score' => 1,
            'is_active' => true,
            'created_by' => $teacher->id,
        ]);
        $correct = QuestionOption::create(['question_id' => $q->id, 'label' => 'A', 'option_text' => '4', 'is_correct' => true, 'sort_order' => 1]);
        $wrong = QuestionOption::create(['question_id' => $q->id, 'label' => 'B', 'option_text' => '5', 'is_correct' => false, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $q->id, 'label' => 'C', 'option_text' => '6', 'is_correct' => false, 'sort_order' => 3]);

        $exam = Exam::create([
            'name' => 'UTS IA',
            'subject_id' => $subject->id,
            'duration_minutes' => 30,
            'status' => Exam::STATUS_PUBLISHED,
            'created_by' => $teacher->id,
        ]);
        $exam->questions()->attach($q->id, ['order_in_exam' => 1, 'score' => 1]);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi IA',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
            'status' => ExamSession::STATUS_OPEN,
        ]);

        return compact('q', 'correct', 'wrong', 'exam', 'session', 'teacher');
    }

    private function createAttempt(array $data, User $student, bool $correct): void
    {
        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $student->id,
            'status' => 'submitted',
            'started_at' => now()->subMinutes(10),
            'ended_at' => now(),
            'submitted_at' => now(),
        ]);
        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['q']->id,
            'selected_options' => [$correct ? $data['correct']->id : $data['wrong']->id],
            'answered_at' => now(),
        ]);
        ExamResult::create([
            'exam_attempt_id' => $attempt->id,
            'total_score' => $correct ? 1 : 0,
            'max_possible_score' => 1,
            'percentage' => $correct ? 100 : 0,
            'correct_count' => $correct ? 1 : 0,
            'incorrect_count' => $correct ? 0 : 1,
            'grading_status' => 'completed',
            'graded_at' => now(),
        ]);
    }

    public function test_item_analysis_index_lists_exams(): void
    {
        $data = $this->setupExam();
        $response = $this->actingAs($data['teacher'])->get(route('item-analysis.index'));
        $response->assertOk();
        $response->assertSee('UTS IA');
    }

    public function test_item_analysis_computes_difficulty_and_daya_beda(): void
    {
        $data = $this->setupExam();

        // 4 attempts: 3 correct, 1 incorrect => difficulty 0.75, daya beda > 0
        for ($i = 0; $i < 4; $i++) {
            $siswa = User::create([
                'name' => "Siswa $i", 'email' => "s$i@t.com", 'password' => 'password',
                'role_id' => Role::firstOrCreate(['name' => 'siswa'], ['description' => 'siswa'])->id,
                'class_id' => $data['q']->class_id, 'is_active' => true,
            ]);
            $this->createAttempt($data, $siswa, ($i % 4) !== 3); // 3 benar, 1 salah
        }

        $response = $this->actingAs($data['teacher'])->get(route('item-analysis.show', $data['exam']));
        $response->assertOk();
        $response->assertViewHas('totalAttempts', 4);
        $response->assertViewHas('analysis');

        $analysis = collect($response->viewData('analysis'))->first();
        // difficulty = 3/4 = 0.75
        $this->assertEqualsWithDelta(0.75, $analysis['difficulty'], 0.001);
        $this->assertEquals(3, $analysis['correct']);
        $this->assertEquals(1, $analysis['incorrect']);
        $this->assertEquals(0, $analysis['unanswered']);
        // 27% of 4 = 1 upper, 1 lower; upper group (best score) answered correctly, lower got wrong
        $this->assertGreaterThan(0, $analysis['discrimination']);
    }

    public function test_item_analysis_handles_no_attempts(): void
    {
        $data = $this->setupExam();
        $response = $this->actingAs($data['teacher'])->get(route('item-analysis.show', $data['exam']));
        $response->assertOk();
        $response->assertViewHas('totalAttempts', 0);
        $analysis = collect($response->viewData('analysis'))->first();
        $this->assertEquals(0.0, $analysis['difficulty']);
    }

    public function test_item_analysis_blocks_siswa(): void
    {
        $data = $this->setupExam();
        $siswa = $this->makeUser('siswa');
        $this->actingAs($siswa)->get(route('item-analysis.index'))->assertRedirect(route('dashboard'));
    }
}
