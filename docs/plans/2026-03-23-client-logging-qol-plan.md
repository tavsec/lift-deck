# Client Logging QoL Improvements Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Three QoL improvements to the client workout logging screen: empty set rows by default, coach lock on exercise removal per workout day, and offline state persistence with restore banner.

**Architecture:** Feature 1 is a two-line change. Feature 2 adds a boolean column to `program_workouts`, a new route/controller action, and a UI toggle for coaches + conditional hide for clients. Feature 3 is purely frontend (Alpine.js + localStorage) — no backend changes.

**Tech Stack:** Laravel 12, Alpine.js v3, Pest 4, Tailwind CSS v3

---

### Task 1: Empty set rows — fix controller initialization

**Files:**
- Modify: `app/Http/Controllers/Client/LogController.php` (line 99–102)

**Step 1: Write the failing test**

Add to `tests/Feature/Client/WorkoutLogTest.php`:

```php
it('initializes set rows with empty weight and reps', function () {
    $response = $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout));

    $response->assertOk();
    // Sets should be initialized with empty strings, not 0
    $response->assertSee('"weight":""', false);
    $response->assertSee('"reps":""', false);
    $response->assertDontSee('"weight":0', false);
    $response->assertDontSee('"reps":0', false);
});
```

**Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter="initializes set rows with empty"
```

Expected: FAIL

**Step 3: Implement the fix**

In `app/Http/Controllers/Client/LogController.php`, change line 99–102:

```php
// Before:
'sets' => collect(range(1, $we->sets))->map(fn ($i) => [
    'weight' => 0,
    'reps' => 0,
])->values()->all(),

// After:
'sets' => collect(range(1, $we->sets))->map(fn () => [
    'weight' => '',
    'reps' => '',
])->values()->all(),
```

**Step 4: Run test to verify it passes**

```bash
php artisan test --compact --filter="initializes set rows with empty"
```

Expected: PASS

**Step 5: Commit**

```bash
git add app/Http/Controllers/Client/LogController.php tests/Feature/Client/WorkoutLogTest.php
git commit -m "fix: initialize workout set rows with empty values instead of zeros"
```

---

### Task 2: Empty set rows — fix addSet in Alpine.js view

**Files:**
- Modify: `resources/views/client/log-workout.blade.php` (line ~429)

**Step 1: Update addSet in Alpine component**

In `resources/views/client/log-workout.blade.php`, change `addSet`:

```js
// Before:
addSet(exerciseIndex) {
    this.exercises[exerciseIndex].sets.push({ weight: 0, reps: 0 });
},

// After:
addSet(exerciseIndex) {
    this.exercises[exerciseIndex].sets.push({ weight: '', reps: '' });
},
```

No test needed here (frontend only, no server-side behavior change).

**Step 2: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 3: Commit**

```bash
git add resources/views/client/log-workout.blade.php
git commit -m "fix: add set button initializes with empty values"
```

---

### Task 3: Lock exercise removal — migration

**Files:**
- Create: new migration file (generated via artisan)

**Step 1: Generate the migration**

```bash
php artisan make:migration add_lock_exercise_removal_to_program_workouts_table --no-interaction
```

**Step 2: Write the migration**

In the generated file under `database/migrations/`:

```php
public function up(): void
{
    Schema::table('program_workouts', function (Blueprint $table) {
        $table->boolean('lock_exercise_removal')->default(false)->after('order');
    });
}

public function down(): void
{
    Schema::table('program_workouts', function (Blueprint $table) {
        $table->dropColumn('lock_exercise_removal');
    });
}
```

**Step 3: Run the migration**

```bash
php artisan migrate --no-interaction
```

Expected: Migration runs successfully.

**Step 4: Commit**

```bash
git add database/migrations/
git commit -m "feat: add lock_exercise_removal column to program_workouts"
```

---

### Task 4: Lock exercise removal — update ProgramWorkout model

**Files:**
- Modify: `app/Models/ProgramWorkout.php`

**Step 1: Add to fillable and casts**

```php
protected $fillable = [
    'program_id',
    'name',
    'day_number',
    'notes',
    'order',
    'lock_exercise_removal',  // add this
];

protected function casts(): array
{
    return [
        'day_number' => 'integer',
        'order' => 'integer',
        'lock_exercise_removal' => 'boolean',  // add this
    ];
}
```

**Step 2: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 3: Commit**

```bash
git add app/Models/ProgramWorkout.php
git commit -m "feat: add lock_exercise_removal to ProgramWorkout model"
```

---

### Task 5: Lock exercise removal — coach toggle route and controller action

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/Coach/ProgramController.php`

**Step 1: Add route**

In `routes/web.php`, after the existing `updateWorkout` route (line ~48):

```php
Route::patch('programs/{program}/workouts/{workout}/lock-removal', [Coach\ProgramController::class, 'toggleWorkoutLockRemoval'])->name('programs.workouts.toggle-lock-removal');
```

**Step 2: Write the failing test**

Create `tests/Feature/Coach/WorkoutLockRemovalTest.php`:

```php
<?php

use App\Models\ClientProgram;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->program = Program::factory()->create(['coach_id' => $this->coach->id]);
    $this->workout = ProgramWorkout::factory()->create([
        'program_id' => $this->program->id,
        'lock_exercise_removal' => false,
    ]);
});

it('coach can lock exercise removal on a workout', function () {
    $this->actingAs($this->coach)
        ->patch(route('coach.programs.workouts.toggle-lock-removal', [$this->program, $this->workout]), [
            'lock_exercise_removal' => true,
        ])
        ->assertRedirect();

    expect($this->workout->fresh()->lock_exercise_removal)->toBeTrue();
});

it('coach can unlock exercise removal on a workout', function () {
    $this->workout->update(['lock_exercise_removal' => true]);

    $this->actingAs($this->coach)
        ->patch(route('coach.programs.workouts.toggle-lock-removal', [$this->program, $this->workout]), [
            'lock_exercise_removal' => false,
        ])
        ->assertRedirect();

    expect($this->workout->fresh()->lock_exercise_removal)->toBeFalse();
});

it('another coach cannot toggle lock on someone elses workout', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($otherCoach)
        ->patch(route('coach.programs.workouts.toggle-lock-removal', [$this->program, $this->workout]), [
            'lock_exercise_removal' => true,
        ])
        ->assertForbidden();
});
```

**Step 3: Run tests to verify they fail**

```bash
php artisan test --compact --filter="WorkoutLockRemoval"
```

Expected: FAIL

**Step 4: Add controller action**

In `app/Http/Controllers/Coach/ProgramController.php`, add:

```php
public function toggleWorkoutLockRemoval(Program $program, ProgramWorkout $workout): RedirectResponse
{
    if ($program->coach_id !== auth()->id()) {
        abort(403);
    }

    $workout->update([
        'lock_exercise_removal' => request()->boolean('lock_exercise_removal'),
    ]);

    return redirect()->back()->with('success', 'Workout updated.');
}
```

Also ensure `RedirectResponse` is imported at the top of the file (check existing imports).

**Step 5: Run tests to verify they pass**

```bash
php artisan test --compact --filter="WorkoutLockRemoval"
```

Expected: PASS

**Step 6: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 7: Commit**

```bash
git add routes/web.php app/Http/Controllers/Coach/ProgramController.php tests/Feature/Coach/WorkoutLockRemovalTest.php
git commit -m "feat: coach can toggle lock_exercise_removal per workout day"
```

---

### Task 6: Lock exercise removal — coach UI toggle in program edit view

**Files:**
- Modify: `resources/views/coach/programs/edit.blade.php`

**Step 1: Add toggle to each workout day header**

In the workout header section (around line 119–131), add a lock toggle form after the existing delete form:

```blade
<div class="flex items-center gap-3">
    <!-- Lock toggle -->
    <form method="POST" action="{{ route('coach.programs.workouts.toggle-lock-removal', [$program, $workout]) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="lock_exercise_removal" value="{{ $workout->lock_exercise_removal ? '0' : '1' }}">
        <button type="submit"
            class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-1 rounded {{ $workout->lock_exercise_removal ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }} hover:opacity-80 transition-opacity"
            title="{{ $workout->lock_exercise_removal ? 'Clients cannot remove exercises — click to unlock' : 'Clients can remove exercises — click to lock' }}"
        >
            @if($workout->lock_exercise_removal)
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Locked
            @else
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                </svg>
                Unlocked
            @endif
        </button>
    </form>

    <!-- Existing delete form -->
    <form method="POST" action="{{ route('coach.programs.workouts.destroy', [$program, $workout]) }}" onsubmit="return confirm('Delete this workout and all its exercises?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
    </form>
</div>
```

Replace the existing `<div class="flex gap-2">` block that only contained the delete form.

**Step 2: Commit**

```bash
git add resources/views/coach/programs/edit.blade.php
git commit -m "feat: coach UI toggle for locking exercise removal on workout days"
```

---

### Task 7: Lock exercise removal — pass flag to client logging view

**Files:**
- Modify: `app/Http/Controllers/Client/LogController.php`
- Modify: `resources/views/client/log-workout.blade.php`

**Step 1: Write the failing test**

Add to `tests/Feature/Client/WorkoutLogTest.php`:

```php
it('passes lock_removal flag to exercises when workout has it locked', function () {
    $this->workout->update(['lock_exercise_removal' => true]);

    $response = $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout));

    $response->assertOk();
    $response->assertSee('"lock_removal":true', false);
});

it('passes lock_removal as false when workout is not locked', function () {
    $response = $this->actingAs($this->client)
        ->get(route('client.log.create', $this->workout));

    $response->assertOk();
    $response->assertSee('"lock_removal":false', false);
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact --filter="lock_removal flag"
```

Expected: FAIL

**Step 3: Update LogController::create()**

In `app/Http/Controllers/Client/LogController.php`, in the `$exercisesData` map (around line 89–103), add `'lock_removal'` to each exercise entry:

```php
$exercisesData = $workout->exercises->map(fn ($we) => [
    'workout_exercise_id' => $we->id,
    'exercise_id' => $we->exercise_id,
    'name' => $we->exercise->name,
    'muscle_group' => $we->exercise->muscle_group,
    'description' => $we->exercise->description,
    'embed_url' => $we->exercise->getYoutubeEmbedUrl(),
    'prescribed_sets' => $we->sets,
    'prescribed_reps' => $we->reps,
    'previous_sets' => $previousSets->get($we->exercise_id, []),
    'lock_removal' => $workout->lock_exercise_removal,  // add this
    'sets' => collect(range(1, $we->sets))->map(fn () => [
        'weight' => '',
        'reps' => '',
    ])->values()->all(),
])->values()->all();
```

**Step 4: Update client view — hide remove button when locked**

In `resources/views/client/log-workout.blade.php`, find the remove exercise button (around line 101–107):

```blade
<!-- Before: always shows remove button -->
<button type="button" @click="removeExercise(exerciseIndex)" ...>

<!-- After: hide when locked -->
<button type="button" @click="removeExercise(exerciseIndex)"
    x-show="!exercise.lock_removal"
    ...>
```

**Step 5: Run tests to verify they pass**

```bash
php artisan test --compact --filter="lock_removal flag"
```

Expected: PASS

**Step 6: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 7: Commit**

```bash
git add app/Http/Controllers/Client/LogController.php resources/views/client/log-workout.blade.php tests/Feature/Client/WorkoutLogTest.php
git commit -m "feat: hide exercise remove button when coach has locked the workout day"
```

---

### Task 8: Offline state — Alpine.js localStorage auto-save and restore

**Files:**
- Modify: `resources/views/client/log-workout.blade.php`

This is a purely frontend task. No new tests needed beyond verifying existing tests still pass.

**Step 1: Add storage key computation**

At the top of the `workoutLogger()` function, add a computed storage key:

```js
function workoutLogger() {
    const storageKey = {{ $isCustom ? '"workout_logger_custom"' : '"workout_logger_" + ' . $workout->id }};

    return {
        // ... existing properties ...
        restoreBanner: false,
        isOffline: false,
        _saveTimer: null,
```

Actually since this is Blade + Alpine, the key should be a PHP-rendered string. Use:

```js
const storageKey = '{{ $isCustom ? "workout_logger_custom" : "workout_logger_" . $workout->id }}';
```

**Step 2: Add init(), saveState(), clearSavedState(), and restoreState() methods**

Add to the Alpine component:

```js
init() {
    this.isOffline = !navigator.onLine;
    window.addEventListener('online', () => { this.isOffline = false; });
    window.addEventListener('offline', () => { this.isOffline = true; });

    const saved = localStorage.getItem(storageKey);
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            const savedAt = new Date(parsed.savedAt);
            const ageHours = (Date.now() - savedAt.getTime()) / (1000 * 60 * 60);
            if (ageHours < 24) {
                this._pendingRestore = parsed;
                this.restoreBanner = true;
                this._savedAtFormatted = savedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } else {
                localStorage.removeItem(storageKey);
            }
        } catch {
            localStorage.removeItem(storageKey);
        }
    }

    this.$watch('exercises', () => { this.debouncedSave(); }, { deep: true });
},

debouncedSave() {
    clearTimeout(this._saveTimer);
    this._saveTimer = setTimeout(() => { this.saveState(); }, 800);
},

saveState() {
    const state = {
        exercises: this.exercises,
        savedAt: new Date().toISOString(),
    };
    localStorage.setItem(storageKey, JSON.stringify(state));
},

clearSavedState() {
    localStorage.removeItem(storageKey);
},

confirmRestore() {
    if (this._pendingRestore) {
        this.exercises = this._pendingRestore.exercises;
    }
    this._pendingRestore = null;
    this.restoreBanner = false;
},

discardRestore() {
    this._pendingRestore = null;
    this.restoreBanner = false;
    this.clearSavedState();
},
```

**Step 3: Add notes watch for saving notes too**

After the exercises watch in `init()`:

```js
// Also watch custom name if custom workout
```

Actually notes is in a plain textarea, not in Alpine state. Add `x-model="notes"` to the notes textarea and add `notes: ''` to the data, then also include `notes` in saveState/restore. Update the textarea:

```blade
<textarea
    id="notes"
    name="notes"
    x-model="notes"
    rows="2"
    ...
>{{ old('notes') }}</textarea>
```

And in `saveState()`:
```js
const state = {
    exercises: this.exercises,
    notes: this.notes,
    savedAt: new Date().toISOString(),
};
```

In `confirmRestore()`:
```js
this.exercises = this._pendingRestore.exercises;
this.notes = this._pendingRestore.notes ?? '';
```

**Step 4: Add restore banner UI**

Near the top of the form (after the `@if($errors->any())` block), add:

```blade
<!-- Restore banner -->
<div x-show="restoreBanner" x-cloak
    class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Unfinished workout found</p>
            <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">
                We saved your progress from <span x-text="_savedAtFormatted"></span>. Continue where you left off?
            </p>
        </div>
        <div class="flex gap-2 shrink-0">
            <button type="button" @click="confirmRestore()"
                class="text-xs font-semibold px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Restore
            </button>
            <button type="button" @click="discardRestore()"
                class="text-xs font-medium px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50">
                Start Fresh
            </button>
        </div>
    </div>
</div>

<!-- Offline banner -->
<div x-show="isOffline" x-cloak
    class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-3">
    <p class="text-sm text-amber-700 dark:text-amber-400 flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M12 12h.01M8.464 15.536a5 5 0 01-.068-7.004M5.636 5.636a9 9 0 000 12.728"/>
        </svg>
        You're offline — your progress is being saved locally.
    </p>
</div>
```

**Step 5: Clear state on form submit**

Change the form opening tag to handle clearing on submit:

```blade
<form method="POST" action="{{ route('client.log.store') }}" @submit="clearSavedState()">
```

**Step 6: Run existing tests to make sure nothing broke**

```bash
php artisan test --compact tests/Feature/Client/WorkoutLogTest.php
```

Expected: All existing tests PASS.

**Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 8: Commit**

```bash
git add resources/views/client/log-workout.blade.php
git commit -m "feat: offline state auto-save and restore banner on client workout logging"
```

---

### Task 9: Final test run and cleanup

**Step 1: Run all related tests**

```bash
php artisan test --compact tests/Feature/Client/WorkoutLogTest.php tests/Feature/Coach/WorkoutLockRemovalTest.php
```

Expected: All PASS.

**Step 2: Run pint one final time**

```bash
vendor/bin/pint --dirty --format agent
```
