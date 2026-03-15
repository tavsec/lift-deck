<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\XpEventType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\XpTransaction>
 */
class XpTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->client(),
            'xp_event_type_id' => XpEventType::factory(),
            'xp_amount' => fake()->numberBetween(5, 50),
            'points_amount' => fake()->numberBetween(5, 50),
            'metadata' => null,
            'created_at' => now(),
        ];
    }
}
