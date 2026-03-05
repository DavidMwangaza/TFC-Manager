<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'is_closed',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'is_closed' => 'boolean',
        ];
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Récupère l'année académique en cours.
     */
    public static function current(): ?self
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Définit cette année comme l'année en cours (et désactive les autres).
     */
    public function setAsCurrent(): void
    {
        static::where('is_current', true)->update(['is_current' => false]);
        $this->update(['is_current' => true]);
    }

    /**
     * Clôture l'année académique (archive).
     */
    public function close(): void
    {
        $this->update(['is_closed' => true, 'is_current' => false]);
    }
}
