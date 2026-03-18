<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;

it('coach can submit a check-in for a client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $metric = TrackingMetric::factory()->create(['coach_id' => $coach->id, 'type' => 'number']);
    ClientTrackingMetric::factory()->create(['client_id' => $client->id, 'tracking_metric_id' => $metric->id]);

    $this->actingAs($coach)
        ->post(route('coach.clients.check-in.store', $client), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [$metric->id => '75'],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('daily_logs', [
        'client_id' => $client->id,
        'tracking_metric_id' => $metric->id,
        'value' => '75',
    ]);
});

it('coach can update an existing check-in entry', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $metric = TrackingMetric::factory()->create(['coach_id' => $coach->id, 'type' => 'number']);
    ClientTrackingMetric::factory()->create(['client_id' => $client->id, 'tracking_metric_id' => $metric->id]);

    DailyLog::factory()->create([
        'client_id' => $client->id,
        'tracking_metric_id' => $metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '70',
    ]);

    $this->actingAs($coach)
        ->post(route('coach.clients.check-in.store', $client), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [$metric->id => '80'],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('daily_logs', [
        'client_id' => $client->id,
        'tracking_metric_id' => $metric->id,
        'value' => '80',
    ]);
});

it('coach cannot check in for another coach\'s client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $otherCoach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $otherCoach->id])->create();

    $this->actingAs($coach)
        ->post(route('coach.clients.check-in.store', $client), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
        ])
        ->assertForbidden();
});
