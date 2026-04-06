<?php

use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\RelationManagers\ClientsRelationManager;
use App\Models\TrackingMetric;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

describe('Clients Relation Manager', function () {
    it('can list clients on the coach view page', function () {
        $coach = User::factory()->create(['role' => 'coach']);
        $clients = User::factory()->count(3)->create(['role' => 'client', 'coach_id' => $coach->id]);
        $otherClient = User::factory()->create(['role' => 'client']);

        Livewire::test(ClientsRelationManager::class, [
            'ownerRecord' => $coach,
            'pageClass' => ViewUser::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords($clients)
            ->assertCanNotSeeTableRecords([$otherClient]);
    });

    it('shows tracking metrics for each client', function () {
        $coach = User::factory()->create(['role' => 'coach']);
        $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);
        $metric = TrackingMetric::factory()->create(['coach_id' => $coach->id, 'name' => 'Body Weight']);

        $client->assignedTrackingMetrics()->create([
            'tracking_metric_id' => $metric->id,
            'order' => 1,
        ]);

        Livewire::test(ClientsRelationManager::class, [
            'ownerRecord' => $coach,
            'pageClass' => ViewUser::class,
        ])
            ->assertOk()
            ->assertSee('Body Weight');
    });
});
