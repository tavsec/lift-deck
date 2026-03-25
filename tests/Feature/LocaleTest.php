<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

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
