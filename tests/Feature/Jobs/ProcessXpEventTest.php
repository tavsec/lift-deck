<?php

use App\Jobs\CheckLevelUp;
use App\Jobs\EvaluateAchievements;
use App\Jobs\ProcessXpEvent;
use App\Models\User;
use App\Models\UserXpSummary;
use App\Models\XpEventType;
use App\Models\XpTransaction;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    $this->eventType = XpEventType::factory()->create([
        'key' => 'workout_logged',
        'xp_amount' => 20,
        'points_amount' => 20,
        'is_active' => true,
        'cooldown_hours' => null,
    ]);
});

it('creates an xp transaction and updates summary', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    $this->assertDatabaseHas('xp_transactions', [
        'user_id' => $this->client->id,
        'xp_event_type_id' => $this->eventType->id,
        'xp_amount' => 20,
        'points_amount' => 20,
    ]);

    $summary = UserXpSummary::where('user_id', $this->client->id)->first();
    expect($summary->total_xp)->toBe(20);
    expect($summary->available_points)->toBe(20);
});

it('dispatches CheckLevelUp and EvaluateAchievements jobs', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    Queue::assertPushed(CheckLevelUp::class);
    Queue::assertPushed(EvaluateAchievements::class);
});

it('skips inactive event types', function () {
    $this->eventType->update(['is_active' => false]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    expect(XpTransaction::count())->toBe(0);
});

it('respects cooldown period', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    $this->eventType->update(['cooldown_hours' => 24]);

    XpTransaction::create([
        'user_id' => $this->client->id,
        'xp_event_type_id' => $this->eventType->id,
        'xp_amount' => 20,
        'points_amount' => 20,
        'created_at' => now()->subHours(1),
    ]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    expect(XpTransaction::count())->toBe(1);
});

it('allows event after cooldown expires', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    $this->eventType->update(['cooldown_hours' => 24]);

    XpTransaction::create([
        'user_id' => $this->client->id,
        'xp_event_type_id' => $this->eventType->id,
        'xp_amount' => 20,
        'points_amount' => 20,
        'created_at' => now()->subHours(25),
    ]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    expect(XpTransaction::count())->toBe(2);
});

it('stores metadata when provided', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    (new ProcessXpEvent($this->client->id, 'workout_logged', ['workout_log_id' => 5]))->handle();

    $transaction = XpTransaction::first();
    expect($transaction->metadata)->toBe(['workout_log_id' => 5]);
});
