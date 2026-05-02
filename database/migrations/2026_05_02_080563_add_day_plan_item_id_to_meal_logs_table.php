<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meal_logs', function (Blueprint $table) {
            $table->foreignId('day_plan_item_id')
                ->nullable()
                ->after('meal_id')
                ->constrained('day_plan_items')
                ->nullOnDelete();
            $table->index('day_plan_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meal_logs', function (Blueprint $table) {
            $table->dropForeign(['day_plan_item_id']);
            $table->dropIndex(['day_plan_item_id']);
            $table->dropColumn('day_plan_item_id');
        });
    }
};
