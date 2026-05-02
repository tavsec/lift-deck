<?php

use App\Models\MacroGoal;
use App\Models\Meal;
use App\Models\MealLog;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('shows the nutrition page', function () {
    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewIs('client.nutrition');
});

it('shows the nutrition page with a macro goal', function () {
    MacroGoal::factory()->create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'effective_date' => now()->subDay(),
    ]);

    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertSee('Calories');
});

it('logs a custom meal', function () {
    $this->actingAs($this->client)
        ->post(route('client.nutrition.store'), [
            'date' => now()->format('Y-m-d'),
            'meal_type' => 'Lunch',
            'name' => 'Custom Salad',
            'calories' => 350,
            'protein' => 25,
            'carbs' => 30,
            'fat' => 12,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('meal_logs', [
        'client_id' => $this->client->id,
        'name' => 'Custom Salad',
        'meal_type' => 'Lunch',
    ]);
});

it('logs a meal from the library', function () {
    $meal = Meal::factory()->create(['coach_id' => $this->coach->id, 'name' => 'Chicken Bowl']);

    $this->actingAs($this->client)
        ->post(route('client.nutrition.store'), [
            'date' => now()->format('Y-m-d'),
            'meal_id' => $meal->id,
            'meal_type' => 'Dinner',
            'name' => $meal->name,
            'calories' => $meal->calories,
            'protein' => $meal->protein,
            'carbs' => $meal->carbs,
            'fat' => $meal->fat,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('meal_logs', [
        'client_id' => $this->client->id,
        'meal_id' => $meal->id,
        'name' => 'Chicken Bowl',
    ]);
});

it('prevents logging a meal from another coachs library', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $meal = Meal::factory()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->client)
        ->post(route('client.nutrition.store'), [
            'date' => now()->format('Y-m-d'),
            'meal_id' => $meal->id,
            'meal_type' => 'Lunch',
            'name' => $meal->name,
            'calories' => $meal->calories,
            'protein' => $meal->protein,
            'carbs' => $meal->carbs,
            'fat' => $meal->fat,
        ])
        ->assertForbidden();
});

it('deletes a meal log', function () {
    $mealLog = MealLog::factory()->create(['client_id' => $this->client->id]);

    $this->actingAs($this->client)
        ->delete(route('client.nutrition.destroy', $mealLog))
        ->assertRedirect();

    $this->assertDatabaseMissing('meal_logs', ['id' => $mealLog->id]);
});

it('prevents deleting another clients meal log', function () {
    $otherClient = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $mealLog = MealLog::factory()->create(['client_id' => $otherClient->id]);

    $this->actingAs($this->client)
        ->delete(route('client.nutrition.destroy', $mealLog))
        ->assertForbidden();
});

it('returns coach meals as json', function () {
    Meal::factory()->count(3)->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->client)
        ->getJson(route('client.nutrition.meals'))
        ->assertOk()
        ->assertJsonCount(3);
});

it('filters coach meals by search', function () {
    Meal::factory()->create(['coach_id' => $this->coach->id, 'name' => 'Chicken Bowl']);
    Meal::factory()->create(['coach_id' => $this->coach->id, 'name' => 'Pasta Salad']);

    $this->actingAs($this->client)
        ->getJson(route('client.nutrition.meals', ['search' => 'Chicken']))
        ->assertOk()
        ->assertJsonCount(1);
});

it('validates required fields when logging', function () {
    $this->actingAs($this->client)
        ->post(route('client.nutrition.store'), [])
        ->assertSessionHasErrors(['date', 'meal_type', 'name', 'calories', 'protein', 'carbs', 'fat']);
});

it('passes nutrition chart data to the nutrition view', function () {
    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewHas('nutritionData')
        ->assertViewHas('nutritionStats');
});

it('passes hasPreviousDayLogs and favorites to the nutrition view', function () {
    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewHas('hasPreviousDayLogs')
        ->assertViewHas('favorites');
});

it('shows hasPreviousDayLogs as true when previous day has logs and current day is empty', function () {
    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->subDay()->format('Y-m-d'),
    ]);

    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewHas('hasPreviousDayLogs', true);
});

it('shows hasPreviousDayLogs as false when current day already has logs', function () {
    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->subDay()->format('Y-m-d'),
    ]);
    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->format('Y-m-d'),
    ]);

    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewHas('hasPreviousDayLogs', false);
});

it('copies yesterdays meals to the current date', function () {
    MealLog::factory()->count(3)->create([
        'client_id' => $this->client->id,
        'date' => now()->subDay()->format('Y-m-d'),
    ]);

    $this->actingAs($this->client)
        ->post(route('client.nutrition.copy-yesterday'), [
            'date' => now()->format('Y-m-d'),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(MealLog::query()
        ->where('client_id', $this->client->id)
        ->whereDate('date', now()->format('Y-m-d'))
        ->count()
    )->toBe(3);
});

it('redirects with error when there is nothing to copy from yesterday', function () {
    $this->actingAs($this->client)
        ->post(route('client.nutrition.copy-yesterday'), [
            'date' => now()->format('Y-m-d'),
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});

it('builds favorites from recent meal logs grouped by name', function () {
    MealLog::factory()->count(3)->create([
        'client_id' => $this->client->id,
        'name' => 'Chicken Bowl',
        'date' => now()->subDays(5)->format('Y-m-d'),
    ]);
    MealLog::factory()->count(1)->create([
        'client_id' => $this->client->id,
        'name' => 'Protein Shake',
        'date' => now()->subDays(2)->format('Y-m-d'),
    ]);

    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewHas('favorites', function ($favorites) {
            return $favorites->count() === 2
                && $favorites->first()['name'] === 'Chicken Bowl';
        });
});

it('limits favorites to 5 entries', function () {
    foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $name) {
        MealLog::factory()->create([
            'client_id' => $this->client->id,
            'name' => $name,
            'date' => now()->subDays(1)->format('Y-m-d'),
        ]);
    }

    $this->actingAs($this->client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewHas('favorites', fn ($favorites) => $favorites->count() === 5);
});
