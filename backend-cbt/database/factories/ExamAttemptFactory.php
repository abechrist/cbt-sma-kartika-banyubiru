<?php

namespace Database\Factories;

use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamAttemptFactory extends Factory
{
    protected $model = ExamAttempt::class;

    public function definition(): array
    {
        return [
            'exam_session_id' => ExamSession::factory(),
            'user_id' => User::factory(),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(60),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'current_question_order' => 0,
            'is_resumed' => false,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => [
            'status' => ExamAttempt::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'ended_at' => now(),
        ]);
    }
}
