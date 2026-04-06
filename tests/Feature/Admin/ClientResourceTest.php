<?php

use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\ViewClient;
use App\Models\TrackingMetric;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

describe('Client Resource', function () {
    it('lists only clients', function () {
        $clients = User::factory()->count(3)->create(['role' => 'client']);
        $coaches = User::factory()->count(2)->create(['role' => 'coach']);

        Livewire::test(ListClients::class)
            ->assertOk()
            ->assertCanSeeTableRecords($clients)
            ->assertCanNotSeeTableRecords($coaches);
    });

    it('can render the client view page', function () {
        $client = User::factory()->create(['role' => 'client']);

        Livewire::test(ViewClient::class, ['record' => $client->getRouteKey()])
            ->assertOk();
    });

    it('shows coach name on the client view page', function () {
        $coach = User::factory()->create(['role' => 'coach', 'name' => 'Coach John']);
        $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

        Livewire::test(ViewClient::class, ['record' => $client->getRouteKey()])
            ->assertSee('Coach John');
    });

    it('shows assigned tracking metrics on the client view page', function () {
        $coach = User::factory()->create(['role' => 'coach']);
        $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);
        $metric = TrackingMetric::factory()->create(['coach_id' => $coach->id, 'name' => 'Sleep Quality']);

        $client->assignedTrackingMetrics()->create([
            'tracking_metric_id' => $metric->id,
            'order' => 1,
        ]);

        Livewire::test(ViewClient::class, ['record' => $client->getRouteKey()])
            ->assertSee('Sleep Quality');
    });
});
