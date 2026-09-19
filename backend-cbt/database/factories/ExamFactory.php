<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'subject_id' => Subject::factory(),
            'description' => fake()->sentence(),
            'duration_minutes' => fake()->randomElement([30, 60, 90, 120]),
            'randomize_questions' => fake()->boolean(),
            'randomize_options' => fake()->boolean(),
            'allow_back' => true,
            'show_result_after' => true,
            'status' => Exam::STATUS_DRAFT,
            'created_by' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => Exam::STATUS_PUBLISHED]);
    }
}
