<?php

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserXpSummary;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
});

it('shows the achievements index', function () {
    Achievement::factory()->create(['coach_id' => $this->coach->id]);
    Achievement::factory()->global()->create();

    $this->actingAs($this->coach)
        ->get(route('coach.achievements.index'))
        ->assertOk()
        ->assertViewIs('coach.achievements.index');
});

it('shows the create achievement form', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.achievements.create'))
        ->assertOk();
});

it('creates an achievement', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.achievements.store'), [
            'name' => 'First Workout',
            'description' => 'Complete your first workout',
            'type' => 'automatic',
            'condition_type' => 'workout_count',
            'condition_value' => 1,
            'xp_reward' => 50,
            'points_reward' => 25,
        ])
        ->assertRedirect(route('coach.achievements.index'));

    $this->assertDatabaseHas('achievements', [
        'coach_id' => $this->coach->id,
        'name' => 'First Workout',
        'type' => 'automatic',
    ]);
});

it('updates an achievement', function () {
    $achievement = Achievement::factory()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->put(route('coach.achievements.update', $achievement), [
            'name' => 'Updated Achievement',
            'type' => 'manual',
            'xp_reward' => 0,
            'points_reward' => 0,
        ])
        ->assertRedirect(route('coach.achievements.index'));

    expect($achievement->fresh()->name)->toBe('Updated Achievement');
});

it('archives an achievement on destroy', function () {
    $achievement = Achievement::factory()->create(['coach_id' => $this->coach->id, 'is_active' => true]);

    $this->actingAs($this->coach)
        ->delete(route('coach.achievements.destroy', $achievement))
        ->assertRedirect(route('coach.achievements.index'));

    expect($achievement->fresh()->is_active)->toBeFalse();
});

it('prevents editing another coachs achievement', function () {
    $otherCoach = User::factory()->coach()->create();
    $achievement = Achievement::factory()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.achievements.edit', $achievement))
        ->assertForbidden();
});

it('prevents editing global achievements', function () {
    $achievement = Achievement::factory()->global()->create();

    $this->actingAs($this->coach)
        ->get(route('coach.achievements.edit', $achievement))
        ->assertForbidden();
});

it('validates required fields', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.achievements.store'), [])
        ->assertSessionHasErrors(['name', 'type']);
});

it('manually awards an achievement to a client', function () {
    $achievement = Achievement::factory()->manual()->create(['coach_id' => $this->coach->id]);
    $client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    UserXpSummary::create(['user_id' => $client->id, 'total_xp' => 0, 'available_points' => 0]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.achievements.award', [$client, $achievement]))
        ->assertRedirect();

    expect($client->fresh()->achievements)->toHaveCount(1);
    expect($client->fresh()->achievements->first()->pivot->awarded_by)->toBe($this->coach->id);
});

it('prevents awarding to another coachs client', function () {
    $otherCoach = User::factory()->coach()->create();
    $client = User::factory()->client()->create(['coach_id' => $otherCoach->id]);
    $achievement = Achievement::factory()->manual()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.achievements.award', [$client, $achievement]))
        ->assertForbidden();
});
