<?php

namespace Database\Factories;

use App\Models\ExamSession;
use App\Models\ExamToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExamTokenFactory extends Factory
{
    protected $model = ExamToken::class;

    public function definition(): array
    {
        return [
            'exam_session_id' => ExamSession::factory(),
            'token' => strtoupper('EXAM-'.Str::random(8)),
            'expires_at' => now()->addDay(),
            'is_active' => true,
            'is_single_use' => true,
            'used_count' => 0,
        ];
    }
}
