<?php

use App\Models\User;
use App\Services\SubscriptionService;

it('reportClientUsage does nothing for non-professional plan', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic_metered',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    // Should not throw — returns early for non-professional plans
    $service = app(SubscriptionService::class);
    $service->reportClientUsage($coach);

    expect(true)->toBeTrue(); // No exception thrown
});

it('reportClientUsage does nothing when no metered subscription item', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    // Professional plan subscription but no metered item
    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_prof_no_metered',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.professional.stripe_price_flat_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    // No subscription items with the metered price — should return early, no exception
    $service = app(SubscriptionService::class);
    $service->reportClientUsage($coach);

    expect(true)->toBeTrue(); // No exception thrown
});
