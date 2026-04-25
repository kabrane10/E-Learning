<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('enrollments', 'price_paid')) {
                $table->decimal('price_paid', 10, 2)->nullable()->after('progress_percentage');
            }
            if (!Schema::hasColumn('enrollments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('price_paid');
            }
            if (!Schema::hasColumn('enrollments', 'enrolled_at')) {
                $table->timestamp('enrolled_at')->nullable()->after('paid_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['price_paid', 'paid_at', 'enrolled_at']);
        });
    }
};