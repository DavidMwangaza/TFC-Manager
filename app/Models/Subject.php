<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject_type',
        'description',
        'context_relevance',
        'challenges',
        'research_question',
        'hypothesis',
        'general_objective',
        'specific_objectives',
        'state_of_art',
        'demarcations',
        'methodologies',
        'status',
        'rejection_reason',
        'defense_validated',
        'defense_date',
        'defense_room',
        'defense_revocation_reason',
        'student_id',
        'teacher_id',
        'department_id',
        'academic_year_id',
    ];

    protected function casts(): array
    {
        return [
            'defense_validated' => 'boolean',
            'defense_date' => 'datetime',
            'specific_objectives' => 'array',
            'state_of_art' => 'array',
        ];
    }

    /**
     * L'étudiant qui a proposé le sujet.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * L'enseignant encadreur assigné.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Le département.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Les fichiers TFC rattachés au sujet.
     */
    public function thesisFiles(): HasMany
    {
        return $this->hasMany(ThesisFile::class);
    }

    /**
     * Jalons (milestones) liés au sujet.
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }
    /**
     * Vérifie si le sujet est validé.
     */
    public function isValidated(): bool
    {
        return $this->status === 'validated';
    }

    /**
     * Vérifie si le sujet est en attente.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
