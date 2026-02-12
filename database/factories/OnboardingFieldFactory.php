<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OnboardingField>
 */
class OnboardingFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'coach_id' => User::factory()->coach(),
            'label' => fake()->sentence(4),
            'type' => fake()->randomElement(['text', 'select', 'textarea']),
            'options' => null,
            'is_required' => true,
            'order' => fake()->numberBetween(1, 10),
        ];
    }

    public function select(array $options = ['Option A', 'Option B', 'Option C']): static
    {
        return $this->state(fn () => [
            'type' => 'select',
            'options' => $options,
        ]);
    }

    public function optional(): static
    {
        return $this->state(fn () => [
            'is_required' => false,
        ]);
    }
}
