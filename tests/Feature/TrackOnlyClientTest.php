<?php

use App\Models\User;

it('track-only user has is_track_only flag', function () {
    $user = User::factory()->trackOnly()->create();

    expect($user->is_track_only)->toBeTrue();
    expect($user->isTrackOnly())->toBeTrue();
    expect($user->email)->toBeNull();
});

it('coach can create a track-only client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();

    $this->actingAs($coach)
        ->post(route('coach.clients.store-track-only'), [
            'name' => 'John Doe',
            'phone' => '555-1234',
        ])
        ->assertRedirect(route('coach.clients.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'coach_id' => $coach->id,
        'is_track_only' => true,
        'email' => null,
    ]);
});

it('coach cannot create track-only client without a name', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();

    $this->actingAs($coach)
        ->post(route('coach.clients.store-track-only'), [])
        ->assertSessionHasErrors('name');
});

it('coach can generate app access invitation for track-only client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->trackOnly()->create(['coach_id' => $coach->id]);

    $this->actingAs($coach)
        ->post(route('coach.clients.enable-app-access', $client))
        ->assertRedirect(route('coach.clients.show', $client));

    $this->assertDatabaseHas('client_invitations', [
        'coach_id' => $coach->id,
        'track_only_client_id' => $client->id,
    ]);
});

it('track-only client is upgraded when redeeming invitation', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $trackOnlyClient = User::factory()->trackOnly()->create(['coach_id' => $coach->id]);

    $invitation = \App\Models\ClientInvitation::create([
        'coach_id' => $coach->id,
        'track_only_client_id' => $trackOnlyClient->id,
        'token' => 'TESTCODE',
        'expires_at' => now()->addDays(7),
    ]);

    $this->post(route('join.register'), [
        'code' => 'TESTCODE',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('client.welcome'));

    $trackOnlyClient->refresh();
    expect($trackOnlyClient->email)->toBe('john@example.com');
    expect($trackOnlyClient->is_track_only)->toBeFalse();

    // No new user was created
    expect(User::where('email', 'john@example.com')->count())->toBe(1);
});
