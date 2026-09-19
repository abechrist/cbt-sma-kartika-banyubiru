<?php

namespace Tests\Feature\Api;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TkaApiTest extends TestCase
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
            'password' => bcrypt('Password123!'),
            'role_id' => $role->id,
            'is_active' => true,
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

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure TKA subjects exist in test database
        $subjects = [
            ['Literasi Membaca', 'TKA-LMB', 'Literasi Membaca (TKA SMA)'],
            ['Numerasi', 'TKA-NUM', 'Numerasi (TKA SMA)'],
            ['Literasi Sains', 'TKA-LMS', 'Literasi Sains (TKA SMA)'],
            ['Fisika', 'TKA-FIS', 'Fisika (TKA SMA)'],
            ['Kimia', 'TKA-KIM', 'Kimia (TKA SMA)'],
            ['Biologi', 'TKA-BIO', 'Biologi (TKA SMA)'],
            ['Ekonomi', 'TKA-EKO', 'Ekonomi (TKA SMA)'],
            ['Geografi', 'TKA-GEO', 'Geografi (TKA SMA)'],
            ['Sosiologi', 'TKA-SOS', 'Sosiologi (TKA SMA)'],
        ];

        foreach ($subjects as [$name, $code, $desc]) {
            Subject::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'description' => $desc,
                    'is_active' => true,
                ]
            );
        }
    }

    public function test_tka_subjects_are_present(): void
    {
        $tkaSubjects = Subject::where('code', 'like', 'TKA-%')->get();

        $this->assertCount(9, $tkaSubjects);

        $codes = $tkaSubjects->pluck('code')->all();
        foreach (['TKA-LMB', 'TKA-NUM', 'TKA-LMS', 'TKA-FIS', 'TKA-KIM', 'TKA-BIO', 'TKA-EKO', 'TKA-GEO', 'TKA-SOS'] as $expected) {
            $this->assertContains($expected, $codes);
        }
    }

    public function test_kiosk_marks_tka_sessions_with_is_tka_and_subtest(): void
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $subject = Subject::where('code', 'TKA-NUM')->firstOrFail();
        $exam = Exam::create([
            'subject_id' => $subject->id,
            'name' => 'TKA Numerasi',
            'duration_minutes' => 30,
            'created_by' => $admin->id,
        ]);

        ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi TKA',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(25),
            'status' => ExamSession::STATUS_OPEN,
        ]);

        $res = $this->getJson('/api/v1/kiosk/launch');

        $res->assertOk()
            ->assertJsonCount(1, 'data.active_sessions')
            ->assertJsonPath('data.active_sessions.0.is_tka', true)
            ->assertJsonPath('data.active_sessions.0.tka_subtest', 'Numerasi');
    }

    public function test_kiosk_does_not_mark_regular_exam_as_tka(): void
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK', 'is_active' => true]);
        $exam = Exam::create([
            'subject_id' => $subject->id,
            'name' => 'UTS Matematika',
            'duration_minutes' => 30,
            'created_by' => $admin->id,
        ]);

        ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi Reguler',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(25),
            'status' => ExamSession::STATUS_OPEN,
        ]);

        $res = $this->getJson('/api/v1/kiosk/launch');

        $res->assertOk()
            ->assertJsonPath('data.active_sessions.0.is_tka', false)
            ->assertJsonPath('data.active_sessions.0.tka_subtest', null);
    }

    public function test_student_dashboard_exposes_tka_data(): void
    {
        $siswa = $this->makeUser(User::ROLE_SISWA);
        $bearer = $this->login($siswa);

        $res = $this->getJson('/api/v1/dashboard', [
            'Authorization' => 'Bearer '.$bearer,
        ]);

        $res->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'tka_subjects',
                    'tka_exams',
                ],
            ])
            ->assertJsonCount(9, 'data.tka_subjects');
    }
}
