<?php

use App\Models\User;

test('unverified coach is redirected to email verification when accessing plan page', function () {
    $coach = User::factory()->coach()->unverified()->create();

    $this->actingAs($coach)
        ->get('/coach/plan')
        ->assertRedirect(route('verification.notice'));
});

test('unverified coach is redirected to email verification when posting to plan page', function () {
    $coach = User::factory()->coach()->unverified()->create();

    $this->actingAs($coach)
        ->post('/coach/plan')
        ->assertRedirect(route('verification.notice'));
});

test('unverified coach is redirected to email verification when accessing subscription page', function () {
    $coach = User::factory()->coach()->unverified()->create();

    $this->actingAs($coach)
        ->get('/coach/subscription')
        ->assertRedirect(route('verification.notice'));
});

test('unverified coach is redirected to email verification when accessing subscription checkout', function () {
    $coach = User::factory()->coach()->unverified()->create();

    $this->actingAs($coach)
        ->get('/coach/subscription/checkout')
        ->assertRedirect(route('verification.notice'));
});

test('unverified coach is redirected to email verification when accessing subscription portal', function () {
    $coach = User::factory()->coach()->unverified()->create();

    $this->actingAs($coach)
        ->get('/coach/subscription/portal')
        ->assertRedirect(route('verification.notice'));
});

test('verified coach can access plan page', function () {
    $coach = User::factory()->coach()->create();

    $this->actingAs($coach)
        ->get('/coach/plan')
        ->assertSuccessful();
});
