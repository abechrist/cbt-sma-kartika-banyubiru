<?php

namespace Tests\Feature\Curriculum;

use App\Models\Course;
use App\Models\LearningMaterial;
use App\Models\Role;
use App\Models\Rpp;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $guruRole;

    private Role $koordinatorRole;

    private Role $siswaRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(['name' => User::ROLE_ADMIN], ['description' => 'Admin']);
        $this->guruRole = Role::firstOrCreate(['name' => User::ROLE_GURU], ['description' => 'Guru']);
        $this->koordinatorRole = Role::firstOrCreate(['name' => User::ROLE_KOORDINATOR_KURIKULUM], ['description' => 'Koordinator Kurikulum']);
        $this->siswaRole = Role::firstOrCreate(['name' => User::ROLE_SISWA], ['description' => 'Siswa']);
    }

    public function test_koordinator_kurikulum_can_access_dashboard_and_curriculum_supervision(): void
    {
        $koordinator = User::create([
            'name' => 'IMAM HIDAYAT, S.Pd., Gr',
            'email' => 'imam.hidayat@kartika.sch.id',
            'nip' => 'imam.hidayat',
            'password' => 'password',
            'role_id' => $this->koordinatorRole->id,
            'is_active' => true,
        ]);

        $otherTeacher = User::create([
            'name' => 'SUMERI, S.Pd',
            'email' => 'sumeri@kartika.sch.id',
            'nip' => 'sumeri',
            'password' => 'password',
            'role_id' => $this->guruRole->id,
            'is_active' => true,
        ]);

        // 1. Dashboard utama menampilkan Portal Supervisi Waka Kurikulum + Aktivitas Mengajar Pribadi (Opsi A)
        $this->actingAs($koordinator)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Portal Supervisi Waka Kurikulum')
            ->assertSee('Aktivitas Mengajar Pribadi Anda');

        // 2. Halaman Matriks Supervisi Kurikulum (/curriculum)
        $this->actingAs($koordinator)
            ->get(route('curriculum.index'))
            ->assertOk()
            ->assertSee('Dashboard Pemantauan Koordinator Kurikulum')
            ->assertSee('SUMERI, S.Pd');

        // 3. Halaman Detail Supervisi per Guru (/curriculum/teachers/{teacher})
        $this->actingAs($koordinator)
            ->get(route('curriculum.teachers.show', $otherTeacher))
            ->assertOk()
            ->assertSee('SUMERI, S.Pd', false)
            ->assertSee('1. Supervisi Perencanaan (Modul Ajar & RPP)', false);

        // 4. Opsi A: Koordinator Kurikulum tetap bisa mengakses fitur Guru Pengampu (Bank Soal, Ujian, RPP, LMS)
        $this->actingAs($koordinator)->get(route('questions.index'))->assertOk();
        $this->actingAs($koordinator)->get(route('exams.index'))->assertOk();
        $this->actingAs($koordinator)->get(route('rpps.index'))->assertOk();
        $this->actingAs($koordinator)->get(route('lms.courses.index'))->assertOk();
    }

    public function test_koordinator_kurikulum_can_review_and_approve_teacher_rpp(): void
    {
        $koordinator = User::create([
            'name' => 'Koordinator Kurikulum',
            'email' => 'wakakur@kartika.sch.id',
            'nip' => 'koordinator.kurikulum',
            'password' => 'password',
            'role_id' => $this->koordinatorRole->id,
            'is_active' => true,
        ]);

        $teacher = User::create([
            'name' => 'TITIK SUMARTININGSIH, S.Pd',
            'email' => 'titik.sumartiningsih@kartika.sch.id',
            'nip' => 'titik.sumartiningsih',
            'password' => 'password',
            'role_id' => $this->guruRole->id,
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'name' => 'Bahasa Indonesia',
            'code' => 'BIN',
            'is_active' => true,
        ]);

        $studentClass = StudentClass::create([
            'name' => 'X-1',
            'grade' => 10,
            'academic_year' => '2026/2027',
            'is_active' => true,
        ]);

        $rpp = Rpp::create([
            'subject_id' => $subject->id,
            'class_id' => $studentClass->id,
            'teacher_id' => $teacher->id,
            'academic_year' => '2026/2027',
            'semester' => 'Ganjil',
            'topic' => 'Teks Laporan Hasil Observasi',
            'time_allocation' => '4 x 45 Menit',
            'status' => 'integrated',
        ]);

        $response = $this->actingAs($koordinator)
            ->post(route('curriculum.rpps.review', $rpp), [
                'review_status' => 'approved',
                'review_notes' => 'Modul ajar sudah lengkap dan sesuai dengan Capaian Pembelajaran Fase E.',
            ]);

        $response->assertRedirect();

        $rpp->refresh();
        $this->assertSame('approved', $rpp->review_status);
        $this->assertSame('Modul ajar sudah lengkap dan sesuai dengan Capaian Pembelajaran Fase E.', $rpp->review_notes);
        $this->assertSame($koordinator->id, $rpp->reviewed_by);
        $this->assertNotNull($rpp->reviewed_at);
    }

    public function test_admin_can_assign_and_revoke_koordinator_kurikulum_role_for_teacher(): void
    {
        $admin = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@kartika.sch.id',
            'password' => 'password',
            'role_id' => $this->adminRole->id,
            'is_active' => true,
        ]);

        $teacher = User::create([
            'name' => 'HANIF ULI RONA, S.Pd., Gr',
            'email' => 'hanif.rona@kartika.sch.id',
            'nip' => 'hanif.rona',
            'password' => 'password',
            'role_id' => $this->guruRole->id,
            'is_active' => true,
        ]);

        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK', 'is_active' => true]);
        $studentClass = StudentClass::create(['name' => 'XII-1', 'grade' => 12, 'academic_year' => '2026/2027', 'is_active' => true]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'class_id' => $studentClass->id,
            'teacher_id' => $teacher->id,
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
        ]);
        LearningMaterial::create([
            'course_id' => $course->id,
            'title' => 'Turunan Fungsi Aljabar',
            'type' => 'article',
            'is_published' => true,
            'created_by' => $teacher->id,
        ]);

        // Admin menetapkan guru sebagai Koordinator Kurikulum
        $this->actingAs($admin)
            ->post(route('curriculum.assign-coordinator'), [
                'teacher_id' => $teacher->id,
                'action' => 'assign',
            ])
            ->assertRedirect();

        $teacher->refresh();
        $this->assertTrue($teacher->isKoordinatorKurikulum());
        $this->assertTrue($teacher->isGuru());
        $this->assertSame(1, $teacher->teachingCourses()->count());

        // Admin mencopot kembali menjadi Guru Pengampu biasa
        $this->actingAs($admin)
            ->post(route('curriculum.assign-coordinator'), [
                'teacher_id' => $teacher->id,
                'action' => 'revoke',
            ])
            ->assertRedirect();

        $teacher->refresh();
        $this->assertFalse($teacher->isKoordinatorKurikulum());
        $this->assertTrue($teacher->isGuru());
    }

    public function test_regular_guru_and_siswa_cannot_access_curriculum_supervision_routes(): void
    {
        $guru = User::create([
            'name' => 'Guru Biasa',
            'email' => 'guru.biasa@kartika.sch.id',
            'password' => 'password',
            'role_id' => $this->guruRole->id,
            'is_active' => true,
        ]);

        $siswa = User::create([
            'name' => 'Siswa Biasa',
            'email' => 'siswa.biasa@kartika.sch.id',
            'password' => 'password',
            'role_id' => $this->siswaRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($guru)->get(route('curriculum.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($siswa)->get(route('curriculum.index'))->assertRedirect(route('dashboard'));
    }
}
