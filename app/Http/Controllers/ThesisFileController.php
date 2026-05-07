<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\ThesisFile;
use App\Notifications\ThesisFileUploaded;
use App\Services\AiDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ThesisFileController extends Controller
{
    public function __construct(
        protected AiDetectionService $aiDetectionService
    ) {}

    /**
     * L'étudiant dépose son PDF.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|mimetypes:application/pdf|max:20480', // Max 20 MB
            'version_type' => 'required|in:jury,final',
        ]);

        $user = Auth::user();

        // Récupérer le sujet validé de l'étudiant
        $subject = Subject::where('student_id', $user->id)
            ->where('status', 'validated')
            ->firstOrFail();

        // Vérifier si un dépôt final est autorisé
        if ($request->version_type === 'final') {
            $hasDefenseValidation = $subject->defense_validated ?? false;
            if (!$hasDefenseValidation) {
                return back()->with('error', 'Le dépôt final n\'est possible qu\'après validation de la soutenance.');
            }
        }

        // Supprimer l'ancienne version du même type (1 seul fichier par type autorisé)
        $existingFile = ThesisFile::where('subject_id', $subject->id)
            ->where('version_type', $request->version_type)
            ->first();

        if ($existingFile) {
            Storage::disk('public')->delete($existingFile->file_path);
            // Supprimer le rapport IA associé s'il existe
            if ($existingFile->aiReport) {
                $existingFile->aiReport->delete();
            }
            $existingFile->delete();
        }

        // Stocker le fichier
        $file = $request->file('pdf');
        $path = $file->store('tfc_files', 'public');

        $thesisFile = ThesisFile::create([
            'subject_id' => $subject->id,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'version_type' => $request->version_type,
        ]);

        // Lancer l'analyse IA pour toutes les versions (jury et finale)
        try {
            $this->aiDetectionService->analyze($thesisFile);
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas l'upload
            \Log::error('Erreur analyse IA: ' . $e->getMessage());
        }

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
     * Télécharger un fichier TFC.
     */
    public function download(ThesisFile $thesisFile)
    {
        $user = Auth::user();

        // Vérifier les droits d'accès
        $subject = $thesisFile->subject;

        $canDownload = $user->hasRole('Admin')
            || ($user->hasRole('Etudiant') && $subject->student_id === $user->id)
            || ($user->hasRole('Enseignant') && $subject->teacher_id === $user->id)
            || ($user->hasRole('Chef de département') && $subject->department_id === $user->department_id);

        if (!$canDownload) {
            abort(403, 'Accès non autorisé.');
        }

        return Storage::disk('public')->download(
            $thesisFile->file_path,
            $thesisFile->original_name
        );
    }
}
