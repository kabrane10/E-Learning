<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->foreignId('category_id')->constrained('forum_categories')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['general', 'question', 'announcement', 'resource'])->default('general');
            $table->enum('status', ['open', 'closed', 'resolved', 'pinned'])->default('open');
            $table->integer('views_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->boolean('is_sticky')->default(false);
            $table->boolean('is_announcement')->default(false);
            $table->timestamp('last_post_at')->nullable();
            $table->foreignId('last_post_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index('last_post_at');
            
            // FULLTEXT uniquement pour MySQL/MariaDB
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE forum_topics ADD FULLTEXT forum_topics_title_content_fulltext (title, content)');
            } else {
                // Pour SQLite et PostgreSQL, utiliser des index simples
                $table->index('title');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_topics');
    }
};