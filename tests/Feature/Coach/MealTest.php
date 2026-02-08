<?php

use App\Models\Meal;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
});

it('shows the meal library index', function () {
    Meal::factory()->count(3)->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.meals.index'))
        ->assertOk()
        ->assertViewIs('coach.meals.index');
});

it('shows the create meal form', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.meals.create'))
        ->assertOk()
        ->assertViewIs('coach.meals.create');
});

it('creates a meal', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.meals.store'), [
            'name' => 'Chicken & Rice',
            'description' => 'A classic meal',
            'calories' => 500,
            'protein' => 40,
            'carbs' => 50,
            'fat' => 10,
        ])
        ->assertRedirect(route('coach.meals.index'));

    $this->assertDatabaseHas('meals', [
        'coach_id' => $this->coach->id,
        'name' => 'Chicken & Rice',
        'calories' => 500,
    ]);
});

it('shows the edit meal form', function () {
    $meal = Meal::factory()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.meals.edit', $meal))
        ->assertOk()
        ->assertViewIs('coach.meals.edit');
});

it('updates a meal', function () {
    $meal = Meal::factory()->create(['coach_id' => $this->coach->id, 'name' => 'Old Name']);

    $this->actingAs($this->coach)
        ->put(route('coach.meals.update', $meal), [
            'name' => 'New Name',
            'calories' => 600,
            'protein' => 45,
            'carbs' => 55,
            'fat' => 15,
        ])
        ->assertRedirect(route('coach.meals.index'));

    expect($meal->fresh()->name)->toBe('New Name');
});

it('archives a meal on destroy', function () {
    $meal = Meal::factory()->create(['coach_id' => $this->coach->id, 'is_active' => true]);

    $this->actingAs($this->coach)
        ->delete(route('coach.meals.destroy', $meal))
        ->assertRedirect(route('coach.meals.index'));

    expect($meal->fresh()->is_active)->toBeFalse();
});

it('prevents editing another coachs meal', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $meal = Meal::factory()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.meals.edit', $meal))
        ->assertForbidden();
});

it('validates required fields when creating', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.meals.store'), [])
        ->assertSessionHasErrors(['name', 'calories', 'protein', 'carbs', 'fat']);
});
