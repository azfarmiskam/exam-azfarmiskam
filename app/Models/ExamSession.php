<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = [
        'student_id',
        'classroom_id',
        'started_at',
        'completed_at',
        'expires_at',
        'score',
        'total_questions',
        'correct_answers',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class, 'session_id');
    }
}
