<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [User::ROLE_SUPER_ADMIN, 'Super Administrator - Akses penuh ke seluruh sistem'],
            [User::ROLE_ADMIN, 'Administrator - Mengelola data master, ujian, dan sesi'],
            [User::ROLE_GURU, 'Guru - Mengelola bank soal dan ujian mata pelajaran'],
            [User::ROLE_KEPALA_SEKOLAH, 'Kepala Sekolah - Melihat hasil dan laporan'],
            [User::ROLE_PROKTOR, 'Proktor - Memantau jalannya ujian'],
            [User::ROLE_SISWA, 'Siswa - Mengikuti ujian'],
            [User::ROLE_WALI_KELAS, 'Wali Kelas - Melihat hasil siswa kelasnya'],
        ];

        foreach ($roles as [$name, $description]) {
            Role::updateOrCreate(['name' => $name], ['description' => $description]);
        }
    }
}
