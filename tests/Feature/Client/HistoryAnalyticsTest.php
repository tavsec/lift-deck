<?php

use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\WorkoutLog;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('passes exercise progression data to history view', function () {
    $exercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);
    $workoutLog = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'completed_at' => now(),
    ]);
    ExerciseLog::factory()->create([
        'workout_log_id' => $workoutLog->id,
        'exercise_id' => $exercise->id,
        'weight' => 100,
        'reps' => 8,
    ]);

    $this->actingAs($this->client)
        ->get(route('client.history'))
        ->assertOk()
        ->assertViewHas('exerciseProgressionData')
        ->assertViewHas('exercisesByMuscleGroup')
        ->assertViewHas('exerciseTargetHistory');
});

it('passes empty exercise data when no workouts logged', function () {
    $this->actingAs($this->client)
        ->get(route('client.history'))
        ->assertOk()
        ->assertViewHas('exerciseProgressionData', [])
        ->assertViewHas('exerciseTargetHistory', []);
});
