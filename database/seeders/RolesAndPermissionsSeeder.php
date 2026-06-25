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

            // Doyen — statistiques faculté
            'statistics.faculty',

            // Appariteur — validation financière
            'students.validate-financial',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Créer les rôles et assigner les permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all());

        $cp = Role::firstOrCreate(['name' => 'Chef de département']);
        $cp->syncPermissions([
            'subjects.view',
            'subjects.validate',
            'subjects.reject',
            'subjects.assign-teacher',
            'thesis.download',
            'thesis.view-reports',
        ]);

        $enseignant = Role::firstOrCreate(['name' => 'Enseignant']);
        $enseignant->syncPermissions([
            'subjects.view',
            'thesis.download',
            'thesis.view-reports',
            'thesis.validate-defense',
        ]);


        $etudiant = Role::firstOrCreate(['name' => 'Etudiant']);
        $etudiant->syncPermissions([
            'subjects.create',
            'subjects.view',
            'thesis.upload',
            'thesis.final-deposit',
        ]);

        // Doyen de Faculté (Superviseur)
        $doyen = Role::firstOrCreate(['name' => 'Doyen']);
        $doyen->syncPermissions([
            'subjects.view',
            'statistics.faculty',
            'thesis.view-reports',
        ]);

        // Appariteur (Vérificateur administratif et financier)
        $appariteur = Role::firstOrCreate(['name' => 'Appariteur']);
        $appariteur->syncPermissions([
            'subjects.view',
            'students.validate-financial',
        ]);
    }
}
