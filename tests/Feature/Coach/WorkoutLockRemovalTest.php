<?php

use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->program = Program::factory()->create(['coach_id' => $this->coach->id]);
    $this->workout = ProgramWorkout::factory()->create([
        'program_id' => $this->program->id,
        'lock_exercise_removal' => false,
    ]);
});

it('coach can lock exercise removal on a workout', function () {
    $this->actingAs($this->coach)
        ->patch(route('coach.programs.workouts.toggle-lock-removal', [$this->program, $this->workout]), [
            'lock_exercise_removal' => true,
        ])
        ->assertRedirect();

    expect($this->workout->fresh()->lock_exercise_removal)->toBeTrue();
});

it('coach can unlock exercise removal on a workout', function () {
    $this->workout->update(['lock_exercise_removal' => true]);

    $this->actingAs($this->coach)
        ->patch(route('coach.programs.workouts.toggle-lock-removal', [$this->program, $this->workout]), [
            'lock_exercise_removal' => false,
        ])
        ->assertRedirect();

    expect($this->workout->fresh()->lock_exercise_removal)->toBeFalse();
});

it('another coach cannot toggle lock on someone elses workout', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($otherCoach)
        ->patch(route('coach.programs.workouts.toggle-lock-removal', [$this->program, $this->workout]), [
            'lock_exercise_removal' => true,
        ])
        ->assertForbidden();
});
