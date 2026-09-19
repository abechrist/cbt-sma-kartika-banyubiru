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

class ExamFlowEnhancedTest extends TestCase
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

    public function test_token_single_use_cannot_be_reused(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $this->actingAs($siswa)
            ->get(route('exam.start', ['token' => $data['token']->token]))
            ->assertRedirect();

        $data['token']->refresh();
        $this->assertFalse($data['token']->is_active);
    }

    public function test_token_already_used_rejected_on_second_try(): void
    {
        $data = $this->makeExamAndSession();
        $siswaA = $this->makeUser(User::ROLE_SISWA);
        $siswaB = $this->makeUser(User::ROLE_SISWA);

        // First student uses token successfully (start)
        $this->actingAs($siswaA)
            ->get(route('exam.start', ['token' => $data['token']->token]))
            ->assertRedirect();

        // Second student cannot reuse the same token
        $this->actingAs($siswaB)
            ->get(route('exam.start', ['token' => $data['token']->token]))
            ->assertSessionHasErrors('token');
    }

    public function test_student_cannot_access_other_students_exam(): void
    {
        $data = $this->makeExamAndSession();
        $siswaA = $this->makeUser(User::ROLE_SISWA);
        $siswaB = $this->makeUser(User::ROLE_SISWA);

        $attemptA = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswaA->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $this->actingAs($siswaB)
            ->get(route('exam.take', $attemptA))
            ->assertForbidden();
    }

    public function test_save_answer_after_exam_submitted_is_blocked(): void
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

    public function test_answer_save_idempotent_same_question(): void
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

        // First save
        $this->actingAs($siswa)
            ->postJson(route('exam.answer.save'), [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $data['pgQuestion']->id,
                'selected_options' => [$data['correctOpt']->id],
            ])
            ->assertOk();

        // Second save with same answer
        $this->actingAs($siswa)
            ->postJson(route('exam.answer.save'), [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $data['pgQuestion']->id,
                'selected_options' => [$data['correctOpt']->id],
            ])
            ->assertOk();

        // Should only have one answer record
        $this->assertEquals(1, Answer::where('exam_attempt_id', $attempt->id)
            ->where('question_id', $data['pgQuestion']->id)
            ->count());
    }

    public function test_save_answer_for_different_questions(): void
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

        // Save PG answer
        $this->actingAs($siswa)
            ->postJson(route('exam.answer.save'), [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $data['pgQuestion']->id,
                'selected_options' => [$data['correctOpt']->id],
            ])
            ->assertOk();

        // Save isian answer
        $this->actingAs($siswa)
            ->postJson(route('exam.answer.save'), [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $data['isianQuestion']->id,
                'answer_text' => '9',
            ])
            ->assertOk();

        // Save bs answer
        $this->actingAs($siswa)
            ->postJson(route('exam.answer.save'), [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $data['bsQuestion']->id,
                'answer_text' => 'benar',
            ])
            ->assertOk();

        $this->assertEquals(3, Answer::where('exam_attempt_id', $attempt->id)->count());
    }

    public function test_double_submit_exam_is_idempotent(): void
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

        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['pgQuestion']->id,
            'selected_options' => [$data['correctOpt']->id],
            'answered_at' => now(),
        ]);

        // First submit
        $this->actingAs($siswa)
            ->post(route('exam.submit', $attempt), ['exam_attempt_id' => $attempt->id])
            ->assertRedirect();

        $attempt->refresh();
        $firstSubmittedAt = $attempt->submitted_at;
        $this->assertEquals(ExamAttempt::STATUS_SUBMITTED, $attempt->status);

        // Second submit (should redirect to result without re-grading)
        $this->actingAs($siswa)
            ->post(route('exam.submit', $attempt), ['exam_attempt_id' => $attempt->id])
            ->assertRedirect();

        $attempt->refresh();
        $secondSubmittedAt = $attempt->submitted_at;
        $this->assertEquals(ExamAttempt::STATUS_SUBMITTED, $attempt->status);
        $this->assertEquals($firstSubmittedAt->toIso8601String(), $secondSubmittedAt->toIso8601String());
    }

    public function test_submit_after_auto_submit_is_blocked(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_AUTO_SUBMITTED,
            'started_at' => now()->subMinutes(35),
            'ended_at' => now()->subMinutes(5),
            'submitted_at' => now()->subMinutes(5),
        ]);

        // After auto-submit, submit should redirect to result (idempotent)
        $this->actingAs($siswa)
            ->post(route('exam.submit', $attempt), ['exam_attempt_id' => $attempt->id])
            ->assertRedirect();

        $attempt->refresh();
        $this->assertEquals(ExamAttempt::STATUS_AUTO_SUBMITTED, $attempt->status);
    }

    public function test_submit_expired_exam_is_rejected(): void
    {
        $data = $this->makeExamAndSession();
        $data['session']->update([
            'status' => ExamSession::STATUS_FINISHED,
        ]);

        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now()->subMinutes(40),
            'ended_at' => now()->subMinutes(10),
        ]);

        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['pgQuestion']->id,
            'selected_options' => [$data['correctOpt']->id],
            'answered_at' => now(),
        ]);

        $this->actingAs($siswa)
            ->post(route('exam.submit', $attempt), ['exam_attempt_id' => $attempt->id])
            ->assertStatus(403);
    }

    public function test_canceled_exam_cannot_be_submitted(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_CANCELLED,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $this->actingAs($siswa)
            ->post(route('exam.submit', $attempt), ['exam_attempt_id' => $attempt->id])
            ->assertStatus(403);
    }

    public function test_expired_token_cannot_start_exam(): void
    {
        $data = $this->makeExamAndSession();
        $data['token']->update(['expires_at' => now()->subHour(), 'is_active' => true]);

        $siswa = $this->makeUser(User::ROLE_SISWA);

        $this->actingAs($siswa)
            ->post(route('exam.token.validate'), ['token' => $data['token']->token])
            ->assertRedirect()
            ->assertSessionHasErrors('token');
    }

    public function test_single_use_token_inactivates_after_first_use(): void
    {
        $data = $this->makeExamAndSession();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $this->actingAs($siswa)
            ->get(route('exam.start', ['token' => $data['token']->token]))
            ->assertRedirect();

        $data['token']->refresh();
        $this->assertFalse($data['token']->is_active);
        $this->assertEquals(1, $data['token']->used_count);
    }

    public function test_exam_start_with_expired_session_token_redirects(): void
    {
        $data = $this->makeExamAndSession();
        $data['token']->update(['expires_at' => now()->subHour()]);

        $siswa = $this->makeUser(User::ROLE_SISWA);

        $this->actingAs($siswa)
            ->post(route('exam.token.validate'), ['token' => $data['token']->token])
            ->assertSessionHasErrors('token');
    }

    public function test_multiple_sessions_independent_token_validation(): void
    {
        $data = $this->makeExamAndSession();

        $data['session2'] = ExamSession::create([
            'exam_id' => $data['exam']->id,
            'name' => 'Sesi 2',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
            'room' => 'Lab 2',
            'status' => ExamSession::STATUS_OPEN,
        ]);

        $token2 = ExamToken::create([
            'exam_session_id' => $data['session2']->id,
            'token' => 'SESI2-TOKEN',
            'expires_at' => now()->addHours(1),
            'is_active' => true,
            'is_single_use' => true,
        ]);

        $siswa = $this->makeUser(User::ROLE_SISWA);

        // Start with session 1 token
        $this->actingAs($siswa)
            ->get(route('exam.start', ['token' => $data['token']->token]))
            ->assertRedirect();

        $data['token']->refresh();
        $this->assertFalse($data['token']->is_active);

        // Verify student has attempt in session 1
        $attempt1 = ExamAttempt::where('user_id', $siswa->id)
            ->where('exam_session_id', $data['session']->id)
            ->first();
        $this->assertNotNull($attempt1);

        // Start with session 2 token (creates new attempt)
        $this->actingAs($siswa)
            ->get(route('exam.start', ['token' => $token2->token]))
            ->assertRedirect();

        $token2->refresh();
        $this->assertFalse($token2->is_active);

        // Verify student has attempt in session 2
        $attempt2 = ExamAttempt::where('user_id', $siswa->id)
            ->where('exam_session_id', $data['session2']->id)
            ->first();
        $this->assertNotNull($attempt2);

        // Both tokens should be inactive
        $this->assertNotEquals($attempt1->id, $attempt2->id);
    }

    public function test_exam_attempt_auto_submit_on_timeout(): void
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
        $this->assertNotNull($attempt->submitted_at);
    }

    public function test_exam_attempt_status_transition_in_progress_to_submitted(): void
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

        $this->assertEquals(ExamAttempt::STATUS_IN_PROGRESS, $attempt->status);

        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['pgQuestion']->id,
            'selected_options' => [$data['correctOpt']->id],
            'answered_at' => now(),
        ]);

        $this->actingAs($siswa)
            ->post(route('exam.submit', $attempt), ['exam_attempt_id' => $attempt->id])
            ->assertRedirect();

        $attempt->refresh();
        $this->assertEquals(ExamAttempt::STATUS_SUBMITTED, $attempt->status);
        $this->assertNotEquals(ExamAttempt::STATUS_IN_PROGRESS, $attempt->status);
    }
}
