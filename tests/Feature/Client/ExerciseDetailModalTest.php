<?php

use App\Models\ClientProgram;
use App\Models\Exercise;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WorkoutExercise;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);

    $this->exercise = Exercise::factory()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Barbell Squat',
        'description' => 'Keep your back straight and core tight.',
        'muscle_group' => 'quads',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ]);

    $program = Program::factory()->create(['coach_id' => $this->coach->id]);
    $this->workout = ProgramWorkout::factory()->create([
        'program_id' => $program->id,
        'name' => 'Day 1',
        'day_number' => 1,
    ]);
    WorkoutExercise::factory()->create([
        'program_workout_id' => $this->workout->id,
        'exercise_id' => $this->exercise->id,
        'sets' => 4,
        'reps' => 8,
    ]);

    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    ClientProgram::factory()->create([
        'client_id' => $this->client->id,
        'program_id' => $program->id,
        'started_at' => now(),
    ]);
});

it('renders exercise description and video embed url in the program page', function () {
    $response = $this->actingAs($this->client)->get(route('client.program'));

    $response->assertOk();
    $response->assertSee('Barbell Squat');
    $response->assertSee('Keep your back straight and core tight.');
    $response->assertSee('youtube.com/embed/dQw4w9WgXcQ');
});

it('shows a placeholder when exercise has no description or video', function () {
    $noDetailExercise = Exercise::factory()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Plank',
        'description' => null,
        'muscle_group' => 'core',
        'video_url' => null,
    ]);
    WorkoutExercise::factory()->create([
        'program_workout_id' => $this->workout->id,
        'exercise_id' => $noDetailExercise->id,
        'sets' => 3,
        'reps' => 60,
    ]);

    $response = $this->actingAs($this->client)->get(route('client.program'));

    $response->assertOk();
    $response->assertSee('Plank');
    $response->assertSee('No description provided');
    $response->assertSee('No video available');
});
