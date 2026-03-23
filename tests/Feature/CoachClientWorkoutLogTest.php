<?php

use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutLog;

it('coach can log a workout on behalf of a client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $exercise = Exercise::factory()->create(['coach_id' => $coach->id, 'is_active' => true]);

    $this->actingAs($coach)
        ->post(route('coach.clients.workout-logs.store', $client), [
            'custom_name' => 'Test Workout',
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'exercises' => [
                [
                    'workout_exercise_id' => null,
                    'exercise_id' => $exercise->id,
                    'sets' => [
                        ['weight' => 100, 'reps' => 10],
                    ],
                ],
            ],
        ])
        ->assertRedirect(route('coach.clients.show', $client));

    $this->assertDatabaseHas('workout_logs', [
        'client_id' => $client->id,
        'custom_name' => 'Test Workout',
    ]);

    $this->assertDatabaseHas('exercise_logs', [
        'exercise_id' => $exercise->id,
        'weight' => 100,
        'reps' => 10,
    ]);
});

it('coach can update a client workout log', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $log = WorkoutLog::factory()->for($client, 'client')->create(['custom_name' => 'Old Name']);

    $this->actingAs($coach)
        ->put(route('coach.clients.workout-logs.update', [$client, $log]), [
            'custom_name' => 'Updated Name',
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'exercises' => [],
        ])
        ->assertRedirect(route('coach.clients.show', $client));

    expect($log->fresh()->custom_name)->toBe('Updated Name');
});

it('coach can delete a client workout log', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $log = WorkoutLog::factory()->for($client, 'client')->create();

    $this->actingAs($coach)
        ->delete(route('coach.clients.workout-logs.destroy', [$client, $log]))
        ->assertRedirect(route('coach.clients.show', $client));

    $this->assertDatabaseMissing('workout_logs', ['id' => $log->id]);
});

it('coach cannot manage workout logs of another coach\'s client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $otherCoach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $otherCoach->id])->create();
    $log = WorkoutLog::factory()->for($client, 'client')->create();

    $this->actingAs($coach)
        ->delete(route('coach.clients.workout-logs.destroy', [$client, $log]))
        ->assertForbidden();
});
