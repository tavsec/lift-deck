<?php

use App\Models\ClientDayAssignment;
use App\Models\DayPlan;
use App\Models\DayPlanItem;
use App\Models\Meal;
use App\Models\MealLog;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('shows the assigned plan and its items on the index for that date', function () {
    $plan = DayPlan::factory()->create([
        'coach_id' => $this->coach->id,
        'client_id' => $this->client->id,
        'name' => 'Test Plan',
    ]);
    $meal = Meal::factory()->create(['coach_id' => $this->coach->id, 'name' => 'Eggs & Toast']);
    $item = DayPlanItem::create([
        'day_plan_id' => $plan->id,
        'meal_id' => $meal->id,
        'meal_type' => 'Breakfast',
        'name' => 'Eggs & Toast',
        'calories' => 350,
        'protein' => 22,
        'carbs' => 30,
        'fat' => 12,
        'sort_order' => 0,
    ]);
    ClientDayAssignment::create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'day_plan_id' => $plan->id,
        'date' => now()->format('Y-m-d'),
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertSee('Test Plan')
        ->assertSee('Eggs & Toast');

    $assignedItems = $response->viewData('assignedItems');
    expect($assignedItems)->toHaveCount(1);
    expect($assignedItems->first()['item']->id)->toBe($item->id);
    expect($assignedItems->first()['completed'])->toBeFalse();
});

it('marks an item as eaten and creates a meal log linked to the day_plan_item', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'client_id' => $this->client->id]);
    $meal = Meal::factory()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Chicken & Rice',
        'calories' => 500,
        'protein' => 40,
        'carbs' => 50,
        'fat' => 10,
    ]);
    $item = DayPlanItem::create([
        'day_plan_id' => $plan->id,
        'meal_id' => $meal->id,
        'meal_type' => 'Lunch',
        'name' => 'Chicken & Rice',
        'calories' => 500,
        'protein' => 40,
        'carbs' => 50,
        'fat' => 10,
        'sort_order' => 0,
    ]);
    ClientDayAssignment::create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'day_plan_id' => $plan->id,
        'date' => now()->format('Y-m-d'),
    ]);

    $this->actingAs($this->client)
        ->post(route('client.nutrition.store'), [
            'date' => now()->format('Y-m-d'),
            'meal_id' => $meal->id,
            'day_plan_item_id' => $item->id,
            'meal_type' => 'Lunch',
            'name' => 'Chicken & Rice',
            'calories' => 500,
            'protein' => 40,
            'carbs' => 50,
            'fat' => 10,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('meal_logs', [
        'client_id' => $this->client->id,
        'meal_id' => $meal->id,
        'day_plan_item_id' => $item->id,
        'name' => 'Chicken & Rice',
        'calories' => 500,
    ]);
});

it('marks an item as completed when a meal log already exists for it', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'client_id' => $this->client->id]);
    $meal = Meal::factory()->create(['coach_id' => $this->coach->id]);
    $item = DayPlanItem::create([
        'day_plan_id' => $plan->id,
        'meal_id' => $meal->id,
        'meal_type' => 'Breakfast',
        'name' => $meal->name,
        'calories' => $meal->calories,
        'protein' => $meal->protein,
        'carbs' => $meal->carbs,
        'fat' => $meal->fat,
        'sort_order' => 0,
    ]);
    ClientDayAssignment::create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'day_plan_id' => $plan->id,
        'date' => now()->format('Y-m-d'),
    ]);
    MealLog::create([
        'client_id' => $this->client->id,
        'meal_id' => $meal->id,
        'day_plan_item_id' => $item->id,
        'date' => now()->format('Y-m-d'),
        'meal_type' => 'Breakfast',
        'name' => $meal->name,
        'calories' => $meal->calories,
        'protein' => $meal->protein,
        'carbs' => $meal->carbs,
        'fat' => $meal->fat,
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk();

    $assignedItems = $response->viewData('assignedItems');
    expect($assignedItems->first()['completed'])->toBeTrue();
});

it('does not show another clients assignment', function () {
    $otherClient = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $plan = DayPlan::factory()->create([
        'coach_id' => $this->coach->id,
        'client_id' => $otherClient->id,
        'name' => 'Other Client Plan',
    ]);
    DayPlanItem::create([
        'day_plan_id' => $plan->id,
        'meal_id' => Meal::factory()->create(['coach_id' => $this->coach->id])->id,
        'meal_type' => 'Breakfast',
        'name' => 'Some meal',
        'calories' => 100,
        'protein' => 10,
        'carbs' => 10,
        'fat' => 1,
        'sort_order' => 0,
    ]);
    ClientDayAssignment::create([
        'client_id' => $otherClient->id,
        'coach_id' => $this->coach->id,
        'day_plan_id' => $plan->id,
        'date' => now()->format('Y-m-d'),
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertDontSee('Other Client Plan');

    expect($response->viewData('assignment'))->toBeNull();
});

it('orders sections by first-seen of each items meal_type and renders snapshot macros', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'client_id' => $this->client->id]);

    // Order in DB: Pre-workout (sort_order 0), Lunch (1), Pre-workout (2)
    // First-seen: Pre-workout, Lunch
    DayPlanItem::create([
        'day_plan_id' => $plan->id,
        'meal_id' => null,
        'meal_type' => 'Pre-workout',
        'name' => 'Banana + coffee',
        'calories' => 110,
        'protein' => 1,
        'carbs' => 27,
        'fat' => 0.3,
        'sort_order' => 0,
    ]);
    DayPlanItem::create([
        'day_plan_id' => $plan->id,
        'meal_id' => null,
        'meal_type' => 'Lunch',
        'name' => 'Chicken bowl',
        'calories' => 600,
        'protein' => 45,
        'carbs' => 60,
        'fat' => 15,
        'sort_order' => 1,
    ]);
    DayPlanItem::create([
        'day_plan_id' => $plan->id,
        'meal_id' => null,
        'meal_type' => 'Pre-workout',
        'name' => 'Whey shake',
        'calories' => 150,
        'protein' => 30,
        'carbs' => 5,
        'fat' => 2,
        'sort_order' => 2,
    ]);
    ClientDayAssignment::create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'day_plan_id' => $plan->id,
        'date' => now()->format('Y-m-d'),
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertSee('Banana + coffee')
        ->assertSee('Chicken bowl')
        ->assertSee('Whey shake')
        ->assertSee('Pre-workout')
        ->assertSee('Lunch');

    // First-seen order check: "Pre-workout" header should appear before "Lunch" header in HTML
    $html = (string) $response->getContent();
    $preIdx = mb_strpos($html, '>Pre-workout<');
    $lunchIdx = mb_strpos($html, '>Lunch<');
    expect($preIdx)->not->toBeFalse();
    expect($lunchIdx)->not->toBeFalse();
    expect($preIdx)->toBeLessThan($lunchIdx);

    // Snapshot macros render directly (no meal relationship)
    $response->assertSee('110 kcal');
    $response->assertSee('600 kcal');
});
