<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use App\Models\Department;
use App\Models\User;
use App\Models\Subject;
use Carbon\Carbon;

$roles = ['Etudiant','Enseignant','Chef de département','Admin'];
foreach ($roles as $r) {
    Role::firstOrCreate(['name' => $r]);
}

$dept = Department::first() ?: Department::create(['name' => 'Departement Test', 'code' => 'DT']);

$student = User::where('email', 'student@test.local')->first();
if (! $student) {
    $student = User::create([
        'name' => 'Etudiant Test',
        'email' => 'student@test.local',
        'password' => 'password',
        'department_id' => $dept->id,
    ]);
    $student->assignRole('Etudiant');
}

$teacher = User::where('email', 'teacher@test.local')->first();
if (! $teacher) {
    $teacher = User::create([
        'name' => 'Enseignant Test',
        'email' => 'teacher@test.local',
        'password' => 'password',
        'department_id' => $dept->id,
    ]);
    $teacher->assignRole('Enseignant');
}

$cp = User::where('email', 'cp@test.local')->first();
if (! $cp) {
    $cp = User::create([
        'name' => 'Chef Test',
        'email' => 'cp@test.local',
        'password' => 'password',
        'department_id' => $dept->id,
    ]);
    $cp->assignRole('Chef de département');
}

$subject = Subject::where('title', 'Sujet de test')->first();
if (! $subject) {
    $subject = Subject::create([
        'title' => 'Sujet de test',
        'student_id' => $student->id,
        'teacher_id' => $teacher->id,
        'department_id' => $dept->id,
        'status' => 'validated',
    ]);
}

$milestone = $subject->milestones()->create([
    'title' => 'Jalon test',
    'due_date' => Carbon::now()->addDays(7),
    'correction_deadline' => Carbon::now()->addDays(10),
    'status' => 'pending',
]);

echo "MILSTONE_ID:" . $milestone->id . PHP_EOL;
