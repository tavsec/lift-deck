<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrackingMetric>
 */
class TrackingMetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coach_id' => User::factory()->state(['role' => 'coach']),
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement(['number', 'scale', 'boolean', 'text']),
            'unit' => null,
            'scale_min' => 1,
            'scale_max' => 5,
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    public function number(string $unit = 'kg'): static
    {
        return $this->state(fn () => [
            'type' => 'number',
            'unit' => $unit,
        ]);
    }

    public function scale(int $min = 1, int $max = 5): static
    {
        return $this->state(fn () => [
            'type' => 'scale',
            'scale_min' => $min,
            'scale_max' => $max,
        ]);
    }

    public function boolean(): static
    {
        return $this->state(fn () => [
            'type' => 'boolean',
        ]);
    }

    public function text(): static
    {
        return $this->state(fn () => [
            'type' => 'text',
        ]);
    }

    public function image(): static
    {
        return $this->state(fn () => [
            'type' => 'image',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
