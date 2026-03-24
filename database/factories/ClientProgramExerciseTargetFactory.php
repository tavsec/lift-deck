<?php

namespace Database\Factories;

use App\Models\ClientProgram;
use App\Models\WorkoutExercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientProgramExerciseTarget>
 */
class ClientProgramExerciseTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_program_id' => ClientProgram::factory(),
            'workout_exercise_id' => WorkoutExercise::factory(),
            'target_weight' => fake()->randomFloat(2, 20, 200),
        ];
    }
}
