<?php

use App\Models\MacroGoal;
use App\Models\MealLog;
use App\Models\User;

beforeEach(function (): void {
    $this->coach = User::factory()->coach()->create();
});

test('card is hidden when coach has no clients', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.dashboard.needs_attention.heading'));
});

test('card is hidden when no clients match any flag', function () {
    $client = User::factory()->client()->create(['coach_id' => $this->coach->id]);

    MacroGoal::factory()->create([
        'client_id' => $client->id,
        'coach_id' => $this->coach->id,
        'calories' => 2000,
        'effective_date' => now()->subWeek()->toDateString(),
    ]);

    // Logged today on target.
    MealLog::factory()->create([
        'client_id' => $client->id,
        'date' => now()->toDateString(),
        'calories' => 2000,
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.dashboard.needs_attention.heading'));
});

test('inactive client is flagged when no logs in last 3 days but has active goal', function () {
    $client = User::factory()->client()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Inactive Ivy',
        'created_at' => now()->subWeeks(2),
    ]);

    MacroGoal::factory()->create([
        'client_id' => $client->id,
        'coach_id' => $this->coach->id,
        'calories' => 2000,
        'effective_date' => now()->subWeek()->toDateString(),
    ]);

    // A meal log older than 3 days (so does not count toward the 3-day window).
    MealLog::factory()->create([
        'client_id' => $client->id,
        'date' => now()->subDays(7)->toDateString(),
        'calories' => 1000,
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.dashboard.needs_attention.heading'))
        ->assertSee('Inactive Ivy')
        ->assertSee(__('coach.dashboard.needs_attention.flags.inactive'));
});

test('brand-new client (joined within 3 days) is NOT flagged as inactive', function () {
    $client = User::factory()->client()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Newcomer Nora',
        'created_at' => now()->subDay(),
    ]);

    MacroGoal::factory()->create([
        'client_id' => $client->id,
        'coach_id' => $this->coach->id,
        'calories' => 2000,
        'effective_date' => now()->subDay()->toDateString(),
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee('Newcomer Nora');
});

test('off_target client is flagged when avg daily calories diverge from goal', function () {
    $client = User::factory()->client()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Off Target Ollie',
    ]);

    MacroGoal::factory()->create([
        'client_id' => $client->id,
        'coach_id' => $this->coach->id,
        'calories' => 2000,
        'effective_date' => now()->subWeek()->toDateString(),
    ]);

    // 50% of target on each of the last 3 days -> avg ratio = 0.5.
    foreach ([0, 1, 2] as $daysAgo) {
        MealLog::factory()->create([
            'client_id' => $client->id,
            'date' => now()->subDays($daysAgo)->toDateString(),
            'calories' => 1000,
        ]);
    }

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee('Off Target Ollie')
        ->assertSee(__('coach.dashboard.needs_attention.flags.off_target'));
});

test('no_goal client is flagged when logged in last 14 days but has no active goal', function () {
    $client = User::factory()->client()->create([
        'coach_id' => $this->coach->id,
        'name' => 'No Goal Nora',
    ]);

    MealLog::factory()->create([
        'client_id' => $client->id,
        'date' => now()->subDays(5)->toDateString(),
        'calories' => 1500,
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee('No Goal Nora')
        ->assertSee(__('coach.dashboard.needs_attention.flags.no_goal'));
});

test('inactive flag wins over no_goal when client has both signals', function () {
    // Client with active goal, no logs in 3 days, but has older logs.
    $client = User::factory()->client()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Mixed Mia',
        'created_at' => now()->subWeeks(2),
    ]);

    MacroGoal::factory()->create([
        'client_id' => $client->id,
        'coach_id' => $this->coach->id,
        'calories' => 2000,
        'effective_date' => now()->subMonth()->toDateString(),
    ]);

    MealLog::factory()->create([
        'client_id' => $client->id,
        'date' => now()->subDays(5)->toDateString(),
        'calories' => 1500,
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.dashboard.needs_attention.flags.inactive'))
        ->assertDontSee(__('coach.dashboard.needs_attention.flags.no_goal'));
});

test('list is limited to 5 clients', function () {
    foreach (range(1, 7) as $i) {
        $client = User::factory()->client()->create([
            'coach_id' => $this->coach->id,
            'name' => "Client {$i}",
        ]);

        // Each is a no_goal flag (logged in last 14 days, no macro goal).
        MealLog::factory()->create([
            'client_id' => $client->id,
            'date' => now()->subDays(2)->toDateString(),
            'calories' => 1000,
        ]);
    }

    $response = $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk();

    $count = substr_count($response->getContent(), __('coach.dashboard.needs_attention.flags.no_goal'));

    expect($count)->toBe(5);
});

test('clients of other coaches are not included', function () {
    $otherCoach = User::factory()->coach()->create();
    $otherClient = User::factory()->client()->create([
        'coach_id' => $otherCoach->id,
        'name' => 'Stranger Sam',
    ]);

    MealLog::factory()->create([
        'client_id' => $otherClient->id,
        'date' => now()->subDays(2)->toDateString(),
        'calories' => 1000,
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee('Stranger Sam')
        ->assertDontSee(__('coach.dashboard.needs_attention.heading'));
});
