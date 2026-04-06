<?php

use App\Models\User;

it('selecting basic plan redirects to stripe checkout for trial', function (): void {
    $coach = User::factory()->state([
        'role' => 'coach',
        'trial_ends_at' => null,
        'selected_plan' => null,
    ])->create();

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'basic'])
        ->assertRedirect(route('coach.subscription.checkout'));

    $coach->refresh();

    expect($coach->selected_plan)->toBe('basic');
    expect($coach->trial_ends_at)->toBeNull();
});
