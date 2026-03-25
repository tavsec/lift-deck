<?php

use App\Models\User;

test('user locale defaults to en', function (): void {
    $user = User::factory()->create();

    expect($user->fresh()->locale)->toBe('en');
});

test('saves locale to user record', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    expect($user->locale)->toBe('en');

    $user->update(['locale' => 'sl']);

    expect($user->fresh()->locale)->toBe('sl');
});

test('middleware sets app locale from authenticated user locale', function (): void {
    $user = User::factory()->create(['locale' => 'sl', 'role' => 'coach']);

    $this->actingAs($user)
        ->get(route('coach.dashboard'))
        ->assertOk();
});

test('user locale can be updated via patch request', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->patch(route('user.locale.update'), ['locale' => 'sl'])
        ->assertRedirect();

    expect($user->fresh()->locale)->toBe('sl');
});

test('invalid locale value is rejected', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->patch(route('user.locale.update'), ['locale' => 'xx'])
        ->assertSessionHasErrors('locale');
});
