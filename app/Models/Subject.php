<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'bat_signed_by',
        'bat_signed_at',
        'bat_signature_hash',
        'financial_status',
        'financial_validated_by',
        'financial_validated_at',
        'financial_notes',
    ];

    protected function casts(): array
    {
        return [
            'defense_validated' => 'boolean',
            'defense_date' => 'datetime',
            'bat_signed_at' => 'datetime',
            'financial_validated_at' => 'datetime',
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
     * Les chapitres rattachés au sujet.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(\App\Models\Chapter::class);
    }

    public function defenseSchedule(): HasOne
    {
        return $this->hasOne(DefenseSchedule::class);
    }

    /**
     * Messages de la conversation Directeur ↔ Étudiant.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * L'utilisateur qui a signé le BAT.
     */
    public function batSigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bat_signed_by');
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

    /**
     * L'utilisateur qui a validé la conformité financière (Appariteur).
     */
    public function financialValidator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'financial_validated_by');
    }

    /**
     * Vérifie si la conformité financière est validée.
     */
    public function isFinanciallyValidated(): bool
    {
        return $this->financial_status === 'validated';
    }
}
