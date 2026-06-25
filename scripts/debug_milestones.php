<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Subject;
use App\Models\Milestone;

echo "=== USERS ===\n";
$users = User::with('roles')->get();
foreach ($users as $u) {
    echo "ID={$u->id} Email={$u->email} Roles=" . $u->roles->pluck('name')->join(',') . " dept={$u->department_id}\n";
}

echo "\n=== SUBJECTS ===\n";
$subjects = Subject::all();
foreach ($subjects as $s) {
    echo "ID={$s->id} status={$s->status} student_id={$s->student_id} teacher_id={$s->teacher_id}\n";
    echo "  title=" . substr($s->title, 0, 60) . "\n";
}

echo "\n=== MILESTONES ===\n";
$milestones = Milestone::all();
foreach ($milestones as $m) {
    echo "ID={$m->id} subject_id={$m->subject_id} status={$m->status} title={$m->title}\n";
    echo "  due_date={$m->due_date} submission_date={$m->submission_date}\n";
}

echo "\ndone\n";
