<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $newPermissions = [
            'academic-years.manage',
            'settings.manage',
            'logs.view',
            'users.block',
            'users.reset-password',
        ];

        foreach ($newPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Attribuer toutes les nouvelles permissions à l'Admin (s'il existe déjà)
        $admin = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        if ($admin) {
            $admin->givePermissionTo($newPermissions);
        }
    }

    public function down(): void
    {
        $perms = [
            'academic-years.manage',
            'settings.manage',
            'logs.view',
            'users.block',
            'users.reset-password',
        ];

        foreach ($perms as $perm) {
            $p = Permission::findByName($perm);
            if ($p) $p->delete();
        }
    }
};
