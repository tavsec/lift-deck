<?php

use App\Models\User;

it('settings page passes subscription data to view for trial coach', function (): void {
    $coach = User::factory()->create([
        'role' => 'coach',
        'trial_ends_at' => now()->addDays(5),
        'is_free_access' => false,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.settings.edit'))
        ->assertOk()
        ->assertViewHas('isOnTrial', true)
        ->assertViewHas('clientCount', 0)
        ->assertViewHas('isInGracePeriod', false);
});
