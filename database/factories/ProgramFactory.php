<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coach_id' => User::factory()->coach(),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'duration_weeks' => fake()->numberBetween(4, 12),
            'type' => fake()->randomElement(['strength', 'hypertrophy', 'fat_loss', 'general']),
            'is_template' => false,
        ];
    }
}
