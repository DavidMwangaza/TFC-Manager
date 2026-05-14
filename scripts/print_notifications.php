<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = [
    'student@test.local',
    'teacher@test.local',
    'cp@test.local',
];

foreach ($users as $email) {
    $user = User::where('email', $email)->first();
    echo "--- Notifications for {$email} (id={$user?->id}) ---\n";
    if (! $user) { echo "(no user)\n\n"; continue; }
    $notes = $user->notifications()->latest()->take(10)->get();
    foreach ($notes as $n) {
        echo $n->id . ' | ' . $n->type . ' | ' . substr(json_encode($n->data),0,120) . "\n";
    }
    echo "\n";
}
