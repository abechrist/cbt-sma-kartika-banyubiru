<?php

namespace Tests\Feature\Examination;

use App\Http\Controllers\Examination\StudentExamController;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\ExamToken;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExamFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeRole(string $name): Role
    {
        return Role::firstOrCreate(['name' => $name], ['description' => $name]);
    }

    private function makeUser(string $roleName, ?StudentClass $class = null): User
    {
        $role = $this->makeRole($roleName);

        return User::create([
            'name' => 'Test '.ucfirst($roleName),
            'email' => str_replace('_', '.', $roleName).Str::random(4).'@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'class_id' => $class?->id,
            'is_active' => true,
        ]);
    }

    private function makeExamAndSession(): array
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK-'.Str::random(3)]);

        $pgQuestion = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_PG,
            'question_text' => 'Berapa hasil 2 + 2?',
            'difficulty' => 'easy',
            'score' => 5,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $correctOpt = QuestionOption::create(['question_id' => $pgQuestion->id, 'label' => 'A', 'option_text' => '4', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $pgQuestion->id, 'label' => 'B', 'option_text' => '5', 'is_correct' => false, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $pgQuestion->id, 'label' => 'C', 'option_text' => '22', 'is_correct' => false, 'sort_order' => 3]);
        QuestionOption::create(['question_id' => $pgQuestion->id, 'label' => 'D', 'option_text' => '2222', 'is_correct' => false, 'sort_order' => 4]);

        $isianQuestion = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_ISIAN_SINGKAT,
            'question_text' => 'Berapa hasil 3 × 3?',
            'difficulty' => 'medium',
            'score' => 5,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        QuestionOption::create(['question_id' => $isianQuestion->id, 'label' => 'KUNCI', 'option_text' => '9', 'is_correct' => true, 'sort_order' => 1]);

        $bsQuestion = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_BENAR_SALAH,
            'question_text' => '1 + 1 = 2 adalah benar.',
            'difficulty' => 'easy',
            'score' => 5,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        QuestionOption::create(['question_id' => $bsQuestion->id, 'label' => 'BENAR', 'option_text' => 'Benar', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $bsQuestion->id, 'label' => 'SALAH', 'option_text' => 'Salah', 'is_correct' => false, 'sort_order' => 2]);

        $exam = Exam::create([
            'name' => 'UTS Matematika',
            'subject_id' => $subject->id,
            'duration_minutes' => 30,
            'randomize_questions' => false,
            'randomize_options' => false,
            'allow_back' => true,
            'show_result_after' => true,
            'status' => Exam::STATUS_PUBLISHED,
            'created_by' => $admin->id,
        ]);

        $exam->questions()->attach($pgQuestion->id, ['order_in_exam' => 1, 'score' => 5]);
        $exam->questions()->attach($isianQuestion->id, ['order_in_exam' => 2, 'score' => 5]);
        $exam->questions()->attach($bsQuestion->id, ['order_in_exam' => 3, 'score' => 5]);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi 1',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
            'room' => 'Lab 1',
            'max_participants' => 40,
            'status' => ExamSession::STATUS_OPEN,
            'allow_resume' => true,
            'auto_submit_on_timeout' => true,
        ]);

        $token = ExamToken::create([
            'exam_session_id' => $session->id,
            'token' => 'TEST-'.Str::upper(Str::random(8)),
            'expires_at' => now()->addHours(1),
            'is_active' => true,
            'is_single_use' => true,
        ]);

        return compact('admin', 'subject', 'exam', 'session', 'token', 'pgQuestion', 'isianQuestion', 'bsQuestion', 'correctOpt');
    }

    public function test_valid_token_redirects_to_start(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $this->actingAs($siswa)
            ->post(route('exam.token.validate'), ['token' => $data['token']->token])
            ->assertRedirect(route('exam.start', ['token' => $data['token']->token]));
    }

    public function test_invalid_token_rejected(): void
    {
        $siswa = $this->makeUser(User::ROLE_SISWA);
        $this->actingAs($siswa)
            ->post(route('exam.token.validate'), ['token' => 'INVALID-TOKEN'])
            ->assertSessionHasErrors('token');
    }

    public function test_expired_token_rejected(): void
    {
        $data = $this->makeExamAndSession();
        $data['token']->update(['expires_at' => now()->subHour()]);

        $siswa = $this->makeUser(User::ROLE_SISWA);
        $this->actingAs($siswa)
            ->post(route('exam.token.validate'), ['token' => $data['token']->token])
            ->assertSessionHasErrors('token');
    }

    public function test_start_creates_attempt_and_marks_token_used(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $this->actingAs($siswa)
            ->get(route('exam.start', ['token' => $data['token']->token]))
            ->assertRedirect();

        $attempt = ExamAttempt::where('user_id', $siswa->id)->first();
        $this->assertNotNull($attempt);
        $this->assertEquals(ExamAttempt::STATUS_IN_PROGRESS, $attempt->status);

        $data['token']->refresh();
        $this->assertFalse($data['token']->is_active);
    }

    public function test_student_can_view_exam_take_page(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $this->actingAs($siswa)
            ->get(route('exam.take', $attempt))
            ->assertOk()
            ->assertSee('Berapa hasil 2 + 2?');
    }

    public function test_student_cannot_view_other_students_exam(): void
    {
        $data = $this->makeExamAndSession();
        $siswaA = $this->makeUser(User::ROLE_SISWA);
        $siswaB = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswaA->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $this->actingAs($siswaB)
            ->get(route('exam.take', $attempt))
            ->assertForbidden();
    }

    public function test_save_answer_for_pg_question(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $response = $this->actingAs($siswa)
            ->postJson(route('exam.answer.save'), [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $data['pgQuestion']->id,
                'selected_options' => [$data['correctOpt']->id],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('answers', [
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['pgQuestion']->id,
        ]);
    }

    public function test_cannot_save_answer_after_submission(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_SUBMITTED,
            'started_at' => now(),
            'ended_at' => now(),
            'submitted_at' => now(),
        ]);

        $this->actingAs($siswa)
            ->postJson(route('exam.answer.save'), [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $data['pgQuestion']->id,
                'selected_options' => [$data['correctOpt']->id],
            ])
            ->assertStatus(403);
    }

    public function test_submit_exam_grades_answers(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        // Answer PG correctly
        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['pgQuestion']->id,
            'selected_options' => [$data['correctOpt']->id],
            'answered_at' => now(),
        ]);

        // Answer isian correctly
        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['isianQuestion']->id,
            'answer_text' => '9',
            'answered_at' => now(),
        ]);

        // Answer benar/salah correctly
        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['bsQuestion']->id,
            'answer_text' => 'benar',
            'answered_at' => now(),
        ]);

        $this->actingAs($siswa)
            ->post(route('exam.submit', $attempt), ['exam_attempt_id' => $attempt->id])
            ->assertRedirect(route('exam.result', $attempt));

        $attempt->refresh();
        $this->assertEquals(ExamAttempt::STATUS_SUBMITTED, $attempt->status);
        $this->assertNotNull($attempt->result);
        $this->assertEquals(15, (float) $attempt->result->total_score);
        $this->assertEquals(3, $attempt->result->correct_count);
        $this->assertEquals(0, $attempt->result->incorrect_count);
        $this->assertEquals(100.0, (float) $attempt->result->percentage);
    }

    public function test_submit_exam_handles_incorrect_and_unanswered(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        // Wrong PG answer
        $wrongOpt = $data['pgQuestion']->options()->where('is_correct', false)->first();
        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['pgQuestion']->id,
            'selected_options' => [$wrongOpt->id],
            'answered_at' => now(),
        ]);

        $this->actingAs($siswa)
            ->post(route('exam.submit', $attempt), ['exam_attempt_id' => $attempt->id])
            ->assertRedirect(route('exam.result', $attempt));

        $attempt->refresh();
        $this->assertEquals(0, (float) $attempt->result->total_score);
        $this->assertEquals(0, $attempt->result->correct_count);
        $this->assertEquals(1, $attempt->result->incorrect_count);
        $this->assertEquals(2, $attempt->result->unanswered_count);
    }

    public function test_auto_submit_sets_status(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now()->subMinutes(35),
            'ended_at' => now()->subMinutes(5),
        ]);

        $controller = new StudentExamController;
        $controller->autoSubmit($attempt);

        $attempt->refresh();
        $this->assertEquals(ExamAttempt::STATUS_AUTO_SUBMITTED, $attempt->status);
        $this->assertNotNull($attempt->result);
    }

    public function test_exam_result_page_shows_score(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_SUBMITTED,
            'started_at' => now(),
            'ended_at' => now(),
            'submitted_at' => now(),
        ]);

        $attempt->result()->create([
            'total_score' => 10,
            'max_possible_score' => 15,
            'percentage' => 66.67,
            'correct_count' => 2,
            'incorrect_count' => 1,
            'unanswered_count' => 0,
            'grading_status' => 'completed',
        ]);

        $this->actingAs($siswa)
            ->get(route('exam.result', $attempt))
            ->assertOk()
            ->assertSee('66.7');
    }

    public function test_used_single_use_token_cannot_be_reused(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        // First use
        $this->actingAs($siswa)
            ->get(route('exam.start', ['token' => $data['token']->token]))
            ->assertRedirect();

        $data['token']->refresh();
        $this->assertFalse($data['token']->isValid());
    }
}
