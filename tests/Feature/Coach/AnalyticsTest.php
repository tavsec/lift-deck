<?php

use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('shows the analytics page', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertViewIs('coach.clients.analytics');
});

it('prevents viewing another coachs client analytics', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $otherClient = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $otherClient))
        ->assertForbidden();
});

it('accepts range query parameters', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', [$this->client, 'range' => '30']))
        ->assertOk();
});

it('accepts custom date range parameters', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', [
            $this->client,
            'range' => 'custom',
            'from' => '2026-01-01',
            'to' => '2026-01-31',
        ]))
        ->assertOk();
});

it('displays daily check-in chart data for numeric metrics', function () {
    $metric = \App\Models\TrackingMetric::factory()->number('kg')->create([
        'coach_id' => $this->coach->id,
        'name' => 'Body Weight',
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

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('Body Weight');
});

it('displays boolean and text metrics in a table', function () {
    $boolMetric = \App\Models\TrackingMetric::factory()->boolean()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Took Supplements',
    ]);

    \App\Models\ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $boolMetric->id,
    ]);

    \App\Models\DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $boolMetric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '1',
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('Took Supplements');
});
