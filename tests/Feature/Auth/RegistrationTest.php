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
