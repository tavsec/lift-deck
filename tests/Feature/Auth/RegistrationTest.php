<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new coach is redirected to plan selection after registration', function () {
    $response = $this->post('/register', [
        'name' => 'Test Coach',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('coach.plan'));
});

test('new coach has no trial_ends_at immediately after registration', function () {
    $this->post('/register', [
        'name' => 'Test Coach',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $coach = \App\Models\User::where('email', 'test@example.com')->first();

    expect($coach->trial_ends_at)->toBeNull();
});

test('gym_name and bio are saved when provided at registration', function () {
    $this->post('/register', [
        'email'                 => 'coach@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
        'gym_name'              => 'Iron Peak Fitness',
        'bio'                   => 'strength coaching',
    ]);

    $coach = \App\Models\User::where('email', 'coach@example.com')->first();

    expect($coach->gym_name)->toBe('Iron Peak Fitness')
        ->and($coach->bio)->toBe('strength coaching');
});

test('registration succeeds without gym_name and bio', function () {
    $response = $this->post('/register', [
        'email'                 => 'coach2@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('coach.plan'));
});

test('name defaults to email prefix when not provided', function () {
    $this->post('/register', [
        'email'                 => 'alex.johnson@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $coach = \App\Models\User::where('email', 'alex.johnson@example.com')->first();

    expect($coach->name)->toBe('alex.johnson');
});
