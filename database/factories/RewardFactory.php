<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reward>
 */
class RewardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'coach_id' => User::factory()->coach(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'points_cost' => fake()->numberBetween(50, 500),
            'stock' => null,
            'is_active' => true,
        ];
    }

    public function global(): static
    {
        return $this->state(fn () => ['coach_id' => null]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
