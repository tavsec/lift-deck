<?php

use App\Models\MealLog;
use App\Models\User;

it('coach can log a meal on behalf of a client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();

    $this->actingAs($coach)
        ->post(route('coach.clients.meal-logs.store', $client), [
            'date' => now()->format('Y-m-d'),
            'meal_type' => 'lunch',
            'name' => 'Grilled Chicken',
            'calories' => 350,
            'protein' => 40,
            'carbs' => 10,
            'fat' => 8,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('meal_logs', [
        'client_id' => $client->id,
        'name' => 'Grilled Chicken',
    ]);
});

it('coach can delete a client meal log', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $mealLog = MealLog::factory()->for($client, 'client')->create();

    $this->actingAs($coach)
        ->delete(route('coach.clients.meal-logs.destroy', [$client, $mealLog]))
        ->assertRedirect();

    $this->assertDatabaseMissing('meal_logs', ['id' => $mealLog->id]);
});

it('coach cannot manage meal logs of another coach\'s client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $otherCoach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $otherCoach->id])->create();
    $mealLog = MealLog::factory()->for($client, 'client')->create();

    $this->actingAs($coach)
        ->delete(route('coach.clients.meal-logs.destroy', [$client, $mealLog]))
        ->assertForbidden();
});
