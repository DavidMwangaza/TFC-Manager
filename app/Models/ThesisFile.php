<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ThesisFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'file_path',
        'original_name',
        'version_type',
    ];

    /**
     * Le sujet associé.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Le rapport IA généré pour ce fichier.
     */
    public function aiReport(): HasOne
    {
        return $this->hasOne(AiReport::class);
    }
}
