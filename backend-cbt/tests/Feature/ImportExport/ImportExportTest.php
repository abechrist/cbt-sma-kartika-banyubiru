<?php

namespace Tests\Feature\ImportExport;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\ExamSession;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::create([
            'name' => ucfirst($roleName),
            'email' => str_replace('_', '.', $roleName).'@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_import_export_page(): void
    {
        $user = $this->makeUser('admin');
        $this->actingAs($user)->get(route('import_export'))->assertOk();
    }

    public function test_import_siswa_csv(): void
    {
        $role = Role::firstOrCreate(['name' => 'siswa'], ['description' => 'siswa']);
        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $user = $this->makeUser('admin');

        $csv = "name,email,nisn,class\n".
            "Andi Pratama,andi@test.com,12345,X-A\n".
            "Budi Santoso,budi@test.com,12346,X-B\n";

        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csv);

        $response = $this->actingAs($user)->post(route('import.siswa'), ['file' => $file]);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'andi@test.com', 'role_id' => $role->id, 'class_id' => $class->id,
        ]);
        // kelas X-B tidak ada -> baris tsb dilewati dengan error
        $this->assertDatabaseMissing('users', ['email' => 'budi@test.com']);
    }

    public function test_import_guru_csv(): void
    {
        $role = Role::firstOrCreate(['name' => 'guru'], ['description' => 'guru']);
        $user = $this->makeUser('admin');

        $csv = "name,email,nip\nPak Ahmad,ahmad@test.com,001";

        $file = UploadedFile::fake()->createWithContent('guru.csv', $csv);

        $this->actingAs($user)->post(route('import.guru'), ['file' => $file])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'ahmad@test.com', 'role_id' => $role->id]);
    }

    public function test_import_kelas_csv(): void
    {
        $user = $this->makeUser('admin');
        $csv = "name,grade,academic_year\nXII-IPA,12,2026/2027\nXI-IPS,11,2026/2027";

        $file = UploadedFile::fake()->createWithContent('kelas.csv', $csv);

        $this->actingAs($user)->post(route('import.kelas'), ['file' => $file])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('classes', ['name' => 'XII-IPA', 'grade' => 12]);
        $this->assertDatabaseHas('classes', ['name' => 'XI-IPS', 'grade' => 11]);
    }

    public function test_import_question_csv(): void
    {
        $this->makeUser('admin');
        $guru = $this->makeUser('guru');

        $csv = "question_text,type,subject,score\n".
            "Berapa hasil 2+2?,pg,Matematika,1\n".
            'Jelaskan fotosintesis,esai,Biologi,5';

        $file = UploadedFile::fake()->createWithContent('soal.csv', $csv);

        $this->actingAs($guru)->post(route('import.question'), ['file' => $file])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('questions', ['question_text' => 'Berapa hasil 2+2?', 'type' => 'pg']);
        $this->assertDatabaseHas('questions', ['question_text' => 'Jelaskan fotosintesis', 'type' => 'esai']);
        $this->assertDatabaseHas('subjects', ['name' => 'Matematika']);
        $this->assertDatabaseHas('subjects', ['name' => 'Biologi']);
    }

    public function test_unauthorized_role_cannot_import(): void
    {
        $user = $this->makeUser('siswa');
        $file = UploadedFile::fake()->createWithContent('kelas.csv', "name,grade,academic_year\nX-A,10,2026/2027");

        $this->actingAs($user)->post(route('import.kelas'), ['file' => $file])
            ->assertRedirect(route('dashboard'));
    }

    public function test_export_siswa_csv(): void
    {
        $role = Role::firstOrCreate(['name' => 'siswa'], ['description' => 'siswa']);
        User::create(['name' => 'Zainab', 'email' => 'z@test.com', 'password' => 'password', 'role_id' => $role->id, 'is_active' => true, 'nisn' => '9999']);
        $user = $this->makeUser('admin');

        $response = $this->actingAs($user)->get(route('export.siswa'));
        $response->assertOk();
        $response->assertDownload();
        $csv = $response->streamedContent();
        $this->assertStringContainsString('Zainab', $csv);
        $this->assertStringContainsString('z@test.com', $csv);
    }

    public function test_export_rekap_csv(): void
    {
        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK-EXP']);
        $exam = Exam::create(['name' => 'PTS', 'subject_id' => $subject->id, 'duration_minutes' => 60, 'max_attempts' => 1]);
        $session = ExamSession::create(['exam_id' => $exam->id, 'name' => 'Sesi 1', 'status' => 'completed', 'start_at' => now()->subHour(), 'end_at' => now()]);

        $siswa = $this->makeUser('siswa');
        $siswa->update(['class_id' => $class->id, 'nisn' => '111']);
        $attempt = ExamAttempt::create(['exam_session_id' => $session->id, 'user_id' => $siswa->id, 'status' => 'submitted', 'started_at' => now()->subMinutes(10), 'ended_at' => now(), 'submitted_at' => now()]);
        ExamResult::create(['exam_attempt_id' => $attempt->id, 'total_score' => 8, 'max_possible_score' => 10, 'percentage' => 80, 'correct_count' => 8, 'grading_status' => 'completed', 'graded_at' => now()]);

        $user = $this->makeUser('admin');
        $response = $this->actingAs($user)->get(route('export.rekap_nilai'));
        $response->assertOk();
        $csv = $response->streamedContent();
        $this->assertStringContainsString('PTS', $csv);
        $this->assertStringContainsString('80.0%', $csv);
    }

    public function test_export_laporan_csv(): void
    {
        $user = $this->makeUser('admin');
        $response = $this->actingAs($user)->get(route('export.laporan'));
        $response->assertOk();
        $this->assertStringContainsString('Laporan Hasil Ujian', $response->streamedContent());
    }
}
