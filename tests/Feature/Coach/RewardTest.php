<?php

use App\Models\Reward;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
});

it('shows the rewards index with coach and global rewards', function () {
    Reward::factory()->create(['coach_id' => $this->coach->id]);
    Reward::factory()->global()->create();

    $this->actingAs($this->coach)
        ->get(route('coach.rewards.index'))
        ->assertOk()
        ->assertViewIs('coach.rewards.index');
});

it('shows the create reward form', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.rewards.create'))
        ->assertOk();
});

it('creates a reward', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.rewards.store'), [
            'name' => 'Free PT Session',
            'description' => 'One free personal training session',
            'points_cost' => 500,
            'stock' => 10,
        ])
        ->assertRedirect(route('coach.rewards.index'));

    $this->assertDatabaseHas('rewards', [
        'coach_id' => $this->coach->id,
        'name' => 'Free PT Session',
        'points_cost' => 500,
    ]);
});

it('updates a reward', function () {
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->put(route('coach.rewards.update', $reward), [
            'name' => 'Updated Reward',
            'points_cost' => 300,
        ])
        ->assertRedirect(route('coach.rewards.index'));

    expect($reward->fresh()->name)->toBe('Updated Reward');
});

it('archives a reward on destroy', function () {
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id, 'is_active' => true]);

    $this->actingAs($this->coach)
        ->delete(route('coach.rewards.destroy', $reward))
        ->assertRedirect(route('coach.rewards.index'));

    expect($reward->fresh()->is_active)->toBeFalse();
});

it('prevents editing another coachs reward', function () {
    $otherCoach = User::factory()->coach()->create();
    $reward = Reward::factory()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.rewards.edit', $reward))
        ->assertForbidden();
});

it('prevents editing global rewards', function () {
    $reward = Reward::factory()->global()->create();

    $this->actingAs($this->coach)
        ->get(route('coach.rewards.edit', $reward))
        ->assertForbidden();
});

it('validates required fields', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.rewards.store'), [])
        ->assertSessionHasErrors(['name', 'points_cost']);
});
