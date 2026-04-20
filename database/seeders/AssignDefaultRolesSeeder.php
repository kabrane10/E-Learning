<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AssignDefaultRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Assigner 'instructor' au premier utilisateur
        $firstUser = User::first();
        if ($firstUser && !$firstUser->hasRole('instructor')) {
            $firstUser->assignRole('instructor');
            $this->command->info("✅ Rôle instructor assigné à : {$firstUser->email}");
        }
        
        // Optionnel : Créer un admin spécifique
        $admin = User::where('email', 'admin@elearning.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@elearning.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }
        
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
            $this->command->info("✅ Rôle admin assigné à : admin@elearning.com");
        }
    }
}