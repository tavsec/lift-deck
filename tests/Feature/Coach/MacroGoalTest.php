<?php

use App\Models\MacroGoal;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('creates a macro goal for a client', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.clients.macro-goals.store', $this->client), [
            'calories' => 2200,
            'protein' => 180,
            'carbs' => 250,
            'fat' => 70,
            'effective_date' => '2026-02-07',
            'notes' => 'Starting goals',
        ])
        ->assertRedirect(route('coach.clients.nutrition', $this->client));

    $this->assertDatabaseHas('macro_goals', [
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'calories' => 2200,
    ]);
});

it('prevents creating a macro goal for another coachs client', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $otherClient = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.macro-goals.store', $otherClient), [
            'calories' => 2200,
            'protein' => 180,
            'carbs' => 250,
            'fat' => 70,
            'effective_date' => '2026-02-07',
        ])
        ->assertForbidden();
});

it('deletes a macro goal', function () {
    $goal = MacroGoal::factory()->create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
    ]);

    $this->actingAs($this->coach)
        ->delete(route('coach.macro-goals.destroy', $goal))
        ->assertRedirect(route('coach.clients.nutrition', $this->client->id));

    $this->assertDatabaseMissing('macro_goals', ['id' => $goal->id]);
});

it('prevents deleting another coachs macro goal', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $goal = MacroGoal::factory()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->delete(route('coach.macro-goals.destroy', $goal))
        ->assertForbidden();
});

it('validates required fields', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.clients.macro-goals.store', $this->client), [])
        ->assertSessionHasErrors(['calories', 'protein', 'carbs', 'fat', 'effective_date']);
});
