<?php

use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('shows the client nutrition page', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.nutrition', $this->client))
        ->assertOk()
        ->assertViewIs('coach.clients.nutrition');
});

it('prevents viewing another coachs client nutrition page', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $otherClient = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.nutrition', $otherClient))
        ->assertForbidden();
});
