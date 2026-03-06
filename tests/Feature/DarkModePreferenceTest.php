<?php

use App\Models\User;

test('user dark_mode defaults to false', function () {
    $user = User::factory()->create();

    expect($user->dark_mode)->toBeFalse();
});

test('user dark_mode can be set to true', function () {
    $user = User::factory()->create();

    $user->update(['dark_mode' => true]);

    expect($user->fresh()->dark_mode)->toBeTrue();
});

test('authenticated user can toggle dark mode on', function () {
    $user = User::factory()->create(['dark_mode' => false]);

    $this->actingAs($user)
        ->patch(route('user.dark-mode.toggle'))
        ->assertRedirect();

    expect($user->fresh()->dark_mode)->toBeTrue();
});

test('authenticated user can toggle dark mode off', function () {
    $user = User::factory()->create(['dark_mode' => true]);

    $this->actingAs($user)
        ->patch(route('user.dark-mode.toggle'))
        ->assertRedirect();

    expect($user->fresh()->dark_mode)->toBeFalse();
});

test('unauthenticated user cannot toggle dark mode', function () {
    $this->patch(route('user.dark-mode.toggle'))
        ->assertRedirect(route('login'));
});
