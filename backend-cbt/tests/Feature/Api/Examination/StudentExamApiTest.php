<?php

namespace Tests\Feature\Api\Examination;

use App\Models\Exam;
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

class StudentExamApiTest extends TestCase
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
            'password' => 'Password123!',
            'role_id' => $role->id,
            'class_id' => $class?->id,
            'is_active' => true,
        ]);
    }

    private function makeOpenSession(): array
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $class = StudentClass::create(['name' => 'XI-A', 'grade' => 11, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Fisika', 'code' => 'FIS-'.Str::random(3)]);

        $exam = Exam::create([
            'subject_id' => $subject->id,
            'name' => 'Ujian Tengah Semester Fisika',
            'description' => 'Ujian tengah semester',
            'duration_minutes' => 30,
            'randomize_questions' => false,
            'randomize_options' => false,
            'created_by' => $admin->id,
        ]);

        $q = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_PG,
            'question_text' => 'Berapa hasil 2 + 2?',
            'difficulty' => 'easy',
            'score' => 10,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        $correctOpt = QuestionOption::create([
            'question_id' => $q->id, 'label' => 'A', 'option_text' => '4',
            'is_correct' => true, 'sort_order' => 1,
        ]);
        QuestionOption::create([
            'question_id' => $q->id, 'label' => 'B', 'option_text' => '5',
            'is_correct' => false, 'sort_order' => 2,
        ]);

        $exam->questions()->attach($q->id, ['score' => 10]);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi 1',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(25),
            'status' => ExamSession::STATUS_OPEN,
        ]);

        $token = ExamToken::create([
            'exam_session_id' => $session->id,
            'token' => 'ABC123',
            'is_active' => true,
            'expires_at' => now()->addHour(),
        ]);

        $siswa = $this->makeUser(User::ROLE_SISWA, $class);
        $siswa->update(['password' => bcrypt('Password123!')]);

        return compact('exam', 'session', 'token', 'siswa', 'correctOpt', 'admin');
    }

    public function test_student_can_run_full_api_exam_flow(): void
    {
        $data = $this->makeOpenSession();
        $siswa = $data['siswa'];

        // 1. Login → dapat token
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $siswa->email,
            'password' => 'Password123!',
        ]);

        $login->assertOk();
        $bearer = $login->json('data.token');
        $this->assertNotEmpty($bearer);

        $headers = ['Authorization' => 'Bearer '.$bearer];

        // 2. Validasi token ujian
        $validate = $this->postJson('/api/v1/exam/token/validate', [
            'session_id' => $data['session']->id,
            'token' => $data['token']->token,
        ], $headers);
        $validate->assertOk()->assertJsonPath('data.valid', true);
        $tokenId = $validate->json('data.token_id');

        // 3. Mulai ujian
        $start = $this->postJson('/api/v1/exam/start', [
            'session_id' => $data['session']->id,
            'token_id' => $tokenId,
        ], $headers);
        $start->assertOk();
        $attemptId = $start->json('data.attempt.id');
        $questionId = $start->json('data.questions.0.id');
        $this->assertNotEmpty($attemptId);
        $this->assertNotEmpty($questionId);

        // 4. Simpan jawaban (pilih option benar)
        $save = $this->postJson('/api/v1/exam/answer', [
            'attempt_id' => $attemptId,
            'question_id' => $questionId,
            'selected_options' => [(string) $data['correctOpt']->id],
        ], $headers);
        $save->assertOk();

        // 5. Heartbeat
        $hb = $this->postJson('/api/v1/exam/heartbeat', [
            'attempt_id' => $attemptId,
        ], $headers);
        $hb->assertOk()->assertJsonStructure(['data' => ['time_remaining']]);

        // 6. Cek status
        $status = $this->getJson('/api/v1/exam/status/'.$attemptId, $headers);
        $status->assertOk()
            ->assertJsonPath('data.answered_questions', 1);

        // 7. Submit
        $submit = $this->postJson('/api/v1/exam/submit', [
            'attempt_id' => $attemptId,
        ], $headers);
        $submit->assertOk();
        $this->assertSame('submitted', $submit->json('data.attempt.status'));

        // 8. Ambil hasil
        $result = $this->getJson('/api/v1/exam/result/'.$attemptId, $headers);
        $result->assertOk()
            ->assertJsonPath('data.result.correct_count', 1)
            ->assertJsonPath('data.result.total_score', '10.00');
    }

    public function test_invalid_token_is_rejected(): void
    {
        $data = $this->makeOpenSession();
        $siswa = $data['siswa'];
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $siswa->email,
            'password' => 'Password123!',
        ]);
        $bearer = $login->json('data.token');

        $this->postJson('/api/v1/exam/token/validate', [
            'session_id' => $data['session']->id,
            'token' => 'NOPE999',
        ], ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(404);
    }
}
