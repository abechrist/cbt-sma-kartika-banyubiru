<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            StudentClassSeeder::class,
            SubjectSeeder::class,
            UserSeeder::class,
            QuestionSeeder::class,
            ExamSeeder::class,
            ExamSessionSeeder::class,
        ]);
    }
}
