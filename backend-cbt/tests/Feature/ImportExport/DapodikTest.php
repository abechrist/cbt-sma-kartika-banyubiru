<?php

namespace Tests\Feature\ImportExport;

use App\Models\Role;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class DapodikTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::create([
            'name' => 'Test '.ucfirst($roleName),
            'email' => str_replace('_', '.', $roleName).Str::random(4).'@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    private function csvContent(string $body): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('dapodik.csv', $body);
    }

    public function test_admin_can_download_template(): void
    {
        $admin = $this->makeUser('admin');
        $this->actingAs($admin)->get(route('dapodik.template'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=utf-8');
    }

    public function test_import_students_from_dapodik_csv(): void
    {
        StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $siswaRole = Role::firstOrCreate(['name' => 'siswa'], ['description' => 'siswa']);
        $admin = $this->makeUser('admin');

        $csv = "nisn,nama,jenis_kelamin,tanggal_lahir,alamat,rombel\n"
            ."9911000001,Budi Santoso,L,2010-02-02,Jl. Melati 1,X-A\n";

        $this->actingAs($admin)->post(route('dapodik.import'), [
            'file' => $this->csvContent($csv),
        ])->assertRedirect();

        $user = User::where('nisn', '9911000001')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Budi Santoso', $user->name);
        $this->assertEquals('L', $user->gender);
        $this->assertEquals($siswaRole->id, $user->role_id);
        $this->assertNotNull($user->class_id);
    }

    public function test_import_updates_existing_student_by_nisn(): void
    {
        $role = Role::firstOrCreate(['name' => 'siswa'], ['description' => 'siswa']);
        $admin = $this->makeUser('admin');
        User::create([
            'name' => 'Nama Lama',
            'email' => 'old@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'nisn' => '9911000002',
            'is_active' => true,
        ]);

        $csv = "nisn,nama,jenis_kelamin\n9911000002,Nama Baru,P\n";

        $this->actingAs($admin)->post(route('dapodik.import'), [
            'file' => $this->csvContent($csv),
        ])->assertRedirect();

        $this->assertEquals('Nama Baru', User::where('nisn', '9911000002')->first()->name);
        $this->assertEquals(1, User::where('nisn', '9911000002')->count());
    }

    public function test_import_requires_nama_and_nisn(): void
    {
        $admin = $this->makeUser('admin');
        $this->actingAs($admin)->post(route('dapodik.import'), [
            'file' => $this->csvContent("nama\nX\n"),
        ])->assertSessionHas('error');
    }

    public function test_siswa_cannot_import(): void
    {
        $siswa = $this->makeUser('siswa');
        $this->actingAs($siswa)->get(route('dapodik.export'))->assertRedirect();
    }

    public function test_export_students_dapodik_format(): void
    {
        $role = Role::firstOrCreate(['name' => 'siswa'], ['description' => 'siswa']);
        $admin = $this->makeUser('admin');
        User::create([
            'name' => 'Dapodik Siswa',
            'email' => 'dap@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'nisn' => '9911000099',
            'gender' => 'P',
            'is_active' => true,
        ]);

        $resp = $this->actingAs($admin)->get(route('dapodik.export'))->assertOk();
        $this->assertStringContainsString('9911000099', $resp->streamedContent());
        $this->assertStringContainsString('Dapodik Siswa', $resp->streamedContent());
    }
}
