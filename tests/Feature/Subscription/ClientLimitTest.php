<?php

use App\Models\User;

it('blocks invitation when basic plan client limit is reached', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic_limit',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    // Create 5 clients (the basic plan limit)
    User::factory()->count(5)->state(['role' => 'client'])->create(['coach_id' => $coach->id]);

    $this->actingAs($coach)
        ->post(route('coach.clients.store'))
        ->assertRedirect(route('coach.clients.index'))
        ->assertSessionHas('error');
});

it('allows invitation when below basic plan limit', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic_under',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    // Only 2 clients — under the limit of 5
    User::factory()->count(2)->state(['role' => 'client'])->create(['coach_id' => $coach->id]);

    $this->actingAs($coach)
        ->post(route('coach.clients.store'))
        ->assertRedirect(route('coach.clients.index'))
        ->assertSessionHas('invitation_code');
});

it('blocks track-only client creation when limit reached', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic_track',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    User::factory()->count(5)->state(['role' => 'client'])->create(['coach_id' => $coach->id]);

    $this->actingAs($coach)
        ->post(route('coach.clients.store-track-only'), [
            'name' => 'New Track Client',
            'email' => 'track@example.com',
        ])
        ->assertRedirect(route('coach.clients.index'))
        ->assertSessionHas('error');

    expect($coach->clients()->count())->toBe(5);
});

it('allows unlimited clients for professional plan', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_prof',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.professional.stripe_price_flat_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    // 30 clients — would be blocked on basic/advanced but not professional
    User::factory()->count(30)->state(['role' => 'client'])->create(['coach_id' => $coach->id]);

    $this->actingAs($coach)
        ->post(route('coach.clients.store'))
        ->assertRedirect(route('coach.clients.index'))
        ->assertSessionHas('invitation_code');
});
