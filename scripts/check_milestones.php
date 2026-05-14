<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Milestone;
use App\Models\Subject;

echo "=== MILESTONES EN BASE ===\n";
echo "Total: " . Milestone::count() . "\n\n";

foreach (Milestone::with('subject')->get() as $m) {
    echo "ID={$m->id} | subject_id={$m->subject_id} | title={$m->title} | status={$m->status} | due_date={$m->due_date}\n";
}

echo "\n=== SUJETS VALIDES (pour référence) ===\n";
foreach (Subject::where('status', 'validated')->with('student')->get() as $s) {
    echo "ID={$s->id} | title=" . substr($s->title, 0, 50) . " | student={$s->student->name}\n";
}
