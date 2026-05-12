<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Milestone;
use App\Models\Subject;
use App\Notifications\MilestoneAssigned;
use App\Notifications\MilestoneSubmitted as MilestoneSubmittedNotif;
use App\Notifications\MilestoneValidated as MilestoneValidatedNotif;
use App\Notifications\MilestoneRejected as MilestoneRejectedNotif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    /**
     * Le professeur (ou CP) crée un jalon pour un sujet.
     */
    public function store(Request $request, Subject $subject)
    {
        $user = Auth::user();

        // Vérifier droits: enseignant responsable ou Chef de département de la filière
        $allowed = ($user->hasRole('Enseignant') && $subject->teacher_id === $user->id)
            || ($user->hasRole('Chef de département') && $subject->department_id === $user->department_id);

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

        // Notifier l'étudiant
        if ($subject->student) {
            $subject->student->notify(new MilestoneAssigned($milestone));
        }

        ActivityLog::log('created', 'Jalon créé', $milestone);

        return back()->with('success', 'Jalon créé et l\'étudiant en a été informé.');
    }

    /**
     * L'étudiant soumet la partie demandée pour un jalon.
     */
    public function submit(Request $request, Milestone $milestone)
    {
        $user = Auth::user();

        if (!$user->hasRole('Etudiant') || $milestone->subject->student_id !== $user->id) {
            abort(403, 'Droits insuffisants pour soumettre ce jalon.');
        }

        if (!in_array($milestone->status, ['pending', 'rejected'])) {
            return back()->with('info', 'Cette étape n\'est pas ouverte pour soumission.');
        }

        $milestone->update([
            'submission_date' => now(),
            'status' => 'submitted',
        ]);

        // Notifier l'enseignant encadreur
        if ($milestone->subject->teacher) {
            $milestone->subject->teacher->notify(new MilestoneSubmittedNotif($milestone));
        }

        ActivityLog::log('milestone_submitted', 'Jalon soumis par l\'étudiant', $milestone);

        return back()->with('success', 'Partie déposée. Le professeur en sera informé.');
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
}
