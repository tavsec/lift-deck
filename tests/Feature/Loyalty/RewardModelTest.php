<?php

use App\Models\Achievement;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;

it('creates a reward using the factory', function () {
    $reward = Reward::factory()->create();

    expect($reward)->toBeInstanceOf(Reward::class)
        ->and($reward->name)->toBeString()
        ->and($reward->points_cost)->toBeInt()
        ->and($reward->is_active)->toBeTrue();
});

it('creates a global reward', function () {
    $reward = Reward::factory()->global()->create();

    expect($reward->coach_id)->toBeNull()
        ->and($reward->isGlobal())->toBeTrue();
});

it('creates an inactive reward', function () {
    $reward = Reward::factory()->inactive()->create();

    expect($reward->is_active)->toBeFalse();
});

it('belongs to a coach', function () {
    $coach = User::factory()->coach()->create();
    $reward = Reward::factory()->create(['coach_id' => $coach->id]);

    expect($reward->coach->id)->toBe($coach->id);
});

it('has many redemptions', function () {
    $reward = Reward::factory()->create();
    RewardRedemption::factory()->count(3)->create(['reward_id' => $reward->id]);

    expect($reward->redemptions)->toHaveCount(3);
});

it('reports stock availability correctly', function () {
    $unlimitedReward = Reward::factory()->create(['stock' => null]);
    $inStockReward = Reward::factory()->create(['stock' => 5]);
    $outOfStockReward = Reward::factory()->create(['stock' => 0]);

    expect($unlimitedReward->hasStock())->toBeTrue()
        ->and($inStockReward->hasStock())->toBeTrue()
        ->and($outOfStockReward->hasStock())->toBeFalse();
});

it('creates a reward redemption using the factory', function () {
    $redemption = RewardRedemption::factory()->create();

    expect($redemption)->toBeInstanceOf(RewardRedemption::class)
        ->and($redemption->status)->toBe('pending')
        ->and($redemption->points_spent)->toBeInt();
});

it('creates a fulfilled redemption', function () {
    $redemption = RewardRedemption::factory()->fulfilled()->create();

    expect($redemption->status)->toBe('fulfilled');
});

it('creates a rejected redemption', function () {
    $redemption = RewardRedemption::factory()->rejected()->create();

    expect($redemption->status)->toBe('rejected');
});

it('redemption belongs to user and reward', function () {
    $client = User::factory()->client()->create();
    $reward = Reward::factory()->create();
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $client->id,
        'reward_id' => $reward->id,
    ]);

    expect($redemption->user->id)->toBe($client->id)
        ->and($redemption->reward->id)->toBe($reward->id);
});

it('creates an achievement using the factory', function () {
    $achievement = Achievement::factory()->create();

    expect($achievement)->toBeInstanceOf(Achievement::class)
        ->and($achievement->type)->toBe('automatic')
        ->and($achievement->condition_type)->toBe('workout_count')
        ->and($achievement->is_active)->toBeTrue();
});

it('creates a manual achievement', function () {
    $achievement = Achievement::factory()->manual()->create();

    expect($achievement->type)->toBe('manual')
        ->and($achievement->condition_type)->toBeNull()
        ->and($achievement->condition_value)->toBeNull();
});

it('creates a global achievement', function () {
    $achievement = Achievement::factory()->global()->create();

    expect($achievement->coach_id)->toBeNull()
        ->and($achievement->isGlobal())->toBeTrue();
});

it('achievement belongs to a coach', function () {
    $coach = User::factory()->coach()->create();
    $achievement = Achievement::factory()->create(['coach_id' => $coach->id]);

    expect($achievement->coach->id)->toBe($coach->id);
});

it('achievement has many users through pivot', function () {
    $achievement = Achievement::factory()->create();
    $users = User::factory()->client()->count(2)->create();

    $achievement->users()->attach($users->pluck('id'), [
        'earned_at' => now(),
    ]);

    expect($achievement->users)->toHaveCount(2)
        ->and($achievement->users->first()->pivot->earned_at)->not->toBeNull();
});

it('identifies automatic achievements', function () {
    $automatic = Achievement::factory()->create(['type' => 'automatic']);
    $manual = Achievement::factory()->manual()->create();

    expect($automatic->isAutomatic())->toBeTrue()
        ->and($manual->isAutomatic())->toBeFalse();
});

it('user has achievements relationship', function () {
    $user = User::factory()->client()->create();
    $achievement = Achievement::factory()->create();

    $user->achievements()->attach($achievement->id, [
        'earned_at' => now(),
    ]);

    expect($user->achievements)->toHaveCount(1)
        ->and($user->achievements->first()->id)->toBe($achievement->id);
});

it('user has reward redemptions relationship', function () {
    $user = User::factory()->client()->create();
    RewardRedemption::factory()->count(2)->create(['user_id' => $user->id]);

    expect($user->rewardRedemptions)->toHaveCount(2);
});
