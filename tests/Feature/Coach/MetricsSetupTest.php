<?php

use App\Models\TrackingMetric;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create(['metrics_onboarded_at' => null]);
});

test('dashboard shows metrics setup popup when metrics_onboarded_at is null', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.metrics_setup.title'));
});

test('dashboard does not show popup when metrics_onboarded_at is set', function () {
    $this->coach->update(['metrics_onboarded_at' => now()]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.metrics_setup.title'));
});

test('choosing yes seeds 6 default metrics and sets metrics_onboarded_at', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('coach.dashboard'));

    $this->coach->refresh();

    expect($this->coach->metrics_onboarded_at)->not->toBeNull();
    expect(TrackingMetric::where('coach_id', $this->coach->id)->count())->toBe(6);
});

test('choosing skip sets metrics_onboarded_at without creating metrics', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.metrics-setup'), ['setup' => '0'])
        ->assertRedirect(route('coach.dashboard'));

    $this->coach->refresh();

    expect($this->coach->metrics_onboarded_at)->not->toBeNull();
    expect(TrackingMetric::where('coach_id', $this->coach->id)->count())->toBe(0);
});

test('yes path seeds metrics with localized names for hr locale', function () {
    $this->coach->update(['locale' => 'hr']);

    $this->actingAs($this->coach)
        ->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('coach.dashboard'));

    expect(TrackingMetric::where('coach_id', $this->coach->id)
        ->where('name', __('coach.default_metrics.weight', locale: 'hr'))
        ->exists()
    )->toBeTrue();
});

test('yes path seeds metrics with localized names for sl locale', function () {
    $this->coach->update(['locale' => 'sl']);

    $this->actingAs($this->coach)
        ->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('coach.dashboard'));

    expect(TrackingMetric::where('coach_id', $this->coach->id)
        ->where('name', __('coach.default_metrics.weight', locale: 'sl'))
        ->exists()
    )->toBeTrue();
});

test('setup route requires authentication', function () {
    $this->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('login'));
});

test('client cannot access coach metrics setup route', function () {
    $client = User::factory()->client()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($client)
        ->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('client.dashboard'));
});
