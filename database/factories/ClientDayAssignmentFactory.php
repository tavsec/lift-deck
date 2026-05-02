<?php

namespace Database\Factories;

use App\Models\DayPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientDayAssignment>
 */
class ClientDayAssignmentFactory extends Factory
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
            'coach_id' => User::factory()->state(['role' => 'coach']),
            'day_plan_id' => DayPlan::factory(),
            'date' => now()->format('Y-m-d'),
        ];
    }
}
