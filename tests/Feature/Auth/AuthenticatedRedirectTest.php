<?php

use App\Models\User;

test('coach visiting client routes is redirected to coach dashboard', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();

    $this->actingAs($coach)
        ->get('/client')
        ->assertRedirect(route('coach.dashboard'));
});

test('client visiting coach routes is redirected to client dashboard', function () {
    $client = User::factory()->state(['role' => 'client'])->create();

    $this->actingAs($client)
        ->get('/coach')
        ->assertRedirect(route('client.dashboard'));
});

test('admin visiting coach routes is redirected to admin panel', function () {
    $admin = User::factory()->state(['role' => 'admin'])->create();

    $this->actingAs($admin)
        ->get('/coach')
        ->assertRedirect('/admin');
});

test('admin visiting client routes is redirected to admin panel', function () {
    $admin = User::factory()->state(['role' => 'admin'])->create();

    $this->actingAs($admin)
        ->get('/client')
        ->assertRedirect('/admin');
});

test('coach visiting admin panel is redirected to coach dashboard', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();

    $this->actingAs($coach)
        ->get('/admin')
        ->assertRedirect(route('coach.dashboard'));
});

test('client visiting admin panel is redirected to client dashboard', function () {
    $client = User::factory()->state(['role' => 'client'])->create();

    $this->actingAs($client)
        ->get('/admin')
        ->assertRedirect(route('client.dashboard'));
});
