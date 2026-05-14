<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Milestone;
use App\Models\ActivityLog;

$milestone = Milestone::first();
if (! $milestone) {
    echo "No milestone found\n";
    exit(1);
}

$milestone->update([
    'status' => 'validated',
    'comments' => 'Validation automatique de test',
]);

if ($milestone->subject->student) {
    $milestone->subject->student->notify(new \App\Notifications\MilestoneValidated($milestone));
}

ActivityLog::log('milestone_validated', 'Jalon validé (script)', $milestone);

echo "Milestone validated: " . $milestone->id . PHP_EOL;
