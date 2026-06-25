<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_file_id',
        'milestone_id',
        'similarity_score',
        'ai_score',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'similarity_score' => 'integer',
            'ai_score' => 'integer',
        ];
    }

    /**
     * Le fichier TFC analysé.
     */
    public function thesisFile(): BelongsTo
    {
        return $this->belongsTo(ThesisFile::class);
    }

    /**
     * Le jalon analysé (pour soumission texte).
     */
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    /**
     * Niveau de risque basé sur le score IA.
     */
    public function getRiskLevelAttribute(): string
    {
        if ($this->ai_score < 20) return 'low';
        if ($this->ai_score < 50) return 'medium';
        return 'high';
    }

    /**
     * Couleur du badge selon le risque.
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
        };
    }
}
