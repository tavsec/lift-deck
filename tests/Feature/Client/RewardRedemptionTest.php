<?php

use App\Jobs\NotifyCoachOfRedemption;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use App\Models\UserXpSummary;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake([NotifyCoachOfRedemption::class]);
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
});

it('shows the rewards shop', function () {
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id, 'is_active' => true]);
    Reward::factory()->global()->create(['is_active' => true]);
    UserXpSummary::factory()->create(['user_id' => $this->client->id, 'available_points' => 100]);

    $this->actingAs($this->client)
        ->get(route('client.rewards'))
        ->assertOk()
        ->assertViewIs('client.rewards');
});

it('redeems a reward and deducts points', function () {
    UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'available_points' => 500,
    ]);
    $reward = Reward::factory()->create([
        'coach_id' => $this->coach->id,
        'points_cost' => 100,
        'stock' => null,
        'is_active' => true,
    ]);

    $this->actingAs($this->client)
        ->post(route('client.rewards.redeem', $reward))
        ->assertRedirect();

    expect($this->client->xpSummary->fresh()->available_points)->toBe(400);
    expect(RewardRedemption::where('user_id', $this->client->id)->count())->toBe(1);
});

it('dispatches notification job on redemption', function () {
    UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'available_points' => 500,
    ]);
    $reward = Reward::factory()->create([
        'coach_id' => $this->coach->id,
        'points_cost' => 100,
        'stock' => null,
        'is_active' => true,
    ]);

    $this->actingAs($this->client)
        ->post(route('client.rewards.redeem', $reward));

    Queue::assertPushed(NotifyCoachOfRedemption::class);
});

it('rejects redemption when insufficient points', function () {
    UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'available_points' => 50,
    ]);
    $reward = Reward::factory()->create([
        'coach_id' => $this->coach->id,
        'points_cost' => 100,
        'is_active' => true,
    ]);

    $this->actingAs($this->client)
        ->post(route('client.rewards.redeem', $reward))
        ->assertForbidden();
});

it('rejects redemption when out of stock', function () {
    UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'available_points' => 500,
    ]);
    $reward = Reward::factory()->create([
        'coach_id' => $this->coach->id,
        'points_cost' => 100,
        'stock' => 0,
        'is_active' => true,
    ]);

    $this->actingAs($this->client)
        ->post(route('client.rewards.redeem', $reward))
        ->assertForbidden();
});

it('decrements stock on redemption', function () {
    UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'available_points' => 500,
    ]);
    $reward = Reward::factory()->create([
        'coach_id' => $this->coach->id,
        'points_cost' => 100,
        'stock' => 5,
        'is_active' => true,
    ]);

    $this->actingAs($this->client)
        ->post(route('client.rewards.redeem', $reward));

    expect($reward->fresh()->stock)->toBe(4);
});
