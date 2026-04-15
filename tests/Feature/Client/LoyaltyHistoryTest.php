<?php

use App\Features\Loyalty;
use App\Models\User;
use Laravel\Pennant\Feature;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create(['is_free_access' => true]);
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    Feature::for($this->coach)->activate(Loyalty::class);
});

it('client can view their own loyalty page', function () {
    $this->actingAs($this->client)
        ->get(route('client.loyalty'))
        ->assertOk()
        ->assertViewIs('client.loyalty');
});

it('coach can view a clients loyalty page', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.loyalty', $this->client))
        ->assertOk()
        ->assertViewIs('coach.clients.loyalty');
});

it('coach cannot view another coachs clients loyalty page', function () {
    $otherCoach = User::factory()->coach()->create();
    $otherClient = User::factory()->client()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.loyalty', $otherClient))
        ->assertForbidden();
});
