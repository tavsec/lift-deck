<?php

namespace Database\Factories;

use App\Models\DayPlan;
use App\Models\Meal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DayPlanItem>
 */
class DayPlanItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'day_plan_id' => DayPlan::factory(),
            'meal_id' => Meal::factory(),
            'off_code' => null,
            'meal_type' => fake()->randomElement(['Breakfast', 'Lunch', 'Dinner', 'Snack']),
            'name' => fake()->words(2, true),
            'calories' => fake()->numberBetween(200, 800),
            'protein' => fake()->randomFloat(1, 10, 60),
            'carbs' => fake()->randomFloat(1, 10, 80),
            'fat' => fake()->randomFloat(1, 5, 30),
            'portion_grams' => null,
            'sort_order' => 0,
        ];
    }
}
