<?php

use App\Models\User;

it('redirects basic plan coach from loyalty route', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic_loyalty',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $client = User::factory()->state(['role' => 'client'])->create(['coach_id' => $coach->id]);

    $this->actingAs($coach)
        ->get(route('coach.clients.loyalty', $client))
        ->assertRedirect(route('coach.subscription'));
});

it('allows advanced plan coach to access loyalty route', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_advanced_loyalty',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.advanced.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $client = User::factory()->state(['role' => 'client'])->create(['coach_id' => $coach->id]);

    $this->actingAs($coach)
        ->get(route('coach.clients.loyalty', $client))
        ->assertOk();
});

it('redirects basic plan coach from branding route', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic_brand',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.branding.edit'))
        ->assertRedirect(route('coach.subscription'));
});

it('redirects advanced plan coach from branding route', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_advanced_brand',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.advanced.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.branding.edit'))
        ->assertRedirect(route('coach.subscription'));
});

it('allows professional plan coach to access branding route', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_prof_brand',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.professional.stripe_price_flat_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.branding.edit'))
        ->assertOk();
});

it('allows free access coach to access all gated routes', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $client = User::factory()->state(['role' => 'client'])->create(['coach_id' => $coach->id]);

    $this->actingAs($coach)
        ->get(route('coach.clients.loyalty', $client))
        ->assertOk();

    $this->actingAs($coach)
        ->get(route('coach.branding.edit'))
        ->assertOk();
});
