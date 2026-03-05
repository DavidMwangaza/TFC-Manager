<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty',
        'name',
        'code',
        'description',
    ];

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
