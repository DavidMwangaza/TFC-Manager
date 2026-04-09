<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    /**
     * Liste des facultés.
     */
    public function index()
    {
        $faculties = Faculty::withCount('departments')->orderBy('name')->get();

        return view('admin.faculties.index', compact('faculties'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('admin.faculties.create');
    }

    /**
     * Enregistre une nouvelle faculté.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:faculties'],
            'code' => ['required', 'string', 'max:20', 'unique:faculties'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $faculty = Faculty::create($request->only('name', 'code', 'description'));

        ActivityLog::log('created', "Faculté \"{$faculty->name}\" ({$faculty->code}) créée.", $faculty);

        return redirect()->route('admin.faculties.index')
            ->with('success', "La faculté {$faculty->name} a été créée.");
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Faculty $faculty)
    {
        return view('admin.faculties.edit', compact('faculty'));
    }

    /**
     * Met à jour une faculté.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:faculties,name,' . $faculty->id],
            'code' => ['required', 'string', 'max:20', 'unique:faculties,code,' . $faculty->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $faculty->update($request->only('name', 'code', 'description'));

        ActivityLog::log('updated', "Faculté \"{$faculty->name}\" modifiée.", $faculty);

        return redirect()->route('admin.faculties.index')
            ->with('success', "La faculté {$faculty->name} a été mise à jour.");
    }

    /**
     * Supprime une faculté (seulement si elle n'a aucune filière).
     */
    public function destroy(Faculty $faculty)
    {
        if ($faculty->departments()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une faculté qui contient des filières.');
        }

        $name = $faculty->name;
        ActivityLog::log('deleted', "Faculté \"{$name}\" supprimée.", $faculty);
        $faculty->delete();

        return redirect()->route('admin.faculties.index')
            ->with('success', "La faculté {$name} a été supprimée.");
    }
}
