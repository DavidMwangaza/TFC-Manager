<?php

namespace App\Policies;

use App\Models\ThesisFile;
use App\Models\User;

class ThesisFilePolicy
{
    public function download(User $user, ThesisFile $thesisFile): bool
    {
        if ($user->hasRole('Admin')) return true;
        $subject = $thesisFile->subject;
        if ($user->hasRole('Etudiant') && $subject->student_id === $user->id) return true;
        if ($user->hasRole('Enseignant') && $subject->teacher_id === $user->id) return true;
        if ($user->hasRole('Chef de département') && $subject->department_id === $user->department_id) return true;
        return false;
    }
}
