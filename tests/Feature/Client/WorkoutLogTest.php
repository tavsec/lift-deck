<?php

use App\Models\ClientProgram;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WorkoutExercise;
use App\Models\WorkoutLog;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);

    $this->program = Program::factory()->create(['coach_id' => $this->coach->id]);
    $this->clientProgram = ClientProgram::factory()->create([
        'client_id' => $this->client->id,
        'program_id' => $this->program->id,
        'status' => 'active',
    ]);

    $this->workout = ProgramWorkout::factory()->create(['program_id' => $this->program->id]);

    $this->exercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);
    $this->workoutExercise = WorkoutExercise::factory()->create([
        'program_workout_id' => $this->workout->id,
        'exercise_id' => $this->exercise->id,
        'sets' => 3,
        'reps' => '10',
        'order' => 0,
    ]);
});

it('shows the log form for a program workout', function () {
    $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout))
        ->assertOk()
        ->assertSee($this->workout->name);
});

it('includes previous set data from the last log of the same workout', function () {
    $previousLog = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'client_program_id' => $this->clientProgram->id,
        'program_workout_id' => $this->workout->id,
        'completed_at' => now()->subDay(),
    ]);

    ExerciseLog::factory()->create([
        'workout_log_id' => $previousLog->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 1,
        'weight' => 80.00,
        'reps' => 10,
    ]);
    ExerciseLog::factory()->create([
        'workout_log_id' => $previousLog->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 2,
        'weight' => 85.00,
        'reps' => 8,
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout));

    $response->assertOk();

    // The previous_sets data is embedded in the Alpine.js JSON
    $response->assertSee('80');
    $response->assertSee('85');
});

it('falls back to any previous log when exercise was not in the last workout log', function () {
    // Create a log for a DIFFERENT workout that has this exercise
    $otherWorkout = ProgramWorkout::factory()->create(['program_id' => $this->program->id]);
    $otherLog = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'client_program_id' => $this->clientProgram->id,
        'program_workout_id' => $otherWorkout->id,
        'completed_at' => now()->subDays(2),
    ]);

    ExerciseLog::factory()->create([
        'workout_log_id' => $otherLog->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 1,
        'weight' => 70.00,
        'reps' => 12,
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout));

    $response->assertOk();
    $response->assertSee('70');
});

it('returns empty previous_sets when there is no history', function () {
    $response = $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout));

    $response->assertOk();
    // No previous data markers should appear
    $response->assertDontSee('Last session');
});

it('returns exercises with previous set data from the JSON endpoint', function () {
    $log = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'client_program_id' => $this->clientProgram->id,
        'program_workout_id' => $this->workout->id,
        'completed_at' => now()->subDay(),
    ]);

    ExerciseLog::factory()->create([
        'workout_log_id' => $log->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 1,
        'weight' => 60.00,
        'reps' => 12,
    ]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.log.exercises'));

    $response->assertOk();

    $exerciseData = collect($response->json())->firstWhere('id', $this->exercise->id);
    expect($exerciseData)->not->toBeNull();
    expect($exerciseData['previous_sets'])->toHaveCount(1);
    expect($exerciseData['previous_sets'][0]['weight'])->toBe('60.00');
    expect($exerciseData['previous_sets'][0]['reps'])->toBe(12);
});

it('returns empty previous_sets for exercises with no history', function () {
    $newExercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.log.exercises'));

    $response->assertOk();

    $exerciseData = collect($response->json())->firstWhere('id', $newExercise->id);
    expect($exerciseData)->not->toBeNull();
    expect($exerciseData['previous_sets'])->toBe([]);
});

it('returns previous set data from the most recent log when multiple logs exist', function () {
    // Older log
    $olderLog = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'client_program_id' => $this->clientProgram->id,
        'program_workout_id' => $this->workout->id,
        'completed_at' => now()->subDays(5),
    ]);

    ExerciseLog::factory()->create([
        'workout_log_id' => $olderLog->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 1,
        'weight' => 50.00,
        'reps' => 15,
    ]);

    // Newer log (should be returned)
    $newerLog = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'client_program_id' => $this->clientProgram->id,
        'program_workout_id' => $this->workout->id,
        'completed_at' => now()->subDay(),
    ]);

    ExerciseLog::factory()->create([
        'workout_log_id' => $newerLog->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 1,
        'weight' => 75.00,
        'reps' => 8,
    ]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.log.exercises'));

    $response->assertOk();

    $exerciseData = collect($response->json())->firstWhere('id', $this->exercise->id);
    expect($exerciseData['previous_sets'])->toHaveCount(1);
    expect($exerciseData['previous_sets'][0]['weight'])->toBe('75.00');
    expect($exerciseData['previous_sets'][0]['reps'])->toBe(8);
});
