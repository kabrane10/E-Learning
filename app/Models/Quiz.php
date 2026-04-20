<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'passing_score',
        'time_limit',
        'shuffle_questions',
        'max_attempts',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'time_limit' => 'integer',
        'shuffle_questions' => 'boolean',
        'max_attempts' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function userAttempts($userId): HasMany
    {
        return $this->attempts()->where('user_id', $userId);
    }

    public function hasUserPassed($userId): bool
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('is_passed', true)
            ->exists();
    }

    public function getUserBestScore($userId): ?int
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->max('score');
    }

    public function getUserAttemptCount($userId): int
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->count();
    }

    public function canUserAttempt($userId): bool
    {
        if ($this->max_attempts === null) {
            return true;
        }

        return $this->getUserAttemptCount($userId) < $this->max_attempts;
    }

    public function getRemainingAttempts($userId): int
    {
        if ($this->max_attempts === null) {
            return PHP_INT_MAX;
        }

        $used = $this->getUserAttemptCount($userId);
        return max(0, $this->max_attempts - $used);
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->questions->sum('points');
    }

    public function getQuestionCountAttribute(): int
    {
        return $this->questions->count();
    }
}