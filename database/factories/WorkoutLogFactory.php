<?php

namespace Database\Factories;

use App\Models\ClientProgram;
use App\Models\ProgramWorkout;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutLog>
 */
class WorkoutLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => User::factory(),
            'client_program_id' => ClientProgram::factory(),
            'program_workout_id' => ProgramWorkout::factory(),
            'completed_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
