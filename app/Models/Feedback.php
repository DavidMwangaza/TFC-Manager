<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'thesis_file_id',
        'author_id',
        'content_remarque',
    ];

    public function thesisFile()
    {
        return $this->belongsTo(ThesisFile::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
