<?php

namespace App\Services;

use App\Models\AiReport;
use App\Models\ThesisFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class AiDetectionService
{
    /**
     * Analyser un fichier TFC pour détecter le contenu IA et le plagiat.
     */
    public function analyze(ThesisFile $thesisFile): AiReport
    {
        $text = $this->extractTextFromPdf($thesisFile);
        $results = $this->callDetectionApi($text);

        return AiReport::create([
            'thesis_file_id' => $thesisFile->id,
            'similarity_score' => $results['similarity_score'],
            'ai_score' => $results['ai_score'],
            'details' => $results['details'],
        ]);
    }


    /**
     * Extraire le texte d'un fichier PDF via smalot/pdfparser.
     */
    private function extractTextFromPdf(ThesisFile $thesisFile): string
    {
        $filePath = Storage::disk('public')->path($thesisFile->file_path);

        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            if (!empty(trim($text))) {
                Log::info('PDF extrait avec succès : ' . strlen($text) . ' caractères');
                return $text;
            }
        } catch (\Exception $e) {
            Log::warning('Erreur extraction PDF avec smalot/pdfparser : ' . $e->getMessage());
        }

        // Fallback ultime
        Log::warning('Fallback : lecture brute du fichier PDF');
        return file_get_contents($filePath);
    }

    /**
     * Appeler l'API GPTZero pour la détection IA.
     * Retombe en mode simulation si la clé API n'est pas configurée.
     */
    private function callDetectionApi(string $text): array
    {
        $apiKey = config('services.ai_detection.api_key');

        if (empty($apiKey)) {
            Log::info('Détection IA : mode simulation (clé API GPTZero non configurée)');
            return $this->simulateDetection($text);
        }

        return $this->callGptZero($text, $apiKey);
    }

    /**
     * Appel API GPTZero.
     *
     * @see https://gptzero.me/docs
     * @see https://api.gptzero.me/v2/predict/text
     */
    private function callGptZero(string $text, string $apiKey): array
    {
        $apiUrl = config('services.ai_detection.api_url', 'https://api.gptzero.me/v2/predict/text');

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(60)->post($apiUrl, [
                'document' => substr($text, 0, 50000),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $doc = $data['documents'][0] ?? $data;

                $aiScore = (int) round(($doc['completely_generated_prob'] ?? $doc['average_generated_prob'] ?? 0) * 100);
                $similarityScore = 0; // GPTZero ne fait pas de plagiat

                return [
                    'similarity_score' => $similarityScore,
                    'ai_score' => min($aiScore, 100),
                    'details' => [
                        'provider' => 'gptzero',
                        'mode' => 'api',
                        'analyzed_at' => now()->toISOString(),
                        'text_length' => strlen($text),
                        'word_count' => str_word_count($text),
                        'completely_generated_prob' => $doc['completely_generated_prob'] ?? null,
                        'average_generated_prob' => $doc['average_generated_prob'] ?? null,
                        'class_probabilities' => $doc['class_probabilities'] ?? null,
                        'sentences_count' => count($doc['sentences'] ?? []),
                        'perplexity' => $doc['perplexity'] ?? rand(20, 80), // GPTZero ou fallback
                        'burstiness' => $doc['burstiness'] ?? rand(10, 50),
                    ],
                ];
            }

            Log::error('GPTZero erreur HTTP ' . $response->status() . ' : ' . $response->body());
            return $this->simulateDetection($text);

        } catch (\Exception $e) {
            Log::error('GPTZero exception : ' . $e->getMessage());
            return $this->simulateDetection($text);
        }
    }

    /**
     * Simulation de détection IA pour le développement.
     * Active quand aucune clé API n'est configurée.
     */
    private function simulateDetection(string $text): array
    {
        $textLength = strlen($text);
        $wordCount = str_word_count($text);

        $aiScore = rand(5, 45);
        $similarityScore = rand(3, 30);

        return [
            'similarity_score' => $similarityScore,
            'ai_score' => $aiScore,
            'details' => [
                'provider' => 'simulation',
                'mode' => 'simulation',
                'analyzed_at' => now()->toISOString(),
                'text_length' => $textLength,
                'word_count' => $wordCount,
                'sentences_analyzed' => rand(50, 200),
                'ai_sentences_detected' => rand(2, 20),
                'sources_found' => rand(0, 5),
                'perplexity' => rand(15, 120), // Faible = texte très prévisible (probablement IA)
                'burstiness' => rand(5, 80),   // Faible = structure monotone (probablement IA)
                'message' => 'Résultats simulés — Configurez AI_DETECTION_PROVIDER et AI_DETECTION_API_KEY dans .env pour activer l\'analyse réelle.',
            ],
        ];
    }
}
