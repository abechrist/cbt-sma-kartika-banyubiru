<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::where('name', User::ROLE_SUPER_ADMIN)->first();
        $admin = Role::where('name', User::ROLE_ADMIN)->first();
        $guru = Role::where('name', User::ROLE_GURU)->first();
        $proktor = Role::where('name', User::ROLE_PROKTOR)->first();
        $kepalaSekolah = Role::where('name', User::ROLE_KEPALA_SEKOLAH)->first();
        $siswa = Role::where('name', User::ROLE_SISWA)->first();
        $waliKelas = Role::where('name', User::ROLE_WALI_KELAS)->first();

        $classes = StudentClass::all();

        // Super Admin
        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@kartika.sch.id'],
            ['name' => 'Super Admin', 'password' => 'password', 'role_id' => $superAdmin->id, 'is_active' => true]
        );

        // Admin
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@kartika.sch.id'],
            ['name' => 'Administrator', 'password' => 'password', 'role_id' => $admin->id, 'is_active' => true]
        );

        // Kepala Sekolah
        User::updateOrCreate(
            ['email' => 'kepsek@kartika.sch.id'],
            ['name' => 'Dra. Siti Nurhaliza, M.Pd.', 'password' => 'password', 'role_id' => $kepalaSekolah->id, 'nip' => '19700101 200001 2 001', 'is_active' => true]
        );

        // Proktor
        User::updateOrCreate(
            ['email' => 'proktor@kartika.sch.id'],
            ['name' => 'Proktor Utama', 'password' => 'password', 'role_id' => $proktor->id, 'nip' => '19900101 201501 1 001', 'is_active' => true]
        );

        // Wali Kelas - ditugaskan ke kelas pertama (X-A)
        $firstClass = $classes->first();
        if ($firstClass) {
            User::updateOrCreate(
                ['email' => 'wali@kartika.sch.id'],
                ['name' => 'Rina Wulandari, S.Pd.', 'password' => 'password', 'role_id' => $waliKelas->id, 'nip' => '19880808 201001 2 004', 'class_id' => $firstClass->id, 'is_active' => true]
            );
        }

        // Guru Mata Pelajaran
        $guruUsers = [
            ['Budi Hartono, S.Pd.', 'budi@kartika.sch.id', 'Matematika', '19850505 201001 1 002'],
            ['Indah Permatasari, S.Pd.', 'indah@kartika.sch.id', 'Bahasa Indonesia', '19870707 201101 2 003'],
        ];

        foreach ($guruUsers as [$name, $email, $subjectName, $nip]) {
            $guruUser = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password', 'role_id' => $guru->id, 'nip' => $nip, 'is_active' => true]
            );

            $subject = Subject::where('name', $subjectName)->first();
            if ($subject) {
                $subject->update(['teacher_id' => $guruUser->id]);
                $guruUser->subjects()->syncWithoutDetaching([$subject->id]);
            }
        }

        // Siswa - 10 per kelas minimal (sample)
        $namaSiswa = [
            'Ahmad Fauzan', 'Bella Safira', 'Candra Wijaya', 'Dewi Lestari', 'Eko Prasetyo',
            'Fitri Handayani', 'Galih Ramadhan', 'Hana Rahmawati', 'Irfan Maulana', 'Joko Susilo',
            'Kartika Sari', 'Lukman Hakim', 'Maya Anggraini', 'Nanda Pratama', 'Oktaviani Putri',
        ];

        $index = 0;
        foreach ($classes->take(2) as $class) {
            for ($i = 0; $i < 20 && $index < count($namaSiswa); $i++, $index++) {
                $name = $namaSiswa[$index];
                User::updateOrCreate(
                    ['email' => 'siswa'.($index + 1).'@kartika.sch.id'],
                    [
                        'name' => $name,
                        'password' => 'password',
                        'role_id' => $siswa->id,
                        'class_id' => $class->id,
                        'nisn' => '00'.str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                        'gender' => $index % 2 === 0 ? 'L' : 'P',
                        'birth_date' => fake()->dateTimeBetween('-18 years', '-15 years')->format('Y-m-d'),
                        'address' => fake()->address(),
                        'phone' => fake()->phoneNumber(),
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
