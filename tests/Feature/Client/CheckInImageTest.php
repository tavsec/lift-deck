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
    $this->imageMetric = TrackingMetric::factory()->image()->create(['coach_id' => $this->coach->id]);
    $this->numberMetric = TrackingMetric::factory()->number()->create(['coach_id' => $this->coach->id]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->imageMetric->id,
    ]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->numberMetric->id,
    ]);
});

it('displays image upload field for image metrics', function () {
    $this->actingAs($this->client)
        ->get(route('client.check-in'))
        ->assertOk()
        ->assertSee($this->imageMetric->name)
        ->assertSee('type="file"', false);
});

it('can upload an image for an image metric', function () {
    $file = UploadedFile::fake()->image('progress.jpg', 800, 600);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [
                $this->numberMetric->id => '82.5',
            ],
            'images' => [
                $this->imageMetric->id => $file,
            ],
        ])
        ->assertRedirect(route('client.check-in', ['date' => now()->format('Y-m-d')]));

    $log = DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->imageMetric->id)
        ->first();

    expect($log)->not->toBeNull();
    expect($log->value)->toBe('uploaded');
    expect($log->getFirstMedia('check-in-image'))->not->toBeNull();
});

it('can replace an existing image', function () {
    $log = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->imageMetric->id,
        'date' => now()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);
    $log->addMedia(UploadedFile::fake()->image('old.jpg'))
        ->toMediaCollection('check-in-image');

    $newFile = UploadedFile::fake()->image('new.jpg', 1200, 900);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
            'images' => [
                $this->imageMetric->id => $newFile,
            ],
        ])
        ->assertRedirect();

    $log->refresh();
    $media = $log->getFirstMedia('check-in-image');
    expect($media)->not->toBeNull();
    expect($media->file_name)->toContain('new');
});

it('can remove an image by sending remove flag', function () {
    $log = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->imageMetric->id,
        'date' => now()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);
    $log->addMedia(UploadedFile::fake()->image('photo.jpg'))
        ->toMediaCollection('check-in-image');

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
            'remove_images' => [
                $this->imageMetric->id => '1',
            ],
        ])
        ->assertRedirect();

    expect(DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->imageMetric->id)
        ->whereDate('date', now())
        ->first()
    )->toBeNull();
});

it('rejects non-image files', function () {
    $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
            'images' => [
                $this->imageMetric->id => $file,
            ],
        ])
        ->assertSessionHasErrors('images.'.$this->imageMetric->id);
});

it('rejects files over 10MB', function () {
    $file = UploadedFile::fake()->image('huge.jpg')->size(11000);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
            'images' => [
                $this->imageMetric->id => $file,
            ],
        ])
        ->assertSessionHasErrors('images.'.$this->imageMetric->id);
});

it('ignores image uploads for non-image metric types', function () {
    $file = UploadedFile::fake()->image('trick.jpg');

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [
                $this->numberMetric->id => '80',
            ],
            'images' => [
                $this->numberMetric->id => $file,
            ],
        ])
        ->assertRedirect();

    $log = DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->numberMetric->id)
        ->first();

    expect($log->getMedia('check-in-image'))->toHaveCount(0);
});
