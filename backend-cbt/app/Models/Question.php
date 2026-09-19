<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id', 'class_id', 'type', 'question_text',
        'image_path', 'audio_path', 'video_path',
        'difficulty', 'score', 'competency_code', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'score' => 'decimal:2',
        ];
    }

    public const TYPE_PG = 'pg';

    public const TYPE_PG_KOMPLEKS = 'pg_kompleks';

    public const TYPE_BENAR_SALAH = 'benar_salah';

    public const TYPE_MENJODOHKAN = 'menjodohkan';

    public const TYPE_ISIAN_SINGKAT = 'isian_singkat';

    public const TYPE_ESAI = 'esai';

    public const DIFFICULTY_EASY = 'easy';

    public const DIFFICULTY_MEDIUM = 'medium';

    public const DIFFICULTY_HARD = 'hard';

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('sort_order');
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_question')
            ->withPivot(['order_in_exam', 'score'])
            ->withTimestamps();
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function getCorrectOptions(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->where('is_correct', true);
    }

    public function isAutoGradable(): bool
    {
        return in_array($this->type, [
            self::TYPE_PG,
            self::TYPE_PG_KOMPLEKS,
            self::TYPE_BENAR_SALAH,
            self::TYPE_ISIAN_SINGKAT,
            self::TYPE_MENJODOHKAN,
        ]);
    }
}
