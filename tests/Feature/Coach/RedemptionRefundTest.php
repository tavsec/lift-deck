<?php

use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use App\Models\UserXpSummary;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
});

it('refunds points to client when rejecting a pending redemption', function () {
    UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'available_points' => 200,
    ]);
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id, 'stock' => null]);
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $this->client->id,
        'reward_id' => $reward->id,
        'points_spent' => 100,
        'status' => 'pending',
    ]);

    $this->actingAs($this->coach)
        ->patch(route('coach.redemptions.update', $redemption), ['status' => 'rejected'])
        ->assertRedirect(route('coach.redemptions.index'));

    expect($this->client->xpSummary->fresh()->available_points)->toBe(300);
});

it('restocks finite reward when rejecting a pending redemption', function () {
    UserXpSummary::factory()->create(['user_id' => $this->client->id, 'available_points' => 200]);
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id, 'stock' => 3]);
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $this->client->id,
        'reward_id' => $reward->id,
        'points_spent' => 100,
        'status' => 'pending',
    ]);

    $this->actingAs($this->coach)
        ->patch(route('coach.redemptions.update', $redemption), ['status' => 'rejected']);

    expect($reward->fresh()->stock)->toBe(4);
});

it('does not change stock when rejecting an unlimited reward', function () {
    UserXpSummary::factory()->create(['user_id' => $this->client->id, 'available_points' => 200]);
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id, 'stock' => null]);
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $this->client->id,
        'reward_id' => $reward->id,
        'points_spent' => 100,
        'status' => 'pending',
    ]);

    $this->actingAs($this->coach)
        ->patch(route('coach.redemptions.update', $redemption), ['status' => 'rejected']);

    expect($reward->fresh()->stock)->toBeNull();
});

it('does not refund points when fulfilling a redemption', function () {
    UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'available_points' => 200,
    ]);
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id, 'stock' => null]);
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $this->client->id,
        'reward_id' => $reward->id,
        'points_spent' => 100,
        'status' => 'pending',
    ]);

    $this->actingAs($this->coach)
        ->patch(route('coach.redemptions.update', $redemption), ['status' => 'fulfilled']);

    expect($this->client->xpSummary->fresh()->available_points)->toBe(200);
});

it('does not double-refund when rejecting an already-rejected redemption', function () {
    UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'available_points' => 200,
    ]);
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id, 'stock' => null]);
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $this->client->id,
        'reward_id' => $reward->id,
        'points_spent' => 100,
        'status' => 'rejected',
    ]);

    $this->actingAs($this->coach)
        ->patch(route('coach.redemptions.update', $redemption), ['status' => 'rejected']);

    expect($this->client->xpSummary->fresh()->available_points)->toBe(200);
});
