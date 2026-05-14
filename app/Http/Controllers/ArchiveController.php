<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\ThesisFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\DocumentConverter;

class ArchiveController extends Controller
{
    /**
     * Afficher les travaux défendus (accès public).
     */
    public function index(Request $request)
    {
        $query = Subject::where('defense_validated', true)
            ->with(['student', 'teacher', 'department', 'academicYear', 'thesisFiles']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('student', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        $subjects = $query->latest()->paginate(12)->withQueryString();
        $departments = Department::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('archives.index', compact('subjects', 'departments', 'academicYears'));
    }

    /**
     * Télécharger un fichier d'un travail défendu (accès public).
     */
    public function download(ThesisFile $thesisFile)
    {
        // Seuls les fichiers finaux de sujets défendus sont téléchargeables publiquement
        if (!$thesisFile->subject || !$thesisFile->subject->defense_validated || $thesisFile->version_type !== 'final') {
            abort(403, 'Ce fichier n\'est pas disponible au téléchargement.');
        }

        return Storage::disk('public')->download(
            $thesisFile->file_path,
            $thesisFile->original_name
        );
    }

    /**
     * Afficher une page d'aperçu (embed) pour un fichier d'archive.
     */
    public function view(ThesisFile $thesisFile)
    {
        if (!$thesisFile->subject || !$thesisFile->subject->defense_validated || $thesisFile->version_type !== 'final') {
            abort(403, 'Ce fichier n\'est pas disponible pour visualisation.');
        }

        $path = Storage::disk('public')->path($thesisFile->file_path);
        if (!file_exists($path)) {
            abort(404);
        }

        $mime = @mime_content_type($path) ?: Storage::disk('public')->mimeType($thesisFile->file_path) ?? 'application/octet-stream';

        // Pour les PDF et images, on affiche directement une page contenant un iframe vers la ressource
        return view('archives.view', compact('thesisFile', 'mime'));
    }

    /**
     * Retourne le fichier en inline (Content-Disposition: inline) pour embedding.
     */
    public function file(ThesisFile $thesisFile)
    {
        if (!$thesisFile->subject || !$thesisFile->subject->defense_validated || $thesisFile->version_type !== 'final') {
            abort(403, 'Ce fichier n\'est pas disponible pour visualisation.');
        }

        $path = Storage::disk('public')->path($thesisFile->file_path);
        if (!file_exists($path)) {
            abort(404);
        }

        // Si le fichier est un document bureautique, tenter une conversion serveur via LibreOffice
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $convertible = ['doc', 'docx', 'odt', 'rtf', 'ppt', 'pptx'];

        if (in_array($ext, $convertible, true)) {
            $converter = app(DocumentConverter::class);
            $converted = $converter->convertToPdf($path);
            if ($converted && file_exists($converted)) {
                return response()->file($converted, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . pathinfo($thesisFile->original_name, PATHINFO_FILENAME) . '.pdf"',
                ]);
            }
        }

        $mime = @mime_content_type($path) ?: Storage::disk('public')->mimeType($thesisFile->file_path) ?? 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $thesisFile->original_name . '"',
        ]);
    }
}
