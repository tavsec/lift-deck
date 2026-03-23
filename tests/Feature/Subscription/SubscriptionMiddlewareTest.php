<?php

use App\Models\User;

it('redirects to subscription page when coach has no subscription and trial expired', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertRedirect(route('coach.subscription'));
});

it('allows access during active trial', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(3),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertOk();
});

it('allows access for free access coaches', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertOk();
});

it('allows access during grace period', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_grace_test',
        'stripe_status' => 'canceled',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertOk();
});

it('redirects to subscription page when grace period elapsed', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDays(10),
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_elapsed',
        'stripe_status' => 'canceled',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => now()->subDay(),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertRedirect(route('coach.subscription'));
});

it('flashes grace period days to session during grace period', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_grace_flash',
        'stripe_status' => 'canceled',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertSessionHas('subscription_grace_days');
});
