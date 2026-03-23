<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * `trial_ends_at` is set to 14 days in the future by default because coach users require
     * an active trial or subscription to access coach routes (via EnsureCoachSubscribed middleware).
     * This default ensures factory-created coaches pass the middleware in tests without needing
     * explicit subscription setup.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'coach',
            'dark_mode' => false,
            'is_free_access' => false,
            'trial_ends_at' => now()->addDays(14),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function coach(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'coach',
        ]);
    }

    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'client',
        ]);
    }

    public function trackOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'client',
            'email' => null,
            'password' => null,
            'is_track_only' => true,
        ]);
    }
}
