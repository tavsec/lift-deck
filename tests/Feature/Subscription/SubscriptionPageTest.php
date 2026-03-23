<?php

use App\Models\User;

it('shows subscription page to coach on active trial', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Subscription');
});

it('shows trial expiry info on subscription page', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('trial');
});

it('shows all three plans on subscription page', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Basic')
        ->assertSee('Advanced')
        ->assertSee('Professional');
});

it('shows upgrade prompt for coach with expired trial and no subscription', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Subscribe');
});

it('shows manage subscription for coach with active subscription', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_active',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Manage');
});
