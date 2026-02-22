<?php

namespace Database\Factories;

use App\Models\Reward;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RewardRedemption>
 */
class RewardRedemptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->client(),
            'reward_id' => Reward::factory(),
            'points_spent' => fake()->numberBetween(50, 500),
            'status' => 'pending',
            'coach_notes' => null,
        ];
    }

    public function fulfilled(): static
    {
        return $this->state(fn () => ['status' => 'fulfilled']);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }
}
