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

it('redirects to subscription page when coach lacks required feature', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    // Basic plan has no features — simulate by creating a basic subscription
    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_feature_test',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);
    $coach->update(['trial_ends_at' => null]);

    // Try to access a loyalty-gated route (not yet gated, so use middleware directly)
    // We test via the middleware class directly
    $request = \Illuminate\Http\Request::create('/test');
    $request->setUserResolver(fn () => $coach);

    $middleware = app(\App\Http\Middleware\EnsureSubscriptionFeature::class);
    $response = $middleware->handle($request, fn () => response('ok'), 'loyalty');

    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toContain('subscription');
});

it('allows through when coach plan has the required feature', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->create();

    $request = \Illuminate\Http\Request::create('/test');
    $request->setUserResolver(fn () => $coach);

    $middleware = app(\App\Http\Middleware\EnsureSubscriptionFeature::class);
    $response = $middleware->handle($request, fn () => response('ok'), 'loyalty');

    expect($response->getStatusCode())->toBe(200);
});
