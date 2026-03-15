<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'coach_id' => User::factory()->coach(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'type' => 'automatic',
            'condition_type' => 'workout_count',
            'condition_value' => fake()->numberBetween(5, 50),
            'xp_reward' => fake()->numberBetween(10, 100),
            'points_reward' => fake()->numberBetween(10, 100),
            'is_active' => true,
        ];
    }

    public function manual(): static
    {
        return $this->state(fn () => [
            'type' => 'manual',
            'condition_type' => null,
            'condition_value' => null,
        ]);
    }

    public function global(): static
    {
        return $this->state(fn () => ['coach_id' => null]);
    }
}
