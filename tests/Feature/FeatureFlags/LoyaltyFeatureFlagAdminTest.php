<?php

use App\Features\Loyalty;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\User;
use Laravel\Pennant\Feature;
use Livewire\Livewire;

test('admin can enable loyalty for a coach from the view page', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $coach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($admin);

    expect(Feature::for($coach)->active(Loyalty::class))->toBeFalse();

    Livewire::test(ViewUser::class, ['record' => $coach->getRouteKey()])
        ->callInfolistAction('loyalty_feature_status', 'toggle_loyalty');

    expect(Feature::for($coach->fresh())->active(Loyalty::class))->toBeTrue();
});

test('admin can disable loyalty for a coach from the view page', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $coach = User::factory()->create(['role' => 'coach']);
    Feature::for($coach)->activate(Loyalty::class);

    $this->actingAs($admin);

    Livewire::test(ViewUser::class, ['record' => $coach->getRouteKey()])
        ->callInfolistAction('loyalty_feature_status', 'toggle_loyalty');

    expect(Feature::for($coach->fresh())->active(Loyalty::class))->toBeFalse();
});
