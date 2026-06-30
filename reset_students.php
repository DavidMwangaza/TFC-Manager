<?php

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudentProfile;
use Spatie\Permission\Models\Role;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = Role::where('name', 'Etudiant')->first();
if ($role) {
    $users = $role->users;
    foreach ($users as $user) {
        $user->delete();
    }
}

$newStudent = User::create([
    'name' => 'David Mumbere',
    'email' => '20mm381@esisalama.org',
    'password' => Hash::make('password'),
    'matricule' => '20MM381',
    'promotion' => 'L4 Génie Logiciel'
]);

$newStudent->assignRole('Etudiant');

echo "Done\n";
