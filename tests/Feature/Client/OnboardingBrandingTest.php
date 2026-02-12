<?php

use App\Models\OnboardingField;
use App\Models\OnboardingResponse;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create([
        'onboarding_welcome_text' => 'Welcome to my gym!',
    ]);
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    $this->actingAs($this->client);
});

test('welcome page shows coach onboarding welcome text', function () {
    $this->get(route('client.welcome'))
        ->assertOk()
        ->assertSee('Welcome to my gym!');
});

test('welcome page shows default text when coach has no custom text', function () {
    $this->coach->update(['onboarding_welcome_text' => null]);

    $this->get(route('client.welcome'))
        ->assertOk()
        ->assertSee('set up your profile');
});

test('onboarding form renders dynamic fields', function () {
    OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'What is your goal?',
        'type' => 'select',
        'options' => ['Lose weight', 'Build muscle'],
        'order' => 1,
    ]);
    OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'Any injuries?',
        'type' => 'textarea',
        'order' => 2,
        'is_required' => false,
    ]);

    $this->get(route('client.onboarding'))
        ->assertOk()
        ->assertSee('What is your goal?')
        ->assertSee('Lose weight')
        ->assertSee('Any injuries?');
});

test('onboarding stores responses to dynamic fields', function () {
    $field1 = OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'Goal',
        'type' => 'select',
        'options' => ['Lose weight', 'Build muscle'],
        'is_required' => true,
        'order' => 1,
    ]);
    $field2 = OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'Notes',
        'type' => 'textarea',
        'is_required' => false,
        'order' => 2,
    ]);

    $this->post(route('client.onboarding.store'), [
        'fields' => [
            $field1->id => 'Lose weight',
            $field2->id => 'Bad knee',
        ],
    ])->assertRedirect(route('client.dashboard'));

    expect(OnboardingResponse::where('client_id', $this->client->id)->count())->toBe(2);
    expect(OnboardingResponse::where('client_id', $this->client->id)->where('onboarding_field_id', $field1->id)->first()->value)->toBe('Lose weight');
});

test('onboarding validates required fields', function () {
    $field = OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'Goal',
        'type' => 'text',
        'is_required' => true,
        'order' => 1,
    ]);

    $this->post(route('client.onboarding.store'), [
        'fields' => [
            $field->id => '',
        ],
    ])->assertSessionHasErrors("fields.{$field->id}");
});

test('onboarding marks profile as complete', function () {
    $field = OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'type' => 'text',
        'is_required' => true,
        'order' => 1,
    ]);

    $this->post(route('client.onboarding.store'), [
        'fields' => [$field->id => 'My answer'],
    ]);

    expect($this->client->clientProfile->onboarding_completed_at)->not->toBeNull();
});

test('onboarding works with no fields configured', function () {
    $this->get(route('client.onboarding'))
        ->assertOk();

    $this->post(route('client.onboarding.store'), [
        'fields' => [],
    ])->assertRedirect(route('client.dashboard'));
});
