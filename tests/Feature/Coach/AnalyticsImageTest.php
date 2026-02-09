<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');

    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('displays progress photos section when image metrics exist', function () {
    $metric = TrackingMetric::factory()->image()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Front Progress Photo',
    ]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
    ]);

    $log = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);

    $log->addMedia(UploadedFile::fake()->image('front.jpg', 800, 600))
        ->toMediaCollection('check-in-image');

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('Progress Photos')
        ->assertSee('Front Progress Photo');
});

it('shows empty state when no progress photos exist', function () {
    $metric = TrackingMetric::factory()->image()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Body Photo',
    ]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('No progress photos');
});

it('does not show progress photos section when no image metrics assigned', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertDontSee('Progress Photos');
});
