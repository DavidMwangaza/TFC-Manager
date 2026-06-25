<?php

namespace App\Services;

use App\Models\Subject;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Tokenization\WordTokenizer;

class SimilarityDetectionService
{
    /**
     * Calcule la similarité Cosinus basée sur TF-IDF entre un nouveau sujet et les anciens validés.
     * 
     * @param Subject $newSubject
     * @return array ['max_score' => float, 'details' => array]
     */
    public function calculateSimilarity(Subject $newSubject): array
    {
        // On récupère tous les anciens sujets validés (sauf le nouveau s'il a déjà un ID)
        $query = Subject::where('status', 'validated');
        if ($newSubject->id) {
            $query->where('id', '!=', $newSubject->id);
        }
        
        $existingSubjects = $query->get();

        if ($existingSubjects->isEmpty()) {
            return [
                'max_score' => 0.0,
                'details' => [],
            ];
        }

        // 1. Préparation du corpus (Ensemble de documents D)
        // Le corpus contiendra le nouveau sujet à l'index 0, suivi des anciens sujets
        $corpus = [];
        $subjectIds = [];
        
        $corpus[0] = $this->extractText($newSubject);
        $subjectIds[0] = 'NEW';

        $index = 1;
        foreach ($existingSubjects as $subject) {
            $corpus[$index] = $this->extractText($subject);
            $subjectIds[$index] = $subject->id;
            $index++;
        }

        // 2. Tokenisation et calcul du TF (Term Frequency)
        // Utilise WordTokenizer pour séparer les mots
        $vectorizer = new TokenCountVectorizer(new WordTokenizer());
        $vectorizer->fit($corpus);
        $vectorizer->transform($corpus);

        // 3. Transformation TF-IDF (Inverse Document Frequency)
        $transformer = new TfIdfTransformer($corpus);
        $transformer->transform($corpus);

        // Le $corpus contient maintenant les vecteurs mathématiques (V) pour chaque document
        /** @var array $newSubjectVector */
        $newSubjectVector = $corpus[0];

        // 4. Calcul de la Similarité Cosinus
        $details = [];
        $maxScore = 0.0;

        for ($i = 1; $i < count($corpus); $i++) {
            /** @var array $existingVector */
            $existingVector = $corpus[$i];
            
            // Si les vecteurs sont vides (ex: texte trop court), la distance cosinus ne peut pas être calculée
            if (empty(array_filter($newSubjectVector)) || empty(array_filter($existingVector))) {
                $similarity = 0.0;
            } else {
                $similarity = $this->cosineSimilarity($newSubjectVector, $existingVector);
            }

            // On garde les scores pertinents
            if ($similarity > 0.1) {
                $scorePercentage = round($similarity * 100, 2);
                $details[] = [
                    'subject_id' => $subjectIds[$i],
                    'score' => $scorePercentage,
                ];
                
                if ($similarity > $maxScore) {
                    $maxScore = $similarity;
                }
            }
        }

        // Tri par ordre décroissant de similarité
        usort($details, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Ne garder que les 5 plus similaires
        $details = array_slice($details, 0, 5);

        return [
            'max_score' => round($maxScore * 100, 2),
            'details' => $details,
        ];
    }

    /**
     * Concatène les champs textuels clés du sujet pour l'analyse NLP.
     */
    private function extractText(Subject $subject): string
    {
        $text = $subject->title . ' ' .
                $subject->description . ' ' .
                $subject->research_question . ' ' .
                $subject->hypothesis . ' ' .
                $subject->general_objective;
                
        // Nettoyage basique : minuscules, suppression ponctuation
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        
        return (string) $text;
    }

    /**
     * Calcule la similarité Cosinus entre deux vecteurs.
     */
    private function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0.0;
        $norm1 = 0.0;
        $norm2 = 0.0;

        foreach ($vec1 as $index => $value1) {
            $value2 = $vec2[$index] ?? 0.0;
            $dotProduct += $value1 * $value2;
            $norm1 += $value1 * $value1;
        }

        foreach ($vec2 as $value2) {
            $norm2 += $value2 * $value2;
        }

        if ($norm1 == 0 || $norm2 == 0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($norm1) * sqrt($norm2));
    }
}
