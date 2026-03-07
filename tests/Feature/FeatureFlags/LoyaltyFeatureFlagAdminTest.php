<?php

use App\Features\Loyalty;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use Laravel\Pennant\Feature;
use Livewire\Livewire;

test('admin can enable loyalty for a coach via edit page', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $coach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($admin);

    expect(Feature::for($coach)->active(Loyalty::class))->toBeFalse();

    Livewire::test(EditUser::class, ['record' => $coach->getRouteKey()])
        ->fillForm(['feature_loyalty' => true])
        ->call('save');

    expect(Feature::for($coach->fresh())->active(Loyalty::class))->toBeTrue();
});

test('admin can disable loyalty for a coach via edit page', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $coach = User::factory()->create(['role' => 'coach']);
    Feature::for($coach)->activate(Loyalty::class);

    $this->actingAs($admin);

    Livewire::test(EditUser::class, ['record' => $coach->getRouteKey()])
        ->fillForm(['feature_loyalty' => false])
        ->call('save');

    expect(Feature::for($coach->fresh())->active(Loyalty::class))->toBeFalse();
});
