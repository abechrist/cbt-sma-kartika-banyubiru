<?php

namespace Tests\Feature\Grading;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdvancedGradingTest extends TestCase
{
    use RefreshDatabase;

    private function makeRole(string $name): Role
    {
        return Role::firstOrCreate(['name' => $name], ['description' => $name]);
    }

    private function makeUser(string $roleName): User
    {
        $role = $this->makeRole($roleName);

        return User::create([
            'name' => 'Test '.ucfirst($roleName),
            'email' => str_replace('_', '.', $roleName).Str::random(4).'@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    private function makeSetup(): array
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK-'.Str::random(3)]);

        $kompleks = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_PG_KOMPLEKS,
            'question_text' => 'Pilih semua bilangan prima',
            'difficulty' => 'medium',
            'score' => 5,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        $k1 = QuestionOption::create(['question_id' => $kompleks->id, 'label' => 'A', 'option_text' => '2', 'is_correct' => true, 'sort_order' => 1]);
        $k2 = QuestionOption::create(['question_id' => $kompleks->id, 'label' => 'B', 'option_text' => '4', 'is_correct' => false, 'sort_order' => 2]);
        $k3 = QuestionOption::create(['question_id' => $kompleks->id, 'label' => 'C', 'option_text' => '9', 'is_correct' => false, 'sort_order' => 3]);
        $k4 = QuestionOption::create(['question_id' => $kompleks->id, 'label' => 'D', 'option_text' => '3', 'is_correct' => true, 'sort_order' => 4]);

        $menjodohkan = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_MENJODOHKAN,
            'question_text' => 'Pasangkan operasi dengan hasilnya',
            'difficulty' => 'medium',
            'score' => 5,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        $m1 = QuestionOption::create(['question_id' => $menjodohkan->id, 'label' => 'A', 'option_text' => '7 × 8', 'correct_match' => '56', 'is_correct' => false, 'sort_order' => 1]);
        $m2 = QuestionOption::create(['question_id' => $menjodohkan->id, 'label' => 'B', 'option_text' => '96 ÷ 12', 'correct_match' => '8', 'is_correct' => false, 'sort_order' => 2]);
        $m3 = QuestionOption::create(['question_id' => $menjodohkan->id, 'label' => 'C', 'option_text' => '15 × 8', 'correct_match' => '120', 'is_correct' => false, 'sort_order' => 3]);
        $m4 = QuestionOption::create(['question_id' => $menjodohkan->id, 'label' => 'D', 'option_text' => '121 ÷ 11', 'correct_match' => '11', 'is_correct' => false, 'sort_order' => 4]);

        $esai = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_ESAI,
            'question_text' => 'Jelaskan cara menghitung luas lingkaran',
            'difficulty' => 'hard',
            'score' => 10,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $exam = Exam::create([
            'name' => 'UTS MTK Kompleks',
            'subject_id' => $subject->id,
            'duration_minutes' => 30,
            'randomize_questions' => false,
            'randomize_options' => false,
            'allow_back' => true,
            'status' => Exam::STATUS_PUBLISHED,
            'created_by' => $admin->id,
        ]);

        foreach ([$kompleks, $menjodohkan, $esai] as $idx => $q) {
            $exam->questions()->attach($q->id, ['order_in_exam' => $idx + 1, 'score' => $q->score]);
        }

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi Kompleks',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
            'status' => ExamSession::STATUS_OPEN,
            'allow_resume' => true,
        ]);

        return compact('kompleks', 'menjodohkan', 'esai', 'exam', 'session', 'k1', 'k2', 'k3', 'k4', 'm1', 'm2', 'm3', 'm4');
    }

    public function test_auto_gradability_flags(): void
    {
        $data = $this->makeSetup();

        $this->assertTrue($data['kompleks']->isAutoGradable());
        $this->assertTrue($data['menjodohkan']->isAutoGradable());
        $this->assertFalse($data['esai']->isAutoGradable());
    }

    public function test_pg_kompleks_correct_when_all_correct_selected(): void
    {
        $data = $this->makeSetup();

        $answer = new Answer([
            'exam_attempt_id' => 1,
            'question_id' => $data['kompleks']->id,
            'selected_options' => [$data['k1']->id, $data['k4']->id],
        ]);

        $this->assertTrue($answer->isCorrect());
    }

    public function test_pg_kompleks_incorrect_when_selection_incomplete(): void
    {
        $data = $this->makeSetup();

        $answer = new Answer([
            'exam_attempt_id' => 1,
            'question_id' => $data['kompleks']->id,
            'selected_options' => [$data['k1']->id],
        ]);

        $this->assertFalse($answer->isCorrect());
    }

    public function test_pg_kompleks_incorrect_when_wrong_option_selected(): void
    {
        $data = $this->makeSetup();

        $answer = new Answer([
            'exam_attempt_id' => 1,
            'question_id' => $data['kompleks']->id,
            'selected_options' => [$data['k1']->id, $data['k2']->id],
        ]);

        $this->assertFalse($answer->isCorrect());
    }

    public function test_menjodohkan_correct_when_all_matched(): void
    {
        $data = $this->makeSetup();

        $answer = new Answer([
            'exam_attempt_id' => 1,
            'question_id' => $data['menjodohkan']->id,
            'selected_options' => [
                $data['m1']->id => '56',
                $data['m2']->id => '8',
                $data['m3']->id => '120',
                $data['m4']->id => '11',
            ],
        ]);

        $this->assertTrue($answer->isCorrect());
    }

    public function test_menjodohkan_incorrect_when_one_pair_mismatched(): void
    {
        $data = $this->makeSetup();

        $answer = new Answer([
            'exam_attempt_id' => 1,
            'question_id' => $data['menjodohkan']->id,
            'selected_options' => [
                $data['m1']->id => '8',
                $data['m2']->id => '56',
                $data['m3']->id => '120',
                $data['m4']->id => '11',
            ],
        ]);

        $this->assertFalse($answer->isCorrect());
    }

    public function test_menjodohkan_incorrect_when_partially_answered(): void
    {
        $data = $this->makeSetup();

        $answer = new Answer([
            'exam_attempt_id' => 1,
            'question_id' => $data['menjodohkan']->id,
            'selected_options' => [$data['m1']->id => '56'],
        ]);

        $this->assertFalse($answer->isCorrect());
    }

    public function test_submit_grades_all_new_types(): void
    {
        $data = $this->makeSetup();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['kompleks']->id,
            'selected_options' => [$data['k1']->id, $data['k4']->id],
            'answered_at' => now(),
        ]);

        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['menjodohkan']->id,
            'selected_options' => [
                $data['m1']->id => '56',
                $data['m2']->id => '8',
                $data['m3']->id => '120',
                $data['m4']->id => '11',
            ],
            'answered_at' => now(),
        ]);

        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['esai']->id,
            'answer_text' => 'Isi jawaban esai ...',
            'answered_at' => now(),
        ]);

        $this->actingAs($siswa)
            ->post(route('exam.submit', $attempt), ['exam_attempt_id' => $attempt->id])
            ->assertRedirect(route('exam.result', $attempt));

        $attempt->refresh();
        $this->assertEquals(ExamAttempt::STATUS_SUBMITTED, $attempt->status);
        $this->assertEquals(10, (float) $attempt->result->total_score);
        $this->assertEquals(2, $attempt->result->correct_count);
        $this->assertEquals(0, $attempt->result->incorrect_count);
        $this->assertEquals(1, $attempt->result->unanswered_count);
        $this->assertEquals('partial', $attempt->result->grading_status);
    }

    public function test_question_option_validation_accepts_correct_match(): void
    {
        $data = $this->makeSetup();
        $this->actingAs($this->makeUser(User::ROLE_GURU));

        $response = $this->post(route('questions.store'), [
            'subject_id' => $data['exam']->subject_id,
            'type' => Question::TYPE_MENJODOHKAN,
            'question_text' => 'Pasangkan ibu kota dengan negara',
            'difficulty' => 'easy',
            'score' => '5',
            'options' => [
                ['label' => 'A', 'option_text' => 'Indonesia', 'correct_match' => 'Jakarta'],
                ['label' => 'B', 'option_text' => 'Malaysia', 'correct_match' => 'Kuala Lumpur'],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('questions', ['type' => Question::TYPE_MENJODOHKAN, 'question_text' => 'Pasangkan ibu kota dengan negara']);
        $this->assertDatabaseHas('question_options', ['option_text' => 'Indonesia', 'correct_match' => 'Jakarta']);
    }

    public function test_question_option_validation_allows_uncorrected_pg_kompleks(): void
    {
        $data = $this->makeSetup();
        $this->actingAs($this->makeUser(User::ROLE_GURU));

        $response = $this->post(route('questions.store'), [
            'subject_id' => $data['exam']->subject_id,
            'type' => Question::TYPE_PG_KOMPLEKS,
            'question_text' => 'Pilih semua yang benar',
            'difficulty' => 'easy',
            'score' => '5',
            'options' => [
                ['label' => 'A', 'option_text' => 'Opsi A', 'is_correct' => '1'],
                ['label' => 'B', 'option_text' => 'Opsi B'],
                ['label' => 'C', 'option_text' => 'Opsi C'],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('question_options', ['option_text' => 'Opsi B', 'is_correct' => false]);
    }
}
