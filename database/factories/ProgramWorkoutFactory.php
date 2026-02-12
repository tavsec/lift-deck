<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgramWorkout>
 */
class ProgramWorkoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'name' => fake()->words(2, true),
            'day_number' => fake()->numberBetween(1, 7),
            'notes' => fake()->optional()->sentence(),
            'order' => 0,
        ];
    }
}
