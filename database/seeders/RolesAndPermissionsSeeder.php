<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Réinitialiser les rôles et permissions en cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // Sujets
            'subjects.create',
            'subjects.view',
            'subjects.validate',
            'subjects.reject',
            'subjects.assign-teacher',

            // Fichiers TFC
            'thesis.upload',
            'thesis.download',
            'thesis.view-reports',

            // Utilisateurs
            'users.manage',
            'departments.manage',

            // Dépôt final
            'thesis.final-deposit',
            'thesis.validate-defense',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer les rôles et assigner les permissions
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all());

        $cp = Role::create(['name' => 'Chef de département']);
        $cp->givePermissionTo([
            'subjects.view',
            'subjects.validate',
            'subjects.reject',
            'subjects.assign-teacher',
            'thesis.download',
            'thesis.view-reports',
        ]);

        $enseignant = Role::create(['name' => 'Enseignant']);
        $enseignant->givePermissionTo([
            'subjects.view',
            'thesis.download',
            'thesis.view-reports',
            'thesis.validate-defense',
        ]);

        $etudiant = Role::create(['name' => 'Etudiant']);
        $etudiant->givePermissionTo([
            'subjects.create',
            'subjects.view',
            'thesis.upload',
            'thesis.final-deposit',
        ]);
    }
}
