<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role?->name, [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
            User::ROLE_GURU,
            User::ROLE_KEPALA_SEKOLAH,
        ]);
    }

    public function view(User $user, Question $question): bool
    {
        if ($user->isGuru()) {
            return $question->created_by === $user->id
                || $user->subjects->contains($question->subject_id);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role?->name, [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
            User::ROLE_GURU,
        ]);
    }

    public function update(User $user, Question $question): bool
    {
        if ($user->isGuru()) {
            return $question->created_by === $user->id;
        }

        return false;
    }

    public function delete(User $user, Question $question): bool
    {
        if ($user->isGuru()) {
            return $question->created_by === $user->id;
        }

        return false;
    }

    public function import(User $user): bool
    {
        return in_array($user->role?->name, [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
            User::ROLE_GURU,
        ]);
    }
}
