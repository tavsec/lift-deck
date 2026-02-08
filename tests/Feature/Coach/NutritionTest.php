<?php

use App\Models\MealLog;
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

it('shows meal logs for the default 7-day range', function () {
    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->format('Y-m-d'),
        'name' => 'Todays Chicken Bowl',
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.nutrition', $this->client))
        ->assertOk()
        ->assertSee('Todays Chicken Bowl');
});

it('filters meal logs by preset range', function () {
    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->subDays(10)->format('Y-m-d'),
        'name' => 'Old Meal',
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.nutrition', [$this->client, 'range' => '7']))
        ->assertOk()
        ->assertDontSee('Old Meal');

    $this->actingAs($this->coach)
        ->get(route('coach.clients.nutrition', [$this->client, 'range' => '14']))
        ->assertOk()
        ->assertSee('Old Meal');
});

it('filters meal logs by custom date range', function () {
    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => '2026-01-15',
        'name' => 'January Meal',
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.nutrition', [
            $this->client,
            'range' => 'custom',
            'from' => '2026-01-10',
            'to' => '2026-01-20',
        ]))
        ->assertOk()
        ->assertSee('January Meal');
});
