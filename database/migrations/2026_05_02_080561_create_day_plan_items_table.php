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
        Schema::create('day_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('day_plan_id')->constrained('day_plans')->cascadeOnDelete();
            $table->foreignId('meal_id')->constrained('meals')->cascadeOnDelete();
            $table->string('meal_type', 50);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['day_plan_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_plan_items');
    }
};
