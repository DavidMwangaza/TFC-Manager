<?php

namespace App\Jobs;

use App\Models\Subject;
use App\Services\SimilarityDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeSubjectSimilarity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Subject $subject
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SimilarityDetectionService $similarityService): void
    {
        try {
            $similarityResult = $similarityService->calculateSimilarity($this->subject);
            
            $this->subject->update([
                'similarity_score' => $similarityResult['max_score'],
                'similarity_details' => $similarityResult['details'],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du calcul asynchrone de similarité: ' . $e->getMessage());
        }
    }
}
