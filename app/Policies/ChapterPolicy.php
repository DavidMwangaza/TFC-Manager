<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\User;

class ChapterPolicy
{
    /**
     * Determine whether the user can view the chapter.
     */
    public function view(User $user, Chapter $chapter): bool
    {
        if ($user->hasRole('Admin')) return true;
        if ($user->hasRole('Etudiant') && $chapter->subject->student_id === $user->id) return true;
        if ($user->hasRole('Enseignant') && $chapter->subject->teacher_id === $user->id) return true;
        if ($user->hasRole('Chef de département') && $chapter->subject->department_id === $user->department_id) return true;
        return false;
    }

    /**
     * Determine whether the user can create a chapter.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Etudiant');
    }
}
