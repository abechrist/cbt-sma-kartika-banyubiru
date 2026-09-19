<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Request;

/**
 * Writes immutable activity entries for the audit trail (FR-8.3, agent-prompt #36).
 */
class ActivityLogger
{
    public static function log(
        string $action,
        ?string $description = null,
        ?ExamAttempt $attempt = null,
        ?User $user = null,
        array $metadata = [],
    ): ActivityLog {
        $entry = new ActivityLog([
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata ?: null,
        ]);

        $entry->attempt()->associate($attempt);
        $entry->user()->associate($user ?? auth()->user());

        $entry->save();

        return $entry;
    }
}
