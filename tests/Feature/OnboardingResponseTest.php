<?php

use App\Models\OnboardingField;
use App\Models\OnboardingResponse;
use App\Models\User;

it('belongs to a client', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'What is your fitness goal?',
        'type' => 'text',
        'order' => 1,
    ]);

    $response = OnboardingResponse::create([
        'client_id' => $client->id,
        'onboarding_field_id' => $field->id,
        'value' => 'Lose weight',
    ]);

    expect($response->client)
        ->toBeInstanceOf(User::class)
        ->id->toBe($client->id);
});

it('belongs to an onboarding field', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Experience level',
        'type' => 'select',
        'options' => ['Beginner', 'Intermediate', 'Advanced'],
        'order' => 1,
    ]);

    $response = OnboardingResponse::create([
        'client_id' => $client->id,
        'onboarding_field_id' => $field->id,
        'value' => 'Beginner',
    ]);

    expect($response->onboardingField)
        ->toBeInstanceOf(OnboardingField::class)
        ->id->toBe($field->id);
});

it('allows nullable value', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Notes',
        'type' => 'textarea',
        'is_required' => false,
        'order' => 1,
    ]);

    $response = OnboardingResponse::create([
        'client_id' => $client->id,
        'onboarding_field_id' => $field->id,
    ]);

    $response->refresh();

    expect($response->value)->toBeNull();
});

it('enforces unique client and field combination', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Goal',
        'type' => 'text',
        'order' => 1,
    ]);

    OnboardingResponse::create([
        'client_id' => $client->id,
        'onboarding_field_id' => $field->id,
        'value' => 'Lose weight',
    ]);

    OnboardingResponse::create([
        'client_id' => $client->id,
        'onboarding_field_id' => $field->id,
        'value' => 'Build muscle',
    ]);
})->throws(\Illuminate\Database\UniqueConstraintViolationException::class);

it('is accessible via client onboardingResponses relationship', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $field1 = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Goal',
        'type' => 'text',
        'order' => 1,
    ]);

    $field2 = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Experience',
        'type' => 'select',
        'options' => ['Beginner', 'Advanced'],
        'order' => 2,
    ]);

    OnboardingResponse::create([
        'client_id' => $client->id,
        'onboarding_field_id' => $field1->id,
        'value' => 'Lose weight',
    ]);

    OnboardingResponse::create([
        'client_id' => $client->id,
        'onboarding_field_id' => $field2->id,
        'value' => 'Beginner',
    ]);

    expect($client->onboardingResponses)->toHaveCount(2);
});

it('is accessible via field responses relationship', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $client1 = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);
    $client2 = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Goal',
        'type' => 'text',
        'order' => 1,
    ]);

    OnboardingResponse::create([
        'client_id' => $client1->id,
        'onboarding_field_id' => $field->id,
        'value' => 'Lose weight',
    ]);

    OnboardingResponse::create([
        'client_id' => $client2->id,
        'onboarding_field_id' => $field->id,
        'value' => 'Build muscle',
    ]);

    expect($field->responses)->toHaveCount(2);
});

it('cascades delete when client is deleted', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Goal',
        'type' => 'text',
        'order' => 1,
    ]);

    OnboardingResponse::create([
        'client_id' => $client->id,
        'onboarding_field_id' => $field->id,
        'value' => 'Lose weight',
    ]);

    expect(OnboardingResponse::query()->where('client_id', $client->id)->count())->toBe(1);

    $client->delete();

    expect(OnboardingResponse::query()->where('client_id', $client->id)->count())->toBe(0);
});

it('cascades delete when onboarding field is deleted', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Goal',
        'type' => 'text',
        'order' => 1,
    ]);

    OnboardingResponse::create([
        'client_id' => $client->id,
        'onboarding_field_id' => $field->id,
        'value' => 'Lose weight',
    ]);

    expect(OnboardingResponse::query()->where('onboarding_field_id', $field->id)->count())->toBe(1);

    $field->delete();

    expect(OnboardingResponse::query()->where('onboarding_field_id', $field->id)->count())->toBe(0);
});
