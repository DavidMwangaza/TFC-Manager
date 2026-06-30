<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function view(User $user, Subject $subject): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Etudiant') && $subject->student_id === $user->id) return true;
        if ($user->hasRole('Enseignant') && $subject->teacher_id === $user->id) return true;
        if ($user->hasRole('Chef de département') && $subject->department_id === $user->department_id) return true;
        return false;
    }

    public function validate(User $user, Subject $subject): bool
    {
        return $user->hasRole('Chef de département') && $user->department_id === $subject->department_id;
    }

    public function assign(User $user, Subject $subject): bool
    {
        return $this->validate($user, $subject);
    }

    public function authorizeDefense(User $user, Subject $subject): bool
    {
        return $user->hasRole('Enseignant') && $subject->teacher_id === $user->id;
    }

    public function viewOrUpdate(User $user, Subject $subject): bool
    {
        return $user->hasRole('Chef de département') && $user->department_id === $subject->department_id;
    }
}
