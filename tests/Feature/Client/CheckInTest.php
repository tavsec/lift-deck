<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $this->metric = TrackingMetric::factory()->number()->create(['coach_id' => $this->coach->id]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->metric->id,
    ]);
});

it('can save a new check-in value', function () {
    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [
                $this->metric->id => '75.5',
            ],
        ])
        ->assertRedirect();

    $log = DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->metric->id)
        ->first();

    expect($log)->not->toBeNull();
    expect($log->value)->toBe('75.5');
});

it('can update an existing check-in value', function () {
    DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '75.5',
    ]);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [
                $this->metric->id => '76.0',
            ],
        ])
        ->assertRedirect();

    $logs = DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->metric->id)
        ->whereDate('date', now())
        ->get();

    expect($logs)->toHaveCount(1);
    expect($logs->first()->value)->toBe('76.0');
});

it('can save a check-in for a past date and redirects to that date', function () {
    $pastDate = now()->subDays(3)->format('Y-m-d');

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => $pastDate,
            'metrics' => [
                $this->metric->id => '80.0',
            ],
        ])
        ->assertRedirect(route('client.check-in', ['date' => $pastDate]));

    $log = DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->metric->id)
        ->whereDate('date', $pastDate)
        ->first();

    expect($log)->not->toBeNull();
    expect($log->value)->toBe('80.0');

    // Verify the index page loads the saved value
    $this->actingAs($this->client)
        ->get(route('client.check-in', ['date' => $pastDate]))
        ->assertOk()
        ->assertSee('80.0');
});

it('can clear an existing check-in value', function () {
    DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '75.5',
    ]);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [
                $this->metric->id => '',
            ],
        ])
        ->assertRedirect();

    expect(DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->metric->id)
        ->whereDate('date', now())
        ->first()
    )->toBeNull();
});
