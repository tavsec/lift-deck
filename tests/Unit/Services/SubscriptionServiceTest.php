<?php

use App\Models\User;
use App\Services\SubscriptionService;

beforeEach(function (): void {
    $this->service = new SubscriptionService;
});

it('grants access to free access coaches', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->make();
    expect($this->service->isActive($coach))->toBeTrue();
    expect($this->service->isInGracePeriod($coach))->toBeFalse();
});

it('grants access during active trial', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->make([
        'trial_ends_at' => now()->addDays(3),
    ]);
    expect($this->service->isActive($coach))->toBeTrue();
});

it('does not grant access when trial expired and no subscription', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->make([
        'trial_ends_at' => now()->subDay(),
    ]);
    expect($this->service->isActive($coach))->toBeFalse();
    expect($this->service->isInGracePeriod($coach))->toBeFalse();
});

it('free access coach has all features', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->make();
    expect($this->service->hasFeature($coach, 'loyalty'))->toBeTrue();
    expect($this->service->hasFeature($coach, 'custom_branding'))->toBeTrue();
});

it('returns null plan for coach with no subscription', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->make([
        'trial_ends_at' => now()->subDay(),
    ]);
    expect($this->service->currentPlan($coach))->toBeNull();
    expect($this->service->currentPlanKey($coach))->toBeNull();
});

it('free access coach treated as professional for plan', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->make();
    expect($this->service->currentPlanKey($coach))->toBe('professional');
    expect($this->service->clientLimit($coach))->toBeNull();
});
