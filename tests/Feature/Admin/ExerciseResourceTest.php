<?php

use App\Filament\Resources\Exercises\Pages\CreateExercise;
use App\Filament\Resources\Exercises\Pages\EditExercise;
use App\Filament\Resources\Exercises\Pages\ListExercises;
use App\Models\Exercise;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

describe('Exercise Resource', function () {
    it('can render the list page with global exercises only', function () {
        $globalExercises = Exercise::factory()->count(3)->create(['coach_id' => null]);
        $coachExercises = Exercise::factory()->count(2)->create([
            'coach_id' => User::factory()->create(['role' => 'coach'])->id,
        ]);

        Livewire::test(ListExercises::class)
            ->assertOk()
            ->assertCanSeeTableRecords($globalExercises)
            ->assertCanNotSeeTableRecords($coachExercises);
    });

    it('can render the create page', function () {
        Livewire::test(CreateExercise::class)
            ->assertOk();
    });

    it('can create a global exercise', function () {
        $data = Exercise::factory()->make(['coach_id' => null]);

        Livewire::test(CreateExercise::class)
            ->fillForm([
                'name' => $data->name,
                'description' => $data->description,
                'muscle_group' => $data->muscle_group,
                'is_active' => true,
            ])
            ->call('create')
            ->assertNotified();

        $this->assertDatabaseHas(Exercise::class, [
            'name' => $data->name,
            'coach_id' => null,
        ]);
    });

    it('can render the edit page', function () {
        $exercise = Exercise::factory()->create(['coach_id' => null]);

        Livewire::test(EditExercise::class, ['record' => $exercise->getRouteKey()])
            ->assertOk();
    });

    it('can edit a global exercise', function () {
        $exercise = Exercise::factory()->create(['coach_id' => null, 'muscle_group' => 'chest']);

        Livewire::test(EditExercise::class, ['record' => $exercise->getRouteKey()])
            ->fillForm(['name' => 'Updated Name', 'muscle_group' => 'chest'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($exercise->fresh()->name)->toBe('Updated Name');
    });
});
