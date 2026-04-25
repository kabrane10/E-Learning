<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'is_free')) {
                $table->boolean('is_free')->default(true)->after('level');
            }
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('is_free');
            }
            if (!Schema::hasColumn('courses', 'short_description')) {
                $table->string('short_description', 200)->nullable()->after('description');
            }
            if (!Schema::hasColumn('courses', 'learning_outcomes')) {
                $table->json('learning_outcomes')->nullable()->after('description');
            }
            if (!Schema::hasColumn('courses', 'prerequisites')) {
                $table->json('prerequisites')->nullable()->after('learning_outcomes');
            }
            if (!Schema::hasColumn('courses', 'target_audience')) {
                $table->text('target_audience')->nullable()->after('prerequisites');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'is_free',
                'price',
                'short_description',
                'learning_outcomes',
                'prerequisites',
                'target_audience'
            ]);
        });
    }
};