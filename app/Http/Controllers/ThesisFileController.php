<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\ThesisFile;
use App\Models\Milestone;
use App\Models\ActivityLog;
use App\Notifications\ThesisFileUploaded;
use App\Notifications\MilestoneSubmitted as MilestoneSubmittedNotification;
use App\Services\AiDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ThesisFileController extends Controller
{
    public function __construct(
        protected AiDetectionService $aiDetectionService
    ) {}

    /**
     * L'étudiant dépose son PDF (version jury ou finale).
     * Les fichiers sont stockés dans le disk privé secure_thesis.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'pdf'          => 'required|file|mimes:pdf|mimetypes:application/pdf|max:20480', // Max 20 MB
            'version_type' => 'required|in:jury,final',
        ]);

        $user = Auth::user();

        // Récupérer le sujet validé de l'étudiant
        $subject = Subject::where('student_id', $user->id)
            ->where('status', 'validated')
            ->firstOrFail();

        // Vérifier si un dépôt final est autorisé
        if ($request->version_type === 'final') {
            if (!($subject->defense_validated ?? false)) {
                return back()->with('error', 'Le dépôt final n\'est possible qu\'après validation de la soutenance.');
            }

            if ($subject->financial_status !== 'validated') {
                return back()->with('error', 'Le dépôt final est bloqué : votre situation financière n\'a pas encore été validée par l\'Appariteur.');
            }
        }

        // Supprimer l'ancienne version du même type (1 seul fichier par type autorisé)
        $existingFile = ThesisFile::where('subject_id', $subject->id)
            ->where('version_type', $request->version_type)
            ->whereNull('milestone_id')
            ->first();

        if ($existingFile) {
            Storage::disk('secure_thesis')->delete($existingFile->file_path);
            if ($existingFile->aiReport) {
                $existingFile->aiReport->delete();
            }
            $existingFile->delete();
        }

        // Stocker le fichier dans le disk PRIVÉ (jamais exposé directement via HTTP)
        $file      = $request->file('pdf');
        $filename  = 'thesis_' . $subject->id . '_' . $request->version_type . '_' . time() . '.pdf';
        $path      = $file->storeAs('', $filename, 'secure_thesis');

        $thesisFile = ThesisFile::create([
            'subject_id'    => $subject->id,
            'file_path'     => $filename,
            'original_name' => $file->getClientOriginalName(),
            'version_type'  => $request->version_type,
        ]);

        // Audit log — TELEVERSEMENT_PDF (obligatoire selon section 4 du cahier des charges)
        ActivityLog::log(
            'TELEVERSEMENT_PDF',
            'L\'étudiant ' . $user->name . ' a déposé la version "' . strtoupper($request->version_type) . '" du sujet #' . $subject->id . ' ("' . $subject->title . '")',
            $thesisFile
        );

        // NOTE: L'analyse IA n'est PAS lancée automatiquement.
        // Le directeur (Enseignant) doit la demander explicitement via requestAiAnalysis().

        // Notifier l'enseignant encadreur
        $thesisFile->load('subject.student', 'subject.teacher');
        if (
            $subject->teacher
            && $subject->teacher->hasRole('Enseignant')
            && $subject->teacher->department_id === $subject->department_id
        ) {
            $subject->teacher->notify(new ThesisFileUploaded($thesisFile));
        }

        return redirect()->route('dashboard')->with('success', 'Fichier TFC déposé avec succès !');
    }

    /**
     * Télécharger / streamer un fichier TFC depuis le disk privé.
     * Contrôle d'accès : étudiant propriétaire, directeur, CP du département, Admin.
     */
    public function download(ThesisFile $thesisFile): StreamedResponse
    {
        $user    = Auth::user();
        $subject = $thesisFile->subject;

        $canDownload = $user->hasRole('Admin')
            || ($user->hasRole('Etudiant') && $subject->student_id === $user->id)
            || ($user->hasRole('Enseignant') && $subject->teacher_id === $user->id)
            || ($user->hasRole('Chef de département') && $subject->department_id === $user->department_id);

        if (!$canDownload) {
            abort(403, 'Accès non autorisé au document.');
        }

        // Streamer depuis le disk privé
        return Storage::disk('secure_thesis')->download(
            $thesisFile->file_path,
            $thesisFile->original_name ?? 'document.pdf'
        );
    }

    /**
     * Upload d'un fichier TFC lié à un jalon.
     * Stockage dans le disk privé secure_thesis.
     */
    public function uploadForMilestone(Request $request, Milestone $milestone)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:20480',
        ]);

        $user = Auth::user();

        // Sécurité: seul l'étudiant lié au sujet peut déposer pour ce jalon
        if (! $user->hasRole('Etudiant') || $milestone->subject->student_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        // Vérification séquentielle stricte des jalons
        if ($milestone->sequence_number !== null) {
            $previousMilestone = $milestone->subject->milestones()
                ->where('sequence_number', '<', $milestone->sequence_number)
                ->orderBy('sequence_number', 'desc')
                ->first();

            if ($previousMilestone && $previousMilestone->status !== 'validated') {
                return back()->with('error', 'Vous devez d\'abord faire valider le jalon précédent (' . $previousMilestone->title . ').');
            }
        }

        // Supprimer toutes les anciennes versions pour ce jalon (soumission + annotations)
        $existingFiles = ThesisFile::where('milestone_id', $milestone->id)->get();
        foreach ($existingFiles as $existing) {
            Storage::disk('secure_thesis')->delete($existing->file_path);
            if ($existing->aiReport) {
                $existing->aiReport->delete();
            }
            $existing->delete();
        }

        $file     = $request->file('pdf');
        $filename = 'jalon_' . $milestone->id . '_' . time() . '.pdf';
        $path     = $file->storeAs('', $filename, 'secure_thesis');

        $thesisFile = ThesisFile::create([
            'subject_id'    => $milestone->subject_id,
            'file_path'     => $filename,
            'original_name' => $file->getClientOriginalName(),
            'version_type'  => 'jury',
            'milestone_id'  => $milestone->id,
        ]);

        // Audit log — TELEVERSEMENT_PDF pour jalon
        ActivityLog::log(
            'TELEVERSEMENT_PDF',
            'L\'étudiant ' . $user->name . ' a soumis un PDF pour le jalon "' . $milestone->title . '" (Sujet #' . $milestone->subject_id . ')',
            $thesisFile
        );

        // NOTE: L'analyse IA n'est PAS lancée automatiquement.
        // Le directeur (Enseignant) doit la demander explicitement.

        // Mettre à jour le jalon
        $milestone->update([
            'submission_date'      => now(),
            'status'               => 'submitted',
        ]);

        // Notifier l'enseignant encadreur
        if ($milestone->subject->teacher) {
            $milestone->subject->teacher->notify(new MilestoneSubmittedNotification($milestone));
        }

        return redirect()->back()->with('success', 'Fichier soumis pour le jalon.');
    }

    /**
     * Le directeur (Enseignant) demande l'analyse IA d'un fichier TFC.
     * Seul le directeur du sujet peut déclencher cette analyse.
     */
    public function requestAiAnalysis(ThesisFile $thesisFile)
    {
        $user = Auth::user();
        $subject = $thesisFile->subject;

        // Seul le directeur (enseignant encadreur) du sujet peut demander l'analyse
        if (!$user->hasRole('Enseignant') || $subject->teacher_id !== $user->id) {
            abort(403, 'Seul le directeur de ce sujet peut demander l\'analyse IA.');
        }

        // Si un rapport existe déjà, le supprimer pour relancer l'analyse
        if ($thesisFile->aiReport) {
            $thesisFile->aiReport->delete();
        }

        // Lancer l'analyse IA de façon asynchrone
        \App\Jobs\AnalyzeThesisFileAi::dispatch($thesisFile);

        ActivityLog::log(
            'DEMANDE_ANALYSE_IA',
            'Le directeur ' . $user->name . ' a demandé l\'analyse IA du fichier "' . $thesisFile->original_name . '" (Sujet #' . $subject->id . ')',
            $thesisFile
        );

        return back()->with('success', 'Analyse IA lancée. Les résultats seront disponibles sous peu.');
    }
}
