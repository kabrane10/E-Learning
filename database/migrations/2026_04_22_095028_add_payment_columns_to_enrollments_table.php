<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Vérifier si les colonnes existent avant de les ajouter
            if (!Schema::hasColumn('enrollments', 'price_paid')) {
                $table->decimal('price_paid', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn('enrollments', 'payout_id')) {
                $table->foreignId('payout_id')->nullable()->constrained()->nullOnDelete();
            }
            
            if (!Schema::hasColumn('enrollments', 'enrolled_at')) {
                $table->timestamp('enrolled_at')->nullable();
            }
            
            if (!Schema::hasColumn('enrollments', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // SQLite ne supporte pas bien dropColumn, on vérifie d'abord
            $columns = ['price_paid', 'payout_id', 'enrolled_at', 'last_activity_at'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('enrollments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};