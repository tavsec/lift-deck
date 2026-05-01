<?php

use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create(['gym_name' => null]);
    $this->actingAs($this->coach);
});

test('branding notice is shown when gym_name is not set', function () {
    $this->get(route('coach.clients.index'))
        ->assertOk()
        ->assertSee(__('coach.clients.index.branding_notice'));
});

test('branding notice is not shown when gym_name is set', function () {
    $this->coach->update(['gym_name' => 'Power House Gym']);

    $this->get(route('coach.clients.index'))
        ->assertOk()
        ->assertDontSee(__('coach.clients.index.branding_notice'));
});

test('branding notice links to the branding edit page', function () {
    $this->get(route('coach.clients.index'))
        ->assertOk()
        ->assertSee(route('coach.branding.edit'));
});
