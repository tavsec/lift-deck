# Exercise Reordering & Previous Workout Data — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Allow clients to reorder exercises while logging workouts and show them their previous session data for each exercise.

**Architecture:** Two purely additive features on the client logging form. Exercise reordering is client-side only (SortableJS + Alpine.js arrow buttons). Previous workout data is fetched from the backend via existing controller methods and a new query pattern. No new DB tables or migrations.

**Tech Stack:** Alpine.js, SortableJS, Laravel/Eloquent, Pest, Tailwind CSS

---

### Task 1: Create Missing Factories

Several models used in tests lack factories. Create them first so test setup is clean.

**Files:**
- Create: `database/factories/ExerciseFactory.php`
- Create: `database/factories/ProgramFactory.php`
- Create: `database/factories/ProgramWorkoutFactory.php`
- Create: `database/factories/WorkoutExerciseFactory.php`
- Create: `database/factories/ClientProgramFactory.php`
- Modify: `app/Models/Exercise.php` — add `HasFactory` trait
- Modify: `app/Models/Program.php` — add `HasFactory` trait
- Modify: `app/Models/ProgramWorkout.php` — add `HasFactory` trait
- Modify: `app/Models/WorkoutExercise.php` — add `HasFactory` trait
- Modify: `app/Models/ClientProgram.php` — add `HasFactory` trait

**Step 1: Create factories using artisan**

```bash
php artisan make:factory ExerciseFactory --no-interaction
php artisan make:factory ProgramFactory --no-interaction
php artisan make:factory ProgramWorkoutFactory --no-interaction
php artisan make:factory WorkoutExerciseFactory --no-interaction
php artisan make:factory ClientProgramFactory --no-interaction
```

**Step 2: Implement factory definitions**

`database/factories/ExerciseFactory.php`:
```php
public function definition(): array
{
    return [
        'name' => fake()->words(2, true),
        'description' => fake()->optional()->sentence(),
        'muscle_group' => fake()->randomElement(['chest', 'back', 'shoulders', 'legs', 'arms', 'core']),
        'video_url' => null,
        'coach_id' => User::factory()->create(['role' => 'coach'])->id,
        'is_active' => true,
    ];
}
```

`database/factories/ProgramFactory.php`:
```php
public function definition(): array
{
    return [
        'coach_id' => User::factory()->create(['role' => 'coach'])->id,
        'name' => fake()->words(3, true),
        'description' => fake()->optional()->sentence(),
        'duration_weeks' => fake()->numberBetween(4, 12),
        'type' => fake()->randomElement(['strength', 'hypertrophy', 'fat_loss', 'general']),
        'is_template' => false,
    ];
}
```

`database/factories/ProgramWorkoutFactory.php`:
```php
public function definition(): array
{
    return [
        'program_id' => Program::factory(),
        'name' => fake()->words(2, true),
        'day_number' => fake()->numberBetween(1, 7),
        'notes' => fake()->optional()->sentence(),
        'order' => 0,
    ];
}
```

`database/factories/WorkoutExerciseFactory.php`:
```php
public function definition(): array
{
    return [
        'program_workout_id' => ProgramWorkout::factory(),
        'exercise_id' => Exercise::factory(),
        'sets' => fake()->numberBetween(3, 5),
        'reps' => (string) fake()->numberBetween(6, 12),
        'rest_seconds' => fake()->randomElement([60, 90, 120]),
        'notes' => fake()->optional()->sentence(),
        'order' => 0,
    ];
}
```

`database/factories/ClientProgramFactory.php`:
```php
public function definition(): array
{
    return [
        'client_id' => User::factory()->create(['role' => 'client'])->id,
        'program_id' => Program::factory(),
        'started_at' => now(),
        'completed_at' => null,
        'status' => 'active',
    ];
}
```

**Step 3: Add `HasFactory` trait to models that lack it**

Add `use HasFactory;` (with the appropriate import) to: `Exercise`, `Program`, `ProgramWorkout`, `WorkoutExercise`, `ClientProgram`.

**Step 4: Run existing tests to ensure nothing is broken**

```bash
php artisan test --compact
```

Expected: All existing tests pass.

**Step 5: Commit**

```bash
git add database/factories/ app/Models/
git commit -m "feat: add missing factories for Exercise, Program, ProgramWorkout, WorkoutExercise, ClientProgram"
```

---

### Task 2: Previous Workout Data — Backend for Program Workouts

Add previous set data to the `LogController::create()` method.

**Files:**
- Modify: `app/Http/Controllers/Client/LogController.php:27-58` (the `create` method)
- Test: `tests/Feature/Client/WorkoutLogTest.php`

**Step 1: Write the failing tests**

Create `tests/Feature/Client/WorkoutLogTest.php`:

```php
<?php

use App\Models\ClientProgram;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WorkoutExercise;
use App\Models\WorkoutLog;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);

    $this->program = Program::factory()->create(['coach_id' => $this->coach->id]);
    $this->clientProgram = ClientProgram::factory()->create([
        'client_id' => $this->client->id,
        'program_id' => $this->program->id,
        'status' => 'active',
    ]);

    $this->workout = ProgramWorkout::factory()->create(['program_id' => $this->program->id]);

    $this->exercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);
    $this->workoutExercise = WorkoutExercise::factory()->create([
        'program_workout_id' => $this->workout->id,
        'exercise_id' => $this->exercise->id,
        'sets' => 3,
        'reps' => '10',
        'order' => 0,
    ]);
});

it('shows the log form for a program workout', function () {
    $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout))
        ->assertOk()
        ->assertSee($this->workout->name);
});

it('includes previous set data from the last log of the same workout', function () {
    $previousLog = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'client_program_id' => $this->clientProgram->id,
        'program_workout_id' => $this->workout->id,
        'completed_at' => now()->subDay(),
    ]);

    ExerciseLog::factory()->create([
        'workout_log_id' => $previousLog->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 1,
        'weight' => 80.00,
        'reps' => 10,
    ]);
    ExerciseLog::factory()->create([
        'workout_log_id' => $previousLog->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 2,
        'weight' => 85.00,
        'reps' => 8,
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout));

    $response->assertOk();

    // The previous_sets data is embedded in the Alpine.js JSON
    $response->assertSee('80');
    $response->assertSee('85');
});

it('falls back to any previous log when exercise was not in the last workout log', function () {
    // Create a log for a DIFFERENT workout that has this exercise
    $otherWorkout = ProgramWorkout::factory()->create(['program_id' => $this->program->id]);
    $otherLog = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'client_program_id' => $this->clientProgram->id,
        'program_workout_id' => $otherWorkout->id,
        'completed_at' => now()->subDays(2),
    ]);

    ExerciseLog::factory()->create([
        'workout_log_id' => $otherLog->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 1,
        'weight' => 70.00,
        'reps' => 12,
    ]);

    $response = $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout));

    $response->assertOk();
    $response->assertSee('70');
});

it('returns empty previous_sets when there is no history', function () {
    $response = $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout));

    $response->assertOk();
    // No previous data markers should appear
    $response->assertDontSee('Last session');
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact --filter=WorkoutLogTest
```

Expected: The test for previous set data fails (previous_sets not yet passed to view).

**Step 3: Implement previous data lookup in `LogController::create()`**

Modify `app/Http/Controllers/Client/LogController.php` — the `create()` method. After loading `$workout->exercises`, add:

```php
// Find the most recent log for this same workout
$previousLog = WorkoutLog::where('client_id', $user->id)
    ->where('program_workout_id', $workout->id)
    ->latest('completed_at')
    ->first();

$previousSets = collect();
if ($previousLog) {
    $previousSets = $previousLog->exerciseLogs
        ->groupBy('exercise_id')
        ->map(fn ($logs) => $logs->sortBy('set_number')->values()->map(fn ($log) => [
            'weight' => $log->weight,
            'reps' => $log->reps,
        ])->all());
}

// Pre-build exercise data for Alpine.js — update the map to include previous_sets
$exercisesData = $workout->exercises->map(function ($we) use ($user, $previousSets) {
    $prev = $previousSets->get($we->exercise_id);

    // Fallback: if no previous data from this workout, check any workout
    if (! $prev) {
        $lastLog = ExerciseLog::where('exercise_id', $we->exercise_id)
            ->whereHas('workoutLog', fn ($q) => $q->where('client_id', $user->id))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('workout_log_id')
            ->first();

        if ($lastLog) {
            $prev = $lastLog->sortBy('set_number')->values()->map(fn ($log) => [
                'weight' => $log->weight,
                'reps' => $log->reps,
            ])->all();
        }
    }

    return [
        'workout_exercise_id' => $we->id,
        'exercise_id' => $we->exercise_id,
        'name' => $we->exercise->name,
        'muscle_group' => $we->exercise->muscle_group,
        'prescribed_sets' => $we->sets,
        'prescribed_reps' => $we->reps,
        'previous_sets' => $prev ?? [],
        'sets' => collect(range(1, $we->sets))->map(fn ($i) => [
            'weight' => 0,
            'reps' => 0,
        ])->values()->all(),
    ];
})->values()->all();
```

**Step 4: Run tests to verify they pass**

```bash
php artisan test --compact --filter=WorkoutLogTest
```

Expected: All pass.

**Step 5: Commit**

```bash
git add app/Http/Controllers/Client/LogController.php tests/Feature/Client/WorkoutLogTest.php
git commit -m "feat: add previous workout data to program workout logging form"
```

---

### Task 3: Previous Workout Data — Exercises Endpoint

Extend the `/client/log/exercises` JSON endpoint to include `previous_sets` per exercise.

**Files:**
- Modify: `app/Http/Controllers/Client/LogController.php:74-93` (the `exercises` method)
- Test: `tests/Feature/Client/WorkoutLogTest.php` (add tests)

**Step 1: Write the failing test**

Add to `tests/Feature/Client/WorkoutLogTest.php`:

```php
it('returns exercises with previous set data from the JSON endpoint', function () {
    // Create a previous log with this exercise
    $log = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'client_program_id' => $this->clientProgram->id,
        'program_workout_id' => $this->workout->id,
        'completed_at' => now()->subDay(),
    ]);

    ExerciseLog::factory()->create([
        'workout_log_id' => $log->id,
        'exercise_id' => $this->exercise->id,
        'set_number' => 1,
        'weight' => 60.00,
        'reps' => 12,
    ]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.log.exercises'));

    $response->assertOk();

    $exerciseData = collect($response->json())->firstWhere('id', $this->exercise->id);
    expect($exerciseData)->not->toBeNull();
    expect($exerciseData['previous_sets'])->toHaveCount(1);
    expect($exerciseData['previous_sets'][0]['weight'])->toBe('60.00');
    expect($exerciseData['previous_sets'][0]['reps'])->toBe(12);
});

it('returns empty previous_sets for exercises with no history', function () {
    $newExercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.log.exercises'));

    $response->assertOk();

    $exerciseData = collect($response->json())->firstWhere('id', $newExercise->id);
    expect($exerciseData)->not->toBeNull();
    expect($exerciseData['previous_sets'])->toBe([]);
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact --filter=WorkoutLogTest
```

Expected: New tests fail (endpoint doesn't return `previous_sets`).

**Step 3: Implement in `LogController::exercises()`**

Replace the `exercises()` method:

```php
public function exercises(): JsonResponse
{
    $user = auth()->user();
    $coachId = $user->coach_id;

    if (! $coachId) {
        return response()->json([]);
    }

    $exercises = Exercise::where('is_active', true)
        ->where(function ($query) use ($coachId) {
            $query->where('coach_id', $coachId)
                ->orWhereNull('coach_id');
        })
        ->orderBy('muscle_group')
        ->orderBy('name')
        ->get(['id', 'name', 'muscle_group']);

    // Get the most recent workout_log_id per exercise for this client
    $lastLogIds = ExerciseLog::query()
        ->selectRaw('exercise_id, MAX(workout_log_id) as last_log_id')
        ->whereHas('workoutLog', fn ($q) => $q->where('client_id', $user->id))
        ->whereIn('exercise_id', $exercises->pluck('id'))
        ->groupBy('exercise_id')
        ->pluck('last_log_id', 'exercise_id');

    $previousSets = collect();
    if ($lastLogIds->isNotEmpty()) {
        $previousSets = ExerciseLog::query()
            ->whereIn('workout_log_id', $lastLogIds->values())
            ->whereIn('exercise_id', $lastLogIds->keys())
            ->where(function ($q) use ($lastLogIds) {
                foreach ($lastLogIds as $exerciseId => $logId) {
                    $q->orWhere(function ($q2) use ($exerciseId, $logId) {
                        $q2->where('exercise_id', $exerciseId)
                            ->where('workout_log_id', $logId);
                    });
                }
            })
            ->orderBy('set_number')
            ->get()
            ->groupBy('exercise_id')
            ->map(fn ($logs) => $logs->map(fn ($log) => [
                'weight' => $log->weight,
                'reps' => $log->reps,
            ])->values()->all());
    }

    $result = $exercises->map(fn ($exercise) => [
        'id' => $exercise->id,
        'name' => $exercise->name,
        'muscle_group' => $exercise->muscle_group,
        'previous_sets' => $previousSets->get($exercise->id, []),
    ]);

    return response()->json($result);
}
```

**Step 4: Run tests to verify they pass**

```bash
php artisan test --compact --filter=WorkoutLogTest
```

Expected: All pass.

**Step 5: Commit**

```bash
git add app/Http/Controllers/Client/LogController.php tests/Feature/Client/WorkoutLogTest.php
git commit -m "feat: include previous set data in exercises JSON endpoint"
```

---

### Task 4: Install SortableJS

**Files:**
- Modify: `package.json`
- Modify: `resources/js/app.js`

**Step 1: Install sortablejs**

```bash
npm install sortablejs
```

**Step 2: Import in app.js**

Add to `resources/js/app.js`:

```js
import Sortable from 'sortablejs';
window.Sortable = Sortable;
```

**Step 3: Build to verify no errors**

```bash
npm run build
```

Expected: Build succeeds.

**Step 4: Commit**

```bash
git add package.json package-lock.json resources/js/app.js
git commit -m "feat: install and configure SortableJS"
```

---

### Task 5: Exercise Reordering UI + Previous Data Display

Add drag-and-drop handles, up/down arrow buttons, and "Last session" display to the logging form.

**Files:**
- Modify: `resources/views/client/log-workout.blade.php`

**Step 1: Add SortableJS initialization and reorder methods to Alpine component**

In the `workoutLogger()` function, add after `exercisesLoaded: false,`:

```js
initSortable() {
    this.$nextTick(() => {
        const container = this.$refs.exerciseList;
        if (!container) return;
        Sortable.create(container, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: (evt) => {
                const item = this.exercises.splice(evt.oldIndex, 1)[0];
                this.exercises.splice(evt.newIndex, 0, item);
            },
        });
    });
},

moveExerciseUp(index) {
    if (index <= 0) return;
    const temp = this.exercises[index];
    this.exercises.splice(index, 1);
    this.exercises.splice(index - 1, 0, temp);
},

moveExerciseDown(index) {
    if (index >= this.exercises.length - 1) return;
    const temp = this.exercises[index];
    this.exercises.splice(index, 1);
    this.exercises.splice(index + 1, 0, temp);
},
```

**Step 2: Add `x-ref="exerciseList"` and `x-init="initSortable()"` to the exercises container**

Change the `<template x-for>` wrapper. Wrap the exercise cards in a `<div>` with ref and init:

```html
<div x-ref="exerciseList" x-init="initSortable()">
    <template x-for="(exercise, exerciseIndex) in exercises" :key="exercise.exercise_id">
        <!-- exercise card content -->
    </template>
</div>
```

Note: Change the `:key` from `exerciseIndex` to `exercise.exercise_id` so Alpine correctly tracks items during reorder.

**Step 3: Add drag handle and arrow buttons to each exercise card header**

Replace the current header `<div class="flex items-center justify-between">` with:

```html
<div class="flex items-center justify-between">
    <div class="flex items-center gap-2">
        <!-- Drag Handle -->
        <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-gray-600 touch-none">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-base font-semibold text-gray-900" x-text="exercise.name"></h3>
            <p class="text-xs text-gray-500" x-show="exercise.prescribed_sets">
                Prescribed: <span x-text="exercise.prescribed_sets"></span> sets &times; <span x-text="exercise.prescribed_reps"></span> reps
            </p>
        </div>
    </div>
    <div class="flex items-center gap-1">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600" x-text="exercise.muscle_group.replace('_', ' ')"></span>
        <!-- Move Up -->
        <button type="button" @click="moveExerciseUp(exerciseIndex)" :disabled="exerciseIndex === 0"
            class="p-1 text-gray-400 hover:text-gray-600 rounded hover:bg-gray-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Move up">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
        </button>
        <!-- Move Down -->
        <button type="button" @click="moveExerciseDown(exerciseIndex)" :disabled="exerciseIndex === exercises.length - 1"
            class="p-1 text-gray-400 hover:text-gray-600 rounded hover:bg-gray-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Move down">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <!-- Remove -->
        <button type="button" @click="removeExercise(exerciseIndex)"
            class="p-1 text-red-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Remove exercise">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
```

**Step 4: Add "Last session" display above the sets table**

Insert this just before the `<!-- Sets Table -->` comment:

```html
<!-- Previous Session Data -->
<div x-show="exercise.previous_sets && exercise.previous_sets.length > 0" class="text-xs text-gray-500">
    <span class="font-medium">Last session:</span>
    <template x-for="(prev, prevIndex) in (exercise.previous_sets || [])" :key="prevIndex">
        <span>
            <span x-text="`${prev.weight}kg × ${prev.reps}`"></span><span x-show="prevIndex < exercise.previous_sets.length - 1">, </span>
        </span>
    </template>
</div>
```

**Step 5: Update `addExercise()` to carry `previous_sets` from the picker**

```js
addExercise(exercise) {
    this.exercises.push({
        workout_exercise_id: null,
        exercise_id: exercise.id,
        name: exercise.name,
        muscle_group: exercise.muscle_group,
        prescribed_sets: null,
        prescribed_reps: null,
        previous_sets: exercise.previous_sets || [],
        sets: [{ weight: '', reps: '' }],
    });
    this.showExercisePicker = false;
    this.exerciseSearch = '';
},
```

**Step 6: Build frontend assets**

```bash
npm run build
```

Expected: Build succeeds.

**Step 7: Run all tests**

```bash
php artisan test --compact
```

Expected: All tests pass.

**Step 8: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 9: Commit**

```bash
git add resources/views/client/log-workout.blade.php
git commit -m "feat: add exercise reordering and previous session data display to logging form"
```

---

### Task 6: Final Verification

**Step 1: Run full test suite**

```bash
php artisan test --compact
```

Expected: All tests pass.

**Step 2: Run Pint on all changed files**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 3: Manual verification checklist**

- [ ] Program workout logging form shows drag handles on exercise cards
- [ ] Up/down arrows reorder exercises
- [ ] Drag-and-drop reorders exercises
- [ ] "Last session" line appears with data from previous logs
- [ ] Adding an exercise via picker carries `previous_sets`
- [ ] Custom workout form works (no previous_sets initially, picker provides them)
- [ ] Form submits correctly in the new order
