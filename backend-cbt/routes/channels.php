<?php

use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Only proktor, admin, and super_admin may listen to a session's monitoring feed.
Broadcast::channel('monitoring.{sessionId}', function (User $user, int $sessionId) {
    return in_array($user->role?->name, [
        User::ROLE_SUPER_ADMIN,
        User::ROLE_ADMIN,
        User::ROLE_PROKTOR,
    ])
        && ExamSession::whereKey($sessionId)->exists();
});
