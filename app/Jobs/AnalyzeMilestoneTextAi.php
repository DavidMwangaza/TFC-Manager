<?php

namespace App\Jobs;

use App\Models\Milestone;
use App\Services\AiDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeMilestoneTextAi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $text,
        public Milestone $milestone
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AiDetectionService $aiDetectionService): void
    {
        try {
            $aiDetectionService->analyzeText($this->text, $this->milestone);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse IA asynchrone (Texte Jalon): ' . $e->getMessage());
        }
    }
}
