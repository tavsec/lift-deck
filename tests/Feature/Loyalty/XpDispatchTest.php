<?php

use App\Jobs\ProcessXpEvent;
use App\Models\ClientProgram;
use App\Models\ClientTrackingMetric;
use App\Models\Exercise;
use App\Models\Meal;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\TrackingMetric;
use App\Models\User;
use App\Models\WorkoutExercise;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake([ProcessXpEvent::class]);
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
});

it('dispatches XP event when workout is logged', function () {
    $program = Program::factory()->create(['coach_id' => $this->coach->id]);
    $workout = ProgramWorkout::factory()->create(['program_id' => $program->id]);
    $workoutExercise = WorkoutExercise::factory()->create([
        'program_workout_id' => $workout->id,
        'exercise_id' => Exercise::factory()->create(['coach_id' => $this->coach->id])->id,
    ]);

    ClientProgram::factory()->create([
        'client_id' => $this->client->id,
        'program_id' => $program->id,
        'status' => 'active',
    ]);

    $this->actingAs($this->client)
        ->post(route('client.log.store'), [
            'program_workout_id' => $workout->id,
            'exercises' => [
                [
                    'workout_exercise_id' => $workoutExercise->id,
                    'exercise_id' => $workoutExercise->exercise_id,
                    'sets' => [
                        ['weight' => 100, 'reps' => 10],
                    ],
                ],
            ],
        ]);

    Queue::assertPushed(ProcessXpEvent::class, function ($job) {
        return $job->eventKey === 'workout_logged' && $job->userId === $this->client->id;
    });
});

it('dispatches XP events when daily check-in is submitted', function () {
    $metric = TrackingMetric::factory()->number()->create(['coach_id' => $this->coach->id]);
    ClientTrackingMetric::create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
        'order' => 1,
    ]);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->toDateString(),
            'metrics' => [
                $metric->id => '75',
            ],
        ]);

    Queue::assertPushed(ProcessXpEvent::class, fn ($job) => $job->eventKey === 'daily_checkin');
    Queue::assertPushed(ProcessXpEvent::class, fn ($job) => $job->eventKey === 'streak_7_day');
    Queue::assertPushed(ProcessXpEvent::class, fn ($job) => $job->eventKey === 'streak_30_day');
});

it('dispatches XP event when meal is logged', function () {
    $meal = Meal::factory()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->client)
        ->post(route('client.nutrition.store'), [
            'meal_id' => $meal->id,
            'date' => now()->toDateString(),
            'meal_type' => 'lunch',
            'name' => $meal->name,
            'calories' => $meal->calories,
            'protein' => $meal->protein,
            'carbs' => $meal->carbs,
            'fat' => $meal->fat,
        ]);

    Queue::assertPushed(ProcessXpEvent::class, function ($job) {
        return $job->eventKey === 'meal_logged' && $job->userId === $this->client->id;
    });
});
