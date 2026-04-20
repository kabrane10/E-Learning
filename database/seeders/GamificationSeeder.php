<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Badge;
use App\Models\Achievement;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        // Niveaux
        $levels = [
            ['name' => 'Débutant', 'level_number' => 1, 'points_required' => 0, 'icon' => '🌱', 'color' => 'green'],
            ['name' => 'Apprenti', 'level_number' => 2, 'points_required' => 500, 'icon' => '📚', 'color' => 'blue'],
            ['name' => 'Initié', 'level_number' => 3, 'points_required' => 1500, 'icon' => '🔰', 'color' => 'cyan'],
            ['name' => 'Confirmé', 'level_number' => 4, 'points_required' => 3000, 'icon' => '⚡', 'color' => 'yellow'],
            ['name' => 'Expert', 'level_number' => 5, 'points_required' => 6000, 'icon' => '🎯', 'color' => 'orange'],
            ['name' => 'Maître', 'level_number' => 6, 'points_required' => 10000, 'icon' => '👑', 'color' => 'purple'],
            ['name' => 'Grand Maître', 'level_number' => 7, 'points_required' => 20000, 'icon' => '🌟', 'color' => 'pink'],
            ['name' => 'Légende', 'level_number' => 8, 'points_required' => 35000, 'icon' => '💎', 'color' => 'indigo'],
            ['name' => 'Élu', 'level_number' => 9, 'points_required' => 55000, 'icon' => '🔮', 'color' => 'violet'],
            ['name' => 'Immortel', 'level_number' => 10, 'points_required' => 100000, 'icon' => '∞', 'color' => 'red'],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }

        // Badges
        $badges = [
            // Badges de cours
            ['name' => 'Premier pas', 'slug' => 'first-course', 'description' => 'Terminer votre premier cours', 'icon' => '🎓', 'color' => 'green', 'category' => 'course', 'criteria' => ['type' => 'courses_completed', 'count' => 1], 'points_reward' => 100],
            ['name' => 'Étudiant assidu', 'slug' => '5-courses', 'description' => 'Terminer 5 cours', 'icon' => '📖', 'color' => 'blue', 'category' => 'course', 'criteria' => ['type' => 'courses_completed', 'count' => 5], 'points_reward' => 250],
            ['name' => 'Expert certifié', 'slug' => '10-courses', 'description' => 'Terminer 10 cours', 'icon' => '🏆', 'color' => 'gold', 'category' => 'course', 'criteria' => ['type' => 'courses_completed', 'count' => 10], 'points_reward' => 500],
            
            // Badges de quiz
            ['name' => 'Quiz Master', 'slug' => 'quiz-master', 'description' => 'Réussir 10 quiz', 'icon' => '❓', 'color' => 'purple', 'category' => 'quiz', 'criteria' => ['type' => 'quizzes_passed', 'count' => 10], 'points_reward' => 200],
            ['name' => 'Sans faute', 'slug' => 'perfect-quiz', 'description' => 'Obtenir 100% à un quiz', 'icon' => '💯', 'color' => 'gold', 'category' => 'quiz', 'criteria' => ['type' => 'perfect_quizzes', 'count' => 1], 'points_reward' => 150, 'is_secret' => true],
            
            // Badges d'activité
            ['name' => 'Acharné', 'slug' => '7-day-streak', 'description' => 'Maintenir une série de 7 jours', 'icon' => '🔥', 'color' => 'orange', 'category' => 'activity', 'criteria' => ['type' => 'streak_days', 'count' => 7], 'points_reward' => 150],
            ['name' => 'Inarrêtable', 'slug' => '30-day-streak', 'description' => 'Maintenir une série de 30 jours', 'icon' => '⚡', 'color' => 'red', 'category' => 'activity', 'criteria' => ['type' => 'streak_days', 'count' => 30], 'points_reward' => 500],
            
            // Badges de contribution
            ['name' => 'Critique', 'slug' => 'first-review', 'description' => 'Écrire votre premier avis', 'icon' => '✍️', 'color' => 'pink', 'category' => 'special', 'criteria' => ['type' => 'reviews_written', 'count' => 1], 'points_reward' => 50],
            ['name' => 'Influenceur', 'slug' => '10-reviews', 'description' => 'Écrire 10 avis', 'icon' => '📝', 'color' => 'indigo', 'category' => 'special', 'criteria' => ['type' => 'reviews_written', 'count' => 10], 'points_reward' => 200],
        ];

        foreach ($badges as $index => $badge) {
            $badge['order'] = $index;
            Badge::create($badge);
        }

        // Achievements (Succès)
        $achievements = [
            ['name' => 'Marathonien', 'slug' => 'watch-10-hours', 'description' => 'Regarder 10 heures de vidéos', 'icon' => '⏱️', 'color' => 'blue', 'category' => 'learning', 'requirements' => ['type' => 'watch_time', 'minutes' => 600], 'points_reward' => 300, 'tier' => 1],
            ['name' => 'Binge Watcher', 'slug' => 'watch-50-hours', 'description' => 'Regarder 50 heures de vidéos', 'icon' => '🍿', 'color' => 'purple', 'category' => 'learning', 'requirements' => ['type' => 'watch_time', 'minutes' => 3000], 'points_reward' => 800, 'tier' => 2],
            ['name' => 'Collectionneur de savoir', 'slug' => 'watch-100-hours', 'description' => 'Regarder 100 heures de vidéos', 'icon' => '🎬', 'color' => 'gold', 'category' => 'learning', 'requirements' => ['type' => 'watch_time', 'minutes' => 6000], 'points_reward' => 1500, 'tier' => 3],
            
            ['name' => 'Quizzer', 'slug' => 'pass-25-quizzes', 'description' => 'Réussir 25 quiz', 'icon' => '📊', 'color' => 'green', 'category' => 'learning', 'requirements' => ['type' => 'quizzes_passed', 'count' => 25], 'points_reward' => 400, 'tier' => 1],
            ['name' => 'Quiz Master', 'slug' => 'pass-100-quizzes', 'description' => 'Réussir 100 quiz', 'icon' => '🏅', 'color' => 'gold', 'category' => 'learning', 'requirements' => ['type' => 'quizzes_passed', 'count' => 100], 'points_reward' => 1000, 'tier' => 3],
            
            ['name' => 'Enflammé', 'slug' => 'streak-14-days', 'description' => 'Maintenir une série de 14 jours', 'icon' => '🔥', 'color' => 'orange', 'category' => 'activity', 'requirements' => ['type' => 'streak_days', 'days' => 14], 'points_reward' => 250, 'tier' => 1],
            ['name' => 'Phénix', 'slug' => 'streak-60-days', 'description' => 'Maintenir une série de 60 jours', 'icon' => '🦅', 'color' => 'red', 'category' => 'activity', 'requirements' => ['type' => 'streak_days', 'days' => 60], 'points_reward' => 800, 'tier' => 3],
        ];

        foreach ($achievements as $index => $achievement) {
            $achievement['order'] = $index;
            Achievement::create($achievement);
        }

        $this->command->info('✅ Données de gamification créées !');
    }
}