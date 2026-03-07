<?php

use App\Features\Loyalty;
use App\Models\User;
use Laravel\Pennant\Feature;

test('loyalty feature defaults to inactive for new coaches', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);

    expect(Feature::for($coach)->active(Loyalty::class))->toBeFalse();
});

test('coach with loyalty off gets 403 on loyalty routes', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($coach)
        ->get(route('coach.rewards.index'))
        ->assertForbidden();
});

test('coach with loyalty on can access loyalty routes', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);
    Feature::for($coach)->activate(Loyalty::class);

    $this->actingAs($coach)
        ->get(route('coach.rewards.index'))
        ->assertOk();
});

test('client with coach loyalty off gets 403 on loyalty routes', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $this->actingAs($client)
        ->get(route('client.rewards'))
        ->assertForbidden();
});

test('client with coach loyalty on can access loyalty routes', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);
    Feature::for($coach)->activate(Loyalty::class);

    $this->actingAs($client)
        ->get(route('client.rewards'))
        ->assertOk();
});
