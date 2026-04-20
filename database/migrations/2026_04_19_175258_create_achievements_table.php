<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon');
            $table->string('color')->default('indigo');
            $table->string('category'); // learning, teaching, community, special
            $table->json('requirements'); // {"type": "watch_time", "minutes": 600}
            $table->integer('points_reward')->default(0);
            $table->integer('tier')->default(1); // 1: Bronze, 2: Argent, 3: Or, 4: Platine
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};