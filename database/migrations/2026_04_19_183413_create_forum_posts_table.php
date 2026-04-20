<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('forum_topics')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->foreignId('parent_id')->nullable()->constrained('forum_posts')->nullOnDelete();
            $table->integer('likes_count')->default(0);
            $table->boolean('is_solution')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['topic_id', 'created_at']);
            $table->index('user_id');
            $table->index('is_solution');
            
            // FULLTEXT uniquement pour MySQL/MariaDB
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE forum_posts ADD FULLTEXT forum_posts_content_fulltext (content)');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};