<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MacroGoal>
 */
class MacroGoalFactory extends Factory
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
            'coach_id' => User::factory()->state(['role' => 'coach']),
            'calories' => fake()->numberBetween(1500, 3000),
            'protein' => fake()->numberBetween(100, 250),
            'carbs' => fake()->numberBetween(150, 400),
            'fat' => fake()->numberBetween(40, 120),
            'effective_date' => fake()->date(),
            'notes' => null,
        ];
    }
}
