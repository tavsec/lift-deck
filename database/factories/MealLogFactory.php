<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MealLog>
 */
class MealLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => User::factory()->state(['role' => 'client']),
            'meal_id' => null,
            'date' => now()->format('Y-m-d'),
            'meal_type' => fake()->randomElement(['Breakfast', 'Lunch', 'Dinner', 'Snack']),
            'name' => fake()->words(3, true),
            'calories' => fake()->numberBetween(200, 800),
            'protein' => fake()->numberBetween(10, 50),
            'carbs' => fake()->numberBetween(20, 80),
            'fat' => fake()->numberBetween(5, 30),
            'notes' => null,
        ];
    }
}
