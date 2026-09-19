<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = ['exam_attempt_id', 'user_id', 'action', 'description', 'ip_address', 'user_agent', 'metadata'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public const ACTION_LOGIN = 'login';

    public const ACTION_LOGOUT = 'logout';

    public const ACTION_EXAM_STARTED = 'exam_started';

    public const ACTION_ANSWER_SAVED = 'answer_saved';

    public const ACTION_EXAM_SUBMITTED = 'exam_submitted';

    public const ACTION_AUTO_SUBMITTED = 'auto_submitted';

    public const ACTION_TOKEN_USED = 'token_used';

    public const ACTION_TOKEN_REJECTED = 'token_rejected';

    public const ACTION_SESSION_RESUMED = 'session_resumed';

    public const ACTION_SUSPICIOUS_ACTIVITY = 'suspicious_activity';

    public const ACTION_MANUAL_GRADED = 'manual_graded';

    public function scopeExamAttempt($query, $examAttemptId)
    {
        return $query->where('exam_attempt_id', $examAttemptId);
    }

    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
