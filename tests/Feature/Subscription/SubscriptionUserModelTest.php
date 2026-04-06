<?php

use App\Models\User;

it('has is_free_access cast to boolean', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->create();
    expect($coach->is_free_access)->toBeTrue();
});

it('defaults is_free_access to false', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    expect($coach->is_free_access)->toBeFalse();
});

it('has the Billable trait', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    expect(method_exists($coach, 'subscriptions'))->toBeTrue();
    expect(method_exists($coach, 'onTrial'))->toBeTrue();
    expect(method_exists($coach, 'subscribed'))->toBeTrue();
});

it('stores selected_plan on the user model', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'selected_plan' => 'advanced',
    ]);

    $coach->refresh();

    expect($coach->selected_plan)->toBe('advanced');
});

it('selected_plan defaults to basic from factory', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create();

    expect($coach->selected_plan)->toBe('basic');
});
