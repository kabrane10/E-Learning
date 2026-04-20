<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'enrollment_id',
        'score',
        'total_questions',
        'correct_answers',
        'time_spent',
        'is_passed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'time_spent' => 'integer',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function (QuizAttempt $attempt) {
            // Si le quiz est réussi, on peut marquer la leçon comme complétée
            if ($attempt->is_passed) {
                $lesson = $attempt->quiz->lesson;
                
                LessonCompletion::firstOrCreate([
                    'user_id' => $attempt->user_id,
                    'lesson_id' => $lesson->id,
                ], [
                    'enrollment_id' => $attempt->enrollment_id,
                ]);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function getFormattedTimeSpentAttribute(): string
    {
        if (!$this->time_spent) {
            return '0:00';
        }

        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;
        
        return $minutes . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }

    public function getAccuracyAttribute(): float
    {
        if ($this->total_questions === 0) {
            return 0;
        }

        return round(($this->correct_answers / $this->total_questions) * 100, 2);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}