<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'class_id' => StudentClass::factory(),
            'type' => fake()->randomElement([
                Question::TYPE_PG,
                Question::TYPE_PG_KOMPLEKS,
                Question::TYPE_BENAR_SALAH,
                Question::TYPE_ISIAN_SINGKAT,
                Question::TYPE_ESAI,
            ]),
            'question_text' => fake()->sentence(15).'?',
            'difficulty' => fake()->randomElement([
                Question::DIFFICULTY_EASY,
                Question::DIFFICULTY_MEDIUM,
                Question::DIFFICULTY_HARD,
            ]),
            'score' => 5,
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }

    public function pg(): static
    {
        return $this->state(fn () => ['type' => Question::TYPE_PG]);
    }

    public function isian(): static
    {
        return $this->state(fn () => ['type' => Question::TYPE_ISIAN_SINGKAT]);
    }

    public function esai(): static
    {
        return $this->state(fn () => ['type' => Question::TYPE_ESAI]);
    }
}
