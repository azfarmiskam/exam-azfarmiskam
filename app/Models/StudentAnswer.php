<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $fillable = [
        'session_id',
        'question_id',
        'answer',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'session_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
