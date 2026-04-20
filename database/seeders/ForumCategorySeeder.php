<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Annonces',
                'slug' => 'annonces',
                'description' => 'Annonces officielles et actualités de la plateforme',
                'icon' => 'bullhorn',
                'color' => 'red',
                'order' => 1,
            ],
            [
                'name' => 'Discussions générales',
                'slug' => 'general',
                'description' => 'Discussions générales sur l\'apprentissage et les cours',
                'icon' => 'comments',
                'color' => 'blue',
                'order' => 2,
            ],
            [
                'name' => 'Questions / Réponses',
                'slug' => 'questions',
                'description' => 'Posez vos questions et obtenez des réponses de la communauté',
                'icon' => 'question-circle',
                'color' => 'green',
                'order' => 3,
            ],
            [
                'name' => 'Ressources',
                'slug' => 'ressources',
                'description' => 'Partagez des ressources utiles, tutoriels et liens',
                'icon' => 'link',
                'color' => 'purple',
                'order' => 4,
            ],
            [
                'name' => 'Suggestions',
                'slug' => 'suggestions',
                'description' => 'Proposez des améliorations pour la plateforme',
                'icon' => 'lightbulb',
                'color' => 'yellow',
                'order' => 5,
            ],
            [
                'name' => 'Présentations',
                'slug' => 'presentations',
                'description' => 'Présentez-vous à la communauté',
                'icon' => 'user-plus',
                'color' => 'indigo',
                'order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            ForumCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('✅ Catégories du forum créées !');
    }
}