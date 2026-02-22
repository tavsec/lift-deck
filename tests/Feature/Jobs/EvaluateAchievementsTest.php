<?php

use App\Jobs\EvaluateAchievements;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserXpSummary;
use App\Models\WorkoutLog;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 0,
        'available_points' => 0,
    ]);
});

it('awards workout count achievement when threshold met', function () {
    $achievement = Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 3,
        'xp_reward' => 50,
        'points_reward' => 50,
    ]);

    WorkoutLog::factory()->count(3)->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();

    expect($this->client->achievements()->count())->toBe(1);
    expect($this->client->achievements->first()->id)->toBe($achievement->id);
});

it('does not award achievement when threshold not met', function () {
    Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 10,
    ]);

    WorkoutLog::factory()->count(3)->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();

    expect($this->client->achievements()->count())->toBe(0);
});

it('does not double-award achievements', function () {
    Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
    ]);

    WorkoutLog::factory()->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();
    (new EvaluateAchievements($this->client->id))->handle();

    expect($this->client->achievements()->count())->toBe(1);
});

it('awards xp and points bonus for achievement', function () {
    Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
        'xp_reward' => 50,
        'points_reward' => 50,
    ]);

    WorkoutLog::factory()->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();

    $summary = $this->client->xpSummary->fresh();
    expect($summary->total_xp)->toBe(50);
    expect($summary->available_points)->toBe(50);
});

it('only evaluates achievements visible to user (global + coach)', function () {
    $otherCoach = User::factory()->coach()->create();

    Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
    ]);

    Achievement::factory()->create([
        'coach_id' => $this->coach->id,
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
    ]);

    Achievement::factory()->create([
        'coach_id' => $otherCoach->id,
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
    ]);

    WorkoutLog::factory()->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();

    // Should only get global + own coach's achievement (2), not other coach's
    expect($this->client->achievements()->count())->toBe(2);
});
