<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->actingAs($this->coach);
});

test('coach can view branding page', function () {
    $this->get(route('coach.branding.edit'))
        ->assertOk()
        ->assertViewIs('coach.branding');
});

test('coach can update branding identity', function () {
    $this->put(route('coach.branding.update'), [
        'gym_name' => 'Iron Forge Gym',
        'description' => 'Best gym in town',
        'primary_color' => '#FF5733',
        'secondary_color' => '#33FF57',
        'onboarding_welcome_text' => 'Welcome to the team!',
        'welcome_email_text' => 'Thanks for joining us!',
        'fields' => [],
    ])->assertRedirect(route('coach.branding.edit'));

    $this->coach->refresh();
    expect($this->coach->gym_name)->toBe('Iron Forge Gym');
    expect($this->coach->description)->toBe('Best gym in town');
    expect($this->coach->primary_color)->toBe('#FF5733');
    expect($this->coach->secondary_color)->toBe('#33FF57');
});

test('coach can upload logo', function () {
    Storage::fake('public');

    $this->put(route('coach.branding.update'), [
        'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        'fields' => [],
    ])->assertRedirect(route('coach.branding.edit'));

    $this->coach->refresh();
    expect($this->coach->logo)->not->toBeNull();
    Storage::disk('public')->assertExists($this->coach->logo);
});

test('coach can remove logo', function () {
    Storage::fake('public');
    $this->coach->update(['logo' => 'logos/old.png']);

    $this->put(route('coach.branding.update'), [
        'remove_logo' => '1',
        'fields' => [],
    ])->assertRedirect(route('coach.branding.edit'));

    $this->coach->refresh();
    expect($this->coach->logo)->toBeNull();
});

test('coach can save onboarding fields', function () {
    $this->put(route('coach.branding.update'), [
        'fields' => [
            ['label' => 'Your goal?', 'type' => 'select', 'options' => "Lose weight\nBuild muscle", 'is_required' => '1'],
            ['label' => 'Tell us about yourself', 'type' => 'textarea', 'options' => '', 'is_required' => '0'],
        ],
    ])->assertRedirect(route('coach.branding.edit'));

    $this->coach->refresh();
    $fields = $this->coach->onboardingFields;
    expect($fields)->toHaveCount(2);
    expect($fields->first()->label)->toBe('Your goal?');
    expect($fields->first()->options)->toBe(['Lose weight', 'Build muscle']);
    expect($fields->first()->is_required)->toBeTrue();
    expect($fields->last()->label)->toBe('Tell us about yourself');
    expect($fields->last()->is_required)->toBeFalse();
});

test('coach can reorder onboarding fields by array position', function () {
    $this->put(route('coach.branding.update'), [
        'fields' => [
            ['label' => 'First', 'type' => 'text', 'options' => '', 'is_required' => '1'],
            ['label' => 'Second', 'type' => 'text', 'options' => '', 'is_required' => '1'],
        ],
    ])->assertRedirect(route('coach.branding.edit'));

    $fields = $this->coach->onboardingFields()->orderBy('order')->get();
    expect($fields->first()->label)->toBe('First');
    expect($fields->first()->order)->toBe(1);
    expect($fields->last()->label)->toBe('Second');
    expect($fields->last()->order)->toBe(2);
});

test('clients cannot access branding page', function () {
    $client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    $this->actingAs($client);

    $this->get(route('coach.branding.edit'))->assertRedirect(route('client.dashboard'));
});

test('branding validates color format', function () {
    $this->put(route('coach.branding.update'), [
        'primary_color' => 'not-a-color',
        'fields' => [],
    ])->assertSessionHasErrors('primary_color');
});
