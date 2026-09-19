<?php

namespace Tests\Feature\Api;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardAndKioskApiTest extends TestCase
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
            'password' => bcrypt('Password123!'),
            'role_id' => $role->id,
            'class_id' => $class?->id,
            'is_active' => true,
        ]);
    }

    private function makeOpenSession(): ExamSession
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $subject = Subject::create(['name' => 'Fisika', 'code' => 'FIS-'.Str::random(3)]);

        $exam = Exam::create([
            'subject_id' => $subject->id,
            'name' => 'UTS Fisika',
            'duration_minutes' => 30,
            'created_by' => $admin->id,
        ]);

        return ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi Pagi',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(25),
            'status' => ExamSession::STATUS_OPEN,
        ]);
    }

    private function login(User $user): string
    {
        $res = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);
        $res->assertOk();

        return (string) $res->json('data.token');
    }

    public function test_student_dashboard_returns_active_sessions(): void
    {
        $class = StudentClass::create(['name' => 'XI-A', 'grade' => 11, 'academic_year' => '2026/2027']);
        $siswa = $this->makeUser(User::ROLE_SISWA, $class);
        $this->makeOpenSession();

        $bearer = $this->login($siswa);

        $res = $this->getJson('/api/v1/dashboard', [
            'Authorization' => 'Bearer '.$bearer,
        ]);

        $res->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'available_exams',
                    'my_attempts',
                    'completed_exams',
                    'active_sessions',
                    'results',
                ],
            ])
            ->assertJsonCount(1, 'data.active_sessions')
            ->assertJsonPath('data.active_sessions.0.exam.name', 'UTS Fisika');
    }

    public function test_student_dashboard_requires_auth(): void
    {
        $this->getJson('/api/v1/dashboard')->assertStatus(401);
    }

    public function test_kiosk_launch_returns_open_sessions_with_start_end_times(): void
    {
        $this->makeOpenSession();

        $res = $this->getJson('/api/v1/kiosk/launch');

        $res->assertOk()
            ->assertJsonCount(1, 'data.active_sessions')
            ->assertJsonPath('data.active_sessions.0.exam.name', 'UTS Fisika')
            ->assertJsonStructure([
                'data' => [
                    'active_sessions' => [
                        ['id', 'name', 'start_at', 'end_at', 'exam'],
                    ],
                ],
            ]);
    }

    public function test_kiosk_launch_excludes_closed_sessions(): void
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $subject = Subject::create(['name' => 'Kimia', 'code' => 'KIM-'.Str::random(3)]);
        $exam = Exam::create([
            'subject_id' => $subject->id,
            'name' => 'Ujian Kimia',
            'duration_minutes' => 30,
            'created_by' => $admin->id,
        ]);

        ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi Tutup',
            'start_at' => now()->subDay(),
            'end_at' => now()->subHour(),
            'status' => ExamSession::STATUS_COMPLETED,
        ]);

        $res = $this->getJson('/api/v1/kiosk/launch');

        $res->assertOk()
            ->assertJsonCount(0, 'data.active_sessions');
    }
}
