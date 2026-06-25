<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Les filières de cette faculté.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Les doyens de cette faculté.
     */
    public function deans(): HasMany
    {
        return $this->hasMany(User::class, 'faculty_id')
            ->whereHas('roles', fn($q) => $q->where('name', 'Doyen'));
    }
}
