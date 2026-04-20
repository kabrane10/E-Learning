<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'progress_percentage',
        'completed_at',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function updateProgress(): void
    {
        $course = $this->course;
        $totalLessons = $course->lessons()->count();
        
        if ($totalLessons === 0) {
            $this->update(['progress_percentage' => 0]);
            return;
        }

        $completedLessons = $this->lessonCompletions()->count();
        $progress = round(($completedLessons / $totalLessons) * 100);
        
        $this->update([
            'progress_percentage' => $progress,
            'completed_at' => $progress === 100 ? now() : null,
        ]);
    }
}