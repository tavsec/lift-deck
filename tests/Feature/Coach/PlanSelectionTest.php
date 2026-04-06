<?php

use App\Models\User;

it('renders the plan selection page for a new coach', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.plan'))
        ->assertOk()
        ->assertSee('Basic')
        ->assertSee('Advanced')
        ->assertSee('Professional')
        ->assertSee('Free Trial');
});

it('redirects to dashboard if coach already has active trial', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(5),
        'selected_plan' => 'basic',
    ]);

    $this->actingAs($coach)
        ->get(route('coach.plan'))
        ->assertRedirect(route('coach.dashboard'));
});

it('selecting basic plan sets selected_plan and trial_ends_at and redirects to dashboard', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'basic'])
        ->assertRedirect(route('coach.dashboard'));

    $coach->refresh();
    expect($coach->selected_plan)->toBe('basic');
    expect($coach->trial_ends_at)->not->toBeNull();
    expect($coach->trial_ends_at->isFuture())->toBeTrue();
});

it('selecting advanced plan sets selected_plan and redirects to checkout', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'advanced'])
        ->assertRedirect(route('coach.subscription.checkout'));

    $coach->refresh();
    expect($coach->selected_plan)->toBe('advanced');
    expect($coach->trial_ends_at)->toBeNull();
});

it('selecting professional plan sets selected_plan and redirects to checkout', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'professional'])
        ->assertRedirect(route('coach.subscription.checkout'));

    $coach->refresh();
    expect($coach->selected_plan)->toBe('professional');
});

it('rejects invalid plan values', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'enterprise'])
        ->assertSessionHasErrors('plan');
});

it('redirects to subscription page if coach already has selected_plan but is not active', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => 'advanced',
    ]);

    $this->actingAs($coach)
        ->get(route('coach.plan'))
        ->assertRedirect(route('coach.subscription'));
});

it('success page redirects to dashboard', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => 'advanced',
    ]);

    // Simulate an active subscription (Cashier webhook would have created this)
    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test_success',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.advanced.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.plan.success'))
        ->assertRedirect(route('coach.dashboard'));
});
