<?php

use App\Models\User;

test('client layout includes coach branding colors as CSS variables', function () {
    $coach = User::factory()->coach()->create([
        'primary_color' => '#FF0000',
        'secondary_color' => '#00FF00',
    ]);
    $client = User::factory()->client()->create(['coach_id' => $coach->id]);

    $this->actingAs($client)
        ->get(route('client.dashboard'))
        ->assertOk()
        ->assertSee('--color-primary: #FF0000', false)
        ->assertSee('--color-secondary: #00FF00', false);
});

test('coach layout includes own branding colors as CSS variables', function () {
    $coach = User::factory()->coach()->create([
        'primary_color' => '#FF0000',
        'secondary_color' => '#00FF00',
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee('--color-primary: #FF0000', false);
});

test('layouts fall back to default blue when no colors set', function () {
    $coach = User::factory()->coach()->create([
        'primary_color' => null,
        'secondary_color' => null,
    ]);
    $client = User::factory()->client()->create(['coach_id' => $coach->id]);

    $this->actingAs($client)
        ->get(route('client.dashboard'))
        ->assertOk()
        ->assertSee('--color-primary: #2563EB', false);
});

test('coach layout shows logo image when set', function () {
    $coach = User::factory()->coach()->create([
        'logo' => 'logos/test.png',
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee('logos/test.png', false);
});

test('client layout shows coach logo when set', function () {
    $coach = User::factory()->coach()->create([
        'logo' => 'logos/coach-logo.png',
    ]);
    $client = User::factory()->client()->create(['coach_id' => $coach->id]);

    $this->actingAs($client)
        ->get(route('client.dashboard'))
        ->assertOk()
        ->assertSee('logos/coach-logo.png', false);
});
