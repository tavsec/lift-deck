<?php

use App\Models\User;
use App\Services\SubscriptionService;

it('coach can delete their own client', function (): void {
    $coach = User::factory()->create(['role' => 'coach', 'is_free_access' => true]);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id, 'email' => 'client@example.com']);

    $this->partialMock(SubscriptionService::class)
        ->shouldReceive('reportClientUsage')
        ->once()
        ->with(\Mockery::on(fn ($u) => $u->id === $coach->id));

    $response = $this->actingAs($coach)
        ->delete(route('coach.clients.destroy', $client));

    $response->assertRedirect(route('coach.clients.index'));
    $response->assertSessionHas('success');

    // Soft deleted — row still exists but with deleted_at set
    $this->assertSoftDeleted('users', ['id' => $client->id]);

    // Email is scrambled so original address is freed for re-use
    $this->assertDatabaseHas('users', [
        'id' => $client->id,
        'email' => 'client@example.com__deleted_'.$client->id,
    ]);
});

it('original email is freed after client deletion', function (): void {
    $coach = User::factory()->create(['role' => 'coach', 'is_free_access' => true]);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id, 'email' => 'returning@example.com']);

    $this->partialMock(SubscriptionService::class)
        ->shouldReceive('reportClientUsage')->once();

    $this->actingAs($coach)->delete(route('coach.clients.destroy', $client));

    // A new user with the same email can now be created without unique constraint violation
    $newUser = User::factory()->create(['email' => 'returning@example.com']);
    expect($newUser->exists)->toBeTrue();
});

it('coach cannot delete a client belonging to another coach', function (): void {
    $coach = User::factory()->create(['role' => 'coach', 'is_free_access' => true]);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);

    $this->actingAs($coach)
        ->delete(route('coach.clients.destroy', $client))
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $client->id, 'deleted_at' => null]);
});

it('reports metered usage after client deletion', function (): void {
    $coach = User::factory()->create(['role' => 'coach', 'is_free_access' => true]);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $this->partialMock(SubscriptionService::class)
        ->shouldReceive('reportClientUsage')
        ->once()
        ->with(\Mockery::on(fn ($u) => $u->id === $coach->id));

    $this->actingAs($coach)
        ->delete(route('coach.clients.destroy', $client));
});
