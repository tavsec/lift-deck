<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutLogComment>
 */
class WorkoutLogCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_log_id' => WorkoutLog::factory(),
            'user_id' => User::factory(),
            'body' => fake()->sentence(),
        ];
    }
}
