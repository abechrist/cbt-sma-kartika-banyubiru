<?php

namespace Tests\Feature\Kiosk;

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

class KioskTest extends TestCase
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

    private function makeAttempt(): array
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK-'.Str::random(3)]);
        $question = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_PG,
            'question_text' => '1 + 1 = ?',
            'difficulty' => 'easy',
            'score' => 5,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        QuestionOption::create(['question_id' => $question->id, 'label' => 'A', 'option_text' => '2', 'is_correct' => true, 'sort_order' => 1]);

        $exam = Exam::create([
            'name' => 'Ujian Kiosk',
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'duration_minutes' => 30,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);
        $exam->questions()->attach($question->id, ['score' => 5]);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi Kiosk',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
            'status' => ExamSession::STATUS_OPEN,
        ]);

        $siswa = $this->makeUser(User::ROLE_SISWA);
        $attempt = ExamAttempt::create([
            'exam_session_id' => $session->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        return compact('attempt', 'session', 'siswa', 'admin');
    }

    public function test_proktor_can_open_kiosk_launcher(): void
    {
        $proktor = $this->makeUser(User::ROLE_PROKTOR);
        $this->actingAs($proktor)->get(route('kiosk.launch'))->assertOk();
    }

    public function test_siswa_cannot_access_kiosk_launcher(): void
    {
        $siswa = $this->makeUser(User::ROLE_SISWA);
        $this->actingAs($siswa)->get(route('kiosk.launch'))->assertRedirect();
    }

    public function test_proktor_can_open_per_attempt_kiosk(): void
    {
        $data = $this->makeAttempt();
        $proktor = $this->makeUser(User::ROLE_PROKTOR);
        $this->actingAs($proktor)
            ->get(route('kiosk.show', $data['attempt']))
            ->assertOk()
            ->assertSee('kiosk=1');
    }

    public function test_kiosk_launch_url_contains_kiosk_param(): void
    {
        $data = $this->makeAttempt();
        $proktor = $this->makeUser(User::ROLE_PROKTOR);
        $this->actingAs($proktor)->get(route('kiosk.show', $data['attempt']))
            ->assertOk()
            ->assertSee(route('exam.take', $data['attempt']).'?kiosk=1');
    }
}
