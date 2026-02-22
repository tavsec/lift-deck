<?php

use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    $this->reward = Reward::factory()->create(['coach_id' => $this->coach->id]);
});

it('shows the redemptions index for the coach', function () {
    RewardRedemption::factory()->create([
        'user_id' => $this->client->id,
        'reward_id' => $this->reward->id,
        'points_spent' => 100,
        'status' => 'pending',
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.redemptions.index'))
        ->assertOk()
        ->assertViewIs('coach.redemptions.index');
});

it('fulfills a redemption', function () {
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $this->client->id,
        'reward_id' => $this->reward->id,
        'points_spent' => 100,
        'status' => 'pending',
    ]);

    $this->actingAs($this->coach)
        ->patch(route('coach.redemptions.update', $redemption), [
            'status' => 'fulfilled',
            'coach_notes' => 'Delivered in person',
        ])
        ->assertRedirect(route('coach.redemptions.index'));

    expect($redemption->fresh()->status)->toBe('fulfilled');
    expect($redemption->fresh()->coach_notes)->toBe('Delivered in person');
});

it('rejects a redemption', function () {
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $this->client->id,
        'reward_id' => $this->reward->id,
        'points_spent' => 100,
        'status' => 'pending',
    ]);

    $this->actingAs($this->coach)
        ->patch(route('coach.redemptions.update', $redemption), [
            'status' => 'rejected',
        ])
        ->assertRedirect(route('coach.redemptions.index'));

    expect($redemption->fresh()->status)->toBe('rejected');
});

it('prevents managing redemptions for another coachs clients', function () {
    $otherCoach = User::factory()->coach()->create();
    $otherClient = User::factory()->client()->create(['coach_id' => $otherCoach->id]);
    $otherReward = Reward::factory()->create(['coach_id' => $otherCoach->id]);
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $otherClient->id,
        'reward_id' => $otherReward->id,
        'points_spent' => 100,
        'status' => 'pending',
    ]);

    $this->actingAs($this->coach)
        ->patch(route('coach.redemptions.update', $redemption), [
            'status' => 'fulfilled',
        ])
        ->assertForbidden();
});
