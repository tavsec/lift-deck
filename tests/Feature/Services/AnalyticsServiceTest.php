<?php

use App\Models\MacroGoal;
use App\Models\MealLog;
use App\Models\User;
use App\Services\AnalyticsService;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $this->service = new AnalyticsService;
});

it('returns nutrition data for date range', function () {
    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->format('Y-m-d'),
        'calories' => 2000,
        'protein' => 150,
        'carbs' => 200,
        'fat' => 70,
    ]);

    $result = $this->service->getNutritionData(
        $this->client,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result)->toHaveKeys(['nutritionData', 'nutritionStats']);
    expect($result['nutritionStats']['daysLogged'])->toBe(1);
    expect($result['nutritionStats']['avgCalories'])->toBe(2000);
});

it('returns zero stats when no meals logged', function () {
    $result = $this->service->getNutritionData(
        $this->client,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result['nutritionStats']['daysLogged'])->toBe(0);
    expect($result['nutritionStats']['avgCalories'])->toBe(0);
});

it('returns adherence rate of 100 when calories are within 10% of goal', function () {
    $today = now()->format('Y-m-d');

    MacroGoal::factory()->create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'calories' => 2000,
        'effective_date' => $today,
    ]);

    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => $today,
        'calories' => 2000,
    ]);

    $result = $this->service->getNutritionData($this->client, $today, $today);

    expect($result['nutritionStats']['adherenceRate'])->toBe(100);
});

it('returns adherence rate of 0 when calories are outside 10% of goal', function () {
    $today = now()->format('Y-m-d');

    MacroGoal::factory()->create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'calories' => 2000,
        'effective_date' => $today,
    ]);

    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => $today,
        'calories' => 500,
    ]);

    $result = $this->service->getNutritionData($this->client, $today, $today);

    expect($result['nutritionStats']['adherenceRate'])->toBe(0);
});

it('returns null adherence rate when no macro goal is set', function () {
    $today = now()->format('Y-m-d');

    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => $today,
        'calories' => 2000,
    ]);

    $result = $this->service->getNutritionData($this->client, $today, $today);

    expect($result['nutritionStats']['adherenceRate'])->toBeNull();
});

it('returns check-in chart data for numeric metrics', function () {
    $metric = \App\Models\TrackingMetric::factory()->number()->create([
        'coach_id' => $this->coach->id,
    ]);
    \App\Models\ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
    ]);
    \App\Models\DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '82.5',
    ]);

    $result = $this->service->getCheckInChartData(
        $this->client,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result)->toHaveKeys(['checkInCharts', 'tableMetrics', 'checkInTableData', 'imageMetrics', 'imageMetricData']);
    expect($result['checkInCharts'])->toHaveCount(1);
    expect($result['checkInCharts'][0]['data'][0]['value'])->toBe(82.5);
});

it('returns empty check-in data when no metrics assigned', function () {
    $result = $this->service->getCheckInChartData(
        $this->client,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result['checkInCharts'])->toBeEmpty();
    expect($result['tableMetrics']->count())->toBe(0);
});

it('returns check-in table data for boolean metrics', function () {
    $metric = \App\Models\TrackingMetric::factory()->create([
        'coach_id' => $this->coach->id,
        'type' => 'boolean',
    ]);
    \App\Models\ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
    ]);
    \App\Models\DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '1',
    ]);

    $result = $this->service->getCheckInChartData(
        $this->client,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result['tableMetrics']->count())->toBe(1);
    expect($result['checkInTableData'])->not->toBeEmpty();
});

it('returns empty check-in data when client has no coach', function () {
    $clientWithNoCoach = \App\Models\User::factory()->create([
        'role' => 'client',
        'coach_id' => null,
    ]);

    $result = $this->service->getCheckInChartData(
        $clientWithNoCoach,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result['checkInCharts'])->toBeEmpty();
    expect($result['tableMetrics']->count())->toBe(0);
});
