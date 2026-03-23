<?php

use App\Models\User;

it('starts a 7-day trial when coach registers', function (): void {
    $this->post(route('register'), [
        'name' => 'Test Coach',
        'email' => 'coach@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('coach.dashboard'));

    $coach = User::where('email', 'coach@test.com')->first();

    expect($coach)->not->toBeNull();
    expect($coach->trial_ends_at)->not->toBeNull();
    expect($coach->trial_ends_at->isFuture())->toBeTrue();
    expect($coach->onTrial())->toBeTrue();
});

it('trial lasts 7 days from registration', function (): void {
    $this->post(route('register'), [
        'name' => 'Test Coach 2',
        'email' => 'coach2@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $coach = User::where('email', 'coach2@test.com')->first();

    expect(now()->diffInDays($coach->trial_ends_at))->toBeBetween(6, 8);
});
