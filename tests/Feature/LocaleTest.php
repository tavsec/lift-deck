<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('saves locale to user record', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    expect($user->locale)->toBe('en');

    $user->update(['locale' => 'sl']);

    expect($user->fresh()->locale)->toBe('sl');
});
