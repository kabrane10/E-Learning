<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\UserBadge;


class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'category',
        'criteria',
        'points_reward',
        'is_secret',
        'is_active',
        'order',
    ];

    protected $casts = [
        'criteria' => 'array',
        'points_reward' => 'integer',
        'is_secret' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the users that have this badge.
     * Relation Many-to-Many via la table pivot user_badges
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('progress', 'earned_at', 'is_pinned')
                    ->withTimestamps();
    }

    /**
     * Get the user badges pivot records.
     * Relation One-to-Many vers la table pivot
     */
    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class, 'badge_id');
    }

    /**
     * Vérifier si un utilisateur a complété ce badge.
     */
    public function checkCompletion(User $user): bool
    {
        $criteria = $this->criteria;
        $type = $criteria['type'] ?? null;
        $count = $criteria['count'] ?? 1;

        return match ($type) {
            'courses_completed' => $user->enrolledCourses()
                ->whereNotNull('enrollments.completed_at')
                ->count() >= $count,
                
            'quizzes_passed' => $user->quizAttempts()
                ->where('is_passed', true)
                ->count() >= $count,
                
            'streak_days' => $user->streak_days >= $count,
            
            'points_earned' => $user->total_points >= $count,
            
            'reviews_written' => $user->reviews()->count() >= $count,
            
            'perfect_quizzes' => $user->quizAttempts()
                ->where('score', 100)
                ->count() >= $count,
                
            'forum_topics' => $user->forumTopics()->count() >= $count,
            
            'forum_posts' => $user->forumPosts()->count() >= $count,
                
            default => false,
        };
    }

    /**
     * Obtenir la progression d'un utilisateur vers ce badge.
     */
    public function getProgress(User $user): array
    {
        $criteria = $this->criteria;
        $type = $criteria['type'] ?? null;
        $target = $criteria['count'] ?? 1;

        $current = match ($type) {
            'courses_completed' => $user->enrolledCourses()
                ->whereNotNull('enrollments.completed_at')
                ->count(),
                
            'quizzes_passed' => $user->quizAttempts()
                ->where('is_passed', true)
                ->count(),
                
            'streak_days' => $user->streak_days,
            
            'points_earned' => $user->total_points,
            
            'reviews_written' => $user->reviews()->count(),
            
            'perfect_quizzes' => $user->quizAttempts()
                ->where('score', 100)
                ->count(),
                
            'forum_topics' => $user->forumTopics()->count(),
            
            'forum_posts' => $user->forumPosts()->count(),
                
            default => 0,
        };

        return [
            'current' => min($current, $target),
            'target' => $target,
            'percentage' => $target > 0 ? min(100, round(($current / $target) * 100)) : 0,
        ];
    }

    /**
     * Scope pour les badges actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les badges par catégorie.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}