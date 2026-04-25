<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier et ajouter chaque colonne si elle n'existe pas déjà
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'title')) {
                $table->string('title')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('title');
            }
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'twitter')) {
                $table->string('twitter')->nullable()->after('website');
            }
            if (!Schema::hasColumn('users', 'linkedin')) {
                $table->string('linkedin')->nullable()->after('twitter');
            }
            if (!Schema::hasColumn('users', 'youtube')) {
                $table->string('youtube')->nullable()->after('linkedin');
            }
            if (!Schema::hasColumn('users', 'settings')) {
                $table->json('settings')->nullable()->after('youtube');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['title', 'bio', 'website', 'twitter', 'linkedin', 'youtube', 'settings'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};