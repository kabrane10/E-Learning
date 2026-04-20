<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_points')->default(0);
            $table->integer('current_level')->default(1);
            $table->integer('experience_points')->default(0);
            $table->integer('streak_days')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'total_points',
                'current_level',
                'experience_points',
                'streak_days',
                'last_activity_at',
                'last_login_at'
            ]);
        });
    }
};