<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\WorkoutExercise;
use App\Models\WorkoutLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExerciseLog>
 */
class ExerciseLogFactory extends Factory
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
            'workout_exercise_id' => WorkoutExercise::factory(),
            'exercise_id' => Exercise::factory(),
            'set_number' => fake()->numberBetween(1, 5),
            'weight' => fake()->randomFloat(2, 10, 150),
            'reps' => fake()->numberBetween(1, 20),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}
