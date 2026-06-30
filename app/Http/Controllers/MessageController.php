<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Subject;
use App\Notifications\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Envoyer un message dans la conversation du sujet.
     * Seuls l'étudiant propriétaire et son directeur peuvent poster.
     */
    public function store(Request $request, Subject $subject)
    {
        $user = Auth::user();

        // Autorisation : seul l'étudiant du sujet ou son directeur peuvent envoyer
        $isStudent  = $user->hasRole('Etudiant') && $subject->student_id === $user->id;
        $isDirector = $user->hasRole('Enseignant') && $subject->teacher_id === $user->id;

        if (!$isStudent && !$isDirector) {
            abort(403, 'Vous n\'êtes pas autorisé à envoyer un message dans cette conversation.');
        }

        $request->validate([
            'body' => 'required|string|min:1|max:2000',
        ], [
            'body.required' => 'Le message ne peut pas être vide.',
            'body.max'      => 'Le message ne peut pas dépasser 2000 caractères.',
        ]);

        $message = Message::create([
            'subject_id' => $subject->id,
            'sender_id'  => $user->id,
            'body'       => $request->body,
        ]);

        // Notifier l'autre participant
        $recipient = $isStudent ? $subject->teacher : $subject->student;
        if ($recipient) {
            $recipient->notify(new NewMessage($message));
        }

        return redirect()
            ->route('subjects.show', $subject)
            ->with('success', 'Message envoyé.')
            ->withFragment('messagerie');
    }

    /**
     * Marquer tous les messages reçus (non lus) du sujet comme lus.
     */
    public function markRead(Subject $subject)
    {
        $user = Auth::user();

        // Seuls les participants autorisés peuvent marquer comme lus
        $isStudent  = $user->hasRole('Etudiant') && $subject->student_id === $user->id;
        $isDirector = $user->hasRole('Enseignant') && $subject->teacher_id === $user->id;

        if (!$isStudent && !$isDirector) {
            abort(403);
        }

        // Marquer uniquement les messages REÇUS (envoyés par l'autre personne)
        $subject->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'ok']);
    }
}
