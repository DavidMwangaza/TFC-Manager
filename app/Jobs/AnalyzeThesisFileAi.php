<?php

namespace App\Jobs;

use App\Models\ThesisFile;
use App\Services\AiDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeThesisFileAi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ThesisFile $thesisFile
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AiDetectionService $aiDetectionService): void
    {
        try {
            $aiDetectionService->analyze($this->thesisFile);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse IA asynchrone (PDF): ' . $e->getMessage());
        }
    }
}
