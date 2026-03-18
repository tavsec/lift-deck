<?php

use App\Models\MealLog;
use App\Models\User;
use App\Models\WorkoutLog;
use Spatie\Activitylog\Models\Activity;

it('logs activity when a meal log is created', function () {
    $client = User::factory()->state(['role' => 'client'])->create();

    $this->actingAs($client);

    MealLog::create([
        'client_id' => $client->id,
        'date' => now()->format('Y-m-d'),
        'meal_type' => 'lunch',
        'name' => 'Chicken',
        'calories' => 400,
        'protein' => 40,
        'carbs' => 20,
        'fat' => 10,
    ]);

    expect(Activity::query()->where('subject_type', MealLog::class)->count())->toBe(1);
});

it('logs activity when a workout log is created', function () {
    $client = User::factory()->state(['role' => 'client'])->create();

    $this->actingAs($client);

    $log = WorkoutLog::create([
        'client_id' => $client->id,
        'custom_name' => 'Test Workout',
        'completed_at' => now(),
    ]);

    expect(Activity::query()->where('subject_type', WorkoutLog::class)->count())->toBe(1);
});

it('logs activity when a meal log is deleted', function () {
    $client = User::factory()->state(['role' => 'client'])->create();
    $mealLog = MealLog::factory()->for($client, 'client')->create();

    Activity::query()->delete();

    $this->actingAs($client);
    $mealLog->delete();

    expect(Activity::query()->where('event', 'deleted')->count())->toBe(1);
});
