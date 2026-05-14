<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    /**
     * Create a new chapter for a subject (student only).
     */
    public function store(Request $request, Subject $subject)
    {
        $user = Auth::user();

        // Seul l'étudiant propriétaire du sujet peut ajouter des chapitres
        if (! $user->hasRole('Etudiant') || $subject->student_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'nullable|integer',
        ]);

        $chapter = Chapter::create([
            'subject_id' => $subject->id,
            'title' => $request->title,
            'position' => $request->integer('position') ?? 0,
        ]);

        return redirect()->route('subjects.show', $subject)->with('success', 'Chapitre ajouté avec succès.');
    }

    /**
     * Show chapter details (with versions).
     */
    public function show(Chapter $chapter)
    {
        $chapter->load('versions.author');
        return view('chapters.show', compact('chapter'));
    }
}
