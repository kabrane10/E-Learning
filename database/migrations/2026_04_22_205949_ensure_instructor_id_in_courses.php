<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si la colonne existe déjà
        if (!Schema::hasColumn('courses', 'instructor_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('instructor_id')->after('id')->constrained('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courses', 'instructor_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropForeign(['instructor_id']);
                $table->dropColumn('instructor_id');
            });
        }
    }
};