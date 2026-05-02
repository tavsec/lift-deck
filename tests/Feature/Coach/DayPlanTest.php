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

it('shows the coachs day plans only', function () {
    $ownPlan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'name' => 'My Plan']);
    $otherCoach = User::factory()->create(['role' => 'coach']);
    DayPlan::factory()->create(['coach_id' => $otherCoach->id, 'name' => 'Other Plan']);

    $response = $this->actingAs($this->coach)
        ->get(route('coach.day-plans.index'))
        ->assertOk()
        ->assertViewIs('coach.day-plans.index')
        ->assertSee('My Plan')
        ->assertDontSee('Other Plan');

    expect($response->viewData('dayPlans')->pluck('id')->all())->toBe([$ownPlan->id]);
});

it('creates a day plan with items', function () {
    $meal1 = Meal::factory()->create(['coach_id' => $this->coach->id]);
    $meal2 = Meal::factory()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.day-plans.store'), [
            'name' => 'Cutting Day',
            'description' => 'High protein, moderate carbs',
            'items' => [
                ['meal_id' => $meal1->id, 'meal_type' => 'Breakfast', 'sort_order' => 0],
                ['meal_id' => $meal2->id, 'meal_type' => 'Lunch', 'sort_order' => 1],
            ],
        ])
        ->assertRedirect(route('coach.day-plans.index'));

    $plan = DayPlan::query()->where('name', 'Cutting Day')->firstOrFail();
    expect($plan->coach_id)->toBe($this->coach->id);
    expect($plan->items()->count())->toBe(2);
    expect($plan->items()->where('meal_type', 'Breakfast')->first()->meal_id)->toBe($meal1->id);
});

it('updates a day plan and replaces items atomically', function () {
    $meal1 = Meal::factory()->create(['coach_id' => $this->coach->id]);
    $meal2 = Meal::factory()->create(['coach_id' => $this->coach->id]);
    $meal3 = Meal::factory()->create(['coach_id' => $this->coach->id]);

    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'name' => 'Original']);
    $plan->items()->create(['meal_id' => $meal1->id, 'meal_type' => 'Breakfast', 'sort_order' => 0]);

    $this->actingAs($this->coach)
        ->put(route('coach.day-plans.update', $plan), [
            'name' => 'Updated',
            'items' => [
                ['meal_id' => $meal2->id, 'meal_type' => 'Lunch', 'sort_order' => 0],
                ['meal_id' => $meal3->id, 'meal_type' => 'Dinner', 'sort_order' => 1],
            ],
        ])
        ->assertRedirect(route('coach.day-plans.index'));

    $plan->refresh();
    expect($plan->name)->toBe('Updated');
    expect($plan->items()->count())->toBe(2);
    expect($plan->items()->pluck('meal_id')->all())->toEqualCanonicalizing([$meal2->id, $meal3->id]);
});

it('archives the coachs day plan on destroy', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id, 'is_active' => true]);

    $this->actingAs($this->coach)
        ->delete(route('coach.day-plans.destroy', $plan))
        ->assertRedirect(route('coach.day-plans.index'));

    expect($plan->fresh()->is_active)->toBeFalse();
});

it('forbids viewing or modifying another coachs day plan', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $plan = DayPlan::factory()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.day-plans.edit', $plan))
        ->assertForbidden();

    $this->actingAs($this->coach)
        ->put(route('coach.day-plans.update', $plan), ['name' => 'Hacked'])
        ->assertForbidden();

    $this->actingAs($this->coach)
        ->delete(route('coach.day-plans.destroy', $plan))
        ->assertForbidden();
});

it('forbids creating a plan with another coachs meal', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $foreignMeal = Meal::factory()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.day-plans.store'), [
            'name' => 'Sneaky Plan',
            'items' => [
                ['meal_id' => $foreignMeal->id, 'meal_type' => 'Breakfast', 'sort_order' => 0],
            ],
        ])
        ->assertForbidden();
});

it('assigns a day plan to a client on a date', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id]);

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
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id]);
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
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id]);
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
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id]);
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

it('exposes day plan totals computed from items', function () {
    $plan = DayPlan::factory()->create(['coach_id' => $this->coach->id]);
    $meal = Meal::factory()->create([
        'coach_id' => $this->coach->id,
        'calories' => 500,
        'protein' => 40,
        'carbs' => 50,
        'fat' => 10,
    ]);
    DayPlanItem::create(['day_plan_id' => $plan->id, 'meal_id' => $meal->id, 'meal_type' => 'Breakfast', 'sort_order' => 0]);
    DayPlanItem::create(['day_plan_id' => $plan->id, 'meal_id' => $meal->id, 'meal_type' => 'Lunch', 'sort_order' => 1]);

    $plan->refresh();
    expect($plan->total_calories)->toBe(1000);
    expect((float) $plan->total_protein)->toBe(80.0);
});
