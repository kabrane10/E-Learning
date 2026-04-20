<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'category',
        'requirements',
        'points_reward',
        'tier',
        'is_active',
        'order',
    ];

    protected $casts = [
        'requirements' => 'array',
        'points_reward' => 'integer',
        'tier' => 'integer',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the users that have this achievement.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
                    ->withPivot('progress', 'current_tier', 'completed_at', 'claimed_at')
                    ->withTimestamps();
    }

    /**
     * Get the user achievements pivot records.
     */
    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class, 'achievement_id');
    }

    /**
     * Get the tier name.
     */
    public function getTierName(): string
    {
        return match ($this->tier) {
            1 => 'Bronze',
            2 => 'Argent',
            3 => 'Or',
            4 => 'Platine',
            5 => 'Diamant',
            default => 'Standard',
        };
    }

    /**
     * Get the tier color.
     */
    public function getTierColor(): string
    {
        return match ($this->tier) {
            1 => '#CD7F32', // Bronze
            2 => '#C0C0C0', // Argent
            3 => '#FFD700', // Or
            4 => '#E5E4E2', // Platine
            5 => '#B9F2FF', // Diamant
            default => '#6B7280',
        };
    }

    /**
     * Check if user has completed this achievement.
     */
    public function checkCompletion(User $user): bool
    {
        $requirements = $this->requirements;
        $type = $requirements['type'] ?? null;

        return match ($type) {
            'watch_time' => $this->getWatchTime($user) >= ($requirements['minutes'] ?? 0),
            'courses_completed' => $user->enrolledCourses()
                ->whereNotNull('enrollments.completed_at')
                ->count() >= ($requirements['count'] ?? 1),
            'quizzes_passed' => $user->quizAttempts()
                ->where('is_passed', true)
                ->count() >= ($requirements['count'] ?? 1),
            'streak_days' => $user->streak_days >= ($requirements['days'] ?? 1),
            'points_earned' => $user->total_points >= ($requirements['points'] ?? 1),
            'badges_earned' => $user->badges()
                ->whereNotNull('earned_at')
                ->count() >= ($requirements['count'] ?? 1),
            'forum_posts' => $user->forumPosts()->count() >= ($requirements['count'] ?? 1),
            default => false,
        };
    }

    /**
     * Get watch time in minutes.
     */
    private function getWatchTime(User $user): int
    {
        return (int) ($user->lessonCompletions()
            ->join('lessons', 'lesson_completions.lesson_id', '=', 'lessons.id')
            ->sum('lessons.duration') / 60);
    }

    /**
     * Scope for active achievements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for achievements by tier.
     */
    public function scopeByTier($query, int $tier)
    {
        return $query->where('tier', $tier);
    }
}