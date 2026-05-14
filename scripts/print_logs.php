<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ActivityLog;

$logs = ActivityLog::latest()->take(10)->get();
foreach ($logs as $log) {
    echo sprintf("%d | %-20s | %-40s | %s\n", $log->id, $log->action, substr($log->description,0,40), $log->model_name);
}
