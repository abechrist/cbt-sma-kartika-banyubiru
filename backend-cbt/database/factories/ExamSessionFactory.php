<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamSessionFactory extends Factory
{
    protected $model = ExamSession::class;

    public function definition(): array
    {
        return [
            'exam_id' => Exam::factory(),
            'name' => 'Sesi '.fake()->randomDigitNotNull(),
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addMinutes(120),
            'room' => 'Lab Komputer '.fake()->randomDigitNotNull(),
            'max_participants' => 40,
            'status' => ExamSession::STATUS_SCHEDULED,
            'token_prefix' => 'EXAM',
            'instructions' => fake()->sentence(),
            'allow_resume' => true,
            'auto_submit_on_timeout' => true,
        ];
    }

    public function open(): static
    {
        return $this->state(function () {
            $startAt = now()->subMinutes(5);

            return [
                'status' => ExamSession::STATUS_OPEN,
                'start_at' => $startAt,
                'end_at' => $startAt->copy()->addMinutes(120),
            ];
        });
    }
}
