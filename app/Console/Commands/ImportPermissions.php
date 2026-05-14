<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ImportPermissions extends Command
{
    protected $signature = 'permissions:import {path=docs/permissions-matrix.csv}';
    protected $description = 'Import permissions matrix CSV and assign to roles (Spatie)';

    public function handle(): int
    {
        $path = base_path($this->argument('path'));

        if (! file_exists($path)) {
            $this->error("Fichier introuvable: {$path}");
            return 1;
        }

        if (($handle = fopen($path, 'r')) === false) {
            $this->error('Impossible d\'ouvrir le fichier.');
            return 1;
        }

        $header = fgetcsv($handle);
        if (! $header || count($header) < 2) {
            $this->error('Fichier CSV invalide.');
            return 1;
        }

        // mapping CSV columns -> role names used in app
        $map = [
            'etudiant' => 'Etudiant',
            'chef_departement' => 'Chef de département',
            'directeur' => 'Enseignant',
            'doyen' => 'Doyen',
            'admin' => 'Admin',
            'jury' => 'Jury',
        ];

        $cols = array_map(fn($c) => strtolower(trim($c)), $header);

        while (($row = fgetcsv($handle)) !== false) {
            if (! isset($row[0]) || trim($row[0]) === '') {
                continue;
            }
            $permissionName = trim($row[0]);
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            for ($i = 1; $i < count($cols); $i++) {
                $col = $cols[$i] ?? null;
                $val = $row[$i] ?? '0';
                if (! $col) continue;
                if ((int) $val === 1) {
                    $roleName = $map[$col] ?? ucwords(str_replace('_', ' ', $col));
                    $role = Role::firstOrCreate(['name' => $roleName]);
                    $role->givePermissionTo($permission);
                }
            }
        }

        fclose($handle);

        $this->info('Import de permissions terminé.');
        return 0;
    }
}
