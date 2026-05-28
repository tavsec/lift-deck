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

it('reportClientUsage does nothing when coach has no subscription', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
        'stripe_id' => null,
    ]);

    // No subscription at all — should return early, no exception
    $service = app(SubscriptionService::class);
    $service->reportClientUsage($coach);

    expect(true)->toBeTrue(); // No exception thrown
});

it('reportClientUsage does nothing for free-access coach even with a subscription', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->create([
        'trial_ends_at' => null,
        'stripe_id' => 'cus_test_free_access',
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_free_access',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.professional.stripe_price_flat_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    // Free access is a feature grant, not metered billing — it must never report usage.
    $partialCoach = Mockery::mock($coach)->makePartial();
    $partialCoach->shouldReceive('reportMeterEvent')->never();

    $service = app(SubscriptionService::class);
    $service->reportClientUsage($partialCoach);

    expect(true)->toBeTrue(); // reportMeterEvent was never called
});

it('reportClientUsage reports overage clients for professional plan', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
        'stripe_id' => 'cus_test_professional',
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_professional',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.professional.stripe_price_flat_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $reportedQuantity = null;
    $reportedEvent = null;

    // Intercept the reportMeterEvent call on the coach
    $partialCoach = Mockery::mock($coach)->makePartial();
    $partialCoach->shouldReceive('reportMeterEvent')
        ->once()
        ->andReturnUsing(function (string $event, int $quantity) use (&$reportedEvent, &$reportedQuantity): void {
            $reportedEvent = $event;
            $reportedQuantity = $quantity;
        });

    $service = app(SubscriptionService::class);
    $service->reportClientUsage($partialCoach);

    expect($reportedEvent)->toBe('clients')
        ->and($reportedQuantity)->toBe(0); // 0 clients → max(0, 0 - 30) = 0
});
