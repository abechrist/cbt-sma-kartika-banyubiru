<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id', 'user_id', 'status', 'started_at', 'ended_at',
        'submitted_at', 'last_seen_at', 'suspicious_flags', 'ip_address', 'user_agent', 'current_question_order', 'is_resumed',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'submitted_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'suspicious_flags' => 'integer',
        'is_resumed' => 'boolean',
        'current_question_order' => 'integer',
    ];

    public const STATUS_NOT_STARTED = 'not_started';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_AUTO_SUBMITTED = 'auto_submitted';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(ExamResult::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isStarted(): bool
    {
        return in_array($this->status, [
            self::STATUS_IN_PROGRESS,
            self::STATUS_SUBMITTED,
            self::STATUS_AUTO_SUBMITTED,
            self::STATUS_EXPIRED,
        ]);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUBMITTED,
            self::STATUS_AUTO_SUBMITTED,
            self::STATUS_EXPIRED,
            self::STATUS_CANCELLED,
        ]);
    }

    public function canResume(): bool
    {
        return $this->isStarted() && ! $this->isCompleted() && $this->session->allow_resume;
    }

    public function getTimeRemaining(): int
    {
        if (! $this->started_at || $this->isCompleted()) {
            return 0;
        }

        $endTime = $this->ended_at ?: $this->session->end_at;
        if (! $endTime) {
            return 0;
        }

        $seconds = $endTime->getTimestamp() - now()->getTimestamp();

        return max(0, $seconds);
    }

    public function getProgressPercentage(): float
    {
        $totalQuestions = $this->session->exam->questionCount();
        if ($totalQuestions === 0) {
            return 0.0;
        }

        $answered = $this->answers()->count();

        return min(100.0, round(($answered / $totalQuestions) * 100, 2));
    }
}
