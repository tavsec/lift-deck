<?php

use App\Models\User;

test('user locale defaults to en', function (): void {
    $user = User::factory()->create();

    expect($user->locale)->toBe('en');
});

test('saves locale to user record', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    expect($user->locale)->toBe('en');

    $user->update(['locale' => 'sl']);

    expect($user->fresh()->locale)->toBe('sl');
});
