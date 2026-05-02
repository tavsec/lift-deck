<?php

use App\Models\ClientDayAssignment;
use App\Models\DayPlan;
use App\Models\DayPlanItem;
use App\Models\Meal;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('creates a day plan for a client with library items', function () {
    $meal = Meal::factory()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Chicken & Rice',
        'calories' => 520,
        'protein' => 42,
        'carbs' => 58,
        'fat' => 10,
    ]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-plans.store', $this->client), [
            'name' => 'Cutting Day',
            'description' => 'High protein, moderate carbs',
            'items' => [
                [
                    'source' => 'library',
                    'meal_id' => $meal->id,
                    'meal_type' => 'Breakfast',
                    'name' => 'Chicken & Rice',
                    'calories' => 520,
                    'protein' => 42,
                    'carbs' => 58,
                    'fat' => 10,
                ],
            ],
        ])
        ->assertRedirect(route('coach.clients.nutrition', $this->client));

    $plan = DayPlan::query()->where('name', 'Cutting Day')->firstOrFail();
    expect($plan->coach_id)->toBe($this->coach->id);
    expect($plan->client_id)->toBe($this->client->id);
    expect($plan->items()->count())->toBe(1);

    $item = $plan->items()->first();
    expect($item->meal_id)->toBe($meal->id);
    expect($item->off_code)->toBeNull();
    expect($item->portion_grams)->toBeNull();
    expect((int) $item->calories)->toBe(520);
    expect($item->name)->toBe('Chicken & Rice');
});

it('creates a day plan with a custom item (no meal_id)', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-plans.store', $this->client), [
            'name' => 'Custom Plan',
            'items' => [
                [
                    'source' => 'custom',
                    'meal_type' => 'Lunch',
                    'name' => "Mom's soup",
                    'calories' => 400,
                    'protein' => 25,
                    'carbs' => 30,
                    'fat' => 18,
                ],
            ],
        ])
        ->assertRedirect();

    $plan = DayPlan::where('name', 'Custom Plan')->firstOrFail();
    $item = $plan->items()->first();
    expect($item->meal_id)->toBeNull();
    expect($item->off_code)->toBeNull();
    expect($item->portion_grams)->toBeNull();
    expect($item->name)->toBe("Mom's soup");
    expect((int) $item->calories)->toBe(400);
});

it('creates a day plan with an OFF item (off_code + portion_grams set)', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-plans.store', $this->client), [
            'name' => 'OFF Plan',
            'items' => [
                [
                    'source' => 'off',
                    'off_code' => '3017620422003',
                    'meal_type' => 'Snack',
                    'name' => 'Nutella (Ferrero)',
                    'calories' => 130,
                    'protein' => 1.2,
                    'carbs' => 14,
                    'fat' => 7.5,
                    'portion_grams' => 25,
                ],
            ],
        ])
        ->assertRedirect();

    $plan = DayPlan::where('name', 'OFF Plan')->firstOrFail();
    $item = $plan->items()->first();
    expect($item->meal_id)->toBeNull();
    expect($item->off_code)->toBe('3017620422003');
    expect((int) $item->portion_grams)->toBe(25);
    expect($item->name)->toBe('Nutella (Ferrero)');
});

it('creates a day plan with a macros-only item', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-plans.store', $this->client), [
            'name' => 'Macros Plan',
            'items' => [
                [
                    'source' => 'macros',
                    'meal_type' => 'Pre-workout',
                    'name' => 'Custom macros',
                    'calories' => 250,
                    'protein' => 20,
                    'carbs' => 35,
                    'fat' => 3,
                ],
            ],
        ])
        ->assertRedirect();

    $plan = DayPlan::where('name', 'Macros Plan')->firstOrFail();
    $item = $plan->items()->first();
    expect($item->meal_id)->toBeNull();
    expect($item->off_code)->toBeNull();
    expect($item->portion_grams)->toBeNull();
    expect($item->meal_type)->toBe('Pre-workout');
    expect((int) $item->calories)->toBe(250);
});

it('updates a day plan and replaces items atomically', function () {
    $meal1 = Meal::factory()->create(['coach_id' => $this->coach->id]);
    $meal2 = Meal::factory()->create(['coach_id' => $this->coach->id]);

    $plan = DayPlan::factory()->create([
        'coach_id' => $this->coach->id,
        'client_id' => $this->client->id,
        'name' => 'Original',
    ]);
    DayPlanItem::factory()->create([
        'day_plan_id' => $plan->id,
        'meal_id' => $meal1->id,
        'name' => $meal1->name,
        'meal_type' => 'Breakfast',
        'sort_order' => 0,
    ]);

    $this->actingAs($this->coach)
        ->put(route('coach.clients.day-plans.update', [$this->client, $plan]), [
            'name' => 'Updated',
            'items' => [
                [
                    'source' => 'library',
                    'meal_id' => $meal2->id,
                    'meal_type' => 'Lunch',
                    'name' => $meal2->name,
                    'calories' => 500,
                    'protein' => 30,
                    'carbs' => 50,
                    'fat' => 15,
                ],
                [
                    'source' => 'custom',
                    'meal_type' => 'Dinner',
                    'name' => 'Salmon plate',
                    'calories' => 600,
                    'protein' => 40,
                    'carbs' => 30,
                    'fat' => 25,
                ],
            ],
        ])
        ->assertRedirect(route('coach.clients.nutrition', $this->client));

    $plan->refresh();
    expect($plan->name)->toBe('Updated');
    expect($plan->items()->count())->toBe(2);
    expect($plan->items()->where('meal_type', 'Lunch')->first()->meal_id)->toBe($meal2->id);
    expect($plan->items()->where('meal_type', 'Dinner')->first()->meal_id)->toBeNull();
});

it('archives a day plan on destroy', function () {
    $plan = DayPlan::factory()->create([
        'coach_id' => $this->coach->id,
        'client_id' => $this->client->id,
        'is_active' => true,
    ]);

    $this->actingAs($this->coach)
        ->delete(route('coach.clients.day-plans.destroy', [$this->client, $plan]))
        ->assertRedirect(route('coach.clients.nutrition', $this->client));

    expect($plan->fresh()->is_active)->toBeFalse();
});

it('forbids creating a plan for another coachs client', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $strangerClient = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-plans.store', $strangerClient), [
            'name' => 'Sneaky Plan',
            'items' => [],
        ])
        ->assertForbidden();
});

it('forbids editing/updating a plan for another coachs client', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $otherClient = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);
    $plan = DayPlan::factory()->create(['coach_id' => $otherCoach->id, 'client_id' => $otherClient->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.day-plans.edit', [$otherClient, $plan]))
        ->assertForbidden();

    $this->actingAs($this->coach)
        ->put(route('coach.clients.day-plans.update', [$otherClient, $plan]), ['name' => 'Hacked'])
        ->assertForbidden();

    $this->actingAs($this->coach)
        ->delete(route('coach.clients.day-plans.destroy', [$otherClient, $plan]))
        ->assertForbidden();
});

it('forbids creating a plan with another coachs library meal', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $foreignMeal = Meal::factory()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-plans.store', $this->client), [
            'name' => 'Sneaky Plan',
            'items' => [
                [
                    'source' => 'library',
                    'meal_id' => $foreignMeal->id,
                    'meal_type' => 'Breakfast',
                    'name' => $foreignMeal->name,
                    'calories' => 100,
                    'protein' => 5,
                    'carbs' => 10,
                    'fat' => 2,
                ],
            ],
        ])
        ->assertForbidden();
});

it('supports custom meal_type sections like Pre-workout', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-plans.store', $this->client), [
            'name' => 'Workout Plan',
            'items' => [
                [
                    'source' => 'custom',
                    'meal_type' => 'Pre-workout',
                    'name' => 'Banana + coffee',
                    'calories' => 110,
                    'protein' => 1,
                    'carbs' => 27,
                    'fat' => 0.3,
                ],
                [
                    'source' => 'custom',
                    'meal_type' => 'Post-workout',
                    'name' => 'Whey shake',
                    'calories' => 150,
                    'protein' => 30,
                    'carbs' => 5,
                    'fat' => 2,
                ],
            ],
        ])
        ->assertRedirect();

    $plan = DayPlan::where('name', 'Workout Plan')->firstOrFail();
    expect($plan->items()->where('meal_type', 'Pre-workout')->exists())->toBeTrue();
    expect($plan->items()->where('meal_type', 'Post-workout')->exists())->toBeTrue();

    // Verify the section appears in the client's view when assigned.
    ClientDayAssignment::create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'day_plan_id' => $plan->id,
        'date' => now()->format('Y-m-d'),
    ]);

    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertSee('Pre-workout')
        ->assertSee('Banana + coffee');
});

it('assigns a day plan to a client on a date', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'client_id' => $this->client->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-assignments.store', $this->client), [
            'day_plan_id' => $plan->id,
            'date' => now()->format('Y-m-d'),
        ])
        ->assertRedirect(route('coach.clients.nutrition', $this->client));

    $this->assertDatabaseHas('client_day_assignments', [
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'day_plan_id' => $plan->id,
    ]);
});

it('forbids assigning a plan to a non-client', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'client_id' => $this->client->id]);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $strangerClient = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-assignments.store', $strangerClient), [
            'day_plan_id' => $plan->id,
            'date' => now()->format('Y-m-d'),
        ])
        ->assertForbidden();
});

it('rejects a duplicate assignment for the same client + date', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'client_id' => $this->client->id]);
    $date = now()->format('Y-m-d');

    ClientDayAssignment::create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'day_plan_id' => $plan->id,
        'date' => $date,
    ]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.day-assignments.store', $this->client), [
            'day_plan_id' => $plan->id,
            'date' => $date,
        ])
        ->assertSessionHasErrors('date');

    expect(ClientDayAssignment::where('client_id', $this->client->id)->whereDate('date', $date)->count())->toBe(1);
});

it('allows the coach to remove an assignment', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'client_id' => $this->client->id]);
    $assignment = ClientDayAssignment::create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'day_plan_id' => $plan->id,
        'date' => now()->addDay()->format('Y-m-d'),
    ]);

    $this->actingAs($this->coach)
        ->delete(route('coach.clients.day-assignments.destroy', [$this->client, $assignment]))
        ->assertRedirect(route('coach.clients.nutrition', $this->client));

    $this->assertDatabaseMissing('client_day_assignments', ['id' => $assignment->id]);
});

it('exposes day plan totals computed from item snapshots', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'client_id' => $this->client->id]);
    DayPlanItem::factory()->create([
        'day_plan_id' => $plan->id,
        'meal_id' => null,
        'name' => 'Snapshot 1',
        'meal_type' => 'Breakfast',
        'calories' => 500,
        'protein' => 40,
        'carbs' => 50,
        'fat' => 10,
        'sort_order' => 0,
    ]);
    DayPlanItem::factory()->create([
        'day_plan_id' => $plan->id,
        'meal_id' => null,
        'name' => 'Snapshot 2',
        'meal_type' => 'Lunch',
        'calories' => 500,
        'protein' => 40,
        'carbs' => 50,
        'fat' => 10,
        'sort_order' => 1,
    ]);

    $plan->refresh();
    expect($plan->total_calories)->toBe(1000);
    expect((float) $plan->total_protein)->toBe(80.0);
});
