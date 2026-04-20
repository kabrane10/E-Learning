<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('subscribable');
            $table->enum('type', ['instant', 'daily', 'weekly'])->default('instant');
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'subscribable_id', 'subscribable_type'], 'forum_subscriptions_user_subscribable_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_subscriptions');
    }
};