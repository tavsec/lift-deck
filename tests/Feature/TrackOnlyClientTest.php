<?php

use App\Models\User;

it('track-only user has is_track_only flag', function () {
    $user = User::factory()->trackOnly()->create();

    expect($user->is_track_only)->toBeTrue();
    expect($user->isTrackOnly())->toBeTrue();
    expect($user->email)->toBeNull();
});
