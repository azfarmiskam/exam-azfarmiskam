<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'code',
        'questions_per_exam',
        'timer_minutes',
        'show_results_immediately',
        'show_correct_answers',
        'allow_review',
        'shuffle_questions',
        'instructions',
        'is_active',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'show_results_immediately' => 'boolean',
        'show_correct_answers' => 'boolean',
        'allow_review' => 'boolean',
        'shuffle_questions' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created the classroom.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the groups for the classroom.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(ClassroomGroup::class);
    }

    /**
     * Get the students in the classroom.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the questions for the classroom.
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'classroom_questions')
            ->withPivot('order')
            ->withTimestamps();
    }

    /**
     * Get the exam sessions for the classroom.
     */
    public function examSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }
}
