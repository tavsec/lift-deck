# Admin Exercises CRUD & Coach Clients/Metrics Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add global exercises CRUD to the admin Filament panel, and enhance the admin coach view to list each coach's clients with their assigned tracking metrics plus a detailed client view.

**Architecture:** Two new Filament resources (`ExerciseResource` scoped to `coach_id IS NULL`, `ClientResource` scoped to `role = client`) plus a `ClientsRelationManager` added to the existing `UserResource` (coaches). All follow existing resource patterns: resource class + pages + schemas + tables subfolders. Tests mirror `LoyaltyFilamentResourcesTest.php` style.

**Tech Stack:** Filament v5, Laravel 12, Pest 4, Livewire 4

---

## Task 1: ExerciseResource — form schema

**Files:**
- Create: `app/Filament/Resources/Exercises/Schemas/ExerciseForm.php`

**Step 1: Write the failing test**

Create `tests/Feature/Admin/ExerciseResourceTest.php`:

```php
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
        $exercise = Exercise::factory()->create(['coach_id' => null]);

        Livewire::test(EditExercise::class, ['record' => $exercise->getRouteKey()])
            ->fillForm(['name' => 'Updated Name'])
            ->call('save')
            ->assertNotified();

        expect($exercise->fresh()->name)->toBe('Updated Name');
    });
});
```

**Step 2: Run test to verify it fails**

```bash
php artisan test --compact tests/Feature/Admin/ExerciseResourceTest.php
```
Expected: FAIL — class `ListExercises` not found.

**Step 3: Create the form schema**

Create `app/Filament/Resources/Exercises/Schemas/ExerciseForm.php`:

```php
<?php

namespace App\Filament\Resources\Exercises\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExerciseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description'),
                Select::make('muscle_group')
                    ->required()
                    ->options([
                        'chest' => 'Chest',
                        'back' => 'Back',
                        'shoulders' => 'Shoulders',
                        'biceps' => 'Biceps',
                        'triceps' => 'Triceps',
                        'forearms' => 'Forearms',
                        'core' => 'Core',
                        'quadriceps' => 'Quadriceps',
                        'hamstrings' => 'Hamstrings',
                        'glutes' => 'Glutes',
                        'calves' => 'Calves',
                        'full_body' => 'Full Body',
                        'cardio' => 'Cardio',
                    ]),
                TextInput::make('video_url')
                    ->url()
                    ->maxLength(500),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
```

---

## Task 2: ExerciseResource — table schema

**Files:**
- Create: `app/Filament/Resources/Exercises/Tables/ExercisesTable.php`

**Step 1: Create the table schema**

```php
<?php

namespace App\Filament\Resources\Exercises\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExercisesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('muscle_group')
                    ->badge()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

---

## Task 3: ExerciseResource — pages

**Files:**
- Create: `app/Filament/Resources/Exercises/Pages/ListExercises.php`
- Create: `app/Filament/Resources/Exercises/Pages/CreateExercise.php`
- Create: `app/Filament/Resources/Exercises/Pages/EditExercise.php`

**Step 1: Create ListExercises**

```php
<?php

namespace App\Filament\Resources\Exercises\Pages;

use App\Filament\Resources\Exercises\ExerciseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExercises extends ListRecords
{
    protected static string $resource = ExerciseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
```

**Step 2: Create CreateExercise**

```php
<?php

namespace App\Filament\Resources\Exercises\Pages;

use App\Filament\Resources\Exercises\ExerciseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExercise extends CreateRecord
{
    protected static string $resource = ExerciseResource::class;
}
```

**Step 3: Create EditExercise**

```php
<?php

namespace App\Filament\Resources\Exercises\Pages;

use App\Filament\Resources\Exercises\ExerciseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExercise extends EditRecord
{
    protected static string $resource = ExerciseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
```

---

## Task 4: ExerciseResource — resource class

**Files:**
- Create: `app/Filament/Resources/Exercises/ExerciseResource.php`

**Step 1: Create the resource**

```php
<?php

namespace App\Filament\Resources\Exercises;

use App\Filament\Resources\Exercises\Pages\CreateExercise;
use App\Filament\Resources\Exercises\Pages\EditExercise;
use App\Filament\Resources\Exercises\Pages\ListExercises;
use App\Filament\Resources\Exercises\Schemas\ExerciseForm;
use App\Filament\Resources\Exercises\Tables\ExercisesTable;
use App\Models\Exercise;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExerciseResource extends Resource
{
    protected static ?string $model = Exercise::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    public static function form(Schema $schema): Schema
    {
        return ExerciseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExercisesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExercises::route('/'),
            'create' => CreateExercise::route('/create'),
            'edit' => EditExercise::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNull('coach_id');
    }
}
```

**Step 2: Run the exercise tests**

```bash
php artisan test --compact tests/Feature/Admin/ExerciseResourceTest.php
```
Expected: All PASS.

**Step 3: Format**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 4: Commit**

```bash
git add app/Filament/Resources/Exercises/ tests/Feature/Admin/ExerciseResourceTest.php
git commit -m "feat: add global exercises CRUD resource to admin panel"
```

---

## Task 5: ClientsRelationManager on UserResource (coaches)

**Files:**
- Create: `app/Filament/Resources/Users/RelationManagers/ClientsRelationManager.php`
- Modify: `app/Filament/Resources/Users/UserResource.php`

**Step 1: Write the failing test**

Create `tests/Feature/Admin/CoachClientsRelationManagerTest.php`:

```php
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
```

**Step 2: Run test to verify it fails**

```bash
php artisan test --compact tests/Feature/Admin/CoachClientsRelationManagerTest.php
```
Expected: FAIL — class `ClientsRelationManager` not found.

**Step 3: Create the relation manager**

```php
<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('assignedTrackingMetrics.trackingMetric.name')
                    ->label('Tracking Metrics')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->placeholder('None'),
            ])
            ->filters([]);
    }
}
```

**Step 4: Register the relation manager on UserResource**

Modify `app/Filament/Resources/Users/UserResource.php` — change `getRelations()`:

```php
public static function getRelations(): array
{
    return [
        \App\Filament\Resources\Users\RelationManagers\ClientsRelationManager::class,
    ];
}
```

**Step 5: Run the tests**

```bash
php artisan test --compact tests/Feature/Admin/CoachClientsRelationManagerTest.php
```
Expected: All PASS.

**Step 6: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/Resources/Users/ tests/Feature/Admin/CoachClientsRelationManagerTest.php
git commit -m "feat: add clients relation manager to admin coach view"
```

---

## Task 6: ClientResource — detailed client view

**Files:**
- Create: `app/Filament/Resources/Clients/ClientResource.php`
- Create: `app/Filament/Resources/Clients/Pages/ListClients.php`
- Create: `app/Filament/Resources/Clients/Pages/ViewClient.php`
- Create: `app/Filament/Resources/Clients/Schemas/ClientInfolist.php`
- Create: `app/Filament/Resources/Clients/Tables/ClientsTable.php`

**Step 1: Write the failing test**

Create `tests/Feature/Admin/ClientResourceTest.php`:

```php
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
```

**Step 2: Run test to verify it fails**

```bash
php artisan test --compact tests/Feature/Admin/ClientResourceTest.php
```
Expected: FAIL — class `ListClients` not found.

**Step 3: Create ClientInfolist**

```php
<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->copyable(),
                TextEntry::make('coach.name')
                    ->label('Coach')
                    ->placeholder('No coach assigned'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('is_track_only')
                    ->label('Track-only client')
                    ->badge()
                    ->state(fn ($record) => $record->is_track_only ? 'Yes' : 'No')
                    ->color(fn (string $state): string => $state === 'Yes' ? 'warning' : 'success'),
                Section::make('Tracking Metrics')
                    ->schema([
                        RepeatableEntry::make('assignedTrackingMetrics')
                            ->label('')
                            ->schema([
                                TextEntry::make('trackingMetric.name')
                                    ->label('Metric'),
                                TextEntry::make('trackingMetric.type')
                                    ->label('Type')
                                    ->badge(),
                                TextEntry::make('trackingMetric.unit')
                                    ->label('Unit')
                                    ->placeholder('-'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
```

**Step 4: Create ClientsTable**

```php
<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('coach.name')
                    ->label('Coach')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('assignedTrackingMetrics_count')
                    ->label('Metrics')
                    ->counts('assignedTrackingMetrics')
                    ->badge(),
                IconColumn::make('is_track_only')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
```

**Step 5: Create pages**

`app/Filament/Resources/Clients/Pages/ListClients.php`:

```php
<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;
}
```

`app/Filament/Resources/Clients/Pages/ViewClient.php`:

```php
<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;
}
```

**Step 6: Create ClientResource**

```php
<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\ViewClient;
use App\Filament\Resources\Clients\Schemas\ClientInfolist;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Clients';

    protected static ?string $slug = 'clients';

    public static function infolist(Schema $schema): Schema
    {
        return ClientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'view' => ViewClient::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'client');
    }
}
```

**Step 7: Run all new tests**

```bash
php artisan test --compact tests/Feature/Admin/ClientResourceTest.php tests/Feature/Admin/CoachClientsRelationManagerTest.php tests/Feature/Admin/ExerciseResourceTest.php
```
Expected: All PASS.

**Step 8: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/Resources/Clients/ tests/Feature/Admin/ClientResourceTest.php
git commit -m "feat: add admin client resource with tracking metrics detail view"
```

---

## Final verification

Run the full test suite to make sure nothing is broken:

```bash
php artisan test --compact
```

Check that the 9 pre-existing failing tests (Auth/Profile) are the only failures.
