<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\ThesisFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Enregistre un feedback (remarque) sur un fichier (jalon ou mémoire).
     */
    public function store(Request $request, ThesisFile $thesisFile)
    {
        $request->validate([
            'content_remarque' => 'required|string|min:3',
        ]);

        $user = Auth::user();
        $subject = $thesisFile->subject;

        // Contrôle d'accès : Enseignant ou CP du département
        $canComment = ($user->hasRole('Enseignant') && $subject->teacher_id === $user->id)
            || ($user->hasRole('Chef de département') && $subject->department_id === $user->department_id);

        if (!$canComment) {
            abort(403, 'Vous n\'êtes pas autorisé à laisser un feedback sur ce fichier.');
        }

        Feedback::create([
            'thesis_file_id' => $thesisFile->id,
            'author_id' => $user->id,
            'content_remarque' => $request->content_remarque,
        ]);

        return back()->with('success', 'Votre remarque a été ajoutée avec succès.');
    }
}
