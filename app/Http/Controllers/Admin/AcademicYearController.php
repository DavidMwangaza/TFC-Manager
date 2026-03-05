<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Liste des années académiques.
     */
    public function index()
    {
        $years = AcademicYear::withCount('subjects')
            ->orderByDesc('start_date')
            ->get();

        return view('admin.academic-years.index', compact('years'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('admin.academic-years.create');
    }

    /**
     * Enregistre une nouvelle année académique.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:20', 'unique:academic_years'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        $year = AcademicYear::create($request->only('name', 'start_date', 'end_date'));

        ActivityLog::log('created', "Année académique \"{$year->name}\" créée.", $year);

        return redirect()->route('admin.academic-years.index')
            ->with('success', "L'année académique {$year->name} a été créée.");
    }

    /**
     * Définir comme année en cours.
     */
    public function setCurrent(AcademicYear $academicYear)
    {
        if ($academicYear->is_closed) {
            return back()->with('error', 'Impossible de définir une année clôturée comme année en cours.');
        }

        $academicYear->setAsCurrent();

        ActivityLog::log('updated', "Année académique \"{$academicYear->name}\" définie comme année en cours.", $academicYear);

        return back()->with('success', "L'année {$academicYear->name} est maintenant l'année en cours.");
    }

    /**
     * Clôturer une année académique.
     */
    public function close(AcademicYear $academicYear)
    {
        if ($academicYear->is_closed) {
            return back()->with('error', 'Cette année est déjà clôturée.');
        }

        $academicYear->close();

        ActivityLog::log('year_closed', "Année académique \"{$academicYear->name}\" clôturée. Tous les TFC sont archivés.", $academicYear);

        return back()->with('success', "L'année {$academicYear->name} a été clôturée et archivée.");
    }

    /**
     * Supprime une année académique (seulement si pas de sujets liés).
     */
    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->subjects()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une année qui contient des sujets.');
        }

        $name = $academicYear->name;
        ActivityLog::log('deleted', "Année académique \"{$name}\" supprimée.", $academicYear);
        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', "L'année {$name} a été supprimée.");
    }
}
