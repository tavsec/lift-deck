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
            'meal_type' => fake()->randomElement(['Breakfast', 'Lunch', 'Dinner', 'Snack']),
            'sort_order' => 0,
        ];
    }
}
