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

it('subscription page shows subscribe button with plan name when coach has selected_plan but no subscription', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
        'selected_plan' => 'advanced',
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Subscribe to Advanced');
});

it('subscription page shows choose a plan link when coach has no selected_plan', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Choose a Plan');
});

it('checkout route redirects coach with no selected_plan to plan selection', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription.checkout'))
        ->assertRedirect(route('coach.plan'));
});

it('checkout route redirects already subscribed coach to dashboard', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => 'basic',
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_already_active',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription.checkout'))
        ->assertRedirect(route('coach.dashboard'));
});
