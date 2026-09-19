<?php

namespace Tests\Feature\Dashboard;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\ExamSession;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PimpinanDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $roleName, ?StudentClass $class = null): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::create([
            'name' => ucfirst($roleName),
            'email' => str_replace('_', '.', $roleName).'@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'class_id' => $class?->id,
            'is_active' => true,
        ]);
    }

    public function test_kepala_sekolah_can_access_dashboard_with_stats(): void
    {
        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK-PIMP']);
        $exam = Exam::create([
            'name' => 'PTS Ganjil',
            'subject_id' => $subject->id,
            'duration_minutes' => 60,
            'max_attempts' => 1,
            'created_by' => null,
        ]);
        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi 1',
            'status' => 'open',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
        ]);
        $siswa = $this->makeUser('siswa');
        $siswa->update(['class_id' => $class->id]);
        $attempt = ExamAttempt::create([
            'exam_session_id' => $session->id,
            'user_id' => $siswa->id,
            'status' => 'submitted',
            'started_at' => now()->subMinutes(10),
            'ended_at' => now(),
            'submitted_at' => now(),
        ]);
        ExamResult::create([
            'exam_attempt_id' => $attempt->id,
            'total_score' => 80,
            'max_possible_score' => 100,
            'percentage' => 80,
            'correct_count' => 8,
            'incorrect_count' => 0,
            'unanswered_count' => 2,
            'grading_status' => 'completed',
            'graded_at' => now(),
        ]);

        $user = $this->makeUser('kepala_sekolah');
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
        $response->assertViewHas('avgPercentage', 80.0);
        $response->assertViewHas('participants', 1);
        $response->assertSee('Distribusi Nilai');
    }

    public function test_wali_kelas_can_access_dashboard_with_class_stats(): void
    {
        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK-WALI']);
        $exam = Exam::create([
            'name' => 'PTS Ganjil',
            'subject_id' => $subject->id,
            'duration_minutes' => 60,
            'max_attempts' => 1,
            'created_by' => null,
        ]);
        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi 1',
            'status' => 'open',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
        ]);
        $siswa = $this->makeUser('siswa');
        $siswa->update(['class_id' => $class->id]);
        $attempt = ExamAttempt::create([
            'exam_session_id' => $session->id,
            'user_id' => $siswa->id,
            'status' => 'submitted',
            'started_at' => now()->subMinutes(10),
            'ended_at' => now(),
            'submitted_at' => now(),
        ]);
        ExamResult::create([
            'exam_attempt_id' => $attempt->id,
            'total_score' => 75,
            'max_possible_score' => 100,
            'percentage' => 75,
            'correct_count' => 7,
            'incorrect_count' => 1,
            'unanswered_count' => 2,
            'grading_status' => 'completed',
            'graded_at' => now(),
        ]);

        $user = $this->makeUser('wali_kelas', $class);
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
        $response->assertViewHas('classAverage', 75);
        $response->assertSee('Statistik Ujian Kelas');
    }

    public function test_kepala_sekolah_cannot_access_user_management(): void
    {
        $user = $this->makeUser('kepala_sekolah');
        $this->actingAs($user)->get(route('users.index'))->assertRedirect(route('dashboard'));
    }

    public function test_wali_kelas_cannot_access_grading(): void
    {
        $user = $this->makeUser('wali_kelas');
        $this->actingAs($user)->get(route('grading.index'))->assertRedirect(route('dashboard'));
    }
}
