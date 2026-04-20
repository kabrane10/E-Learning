<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->string('type')->default('text'); // text, image, file, system
            $table->json('metadata')->nullable();
            $table->foreignId('reply_to_id')->nullable()->constrained('messages')->nullOnDelete();
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['conversation_id', 'created_at']);
            $table->index('user_id');
            
            // FULLTEXT uniquement pour MySQL/MariaDB
            if (DB::getDriverName() !== 'sqlite') {
                $table->fullText('content');
            }
        });
        
        // Pour SQLite, on peut créer un index normal pour améliorer les recherches
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('messages', function (Blueprint $table) {
                $table->index('content');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};