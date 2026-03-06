<?php

use App\Features\Loyalty;
use App\Models\User;
use Laravel\Pennant\Feature;

test('loyalty feature defaults to inactive for new coaches', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);

    expect(Feature::for($coach)->active(Loyalty::class))->toBeFalse();
});
