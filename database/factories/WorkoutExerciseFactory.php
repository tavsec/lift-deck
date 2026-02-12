<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\ProgramWorkout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutExercise>
 */
class WorkoutExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_workout_id' => ProgramWorkout::factory(),
            'exercise_id' => Exercise::factory(),
            'sets' => fake()->numberBetween(3, 5),
            'reps' => (string) fake()->numberBetween(6, 12),
            'rest_seconds' => fake()->randomElement([60, 90, 120]),
            'notes' => fake()->optional()->sentence(),
            'order' => 0,
        ];
    }
}
