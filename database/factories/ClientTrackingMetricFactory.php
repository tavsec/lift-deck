<?php

namespace Database\Factories;

use App\Models\TrackingMetric;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientTrackingMetric>
 */
class ClientTrackingMetricFactory extends Factory
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
            'order' => fake()->numberBetween(1, 10),
        ];
    }
}
