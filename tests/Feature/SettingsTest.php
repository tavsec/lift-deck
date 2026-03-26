<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create([
        'coach_id' => $this->coach->id,
    ]);
});

// --- Coach ---

test('coach can view settings page', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.settings.edit'))
        ->assertOk()
        ->assertViewIs('coach.settings.edit');
});

test('coach can update profile', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.settings.update'), [
            'name' => 'Jane Coach',
            'email' => 'jane@example.com',
            'phone' => '+1 555-0100',
            'bio' => 'Certified personal trainer.',
        ])
        ->assertRedirect(route('coach.settings.edit'));

    $this->coach->refresh();
    expect($this->coach->name)->toBe('Jane Coach');
    expect($this->coach->email)->toBe('jane@example.com');
    expect($this->coach->phone)->toBe('+1 555-0100');
    expect($this->coach->bio)->toBe('Certified personal trainer.');
});

test('coach can upload avatar', function () {
    Storage::fake();

    $this->actingAs($this->coach)
        ->put(route('coach.settings.update'), [
            'name' => $this->coach->name,
            'email' => $this->coach->email,
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 200, 200),
        ])
        ->assertRedirect(route('coach.settings.edit'));

    $this->coach->refresh();
    expect($this->coach->getRawOriginal('avatar'))->not->toBeNull();
    Storage::assertExists($this->coach->getRawOriginal('avatar'));
});

test('coach can remove avatar', function () {
    Storage::fake();
    $this->coach->update(['avatar' => 'avatars/old.jpg']);

    $this->actingAs($this->coach)
        ->put(route('coach.settings.update'), [
            'name' => $this->coach->name,
            'email' => $this->coach->email,
            'remove_avatar' => '1',
        ])
        ->assertRedirect(route('coach.settings.edit'));

    $this->coach->refresh();
    expect($this->coach->getRawOriginal('avatar'))->toBeNull();
});

test('coach can update password', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.settings.password'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertRedirect(route('coach.settings.edit'));

    expect(Hash::check('new-password-123', $this->coach->fresh()->password))->toBeTrue();
});

test('coach cannot update password with wrong current password', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.settings.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasErrors('current_password');
});

test('coach settings update requires valid email', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.settings.update'), [
            'name' => 'Jane',
            'email' => 'not-an-email',
        ])
        ->assertSessionHasErrors('email');
});

// --- Client ---

test('client can view settings page', function () {
    $this->actingAs($this->client)
        ->get(route('client.settings.edit'))
        ->assertOk()
        ->assertViewIs('client.settings.edit');
});

test('client can update profile', function () {
    $this->actingAs($this->client)
        ->put(route('client.settings.update'), [
            'name' => 'John Client',
            'email' => 'john@example.com',
            'phone' => '+1 555-0200',
            'bio' => 'Fitness enthusiast.',
        ])
        ->assertRedirect(route('client.settings.edit'));

    $this->client->refresh();
    expect($this->client->name)->toBe('John Client');
    expect($this->client->email)->toBe('john@example.com');
    expect($this->client->phone)->toBe('+1 555-0200');
    expect($this->client->bio)->toBe('Fitness enthusiast.');
});

test('client can update password', function () {
    $this->actingAs($this->client)
        ->put(route('client.settings.password'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertRedirect(route('client.settings.edit'));

    expect(Hash::check('new-password-123', $this->client->fresh()->password))->toBeTrue();
});

test('guest cannot access coach settings', function () {
    $this->get(route('coach.settings.edit'))->assertRedirect(route('login'));
});

test('guest cannot access client settings', function () {
    $this->get(route('client.settings.edit'))->assertRedirect(route('login'));
});

// --- Cross-role access ---

test('coach cannot access client settings page', function () {
    $this->actingAs($this->coach)
        ->get(route('client.settings.edit'))
        ->assertRedirect(route('coach.dashboard'));
});

test('client cannot access coach settings page', function () {
    $this->actingAs($this->client)
        ->get(route('coach.settings.edit'))
        ->assertRedirect(route('client.dashboard'));
});
