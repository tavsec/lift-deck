<?php

use App\Jobs\CheckLevelUp;
use App\Jobs\EvaluateAchievements;
use App\Jobs\NotifyCoachOfRedemption;
use App\Jobs\ProcessXpEvent;
use App\Models\Achievement;
use App\Models\DailyLog;
use App\Models\Level;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\TrackingMetric;
use App\Models\User;
use App\Models\UserXpSummary;
use App\Models\XpTransaction;
use Database\Seeders\LevelSeeder;
use Database\Seeders\XpEventTypeSeeder;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake([NotifyCoachOfRedemption::class]);

    $this->seed(XpEventTypeSeeder::class);
    $this->seed(LevelSeeder::class);

    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
});

it('creates XP transaction and updates summary when workout event is processed', function () {
    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    expect(XpTransaction::where('user_id', $this->client->id)->count())->toBe(1);

    $summary = UserXpSummary::where('user_id', $this->client->id)->first();
    expect($summary)->not->toBeNull();
    expect($summary->total_xp)->toBe(20);
    expect($summary->available_points)->toBe(20);
});

it('assigns Beginner level when CheckLevelUp runs with 0 XP', function () {
    UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 0,
        'available_points' => 0,
    ]);

    (new CheckLevelUp($this->client->id))->handle();

    $summary = UserXpSummary::where('user_id', $this->client->id)->with('currentLevel')->first();
    expect($summary->currentLevel)->not->toBeNull();
    expect($summary->currentLevel->name)->toBe('Beginner');
});

it('upgrades to Bronze level when XP reaches 100', function () {
    UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 150,
        'available_points' => 150,
    ]);

    (new CheckLevelUp($this->client->id))->handle();

    $summary = UserXpSummary::where('user_id', $this->client->id)->with('currentLevel')->first();
    expect($summary->currentLevel->name)->toBe('Bronze');
    expect($summary->currentLevel->xp_required)->toBe(100);
});

it('awards automatic achievement after enough check-ins', function () {
    $achievement = Achievement::create([
        'name' => 'Dedicated Logger',
        'type' => 'automatic',
        'condition_type' => 'checkin_count',
        'condition_value' => 5,
        'xp_reward' => 0,
        'points_reward' => 0,
        'is_active' => true,
    ]);

    $metric = TrackingMetric::factory()->number()->create(['coach_id' => $this->coach->id]);

    foreach (range(1, 5) as $day) {
        DailyLog::create([
            'client_id' => $this->client->id,
            'tracking_metric_id' => $metric->id,
            'date' => now()->subDays($day)->toDateString(),
            'value' => '80',
        ]);
    }

    (new EvaluateAchievements($this->client->id))->handle();

    expect($this->client->achievements()->where('achievements.id', $achievement->id)->exists())->toBeTrue();
});

it('grants bonus XP and points when an achievement with rewards is earned', function () {
    Achievement::create([
        'name' => 'First Steps',
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
        'xp_reward' => 50,
        'points_reward' => 25,
        'is_active' => true,
    ]);

    // Give the client a workout log
    $program = \App\Models\Program::factory()->create(['coach_id' => $this->coach->id]);
    $workout = \App\Models\ProgramWorkout::factory()->create(['program_id' => $program->id]);
    \App\Models\WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'program_workout_id' => $workout->id,
    ]);

    UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 0,
        'available_points' => 0,
    ]);

    (new EvaluateAchievements($this->client->id))->handle();

    $summary = UserXpSummary::where('user_id', $this->client->id)->first();
    expect($summary->total_xp)->toBe(50);
    expect($summary->available_points)->toBe(25);
});

it('deducts points and creates redemption when client redeems reward', function () {
    $summary = UserXpSummary::factory()->create([
        'user_id' => $this->client->id,
        'available_points' => 300,
    ]);

    $reward = Reward::factory()->create([
        'coach_id' => $this->coach->id,
        'points_cost' => 150,
        'stock' => null,
        'is_active' => true,
    ]);

    $this->actingAs($this->client)
        ->post(route('client.rewards.redeem', $reward))
        ->assertRedirect();

    expect($summary->fresh()->available_points)->toBe(150);
    expect(RewardRedemption::where('user_id', $this->client->id)->count())->toBe(1);
    Queue::assertPushed(NotifyCoachOfRedemption::class);
});

it('allows coach to fulfill a pending redemption', function () {
    $reward = Reward::factory()->create([
        'coach_id' => $this->coach->id,
        'points_cost' => 100,
        'is_active' => true,
    ]);

    $redemption = RewardRedemption::factory()->create([
        'user_id' => $this->client->id,
        'reward_id' => $reward->id,
        'points_spent' => 100,
        'status' => 'pending',
    ]);

    $this->actingAs($this->coach)
        ->patch(route('coach.redemptions.update', $redemption), [
            'status' => 'fulfilled',
            'coach_notes' => 'Delivered in person',
        ])
        ->assertRedirect();

    expect($redemption->fresh()->status)->toBe('fulfilled');
    expect($redemption->fresh()->coach_notes)->toBe('Delivered in person');
});

it('does not award XP when event type is on cooldown', function () {
    // daily_checkin has a 24-hour cooldown
    (new ProcessXpEvent($this->client->id, 'daily_checkin'))->handle();
    (new ProcessXpEvent($this->client->id, 'daily_checkin'))->handle();

    expect(XpTransaction::where('user_id', $this->client->id)->count())->toBe(1);

    $summary = UserXpSummary::where('user_id', $this->client->id)->first();
    expect($summary->total_xp)->toBe(10);
});

it('does not re-award an achievement already earned', function () {
    $achievement = Achievement::create([
        'name' => 'Early Bird',
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 0,
        'xp_reward' => 10,
        'points_reward' => 10,
        'is_active' => true,
    ]);

    UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 0,
        'available_points' => 0,
    ]);

    (new EvaluateAchievements($this->client->id))->handle();
    (new EvaluateAchievements($this->client->id))->handle();

    expect($this->client->achievements()->where('achievements.id', $achievement->id)->count())->toBe(1);

    $summary = UserXpSummary::where('user_id', $this->client->id)->first();
    expect($summary->total_xp)->toBe(10);
});

it('level does not downgrade when XP decreases', function () {
    $summary = UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 150,
        'available_points' => 0,
    ]);

    (new CheckLevelUp($this->client->id))->handle();

    $bronze = Level::where('name', 'Bronze')->first();
    expect($summary->fresh()->current_level_id)->toBe($bronze->id);

    // Simulate XP dropping below Bronze threshold (edge case guard)
    $summary->update(['total_xp' => 50]);
    (new CheckLevelUp($this->client->id))->handle();

    // Level should NOT have changed
    expect($summary->fresh()->current_level_id)->toBe($bronze->id);
});
