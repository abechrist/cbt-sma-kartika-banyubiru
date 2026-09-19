<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    protected $fillable = [
        'exam_attempt_id', 'total_score', 'max_possible_score', 'percentage',
        'correct_count', 'incorrect_count', 'unanswered_count',
        'grading_status', 'graded_at', 'graded_by',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'max_possible_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'graded_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
