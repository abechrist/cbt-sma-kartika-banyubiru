<?php

namespace Tests\Feature\Monitoring;

use App\Models\ActivityLog;
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

class AuditLogTest extends TestCase
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

    private function makeSessionWithToken(): array
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $class = StudentClass::create(['name' => 'XI-A', 'grade' => 11, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Biologi', 'code' => 'BIO-'.Str::random(3)]);

        $exam = Exam::create([
            'name' => 'PTS Biologi',
            'subject_id' => $subject->id,
            'duration_minutes' => 30,
            'randomize_questions' => false,
            'randomize_options' => false,
            'allow_back' => true,
            'show_result_after' => true,
            'status' => Exam::STATUS_PUBLISHED,
            'created_by' => $admin->id,
        ]);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi 1',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
            'room' => 'Lab 2',
            'max_participants' => 40,
            'status' => ExamSession::STATUS_OPEN,
            'allow_resume' => true,
            'auto_submit_on_timeout' => true,
        ]);

        $token = ExamToken::create([
            'exam_session_id' => $session->id,
            'token' => 'AUDIT-'.Str::upper(Str::random(8)),
            'expires_at' => now()->addHours(1),
            'is_active' => true,
            'is_single_use' => true,
        ]);

        return compact('admin', 'exam', 'session', 'token');
    }

    public function test_login_and_logout_are_logged(): void
    {
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $this->post(route('login'), [
            'login' => $siswa->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $siswa->id,
            'action' => ActivityLog::ACTION_LOGIN,
        ]);

        $this->post(route('logout'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $siswa->id,
            'action' => ActivityLog::ACTION_LOGOUT,
        ]);
    }

    public function test_invalid_token_is_logged_as_rejected(): void
    {
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $this->actingAs($siswa)
            ->post(route('exam.token.validate'), ['token' => 'NOT-A-TOKEN'])
            ->assertSessionHasErrors('token');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $siswa->id,
            'action' => ActivityLog::ACTION_TOKEN_REJECTED,
        ]);
    }

    public function test_exam_start_logs_exam_started(): void
    {
        $data = $this->makeSessionWithToken();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $this->actingAs($siswa)
            ->get(route('exam.start', ['token' => $data['token']->token]))
            ->assertRedirect();

        $attempt = ExamAttempt::where('user_id', $siswa->id)->first();

        $this->assertDatabaseHas('activity_logs', [
            'exam_attempt_id' => $attempt->id,
            'user_id' => $siswa->id,
            'action' => ActivityLog::ACTION_EXAM_STARTED,
        ]);
    }

    public function test_resuming_attempt_logs_session_resumed(): void
    {
        $data = $this->makeSessionWithToken();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now()->subMinutes(5),
            'ended_at' => now()->addMinutes(25),
        ]);

        $secondToken = ExamToken::create([
            'exam_session_id' => $data['session']->id,
            'token' => 'RESUME-'.Str::upper(Str::random(8)),
            'expires_at' => now()->addHours(1),
            'is_active' => true,
            'is_single_use' => true,
        ]);

        $this->actingAs($siswa)
            ->get(route('exam.start', ['token' => $secondToken->token]))
            ->assertRedirect();

        $this->assertDatabaseHas('activity_logs', [
            'exam_attempt_id' => $attempt->id,
            'action' => ActivityLog::ACTION_SESSION_RESUMED,
        ]);
    }

    public function test_heartbeat_updates_last_seen(): void
    {
        $data = $this->makeSessionWithToken();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $this->actingAs($siswa)
            ->postJson(route('exam.heartbeat'), ['exam_attempt_id' => $attempt->id])
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['server_time', 'time_remaining']);

        $attempt->refresh();
        $this->assertNotNull($attempt->last_seen_at);
    }

    public function test_heartbeat_rejected_for_other_user(): void
    {
        $data = $this->makeSessionWithToken();
        $siswa = $this->makeUser(User::ROLE_SISWA);
        $other = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $this->actingAs($other)
            ->postJson(route('exam.heartbeat'), ['exam_attempt_id' => $attempt->id])
            ->assertStatus(403);
    }

    public function test_suspicious_activity_logged_and_flags_incremented(): void
    {
        $data = $this->makeSessionWithToken();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $this->actingAs($siswa)
            ->postJson(route('exam.activity'), [
                'exam_attempt_id' => $attempt->id,
                'event' => 'tab_blur',
                'suspicious' => true,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('activity_logs', [
            'exam_attempt_id' => $attempt->id,
            'action' => ActivityLog::ACTION_SUSPICIOUS_ACTIVITY,
        ]);

        $attempt->refresh();
        $this->assertEquals(1, $attempt->suspicious_flags);
    }

    public function test_normal_activity_does_not_increment_flags(): void
    {
        $data = $this->makeSessionWithToken();
        $siswa = $this->makeUser(User::ROLE_SISWA);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $this->actingAs($siswa)
            ->postJson(route('exam.activity'), [
                'exam_attempt_id' => $attempt->id,
                'event' => 'tab_focus',
                'suspicious' => false,
            ])
            ->assertOk();

        $attempt->refresh();
        $this->assertEquals(0, $attempt->suspicious_flags);
    }

    public function test_save_answer_logs_activity_and_touches_last_seen(): void
    {
        $data = $this->makeSessionWithToken();
        $siswa = $this->makeUser(User::ROLE_SISWA);
        $class = StudentClass::first();

        $question = Question::create([
            'subject_id' => $data['exam']->subject_id,
            'class_id' => $class->id,
            'type' => Question::TYPE_PG,
            'question_text' => 'Soal PG audit?',
            'difficulty' => 'easy',
            'score' => 5,
            'is_active' => true,
            'created_by' => $data['admin']->id,
        ]);
        $opt = QuestionOption::create(['question_id' => $question->id, 'label' => 'A', 'option_text' => 'Benar', 'is_correct' => true, 'sort_order' => 1]);
        $data['exam']->questions()->attach($question->id, ['order_in_exam' => 1, 'score' => 5]);

        $attempt = ExamAttempt::create([
            'exam_session_id' => $data['session']->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        $this->actingAs($siswa)
            ->postJson(route('exam.answer.save'), [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_options' => [$opt->id],
            ])
            ->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'exam_attempt_id' => $attempt->id,
            'action' => ActivityLog::ACTION_ANSWER_SAVED,
        ]);

        $attempt->refresh();
        $this->assertNotNull($attempt->last_seen_at);
    }
}
