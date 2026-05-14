<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Subject;

$s = Subject::where('title','Sujet de test')->first();
if (! $s) { echo "Sujet non trouvé\n"; exit(1); }
$milestones = $s->milestones()->get();
echo "Subject id: " . $s->id . PHP_EOL;
foreach ($milestones as $m) {
    echo "Milestone id: " . $m->id . " | title: " . $m->title . " | status: " . $m->status . PHP_EOL;
}
