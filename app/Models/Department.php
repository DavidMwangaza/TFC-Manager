<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'name',
        'code',
        'description',
    ];

    /**
     * La faculté.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Utilisateurs du département.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Sujets du département.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
