<?php

namespace App\Policies;

use App\Models\ExamAttempt;
use App\Models\User;

class ExamAttemptPolicy
{
    public function view(User $user, ExamAttempt $attempt): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin() || $user->isProktor()) {
            return true;
        }

        if ($user->isGuru()) {
            $exam = $attempt->session->exam;

            return $exam->created_by === $user->id
                || $user->subjects->contains($exam->subject_id);
        }

        return $attempt->user_id === $user->id;
    }
}
