<?php

namespace Database\Seeders;

use App\Models\StudentClass;
use Illuminate\Database\Seeder;

class StudentClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['X-A', 10, '2026/2027', 'Kelas X - A'],
            ['X-B', 10, '2026/2027', 'Kelas X - B'],
            ['XI-A', 11, '2026/2027', 'Kelas XI - A'],
            ['XI-B', 11, '2026/2027', 'Kelas XI - B'],
            ['XII-A', 12, '2026/2027', 'Kelas XII - A'],
            ['XII-B', 12, '2026/2027', 'Kelas XII - B'],
        ];

        foreach ($classes as [$name, $grade, $year, $desc]) {
            StudentClass::updateOrCreate(
                ['name' => $name],
                ['grade' => $grade, 'academic_year' => $year, 'description' => $desc, 'is_active' => true]
            );
        }
    }
}
