<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('day_plans', function (Blueprint $table): void {
            $table->foreignId('client_id')
                ->nullable()
                ->after('coach_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->index(['coach_id', 'client_id']);
        });

        // Add new item columns as nullable (or with safe defaults) so we can backfill before tightening.
        Schema::table('day_plan_items', function (Blueprint $table): void {
            $table->string('off_code', 64)->nullable()->after('meal_id');
            $table->string('name', 255)->nullable()->after('off_code');
            $table->unsignedInteger('calories')->default(0)->after('name');
            $table->decimal('protein', 6, 1)->default(0)->after('calories');
            $table->decimal('carbs', 6, 1)->default(0)->after('protein');
            $table->decimal('fat', 6, 1)->default(0)->after('carbs');
            $table->unsignedInteger('portion_grams')->nullable()->after('fat');
        });

        // Backfill snapshot fields from the linked meal (if any) so existing rows are valid.
        DB::table('day_plan_items')
            ->leftJoin('meals', 'meals.id', '=', 'day_plan_items.meal_id')
            ->orderBy('day_plan_items.id')
            ->select(
                'day_plan_items.id',
                'meals.name as meal_name',
                'meals.calories as meal_calories',
                'meals.protein as meal_protein',
                'meals.carbs as meal_carbs',
                'meals.fat as meal_fat',
            )
            ->chunkById(500, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('day_plan_items')
                        ->where('id', $row->id)
                        ->update([
                            'name' => $row->meal_name ?? 'Meal',
                            'calories' => (int) ($row->meal_calories ?? 0),
                            'protein' => (float) ($row->meal_protein ?? 0),
                            'carbs' => (float) ($row->meal_carbs ?? 0),
                            'fat' => (float) ($row->meal_fat ?? 0),
                        ]);
                }
            }, 'day_plan_items.id', 'id');

        // Now make name NOT NULL and relax meal_id to nullable.
        Schema::table('day_plan_items', function (Blueprint $table): void {
            $table->string('name', 255)->nullable(false)->change();
            $table->foreignId('meal_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_plan_items', function (Blueprint $table): void {
            // Restore meal_id to NOT NULL (best-effort) — any null rows will fail; that's expected on rollback.
            $table->foreignId('meal_id')->nullable(false)->change();
            $table->dropColumn(['off_code', 'name', 'calories', 'protein', 'carbs', 'fat', 'portion_grams']);
        });

        Schema::table('day_plans', function (Blueprint $table): void {
            $table->dropForeign(['client_id']);
            $table->dropIndex(['coach_id', 'client_id']);
            $table->dropColumn('client_id');
        });
    }
};
