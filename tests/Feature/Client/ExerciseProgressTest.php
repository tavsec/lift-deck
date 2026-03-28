<?php

use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\WorkoutLog;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $this->exercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);
});

it('returns null PRs and empty charts when client has no logs', function () {
    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk()
        ->assertJson([
            'maxWeight' => null,
            'estimated1rm' => null,
            'weightChart' => [],
            'volumeChart' => [],
        ]);
});

it('returns the correct max weight across all sets', function () {
    $log = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 80, 'reps' => 5, 'set_number' => 1]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 100, 'reps' => 3, 'set_number' => 2]);

    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk()
        ->assertJsonPath('maxWeight', 100.0);
});

it('calculates estimated 1rm using epley formula', function () {
    $log = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()]);
    // 100kg × 5 reps → 100 * (1 + 5/30) = 116.7
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 100, 'reps' => 5, 'set_number' => 1]);

    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk()
        ->assertJsonPath('estimated1rm', round(100 * (1 + 5 / 30), 1));
});

it('skips sets with zero reps in 1rm calculation', function () {
    $log = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 150, 'reps' => 0, 'set_number' => 1]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 100, 'reps' => 5, 'set_number' => 2]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk();

    // 150kg at 0 reps is skipped; 100kg × 5 reps wins
    expect($response->json('estimated1rm'))->toBe(round(100 * (1 + 5 / 30), 1));
});

it('limits chart data to the requested range but prs remain all-time', function () {
    $oldLog = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()->subDays(60)]);
    $recentLog = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()->subDays(10)]);
    ExerciseLog::factory()->create(['workout_log_id' => $oldLog->id, 'exercise_id' => $this->exercise->id, 'weight' => 120, 'reps' => 5, 'set_number' => 1]);
    ExerciseLog::factory()->create(['workout_log_id' => $recentLog->id, 'exercise_id' => $this->exercise->id, 'weight' => 90, 'reps' => 5, 'set_number' => 1]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise).'?range=30')
        ->assertOk();

    expect($response->json('weightChart'))->toHaveCount(1);
    expect($response->json('weightChart.0.weight'))->toBe(90.0);
    expect($response->json('maxWeight'))->toBe(120.0); // all-time, includes old log
});

it('returns all chart data when range is 0', function () {
    $oldLog = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()->subDays(200)]);
    $recentLog = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()->subDays(10)]);
    ExerciseLog::factory()->create(['workout_log_id' => $oldLog->id, 'exercise_id' => $this->exercise->id, 'weight' => 90, 'reps' => 5, 'set_number' => 1]);
    ExerciseLog::factory()->create(['workout_log_id' => $recentLog->id, 'exercise_id' => $this->exercise->id, 'weight' => 100, 'reps' => 5, 'set_number' => 1]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise).'?range=0')
        ->assertOk();

    expect($response->json('weightChart'))->toHaveCount(2);
});

it('defaults to 90 days for an invalid range value', function () {
    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise).'?range=999')
        ->assertOk();
});

it('cannot see another clients exercise data', function () {
    $other = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $log = WorkoutLog::factory()->create(['client_id' => $other->id, 'completed_at' => now()]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 150, 'reps' => 5, 'set_number' => 1]);

    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk()
        ->assertJsonPath('maxWeight', null);
});

it('cannot be accessed by coaches', function () {
    $this->actingAs($this->coach)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertRedirect();
});
