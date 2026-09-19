<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamToken extends Model
{
    use HasFactory;

    protected $fillable = ['exam_session_id', 'token', 'expires_at', 'is_active', 'is_single_use', 'used_count'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'is_single_use' => 'boolean',
            'used_count' => 'integer',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function isValid(): bool
    {
        return $this->is_active
            && $this->expires_at > now()
            && (! $this->is_single_use || $this->used_count === 0);
    }

    public function markUsed(): void
    {
        $this->increment('used_count');
        if ($this->is_single_use && $this->used_count >= 1) {
            $this->is_active = false;
            $this->save();
        }
    }
}
