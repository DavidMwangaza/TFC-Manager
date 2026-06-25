<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefenseSchedule extends Model
{
    protected $fillable = [
        'subject_id',
        'defense_date',
        'room',
        'jury_members',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'defense_date' => 'datetime',
            'jury_members' => 'array',
        ];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
