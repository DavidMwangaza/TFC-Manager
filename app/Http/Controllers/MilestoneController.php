<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Milestone;
use App\Models\Subject;
use App\Models\ThesisFile;
use App\Notifications\MilestoneAssigned;
use App\Notifications\MilestoneValidated as MilestoneValidatedNotif;
use App\Notifications\MilestoneRejected as MilestoneRejectedNotif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MilestoneController extends Controller
{
    /**
     * Le professeur (ou CP) crée un jalon pour un sujet.
     */
    public function store(Request $request, Subject $subject)
    {
        $user = Auth::user();

        $allowed = ($user->hasRole('Enseignant') && $subject->teacher_id === $user->id);

        if (!$allowed) {
            abort(403, 'Droits insuffisants pour créer un jalon.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date|after:now',
            'correction_deadline' => 'nullable|date|after_or_equal:due_date',
        ]);

        $milestone = $subject->milestones()->create([
            'title' => $validated['title'],
            'due_date' => $validated['due_date'],
            'correction_deadline' => $validated['correction_deadline'] ?? null,
            'status' => 'pending',
        ]);

        if ($subject->student) {
            $subject->student->notify(new MilestoneAssigned($milestone));
        }

        ActivityLog::log('created', 'Jalon créé', $milestone);

        return back()->with('success', 'Jalon créé et l\'étudiant en a été informé.');
    }

    /**
     * L'enseignant valide le jalon.
     */
    public function validateMilestone(Request $request, Milestone $milestone)
    {
        $user = Auth::user();

        if (!$user->hasRole('Enseignant') || $milestone->subject->teacher_id !== $user->id) {
            abort(403, 'Droits insuffisants pour valider ce jalon.');
        }

        $milestone->update([
            'status' => 'validated',
            'comments' => $request->input('comments'),
        ]);

        if ($milestone->subject->student) {
            $milestone->subject->student->notify(new MilestoneValidatedNotif($milestone));
        }

        ActivityLog::log('milestone_validated', 'Jalon validé par l\'enseignant', $milestone);

        return back()->with('success', 'Étape validée.');
    }

    /**
     * L'enseignant rejette le jalon (à refaire) avec commentaires.
     */
    public function reject(Request $request, Milestone $milestone)
    {
        $user = Auth::user();

        if (!$user->hasRole('Enseignant') || $milestone->subject->teacher_id !== $user->id) {
            abort(403, 'Droits insuffisants pour rejeter ce jalon.');
        }

        $request->validate(['comments' => 'required|string|min:10']);

        $milestone->update([
            'status' => 'rejected',
            'comments' => $request->comments,
        ]);

        if ($milestone->subject->student) {
            $milestone->subject->student->notify(new MilestoneRejectedNotif($milestone));
        }

        ActivityLog::log('milestone_rejected', 'Jalon rejeté (corrections demandées)', $milestone);

        return back()->with('success', 'Étape marquée comme "À refaire" et l\'étudiant en a été informé.');
    }

    /**
     * L'enseignant supprime (annule) un jalon fixé par erreur.
     */
    public function destroy(Milestone $milestone)
    {
        $user = Auth::user();

        if (!$user->hasRole('Enseignant') || $milestone->subject->teacher_id !== $user->id) {
            abort(403, 'Droits insuffisants pour supprimer ce jalon.');
        }

        if ($milestone->status !== 'pending') {
            return back()->with('error', 'Impossible de supprimer un jalon qui n\'est plus en attente.');
        }

        // Vérifier s'il y a des fichiers associés au jalon (livrables)
        if ($milestone->thesisFile) {
            return back()->with('error', 'Impossible de supprimer ce jalon car l\'étudiant a déjà soumis un document.');
        }

        $milestoneTitle = $milestone->title;
        $milestone->delete();

        ActivityLog::log('milestone_deleted', 'Jalon supprimé : ' . $milestoneTitle, $milestone->subject);

        return back()->with('success', 'Jalon supprimé avec succès.');
    }
}
