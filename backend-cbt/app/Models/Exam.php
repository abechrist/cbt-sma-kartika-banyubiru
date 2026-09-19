<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'subject_id', 'description', 'duration_minutes',
        'randomize_questions', 'randomize_options', 'allow_back',
        'show_result_after', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'allow_back' => 'boolean',
            'show_result_after' => 'boolean',
        ];
    }

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ARCHIVED = 'archived';

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_question')
            ->withPivot(['order_in_exam', 'score'])
            ->withTimestamps()
            ->orderByPivot('order_in_exam');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    public function totalScore(): float
    {
        return (float) $this->questions->sum('pivot.score');
    }

    public function questionCount(): int
    {
        return $this->questions()->count();
    }

    /**
     * Determine whether this exam is a TKA (Tes Kemampuan Akademik) SMA exam,
     * identified by its subject having a TKA- prefixed code.
     */
    public function isTka(): bool
    {
        $code = $this->subject?->code;

        return is_string($code) && str_starts_with($code, 'TKA-');
    }

    /**
     * TKA subtests shortcut: the subject name without the TKA suffix.
     */
    public function tkaSubtest(): ?string
    {
        if (! $this->isTka()) {
            return null;
        }

        return $this->subject?->name;
    }
}
