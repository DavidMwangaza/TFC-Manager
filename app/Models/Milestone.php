<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\ThesisFile;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'title',
        'due_date',
        'submission_date',
        'correction_deadline',
        'status',
        'comments',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'submission_date' => 'datetime',
            'correction_deadline' => 'datetime',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Le fichier TFC déposé pour ce jalon (optionnel).
     */
    public function thesisFile(): HasOne
    {
        return $this->hasOne(ThesisFile::class);
    }
}
