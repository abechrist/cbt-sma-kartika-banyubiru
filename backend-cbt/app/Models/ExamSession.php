<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    use HasFactory;

    protected $appends = ['is_tka', 'tka_subtest'];

    protected $fillable = [
        'exam_id', 'name', 'start_at', 'end_at', 'room', 'max_participants',
        'status', 'token_prefix', 'instructions', 'allow_resume', 'auto_submit_on_timeout',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'allow_resume' => 'boolean',
            'auto_submit_on_timeout' => 'boolean',
            'max_participants' => 'integer',
        ];
    }

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_OPEN = 'open';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FINISHED = 'finished';

    public const STATUS_CANCELLED = 'cancelled';

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(ExamToken::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN || $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isAccessible(): bool
    {
        $now = now();

        return $this->status === self::STATUS_OPEN
            && $this->start_at <= $now
            && $this->end_at >= $now;
    }

    public function getActiveTokens(): HasMany
    {
        return $this->hasMany(ExamToken::class)->where('is_active', true);
    }

    /**
     * Whether this session belongs to a TKA (Tes Kemampuan Akademik) SMA exam.
     */
    protected function getIsTkaAttribute(): bool
    {
        return (bool) $this->exam?->isTka();
    }

    /**
     * The TKA subtest name (subject) for this session, or null when not TKA.
     */
    protected function getTkaSubtestAttribute(): ?string
    {
        return $this->exam?->tkaSubtest();
    }
}
