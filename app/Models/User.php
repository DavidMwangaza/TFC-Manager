<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'matricule',
        'department_id',
        'is_blocked',
    ];

    protected $attributes = [
        'is_blocked' => false,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_blocked' => 'boolean',
        ];
    }

    /**
     * Vérifie si l'utilisateur est bloqué.
     */
    public function isBlocked(): bool
    {
        return $this->is_blocked;
    }

    /**
     * Département de l'utilisateur.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Sujets soumis par l'étudiant.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'student_id');
    }

    /**
     * Sujets encadrés par l'enseignant.
     */
    public function supervisedSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }
}
