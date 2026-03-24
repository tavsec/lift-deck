<?php

use App\Models\ClientProgram;
use App\Models\ClientProgramExerciseTarget;
use App\Models\Exercise;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WorkoutExercise;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);

    $this->program = Program::factory()->create(['coach_id' => $this->coach->id]);
    $this->workout = ProgramWorkout::factory()->create(['program_id' => $this->program->id]);
    $this->exercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);
    $this->workoutExercise = WorkoutExercise::factory()->create([
        'program_workout_id' => $this->workout->id,
        'exercise_id' => $this->exercise->id,
    ]);

    $this->clientProgram = ClientProgram::factory()->create([
        'client_id' => $this->client->id,
        'program_id' => $this->program->id,
    ]);
});

it('coach can view the targets edit page', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.programs.assignments.targets.edit', [$this->program, $this->clientProgram]))
        ->assertOk()
        ->assertSee($this->exercise->name)
        ->assertSee($this->client->name);
});

it('coach can set a target weight for an exercise', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => '80.00'],
        ])
        ->assertRedirect();

    $target = ClientProgramExerciseTarget::where([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
    ])->first();
    expect($target->target_weight)->toEqual('80.00');
});

it('coach can update an existing target weight', function () {
    ClientProgramExerciseTarget::factory()->create([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'target_weight' => 60.00,
    ]);

    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => '90.00'],
        ])
        ->assertRedirect();

    $target = ClientProgramExerciseTarget::where([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
    ])->first();
    expect($target->target_weight)->toEqual('90.00');

    // Only one record should exist
    expect(ClientProgramExerciseTarget::where('client_program_id', $this->clientProgram->id)->count())->toBe(1);
});

it('clears target when an empty value is submitted', function () {
    ClientProgramExerciseTarget::factory()->create([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'target_weight' => 60.00,
    ]);

    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => null],
        ])
        ->assertRedirect();

    expect(ClientProgramExerciseTarget::where('client_program_id', $this->clientProgram->id)->count())->toBe(0);
});

it('another coach cannot view targets for someone elses program', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($otherCoach)
        ->get(route('coach.programs.assignments.targets.edit', [$this->program, $this->clientProgram]))
        ->assertForbidden();
});

it('another coach cannot update targets for someone elses program', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($otherCoach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => '80.00'],
        ])
        ->assertForbidden();
});

it('rejects negative target weight', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => '-5'],
        ])
        ->assertSessionHasErrors('targets.'.$this->workoutExercise->id);
});

it('coach cannot view targets for a client program belonging to a different program', function () {
    $otherProgram = Program::factory()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.programs.assignments.targets.edit', [$otherProgram, $this->clientProgram]))
        ->assertForbidden();
});
