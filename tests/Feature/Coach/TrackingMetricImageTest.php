<?php

use App\Models\TrackingMetric;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
});

it('can create an image type tracking metric', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.tracking-metrics.store'), [
            'name' => 'Front Progress Photo',
            'type' => 'image',
        ])
        ->assertRedirect(route('coach.tracking-metrics.index'));

    $this->assertDatabaseHas('tracking_metrics', [
        'coach_id' => $this->coach->id,
        'name' => 'Front Progress Photo',
        'type' => 'image',
    ]);
});

it('can create a metric using the image factory state', function () {
    $metric = TrackingMetric::factory()->image()->create(['coach_id' => $this->coach->id]);

    expect($metric->type)->toBe('image');
});
