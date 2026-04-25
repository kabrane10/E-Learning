<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\Level;
use App\Models\Point;
use App\Models\Achievement;
use App\Models\UserBadge;
use App\Models\UserAchievement;
use App\Notifications\BadgeEarned;
use App\Notifications\LevelUp;
use App\Notifications\AchievementCompleted;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Points par action
     */
    private const POINTS = [
        'course_completed' => 500,
        'lesson_completed' => 50,
        'quiz_passed' => 200,
        'perfect_quiz' => 100,
        'daily_login' => 10,
        'streak_bonus' => 50,
        'review_written' => 30,
        'badge_earned' => 100,
        'achievement_completed' => 300,
        'first_course' => 250,
        'first_quiz' => 150,
        'forum_topic_created' => 25,
        'forum_post_created' => 15,
        'post_liked' => 5,
        'solution_marked' => 50,
        'level_up_bonus' => 200,
    ];

    /**
     * Ajouter des points à un utilisateur
     */
    public function addPoints(User $user, string $action, $pointable = null, array $metadata = []): ?Point
    {
        $amount = self::POINTS[$action] ?? 0;
        
        if ($amount <= 0) {
            return null;
        }
        
        // Bonus de streak
        if ($user->streak_days > 0 && !in_array($action, ['daily_login', 'streak_bonus'])) {
            $streakBonus = min(100, $user->streak_days * 5);
            $amount += $streakBonus;
            $metadata['streak_bonus'] = $streakBonus;
        }

        try {
            $point = DB::transaction(function () use ($user, $amount, $action, $pointable, $metadata) {
                // ✅ Gestion du pointable : si null, on ne remplit pas les colonnes
                $pointData = [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'action' => $action,
                    'description' => $this->getDescription($action, $amount),
                    'metadata' => json_encode($metadata),
                ];
                
                // Ajouter pointable seulement s'il existe
                if ($pointable) {
                    $pointData['pointable_type'] = get_class($pointable);
                    $pointData['pointable_id'] = $pointable->id;
                }
                
                $point = Point::create($pointData);

                // Mettre à jour les points totaux et l'XP
                $user->increment('total_points', $amount);
                $user->increment('experience_points', $amount);

                // Vérifier le niveau
                $this->checkLevelUp($user);

                // Vérifier les badges
                $this->checkBadges($user);

                // Vérifier les achievements
                $this->checkAchievements($user);

                return $point;
            });

            return $point;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'ajout de points: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'action' => $action,
                'amount' => $amount,
            ]);
            return null;
        }
    }

    /**
     * Vérifier et mettre à jour le niveau
     */
    public function checkLevelUp(User $user): void
    {
        $currentLevel = Level::find($user->current_level);
        $newLevel = Level::getLevelForPoints($user->total_points);

        if ($newLevel && $newLevel->level_number > $user->current_level) {
            $user->update(['current_level' => $newLevel->level_number]);
            
            // Notification de niveau supérieur
            try {
                $user->notify(new LevelUp($newLevel));
            } catch (\Exception $e) {
                \Log::error('Erreur notification level up: ' . $e->getMessage());
            }
            
            // Points bonus pour le niveau supérieur
            $this->addPoints($user, 'level_up_bonus', $newLevel, [
                'from_level' => $currentLevel->level_number ?? 1,
                'to_level' => $newLevel->level_number,
            ]);
        }
    }

    /**
     * Vérifier et attribuer les badges
     */
    public function checkBadges(User $user): array
    {
        $earnedBadges = [];
        
        $badges = Badge::where('is_active', true)
            ->whereDoesntHave('users', fn($q) => $q->where('user_id', $user->id)->whereNotNull('earned_at'))
            ->get();

        foreach ($badges as $badge) {
            if ($badge->checkCompletion($user)) {
                $this->awardBadge($user, $badge);
                $earnedBadges[] = $badge;
            } else {
                // Mettre à jour la progression
                $progress = $badge->getProgress($user);
                UserBadge::updateOrCreate(
                    ['user_id' => $user->id, 'badge_id' => $badge->id],
                    ['progress' => $progress]
                );
            }
        }

        return $earnedBadges;
    }

    /**
     * Attribuer un badge
     */
    public function awardBadge(User $user, Badge $badge): UserBadge
    {
        $userBadge = UserBadge::updateOrCreate(
            ['user_id' => $user->id, 'badge_id' => $badge->id],
            [
                'progress' => $badge->getProgress($user),
                'earned_at' => now(),
            ]
        );

        // Points pour le badge
        if ($badge->points_reward > 0) {
            $this->addPoints($user, 'badge_earned', $badge);
        }

        // Notification
        try {
            $user->notify(new BadgeEarned($badge));
        } catch (\Exception $e) {
            \Log::error('Erreur notification badge: ' . $e->getMessage());
        }

        return $userBadge;
    }

    /**
     * Vérifier et attribuer les achievements
     */
    public function checkAchievements(User $user): array
    {
        $completedAchievements = [];
        
        $achievements = Achievement::where('is_active', true)->get();

        foreach ($achievements as $achievement) {
            $userAchievement = UserAchievement::firstOrCreate(
                ['user_id' => $user->id, 'achievement_id' => $achievement->id]
            );

            if (!$userAchievement->completed_at && $achievement->checkCompletion($user)) {
                $userAchievement->update(['completed_at' => now()]);
                $completedAchievements[] = $achievement;
            }
        }

        return $completedAchievements;
    }

    /**
     * Réclamer les récompenses d'un achievement
     */
    public function claimAchievement(User $user, Achievement $achievement): void
    {
        $userAchievement = UserAchievement::where('user_id', $user->id)
            ->where('achievement_id', $achievement->id)
            ->first();

        if ($userAchievement && $userAchievement->completed_at && !$userAchievement->claimed_at) {
            DB::transaction(function () use ($user, $achievement, $userAchievement) {
                // Marquer comme réclamé
                $userAchievement->update(['claimed_at' => now()]);
                
                // Ajouter les points
                if ($achievement->points_reward > 0) {
                    $this->addPoints($user, 'achievement_completed', $achievement);
                }
                
                // Notification
                try {
                    $user->notify(new AchievementCompleted($achievement));
                } catch (\Exception $e) {
                    \Log::error('Erreur notification achievement: ' . $e->getMessage());
                }
            });
        }
    }

    /**
     * Mettre à jour la streak quotidienne
     */
    public function updateStreak(User $user): void
    {
        $now = now();
        $lastActivity = $user->last_activity_at;

        if ($lastActivity) {
            $daysDiff = $lastActivity->diffInDays($now);

            if ($daysDiff == 0) {
                // Même jour, ne rien faire
                return;
            } elseif ($daysDiff == 1) {
                // Jour consécutif
                $user->increment('streak_days');
                
                // Points de streak
                $streakBonus = min(100, $user->streak_days * 5);
                $this->addPoints($user, 'streak_bonus', null, [
                    'streak_days' => $user->streak_days,
                    'bonus' => $streakBonus,
                ]);
            } else {
                // Streak brisée
                $user->update(['streak_days' => 0]);
            }
        } else {
            // Première activité
            $user->update(['streak_days' => 1]);
        }

        $user->update(['last_activity_at' => $now]);
    }

    /**
     * Obtenir le classement des utilisateurs
     */
    public function getLeaderboard(string $type = 'points', int $limit = 50): array
    {
        $query = User::query();
        
        switch ($type) {
            case 'points':
                $query->orderBy('total_points', 'desc');
                break;
            case 'level':
                $query->orderBy('current_level', 'desc')
                      ->orderBy('experience_points', 'desc');
                break;
            case 'badges':
                $query->withCount(['badges as badges_count' => function ($q) {
                    $q->whereNotNull('earned_at');
                }])->orderBy('badges_count', 'desc');
                break;
            case 'streak':
                $query->orderBy('streak_days', 'desc');
                break;
            default:
                $query->orderBy('total_points', 'desc');
        }
        
        $users = $query->limit($limit)->get();
        
        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'total_points' => $user->total_points,
                'current_level' => $user->current_level,
                'experience_points' => $user->experience_points,
                'streak_days' => $user->streak_days,
                'badges_count' => $user->badges()->whereNotNull('earned_at')->count(),
            ];
        })->toArray();
    }

    /**
     * Obtenir les statistiques de gamification d'un utilisateur
     */
    public function getUserStats(User $user): array
    {
        $currentLevel = Level::find($user->current_level);
        $nextLevel = Level::getNextLevel($user->current_level);
        
        $badgesEarned = $user->badges()->whereNotNull('earned_at')->count();
        $totalBadges = Badge::where('is_active', true)->count();
        
        $achievementsCompleted = $user->achievements()->whereNotNull('completed_at')->count();
        $totalAchievements = Achievement::where('is_active', true)->count();

        return [
            'total_points' => $user->total_points,
            'current_level' => [
                'number' => $user->current_level,
                'name' => $currentLevel->name ?? 'Débutant',
                'icon' => $currentLevel->icon ?? '🌱',
                'color' => $currentLevel->color ?? 'green',
            ],
            'next_level' => $nextLevel ? [
                'number' => $nextLevel->level_number,
                'name' => $nextLevel->name,
                'points_required' => $nextLevel->points_required,
                'progress' => $currentLevel ? $currentLevel->getProgressToNextLevel($user->total_points) : 0,
            ] : null,
            'streak_days' => $user->streak_days,
            'badges' => [
                'earned' => $badgesEarned,
                'total' => $totalBadges,
                'percentage' => $totalBadges > 0 ? round(($badgesEarned / $totalBadges) * 100) : 0,
            ],
            'achievements' => [
                'completed' => $achievementsCompleted,
                'total' => $totalAchievements,
                'percentage' => $totalAchievements > 0 ? round(($achievementsCompleted / $totalAchievements) * 100) : 0,
            ],
            'rank' => $this->getUserRank($user),
        ];
    }

    /**
     * Obtenir le classement d'un utilisateur
     */
    public function getUserRank(User $user): int
    {
        return User::where('total_points', '>', $user->total_points)->count() + 1;
    }

    /**
     * Obtenir la description d'une action
     */
    private function getDescription(string $action, int $amount): string
    {
        return match ($action) {
            'course_completed' => "Cours terminé (+{$amount} points)",
            'lesson_completed' => "Leçon terminée (+{$amount} points)",
            'quiz_passed' => "Quiz réussi (+{$amount} points)",
            'perfect_quiz' => "Quiz parfait (+{$amount} points bonus)",
            'daily_login' => "Connexion quotidienne (+{$amount} points)",
            'streak_bonus' => "Bonus de série (+{$amount} points)",
            'review_written' => "Avis déposé (+{$amount} points)",
            'badge_earned' => "Badge obtenu (+{$amount} points)",
            'achievement_completed' => "Succès débloqué (+{$amount} points)",
            'first_course' => "Premier cours (+{$amount} points)",
            'first_quiz' => "Premier quiz (+{$amount} points)",
            'forum_topic_created' => "Sujet créé (+{$amount} points)",
            'forum_post_created' => "Réponse postée (+{$amount} points)",
            'post_liked' => "Message aimé (+{$amount} points)",
            'solution_marked' => "Solution marquée (+{$amount} points)",
            'level_up_bonus' => "Niveau supérieur (+{$amount} points)",
            default => "+{$amount} points",
        };
    }
}