<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\XpEventType>
 */
class XpEventTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'name' => fake()->words(3, true),
            'description' => null,
            'xp_amount' => fake()->numberBetween(5, 50),
            'points_amount' => fake()->numberBetween(5, 50),
            'is_active' => true,
            'cooldown_hours' => null,
        ];
    }
}
