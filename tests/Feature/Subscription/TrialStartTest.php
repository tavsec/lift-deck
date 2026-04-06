<?php

use App\Models\User;

it('starts a 7-day trial when coach selects basic plan', function (): void {
    $coach = User::factory()->state([
        'role' => 'coach',
        'trial_ends_at' => null,
        'selected_plan' => null,
    ])->create();

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'basic'])
        ->assertRedirect(route('coach.dashboard'));

    $coach->refresh();

    expect($coach->trial_ends_at)->not->toBeNull();
    expect($coach->trial_ends_at->isFuture())->toBeTrue();
    expect($coach->onTrial())->toBeTrue();
});

it('trial lasts 7 days from plan selection', function (): void {
    $coach = User::factory()->state([
        'role' => 'coach',
        'trial_ends_at' => null,
        'selected_plan' => null,
    ])->create();

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'basic']);

    $coach->refresh();

    expect(now()->diffInDays($coach->trial_ends_at))->toBeBetween(6, 8);
});
