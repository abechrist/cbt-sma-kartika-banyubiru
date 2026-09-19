<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'is_active', 'teacher_id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_subjects');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Whether this subject is a TKA (Tes Kemampuan Akademik) subject.
     */
    public function isTka(): bool
    {
        return str_starts_with($this->code, 'TKA-');
    }

    /**
     * The TKA subtest name (without TKA- prefix).
     */
    public function tkaSubtest(): ?string
    {
        if (! $this->isTka()) {
            return null;
        }

        return str_replace('TKA-', '', $this->code);
    }
}
