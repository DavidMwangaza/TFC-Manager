<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\ThesisFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
}
