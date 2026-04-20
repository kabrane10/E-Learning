<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('passing_score')->default(70); // Pourcentage pour réussir
            $table->integer('time_limit')->nullable(); // En minutes, null = pas de limite
            $table->boolean('shuffle_questions')->default(true);
            $table->integer('max_attempts')->nullable(); // null = tentatives illimitées
            $table->timestamps();
            
            $table->index('lesson_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};