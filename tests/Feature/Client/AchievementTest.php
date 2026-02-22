<?php

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserXpSummary;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
});

it('shows the achievements page', function () {
    Achievement::factory()->create(['coach_id' => $this->coach->id, 'is_active' => true]);
    Achievement::factory()->global()->create(['is_active' => true]);

    $this->actingAs($this->client)
        ->get(route('client.achievements'))
        ->assertOk()
        ->assertViewIs('client.achievements');
});

it('shows earned and unearned achievements', function () {
    $earnedAchievement = Achievement::factory()->create([
        'coach_id' => $this->coach->id,
        'is_active' => true,
    ]);
    $unearnedAchievement = Achievement::factory()->create([
        'coach_id' => $this->coach->id,
        'is_active' => true,
    ]);

    $this->client->achievements()->attach($earnedAchievement->id, [
        'awarded_by' => null,
        'earned_at' => now(),
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.achievements'));

    $response->assertOk();
    $earnedIds = $response->viewData('earnedAchievementIds');
    expect($earnedIds)->toContain($earnedAchievement->id);
    expect($earnedIds)->not->toContain($unearnedAchievement->id);
});

it('passes xp summary to view', function () {
    UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'total_xp' => 150,
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.achievements'));

    $response->assertOk();
    expect($response->viewData('xpSummary'))->not->toBeNull();
    expect($response->viewData('xpSummary')->total_xp)->toBe(150);
});
