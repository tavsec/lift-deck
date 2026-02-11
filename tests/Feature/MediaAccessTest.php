<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake(config('filesystems.default'));

    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $this->metric = TrackingMetric::factory()->image()->create(['coach_id' => $this->coach->id]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->metric->id,
    ]);

    $this->log = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);

    $this->log->addMedia(UploadedFile::fake()->image('photo.jpg', 800, 600))
        ->toMediaCollection('check-in-image');
});

it('allows the client to view their own image', function () {
    $this->actingAs($this->client)
        ->get(route('media.daily-log', $this->log))
        ->assertRedirect();
});

it('allows the coach to view their clients image', function () {
    $this->actingAs($this->coach)
        ->get(route('media.daily-log', $this->log))
        ->assertRedirect();
});

it('denies access to unrelated users', function () {
    $stranger = User::factory()->create(['role' => 'coach']);

    $this->actingAs($stranger)
        ->get(route('media.daily-log', $this->log))
        ->assertForbidden();
});

it('returns the thumb conversion when requested', function () {
    $this->actingAs($this->client)
        ->get(route('media.daily-log', [$this->log, 'thumb']))
        ->assertRedirect();
});

it('returns 404 for log without media', function () {
    $emptyLog = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->metric->id,
        'date' => now()->subDay()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);

    $this->actingAs($this->client)
        ->get(route('media.daily-log', $emptyLog))
        ->assertNotFound();
});

it('requires authentication', function () {
    $this->get(route('media.daily-log', $this->log))
        ->assertRedirect(route('login'));
});
