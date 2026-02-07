<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coach_id' => User::factory()->state(['role' => 'coach']),
            'name' => fake()->words(3, true),
            'description' => null,
            'calories' => fake()->numberBetween(200, 800),
            'protein' => fake()->numberBetween(10, 50),
            'carbs' => fake()->numberBetween(20, 80),
            'fat' => fake()->numberBetween(5, 30),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the meal is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
