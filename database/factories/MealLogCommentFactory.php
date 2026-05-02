<?php

namespace Database\Factories;

use App\Models\MealLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MealLogComment>
 */
class MealLogCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meal_log_id' => MealLog::factory(),
            'author_id' => User::factory()->state(['role' => 'coach']),
            'body' => fake()->sentence(),
            'read_at' => null,
        ];
    }
}
