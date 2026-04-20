<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['single', 'multiple', 'true_false', 'text']);
            $table->text('explanation')->nullable(); // Explication de la réponse
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index('quiz_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};