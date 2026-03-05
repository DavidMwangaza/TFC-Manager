<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Liste de tous les utilisateurs.
     */
    public function index(Request $request)
    {
        $query = User::with(['department', 'roles']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filtre par département
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filtre bloqué/actif
        if ($request->filled('status')) {
            $query->where('is_blocked', $request->status === 'blocked');
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $roles = Role::all();
        $departments = Department::orderBy('faculty')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'departments'));
    }

    /**
     * Formulaire de création d'un utilisateur.
     */
    public function create()
    {
        $roles = Role::all();
        $departments = Department::orderBy('faculty')->orderBy('name')->get();

        return view('admin.users.create', compact('roles', 'departments'));
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'matricule' => ['nullable', 'string', 'max:50', 'unique:users'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'matricule' => $request->matricule,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->role);

        ActivityLog::log('created', "Utilisateur \"{$user->name}\" créé avec le rôle {$request->role}.", $user);

        return redirect()->route('admin.users.index')
            ->with('success', "L'utilisateur {$user->name} a été créé avec succès.");
    }

    /**
     * Formulaire d'édition d'un utilisateur.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $departments = Department::orderBy('faculty')->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'exists:roles,name'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'matricule' => ['nullable', 'string', 'max:50', 'unique:users,matricule,' . $user->id],
        ]);

        $oldRole = $user->getRoleNames()->first();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'matricule' => $request->matricule,
        ]);

        // Mettre à jour le rôle si changé
        if ($oldRole !== $request->role) {
            $user->syncRoles([$request->role]);
            ActivityLog::log('role_changed', "Rôle de \"{$user->name}\" modifié : {$oldRole} → {$request->role}.", $user);
        }

        ActivityLog::log('updated', "Utilisateur \"{$user->name}\" modifié.", $user);

        return redirect()->route('admin.users.index')
            ->with('success', "L'utilisateur {$user->name} a été mis à jour.");
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $name = $user->name;
        ActivityLog::log('deleted', "Utilisateur \"{$name}\" supprimé.", $user);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "L'utilisateur {$name} a été supprimé.");
    }

    /**
     * Bloquer / Débloquer un utilisateur.
     */
    public function toggleBlock(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas vous bloquer vous-même.');
        }

        $user->update(['is_blocked' => !$user->is_blocked]);

        $action = $user->is_blocked ? 'blocked' : 'unblocked';
        $label = $user->is_blocked ? 'bloqué' : 'débloqué';

        ActivityLog::log($action, "Utilisateur \"{$user->name}\" {$label}.", $user);

        return back()->with('success', "L'utilisateur {$user->name} a été {$label}.");
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur.
     */
    public function resetPassword(User $user)
    {
        $newPassword = Str::random(10);
        $user->update(['password' => Hash::make($newPassword)]);

        ActivityLog::log('password_reset', "Mot de passe de \"{$user->name}\" réinitialisé.", $user);

        return back()
            ->with('success', "Le mot de passe de {$user->name} a été réinitialisé avec succès.")
            ->with('generated_password', $newPassword);
    }
}
