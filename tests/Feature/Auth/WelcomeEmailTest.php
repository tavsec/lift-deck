<?php

use App\Mail\WelcomeClientMail;
use App\Models\ClientInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('welcome email is sent after client registration', function () {
    Mail::fake();

    $coach = User::factory()->coach()->create([
        'gym_name' => 'Iron Forge',
    ]);

    ClientInvitation::create([
        'coach_id' => $coach->id,
        'token' => 'TESTCODE',
        'expires_at' => now()->addDays(7),
    ]);

    $this->post(route('join.register'), [
        'code' => 'TESTCODE',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('client.welcome'));

    Mail::assertSent(WelcomeClientMail::class, function ($mail) {
        return $mail->hasTo('john@example.com');
    });
});

test('welcome email contains coach branding', function () {
    $coach = User::factory()->coach()->create([
        'gym_name' => 'Iron Forge',
        'welcome_email_text' => 'Glad to have you on board!',
        'primary_color' => '#FF5733',
    ]);

    $client = User::factory()->client()->create([
        'name' => 'Jane',
        'coach_id' => $coach->id,
    ]);

    $mail = new WelcomeClientMail($client, $coach);
    $rendered = $mail->render();

    expect($rendered)->toContain('Iron Forge');
    expect($rendered)->toContain('Glad to have you on board!');
});

test('welcome email uses default text when coach has no custom text', function () {
    $coach = User::factory()->coach()->create([
        'gym_name' => 'Iron Forge',
        'welcome_email_text' => null,
    ]);

    $client = User::factory()->client()->create([
        'name' => 'Jane',
        'coach_id' => $coach->id,
    ]);

    $mail = new WelcomeClientMail($client, $coach);
    $rendered = $mail->render();

    expect($rendered)->toContain('Iron Forge');
    expect($rendered)->toContain('excited to have you');
});
