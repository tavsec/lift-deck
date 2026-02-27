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
