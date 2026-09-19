<?php

namespace Database\Factories;

use App\Models\StudentClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentClassFactory extends Factory
{
    protected $model = StudentClass::class;

    public function definition(): array
    {
        $grade = fake()->randomElement([10, 11, 12]);
        $suffix = strtoupper(fake()->randomLetter);

        return [
            'name' => "{$grade}{$suffix}",
            'grade' => $grade,
            'academic_year' => '2026/2027',
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
