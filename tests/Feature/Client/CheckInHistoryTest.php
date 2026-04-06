<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('shows the check-in history page', function () {
    $this->actingAs($this->client)
        ->get(route('client.check-in.history'))
        ->assertOk()
        ->assertViewIs('client.check-in-history');
});

it('passes check-in chart data to the view', function () {
    $metric = TrackingMetric::factory()->number()->create(['coach_id' => $this->coach->id]);
    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
    ]);
    DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '80.0',
    ]);

    $this->actingAs($this->client)
        ->get(route('client.check-in.history'))
        ->assertOk()
        ->assertViewHas('checkInCharts')
        ->assertViewHas('tableMetrics');
});

it('accepts range query parameter', function () {
    $this->actingAs($this->client)
        ->get(route('client.check-in.history', ['range' => '7']))
        ->assertOk();
});

it('cannot be accessed by coaches', function () {
    $this->actingAs($this->coach)
        ->get(route('client.check-in.history'))
        ->assertRedirect();
});
