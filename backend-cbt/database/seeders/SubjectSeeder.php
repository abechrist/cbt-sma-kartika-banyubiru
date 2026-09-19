<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['Matematika', 'MTK', 'Matematika Wajib'],
            ['Bahasa Indonesia', 'BIN', 'Bahasa dan Sastra Indonesia'],
            ['Bahasa Inggris', 'BIG', 'Bahasa Inggris'],
            ['Fisika', 'FIS', 'Fisika'],
            ['Kimia', 'KIM', 'Kimia'],
            ['Biologi', 'BIO', 'Biologi'],
            ['Sejarah', 'SJR', 'Sejarah Indonesia'],
            ['Pendidikan Pancasila', 'PP', 'Pendidikan Pancasila dan Kewarganegaraan'],
            // TKA Subjects
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
                ['name' => $name, 'description' => $desc, 'is_active' => true]
            );
        }
    }
}
