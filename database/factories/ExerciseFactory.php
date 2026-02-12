<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'muscle_group' => fake()->randomElement(['chest', 'back', 'shoulders', 'legs', 'arms', 'core']),
            'video_url' => null,
            'coach_id' => User::factory()->coach(),
            'is_active' => true,
        ];
    }
}
