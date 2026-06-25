<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ThesisFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'milestone_id',
        'file_path',
        'original_name',
        'version_type',
        'type',
        'uploaded_by',
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

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * L'utilisateur qui a uploader ce fichier.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Le jalon auquel ce fichier est lié (optionnel).
     */
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }
}
