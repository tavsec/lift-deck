<?php

use App\Models\User;
use App\Services\SubscriptionService;

it('coach can delete their own client', function (): void {
    $coach = User::factory()->create(['role' => 'coach', 'is_free_access' => true]);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $this->partialMock(SubscriptionService::class)
        ->shouldReceive('reportClientUsage')
        ->once()
        ->with(\Mockery::on(fn ($u) => $u->id === $coach->id));

    $response = $this->actingAs($coach)
        ->delete(route('coach.clients.destroy', $client));

    $response->assertRedirect(route('coach.clients.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('users', ['id' => $client->id]);
});

it('coach cannot delete a client belonging to another coach', function (): void {
    $coach = User::factory()->create(['role' => 'coach', 'is_free_access' => true]);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);

    $this->actingAs($coach)
        ->delete(route('coach.clients.destroy', $client))
        ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $client->id]);
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
