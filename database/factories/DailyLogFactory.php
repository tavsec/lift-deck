<?php

namespace Database\Factories;

use App\Models\TrackingMetric;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyLog>
 */
class DailyLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => User::factory()->state(['role' => 'client']),
            'tracking_metric_id' => TrackingMetric::factory(),
            'date' => fake()->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
            'value' => (string) fake()->randomFloat(1, 50, 100),
        ];
    }
}
