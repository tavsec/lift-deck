<?php

use App\Models\OnboardingField;
use App\Models\User;

it('belongs to a coach', function () {
    $coach = User::factory()->create(['role' => 'coach']);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'What is your fitness goal?',
        'type' => 'text',
        'is_required' => true,
        'order' => 1,
    ]);

    expect($field->coach)
        ->toBeInstanceOf(User::class)
        ->id->toBe($coach->id);
});

it('casts options to array', function () {
    $coach = User::factory()->create(['role' => 'coach']);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Experience level',
        'type' => 'select',
        'options' => ['Beginner', 'Intermediate', 'Advanced'],
        'is_required' => true,
        'order' => 1,
    ]);

    $field->refresh();

    expect($field->options)->toBeArray()
        ->toContain('Beginner', 'Intermediate', 'Advanced');
});

it('casts is_required to boolean', function () {
    $coach = User::factory()->create(['role' => 'coach']);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Allergies',
        'type' => 'textarea',
        'is_required' => false,
        'order' => 2,
    ]);

    $field->refresh();

    expect($field->is_required)->toBeFalse()->toBeBool();
});

it('allows nullable options', function () {
    $coach = User::factory()->create(['role' => 'coach']);

    $field = OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Notes',
        'type' => 'text',
        'order' => 0,
    ]);

    $field->refresh();

    expect($field->options)->toBeNull();
});

it('is accessible via coach onboardingFields relationship', function () {
    $coach = User::factory()->create(['role' => 'coach']);

    OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Goal',
        'type' => 'text',
        'order' => 2,
    ]);

    OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Experience',
        'type' => 'select',
        'options' => ['Beginner', 'Advanced'],
        'order' => 1,
    ]);

    $fields = $coach->onboardingFields;

    expect($fields)->toHaveCount(2);
    // Ordered by 'order' column
    expect($fields->first()->label)->toBe('Experience');
    expect($fields->last()->label)->toBe('Goal');
});

it('cascades delete when coach is deleted', function () {
    $coach = User::factory()->create(['role' => 'coach']);

    OnboardingField::create([
        'coach_id' => $coach->id,
        'label' => 'Goal',
        'type' => 'text',
        'order' => 1,
    ]);

    expect(OnboardingField::query()->where('coach_id', $coach->id)->count())->toBe(1);

    $coach->delete();

    expect(OnboardingField::query()->where('coach_id', $coach->id)->count())->toBe(0);
});
