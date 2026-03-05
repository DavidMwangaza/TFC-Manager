<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Liste des filières groupées par faculté.
     */
    public function index()
    {
        $departments = Department::withCount(['users', 'subjects'])
            ->orderBy('faculty')
            ->orderBy('name')
            ->get();

        $faculties = $departments->groupBy('faculty');

        return view('admin.departments.index', compact('departments', 'faculties'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        // Récupérer les facultés existantes pour les proposer
        $existingFaculties = Department::distinct()->pluck('faculty')->sort()->values();

        return view('admin.departments.create', compact('existingFaculties'));
    }

    /**
     * Enregistre une nouvelle filière.
     */
    public function store(Request $request)
    {
        $request->validate([
            'faculty' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255', 'unique:departments'],
            'code' => ['required', 'string', 'max:20', 'unique:departments'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $dept = Department::create($request->only('faculty', 'name', 'code', 'description'));

        ActivityLog::log('created', "Filière \"{$dept->name}\" ({$dept->code}) créée dans la faculté {$dept->faculty}.", $dept);

        return redirect()->route('admin.departments.index')
            ->with('success', "La filière {$dept->name} a été créée.");
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Department $department)
    {
        $existingFaculties = Department::distinct()->pluck('faculty')->sort()->values();

        return view('admin.departments.edit', compact('department', 'existingFaculties'));
    }

    /**
     * Met à jour une filière.
     */
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'faculty' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
            'code' => ['required', 'string', 'max:20', 'unique:departments,code,' . $department->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $department->update($request->only('faculty', 'name', 'code', 'description'));

        ActivityLog::log('updated', "Filière \"{$department->name}\" modifiée.", $department);

        return redirect()->route('admin.departments.index')
            ->with('success', "La filière {$department->name} a été mise à jour.");
    }

    /**
     * Supprime une filière (seulement si elle n'a pas d'utilisateurs ni de sujets).
     */
    public function destroy(Department $department)
    {
        if ($department->users()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une filière qui contient des utilisateurs.');
        }

        if ($department->subjects()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une filière qui contient des sujets.');
        }

        $name = $department->name;
        ActivityLog::log('deleted', "Filière \"{$name}\" supprimée.", $department);
        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', "La filière {$name} a été supprimée.");
    }
}
