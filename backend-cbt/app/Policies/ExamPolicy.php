<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
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
            User::ROLE_PROKTOR,
            User::ROLE_WALI_KELAS,
        ]);
    }

    public function view(User $user, Exam $exam): bool
    {
        if ($user->isGuru()) {
            return $exam->created_by === $user->id
                || $user->subjects->contains($exam->subject_id);
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

    public function update(User $user, Exam $exam): bool
    {
        if ($user->isGuru()) {
            return $exam->created_by === $user->id;
        }

        return false;
    }

    public function delete(User $user, Exam $exam): bool
    {
        if ($user->isGuru()) {
            return $exam->created_by === $user->id;
        }

        return false;
    }

    public function publish(User $user, Exam $exam): bool
    {
        return in_array($user->role?->name, [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
        ]);
    }
}
