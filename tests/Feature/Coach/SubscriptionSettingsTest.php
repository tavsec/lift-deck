<?php

use App\Models\User;

it('settings page passes subscription data to view for trial coach', function (): void {
    $coach = User::factory()->create([
        'role' => 'coach',
        'trial_ends_at' => now()->addDays(5),
        'is_free_access' => false,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.settings.edit'))
        ->assertOk()
        ->assertViewHas('isOnTrial', true)
        ->assertViewHas('clientCount', 0)
        ->assertViewHas('isInGracePeriod', false);
});

it('settings subscription card shows trial state with manage link', function (): void {
    $coach = User::factory()->create([
        'role' => 'coach',
        'trial_ends_at' => now()->addDays(5),
        'is_free_access' => false,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.settings.edit'))
        ->assertOk()
        ->assertSee('Free trial')
        ->assertSee('Manage on Stripe');
});

it('settings subscription card shows active plan for free access coach', function (): void {
    $coach = User::factory()->create([
        'role' => 'coach',
        'trial_ends_at' => null,
        'is_free_access' => true,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.settings.edit'))
        ->assertOk()
        ->assertSee('Professional')
        ->assertSee('Manage on Stripe');
});

it('settings subscription card shows grace period state', function (): void {
    $coach = User::factory()->create([
        'role' => 'coach',
        'trial_ends_at' => null,
        'is_free_access' => false,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test_grace',
        'stripe_status' => 'canceled',
        'stripe_price' => 'price_test',
        'quantity' => 1,
        'ends_at' => now()->addDays(3),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.settings.edit'))
        ->assertOk()
        ->assertSee('Your subscription has ended')
        ->assertSee('Manage on Stripe');
});

it('settings subscription card shows no subscription state', function (): void {
    $coach = User::factory()->create([
        'role' => 'coach',
        'trial_ends_at' => null,
        'is_free_access' => false,
    ]);

    $this->actingAs($coach)
        ->withoutMiddleware(\App\Http\Middleware\EnsureCoachSubscribed::class)
        ->get(route('coach.settings.edit'))
        ->assertOk()
        ->assertSee('No active subscription')
        ->assertSee('Choose a plan');
});
