<?php

use App\Models\ClientProgram;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WorkoutExercise;

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

it('stores rpe value when submitted with a set', function () {
    $this->actingAs($this->client)
        ->postJson(route('client.log.store'), [
            'program_workout_id' => $this->workout->id,
            'completed_at' => now()->format('Y-m-d\TH:i'),
            'exercises' => [
                [
                    'workout_exercise_id' => $this->workoutExercise->id,
                    'exercise_id' => $this->exercise->id,
                    'sets' => [
                        ['weight' => '100', 'reps' => '10', 'rpe' => 8],
                    ],
                ],
            ],
        ])
        ->assertOk();

    expect(ExerciseLog::latest()->first()->rpe)->toBe(8);
});

it('stores null rpe when rpe is omitted', function () {
    $this->actingAs($this->client)
        ->postJson(route('client.log.store'), [
            'program_workout_id' => $this->workout->id,
            'completed_at' => now()->format('Y-m-d\TH:i'),
            'exercises' => [
                [
                    'workout_exercise_id' => $this->workoutExercise->id,
                    'exercise_id' => $this->exercise->id,
                    'sets' => [
                        ['weight' => '100', 'reps' => '10'],
                    ],
                ],
            ],
        ])
        ->assertOk();

    expect(ExerciseLog::latest()->first()->rpe)->toBeNull();
});
