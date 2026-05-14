<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ThesisFile;
use App\Models\Milestone;
use Carbon\Carbon;

$milestone = Milestone::first();
if (! $milestone) {
    echo "No milestone found\n";
    exit(1);
}

$subject = $milestone->subject;
$dir = __DIR__ . '/../storage/app/public/tfc_files';
if (! is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$path = $dir . '/test_milestone.pdf';
file_put_contents($path, "Dummy PDF content for milestone test\n");
$relative = 'tfc_files/test_milestone.pdf';

$existing = ThesisFile::where('milestone_id', $milestone->id)->first();
if ($existing) {
    echo "Existing ThesisFile id: " . $existing->id . "\n";
} else {
    $thesisFile = ThesisFile::create([
        'subject_id' => $subject->id,
        'file_path' => $relative,
        'original_name' => 'test_milestone.pdf',
        'version_type' => 'jury',
        'milestone_id' => $milestone->id,
    ]);

    $milestone->update([
        'submission_date' => Carbon::now(),
        'status' => 'submitted',
    ]);

    echo "Created ThesisFile id: " . $thesisFile->id . "\n";
}
