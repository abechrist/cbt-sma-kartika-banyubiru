<?php

namespace Tests\Feature\Grading;

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
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GradingTest extends TestCase
{
    use RefreshDatabase;

    private function makeRole(string $name): Role
    {
        return Role::firstOrCreate(['name' => $name], ['description' => $name]);
    }

    private function makeGuruAndSetup(): array
    {
        $guru = User::create([
            'name' => 'Guru A',
            'email' => 'guru'.Str::random(4).'@test.com',
            'password' => 'password',
            'role_id' => $this->makeRole(User::ROLE_GURU)->id,
            'is_active' => true,
        ]);

        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK-'.Str::random(3)]);

        $esai = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_ESAI,
            'question_text' => 'Jelaskan cara menghitung luas lingkaran',
            'difficulty' => 'hard',
            'score' => 10,
            'is_active' => true,
            'created_by' => $guru->id,
        ]);

        $pg = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_PG,
            'question_text' => '2 + 2 = ?',
            'difficulty' => 'easy',
            'score' => 5,
            'is_active' => true,
            'created_by' => $guru->id,
        ]);
        $pgCorrect = QuestionOption::create(['question_id' => $pg->id, 'label' => 'A', 'option_text' => '4', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $pg->id, 'label' => 'B', 'option_text' => '5', 'is_correct' => false, 'sort_order' => 2]);

        $exam = Exam::create([
            'name' => 'UTS Praktek',
            'subject_id' => $subject->id,
            'duration_minutes' => 30,
            'status' => Exam::STATUS_PUBLISHED,
            'created_by' => $guru->id,
        ]);
        $exam->questions()->attach($pg->id, ['order_in_exam' => 1, 'score' => 5]);
        $exam->questions()->attach($esai->id, ['order_in_exam' => 2, 'score' => 10]);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi Grading',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
            'status' => ExamSession::STATUS_OPEN,
        ]);

        return compact('guru', 'esai', 'pg', 'pgCorrect', 'exam', 'session');
    }

    private function makeSubmittedAttempt(array $data): array
    {
        $siswa = User::create([
            'name' => 'Siswa X',
            'email' => 's'.Str::random(4).'@test.com',
            'password' => 'password',
            'role_id' => $this->makeRole(User::ROLE_SISWA)->id,
            'class_id' => $data['session']->class_id ?? null,
            'is_active' => true,
        ]);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_SUBMITTED,
            'started_at' => now(),
            'ended_at' => now(),
            'submitted_at' => now(),
        ]);

        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['pg']->id,
            'selected_options' => [$data['pgCorrect']->id],
            'answered_at' => now(),
        ]);
        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['esai']->id,
            'answer_text' => 'L = pi r^2, dengan r jari-jari.',
            'answered_at' => now(),
        ]);

        $result = ExamResult::create([
            'exam_attempt_id' => $attempt->id,
            'total_score' => 5,
            'max_possible_score' => 15,
            'percentage' => 33.33,
            'correct_count' => 1,
            'incorrect_count' => 0,
            'unanswered_count' => 1,
            'grading_status' => 'partial',
        ]);

        return [$attempt, $result, $siswa, $attempt->answers()->where('question_id', $data['esai']->id)->first()->id];
    }

    public function test_grading_index_lists_partial_results(): void
    {
        $data = $this->makeGuruAndSetup();
        [$attempt, $result] = $this->makeSubmittedAttempt($data);

        $this->actingAs($data['guru'])
            ->get(route('grading.index'))
            ->assertOk()
            ->assertSee('Siswa X');
    }

    public function test_grade_page_shows_esai_answers(): void
    {
        $data = $this->makeGuruAndSetup();
        [$attempt, $result] = $this->makeSubmittedAttempt($data);

        $this->actingAs($data['guru'])
            ->get(route('grading.grade', $result))
            ->assertOk()
            ->assertSee('Jelaskan cara menghitung luas lingkaran')
            ->assertSee('L = pi r^2, dengan r jari-jari.');
    }

    public function test_update_grades_esai_and_recomputes_result(): void
    {
        $data = $this->makeGuruAndSetup();
        [$attempt, $result] = $this->makeSubmittedAttempt($data);
        $esaiId = $attempt->answers()->where('question_id', $data['esai']->id)->first()->id;

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->actingAs($data['guru'])
            ->post(route('grading.update', $result), [
                'scores' => [$esaiId => 8],
                'notes' => [$esaiId => 'Logika benar, kurang contoh'],
            ])
            ->assertRedirect(route('grading.show', $result));

        $result->refresh();
        $this->assertEquals(13, (float) $result->total_score);
        $this->assertEquals(86.67, (float) $result->percentage);
        $this->assertEquals('completed', $result->grading_status);
        $this->assertNotNull($result->graded_at);
    }

    public function test_regrade_recomputes_auto_and_manual(): void
    {
        $data = $this->makeGuruAndSetup();
        [$attempt, $result] = $this->makeSubmittedAttempt($data);
        $esaiId = $attempt->answers()->where('question_id', $data['esai']->id)->first()->id;
        $attempt->answers()->where('id', $esaiId)->first()->update(['score' => 8, 'graded_by' => $data['guru']->id, 'graded_at' => now()]);

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->actingAs($data['guru'])
            ->post(route('grading.regrade', $result))
            ->assertRedirect(route('grading.show', $result));

        $result->refresh();
        $this->assertEquals(13, (float) $result->total_score);
        $this->assertEquals('completed', $result->grading_status);
    }

    public function test_unrelated_guru_cannot_grade(): void
    {
        $data = $this->makeGuruAndSetup();
        [$attempt, $result] = $this->makeSubmittedAttempt($data);

        $other = User::create([
            'name' => 'Guru B',
            'email' => 'gurub'.Str::random(4).'@test.com',
            'password' => 'password',
            'role_id' => $this->makeRole(User::ROLE_GURU)->id,
            'is_active' => true,
        ]);

        $this->actingAs($other)
            ->get(route('grading.grade', $result))
            ->assertForbidden();
    }
}
