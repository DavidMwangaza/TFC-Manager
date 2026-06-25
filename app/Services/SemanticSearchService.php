<?php

namespace App\Services;

use App\Models\Subject;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Tokenization\WordTokenizer;

class SemanticSearchService
{
    /**
     * Dictionnaire basique de synonymes / extension de concepts pour la recherche sémantique.
     * Dans un système réel, cela peut être alimenté par WordNet ou une API NLP.
     */
    private array $synonyms = [
        'cyberattaque' => ['sécurité', 'piratage', 'hacker', 'virus', 'malware', 'vulnérabilité', 'informatique'],
        'cryptographie' => ['chiffrement', 'sécurité', 'clé', 'algorithme', 'rsa', 'aes'],
        'réseau' => ['infrastructure', 'cisco', 'lan', 'wan', 'routage', 'télécommunication'],
        'ia' => ['intelligence artificielle', 'machine learning', 'deep learning', 'réseau de neurones', 'nlp'],
        'web' => ['site', 'application', 'internet', 'php', 'laravel', 'javascript', 'frontend', 'backend'],
        'base de données' => ['sql', 'mysql', 'postgresql', 'nosql', 'mongodb', 'sgbd'],
        'gestion' => ['management', 'administration', 'planification', 'erp', 'crm'],
        'plagiat' => ['tricherie', 'copie', 'similarité', 'intégrité scientifique', 'fraude'],
    ];

    /**
     * Effectue une recherche sémantique sur les sujets archivés.
     * 
     * @param string $query
     * @param \Illuminate\Database\Eloquent\Collection $subjects
     * @return array Tableau de résultats avec les scores et l'ID du sujet
     */
    public function search(string $query, $subjects): array
    {
        if ($subjects->isEmpty() || empty(trim($query))) {
            return [];
        }

        $expandedQuery = $this->expandQuery($query);

        $corpus = [];
        $subjectIds = [];
        
        // Index 0 est notre requête
        $corpus[0] = mb_strtolower($expandedQuery, 'UTF-8');
        $subjectIds[0] = 'QUERY';

        $index = 1;
        foreach ($subjects as $subject) {
            $text = $subject->title . ' ' . $subject->description . ' ' . $subject->research_question;
            $text = mb_strtolower($text, 'UTF-8');
            // Retirer la ponctuation
            $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
            
            $corpus[$index] = $text;
            $subjectIds[$index] = $subject->id;
            $index++;
        }

        // Tokenisation et calcul TF
        $vectorizer = new TokenCountVectorizer(new WordTokenizer());
        $vectorizer->fit($corpus);
        $vectorizer->transform($corpus);

        // Transformation TF-IDF
        $transformer = new TfIdfTransformer($corpus);
        $transformer->transform($corpus);

        /** @var array $queryVector */
        $queryVector = $corpus[0];
        
        $results = [];

        for ($i = 1; $i < count($corpus); $i++) {
            /** @var array $docVector */
            $docVector = $corpus[$i];
            
            if (empty(array_filter($queryVector)) || empty(array_filter($docVector))) {
                $similarity = 0.0;
            } else {
                $similarity = $this->cosineSimilarity($queryVector, $docVector);
            }

            // On ne garde que les documents ayant une similarité minimale
            if ($similarity > 0.05) {
                $results[] = [
                    'subject_id' => $subjectIds[$i],
                    'score' => round($similarity * 100, 2),
                ];
            }
        }

        // Tri décroissant
        usort($results, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $results;
    }

    /**
     * Étend la requête utilisateur avec des concepts proches (synonymes).
     */
    public function expandQuery(string $query): string
    {
        $expanded = $query;
        $queryLower = mb_strtolower($query, 'UTF-8');

        foreach ($this->synonyms as $keyword => $related) {
            if (str_contains($queryLower, $keyword)) {
                $expanded .= ' ' . implode(' ', $related);
            } else {
                foreach ($related as $rel) {
                    if (str_contains($queryLower, $rel)) {
                        $expanded .= ' ' . $keyword . ' ' . implode(' ', array_diff($related, [$rel]));
                        break;
                    }
                }
            }
        }

        return $expanded;
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
