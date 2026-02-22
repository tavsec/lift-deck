<?php

use App\Jobs\CheckLevelUp;
use App\Models\Level;
use App\Models\User;
use App\Models\UserXpSummary;

beforeEach(function () {
    $this->client = User::factory()->client()->create();

    Level::factory()->create(['level_number' => 1, 'name' => 'Beginner', 'xp_required' => 0]);
    Level::factory()->create(['level_number' => 2, 'name' => 'Bronze', 'xp_required' => 100]);
    Level::factory()->create(['level_number' => 3, 'name' => 'Silver', 'xp_required' => 500]);
});

it('assigns correct level based on total xp', function () {
    $summary = UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 150,
        'available_points' => 150,
    ]);

    (new CheckLevelUp($this->client->id))->handle();

    $summary->refresh();
    expect($summary->currentLevel->name)->toBe('Bronze');
});

it('upgrades level when xp crosses threshold', function () {
    $beginner = Level::where('level_number', 1)->first();
    $summary = UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 500,
        'available_points' => 500,
        'current_level_id' => $beginner->id,
    ]);

    (new CheckLevelUp($this->client->id))->handle();

    $summary->refresh();
    expect($summary->currentLevel->name)->toBe('Silver');
});

it('does not downgrade level', function () {
    $bronze = Level::where('level_number', 2)->first();
    $summary = UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 50,
        'available_points' => 50,
        'current_level_id' => $bronze->id,
    ]);

    (new CheckLevelUp($this->client->id))->handle();

    $summary->refresh();
    expect($summary->currentLevel->name)->toBe('Bronze');
});
