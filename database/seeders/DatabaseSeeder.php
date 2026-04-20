<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
    
        // Création d'un admin par défaut pour tester
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@elearning.com',
        ]);
        $admin->assignRole('admin');
    }
}
