<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'chapter',
        'type',
        'file_path',
        'video_url',
        'content_text',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(MaterialProgress::class, 'material_id');
    }

    public function isCompletedBy(int $userId): bool
    {
        return $this->progresses()->where('user_id', $userId)->whereNotNull('completed_at')->exists();
    }
}
