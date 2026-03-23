# Coach Client Entries & Track-Only Clients Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Allow coaches to add/edit all client log entries (workout, nutrition, check-in), add track-only clients (name-only, no login), with all activity logged via Spatie activitylog.

**Architecture:** New controllers in the `Coach` namespace handle entries on behalf of clients using `{client}` route parameters. Track-only clients are regular `User` records with `is_track_only=true` and a nullable email. Spatie `LogsActivity` trait is added to all loggable models.

**Tech Stack:** Laravel 12, Pest 4, spatie/laravel-activitylog, Tailwind CSS v3, Alpine.js v3

---

## Task 1: Install spatie/laravel-activitylog

**Files:**
- Modify: `composer.json` (via composer CLI)
- Modify: `config/activitylog.php` (published after install)

**Step 1: Require the package**

```bash
composer require spatie/laravel-activitylog
```

**Step 2: Publish config and migration**

```bash
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
```

**Step 3: Run the migration**

```bash
php artisan migrate
```

**Step 4: Verify**

```bash
php artisan migrate:status | grep activity
```
Expected: `activity_log` table shown as "Ran".

**Step 5: Commit**

```bash
git add composer.json composer.lock database/migrations config/activitylog.php
git commit -m "feat: install spatie/laravel-activitylog"
```

---

## Task 2: Add is_track_only to users + make email nullable

**Files:**
- Create: `database/migrations/2026_03_18_000001_add_is_track_only_to_users_table.php`
- Modify: `app/Models/User.php`

**Step 1: Write the failing test**

File: `tests/Feature/TrackOnlyClientTest.php`

```bash
php artisan make:test --pest TrackOnlyClientTest
```

```php
<?php

use App\Models\User;

it('coach can create a track-only client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();

    $this->actingAs($coach)
        ->post(route('coach.clients.store-track-only'), [
            'name' => 'John Doe',
        ])
        ->assertRedirect(route('coach.clients.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'coach_id' => $coach->id,
        'is_track_only' => true,
        'email' => null,
    ]);
});

it('track-only client has no email or password', function () {
    $client = User::factory()->state([
        'role' => 'client',
        'email' => null,
        'password' => null,
        'is_track_only' => true,
    ])->create();

    expect($client->isTrackOnly())->toBeTrue();
});
```

**Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=TrackOnlyClientTest
```
Expected: FAIL — `is_track_only` column does not exist.

**Step 3: Create migration**

```bash
php artisan make:migration add_is_track_only_to_users_table --no-interaction
```

In the new migration file:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_track_only')->default(false)->after('role');
        $table->string('email')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('is_track_only');
        $table->string('email')->nullable(false)->change();
    });
}
```

**Step 4: Run migration**

```bash
php artisan migrate
```

**Step 5: Update User model**

Add `is_track_only` to `$fillable` and add helper methods:

```php
protected $fillable = [
    // ... existing fields ...
    'is_track_only',
];

protected function casts(): array
{
    return [
        // ... existing casts ...
        'is_track_only' => 'boolean',
    ];
}

public function isTrackOnly(): bool
{
    return (bool) $this->is_track_only;
}
```

**Step 6: Update UserFactory** — add `is_track_only` default state in `database/factories/UserFactory.php`:

```php
// In definition(), ensure email is only nullable when is_track_only is true
// Add a new state:
public function trackOnly(): static
{
    return $this->state(fn (array $attributes) => [
        'role' => 'client',
        'email' => null,
        'password' => null,
        'is_track_only' => true,
    ]);
}
```

**Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 8: Run tests to confirm they still pass (minus route test)**

```bash
php artisan test --compact --filter=TrackOnlyClientTest
```
Expected: FAIL only on the route test — column and model helper now work.

**Step 9: Commit**

```bash
git add database/migrations/ app/Models/User.php database/factories/UserFactory.php tests/Feature/TrackOnlyClientTest.php
git commit -m "feat: add is_track_only column to users and nullable email"
```

---

## Task 3: Add LogsActivity to loggable models

**Files:**
- Modify: `app/Models/WorkoutLog.php`
- Modify: `app/Models/ExerciseLog.php`
- Modify: `app/Models/MealLog.php`
- Modify: `app/Models/DailyLog.php`

**Step 1: Write the failing test**

File: `tests/Feature/ActivityLoggingTest.php`

```bash
php artisan make:test --pest ActivityLoggingTest
```

```php
<?php

use App\Models\MealLog;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

it('logs activity when a meal log is created', function () {
    $client = User::factory()->state(['role' => 'client'])->create();
    $coach = User::factory()->state(['role' => 'coach'])->create(['id' => $client->coach_id]);

    $this->actingAs($client);

    MealLog::create([
        'client_id' => $client->id,
        'date' => now()->format('Y-m-d'),
        'meal_type' => 'lunch',
        'name' => 'Chicken',
        'calories' => 400,
        'protein' => 40,
        'carbs' => 20,
        'fat' => 10,
    ]);

    expect(Activity::query()->count())->toBe(1);
});

it('logs activity when a meal log is deleted', function () {
    $client = User::factory()->state(['role' => 'client'])->create();
    $mealLog = MealLog::factory()->for($client, 'client')->create();

    Activity::query()->delete(); // reset

    $this->actingAs($client);
    $mealLog->delete();

    expect(Activity::query()->where('event', 'deleted')->count())->toBe(1);
});
```

**Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=ActivityLoggingTest
```
Expected: FAIL — `LogsActivity` trait not added yet.

**Step 3: Add LogsActivity to models**

In each of the four models (`WorkoutLog`, `ExerciseLog`, `MealLog`, `DailyLog`), add:

```php
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkoutLog extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    // ...
}
```

Repeat this pattern for `ExerciseLog`, `MealLog`, and `DailyLog`.

**Step 4: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 5: Run tests**

```bash
php artisan test --compact --filter=ActivityLoggingTest
```
Expected: PASS.

**Step 6: Commit**

```bash
git add app/Models/WorkoutLog.php app/Models/ExerciseLog.php app/Models/MealLog.php app/Models/DailyLog.php tests/Feature/ActivityLoggingTest.php
git commit -m "feat: add activity logging to WorkoutLog, ExerciseLog, MealLog, DailyLog"
```

---

## Task 4: Track-only client creation

**Files:**
- Modify: `app/Http/Controllers/Coach/ClientController.php`
- Create: `app/Http/Requests/StoreTrackOnlyClientRequest.php`
- Create: `resources/views/coach/clients/create-track-only.blade.php`
- Modify: `resources/views/coach/clients/index.blade.php`
- Modify: `resources/views/coach/clients/show.blade.php`
- Modify: `routes/web.php`

**Step 1: Tests already exist in TrackOnlyClientTest.php — run them**

```bash
php artisan test --compact --filter=TrackOnlyClientTest
```
Expected: FAIL — route does not exist.

**Step 2: Create form request**

```bash
php artisan make:request StoreTrackOnlyClientRequest --no-interaction
```

```php
public function authorize(): bool
{
    return $this->user()->isCoach();
}

public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'phone' => ['nullable', 'string', 'max:20'],
    ];
}
```

**Step 3: Add routes to routes/web.php** (inside the `role:coach` group):

```php
Route::get('clients/create-track-only', [Coach\ClientController::class, 'createTrackOnly'])->name('clients.create-track-only');
Route::post('clients/store-track-only', [Coach\ClientController::class, 'storeTrackOnly'])->name('clients.store-track-only');
Route::post('clients/{client}/enable-app-access', [Coach\ClientController::class, 'enableAppAccess'])->name('clients.enable-app-access');
```

> **Important:** Place these routes BEFORE the `resource('clients', ...)` line to avoid route conflicts.

**Step 4: Add controller methods to ClientController**

```php
public function createTrackOnly(): View
{
    return view('coach.clients.create-track-only');
}

public function storeTrackOnly(StoreTrackOnlyClientRequest $request): RedirectResponse
{
    $coach = auth()->user();

    User::create([
        ...$request->validated(),
        'role' => 'client',
        'coach_id' => $coach->id,
        'is_track_only' => true,
        'password' => null,
    ]);

    return redirect()->route('coach.clients.index')
        ->with('success', 'Track-only client created successfully.');
}

public function enableAppAccess(User $client): RedirectResponse
{
    if ($client->coach_id !== auth()->id()) {
        abort(403);
    }

    if (! $client->isTrackOnly()) {
        return redirect()->route('coach.clients.show', $client)
            ->with('error', 'This client already has app access.');
    }

    $invitation = ClientInvitation::create([
        'coach_id' => auth()->id(),
        'token' => ClientInvitation::generateUniqueToken(),
        'expires_at' => now()->addDays(7),
    ]);

    return redirect()->route('coach.clients.show', $client)
        ->with('success', 'Invitation created!')
        ->with('invitation_code', $invitation->token);
}
```

**Step 5: Create view `resources/views/coach/clients/create-track-only.blade.php`**

Use the same structure as `coach/clients/create.blade.php` but with a simple form (name + optional phone).

**Step 6: Update `coach/clients/index.blade.php`**

Add a second button alongside the existing "Invite Client" button:
```html
<a href="{{ route('coach.clients.create-track-only') }}" class="...">
    Add Track-Only Client
</a>
```

Add a "Track only" badge for `$client->is_track_only` clients in the client list rows.

**Step 7: Update `coach/clients/show.blade.php`**

In the header section, if `$client->isTrackOnly()`, show the "Enable app access" button + invitation code if flashed in session.

**Step 8: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 9: Run tests**

```bash
php artisan test --compact --filter=TrackOnlyClientTest
```
Expected: PASS.

**Step 10: Commit**

```bash
git add app/Http/Controllers/Coach/ClientController.php app/Http/Requests/StoreTrackOnlyClientRequest.php resources/views/coach/clients/ routes/web.php
git commit -m "feat: add track-only client creation and enable-app-access flow"
```

---

## Task 5: Coach manages workout logs for clients

**Files:**
- Create: `app/Http/Controllers/Coach/ClientWorkoutLogController.php`
- Create: `app/Http/Requests/StoreClientWorkoutLogRequest.php`
- Create: `resources/views/coach/clients/workout-log-form.blade.php`
- Modify: `resources/views/coach/clients/show.blade.php`
- Modify: `routes/web.php`

**Step 1: Write failing tests**

File: `tests/Feature/CoachClientWorkoutLogTest.php`

```bash
php artisan make:test --pest CoachClientWorkoutLogTest
```

```php
<?php

use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutLog;

it('coach can log a workout on behalf of a client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $exercise = Exercise::factory()->create(['coach_id' => $coach->id]);

    $this->actingAs($coach)
        ->post(route('coach.clients.workout-logs.store', $client), [
            'custom_name' => 'Test Workout',
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'notes' => null,
            'exercises' => [
                [
                    'workout_exercise_id' => null,
                    'exercise_id' => $exercise->id,
                    'sets' => [
                        ['weight' => 100, 'reps' => 10],
                    ],
                ],
            ],
        ])
        ->assertRedirect(route('coach.clients.show', $client));

    $this->assertDatabaseHas('workout_logs', [
        'client_id' => $client->id,
        'custom_name' => 'Test Workout',
    ]);
});

it('coach can update a workout log', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $log = WorkoutLog::factory()->for($client, 'client')->create(['custom_name' => 'Old Name']);

    $this->actingAs($coach)
        ->put(route('coach.clients.workout-logs.update', [$client, $log]), [
            'custom_name' => 'Updated Name',
            'completed_at' => $log->completed_at->format('Y-m-d H:i:s'),
            'exercises' => [],
        ])
        ->assertRedirect(route('coach.clients.show', $client));

    expect($log->fresh()->custom_name)->toBe('Updated Name');
});

it('coach can delete a workout log', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $log = WorkoutLog::factory()->for($client, 'client')->create();

    $this->actingAs($coach)
        ->delete(route('coach.clients.workout-logs.destroy', [$client, $log]))
        ->assertRedirect(route('coach.clients.show', $client));

    $this->assertDatabaseMissing('workout_logs', ['id' => $log->id]);
});

it('coach cannot manage workout logs of another coach\'s client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $otherCoach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $otherCoach->id])->create();
    $log = WorkoutLog::factory()->for($client, 'client')->create();

    $this->actingAs($coach)
        ->delete(route('coach.clients.workout-logs.destroy', [$client, $log]))
        ->assertForbidden();
});
```

**Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=CoachClientWorkoutLogTest
```
Expected: FAIL — routes do not exist.

**Step 3: Create form request**

```bash
php artisan make:request StoreClientWorkoutLogRequest --no-interaction
```

Same rules as `StoreWorkoutLogRequest` but authorize for coaches:

```php
public function authorize(): bool
{
    return $this->user()->isCoach();
}

public function rules(): array
{
    return [
        'custom_name' => ['nullable', 'string', 'max:255'],
        'completed_at' => ['nullable', 'date', 'before_or_equal:now'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'exercises' => ['nullable', 'array'],
        'exercises.*.workout_exercise_id' => ['nullable', 'exists:workout_exercises,id'],
        'exercises.*.exercise_id' => ['required_with:exercises', 'exists:exercises,id'],
        'exercises.*.sets' => ['nullable', 'array'],
        'exercises.*.sets.*.weight' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        'exercises.*.sets.*.reps' => ['required_with:exercises.*.sets', 'integer', 'min:0', 'max:999'],
    ];
}
```

**Step 4: Create `Coach\ClientWorkoutLogController`**

```bash
php artisan make:controller Coach/ClientWorkoutLogController --no-interaction
```

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientWorkoutLogRequest;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientWorkoutLogController extends Controller
{
    public function create(User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $exercises = Exercise::where('is_active', true)
            ->where(function ($query) {
                $query->where('coach_id', auth()->id())
                    ->orWhereNull('coach_id');
            })
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get(['id', 'name', 'muscle_group', 'description', 'video_url']);

        return view('coach.clients.workout-log-form', compact('client', 'exercises'));
    }

    public function store(StoreClientWorkoutLogRequest $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        $workoutLog = WorkoutLog::create([
            'client_id' => $client->id,
            'custom_name' => $validated['custom_name'] ?? 'Custom Workout',
            'completed_at' => $validated['completed_at'] ?? now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['exercises'] ?? [] as $exerciseData) {
            foreach ($exerciseData['sets'] ?? [] as $setIndex => $setData) {
                if (empty($setData['reps'])) {
                    continue;
                }

                ExerciseLog::create([
                    'workout_log_id' => $workoutLog->id,
                    'workout_exercise_id' => $exerciseData['workout_exercise_id'] ?? null,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'set_number' => $setIndex + 1,
                    'weight' => $setData['weight'] ?? null,
                    'reps' => $setData['reps'],
                ]);
            }
        }

        return redirect()->route('coach.clients.show', $client)
            ->with('success', 'Workout logged for client.');
    }

    public function edit(User $client, WorkoutLog $workoutLog): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($workoutLog->client_id !== $client->id) {
            abort(403);
        }

        $workoutLog->load('exerciseLogs.exercise');

        $exercises = Exercise::where('is_active', true)
            ->where(function ($query) {
                $query->where('coach_id', auth()->id())
                    ->orWhereNull('coach_id');
            })
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get(['id', 'name', 'muscle_group', 'description', 'video_url']);

        return view('coach.clients.workout-log-form', compact('client', 'workoutLog', 'exercises'));
    }

    public function update(StoreClientWorkoutLogRequest $request, User $client, WorkoutLog $workoutLog): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($workoutLog->client_id !== $client->id) {
            abort(403);
        }

        $validated = $request->validated();

        $workoutLog->update([
            'custom_name' => $validated['custom_name'] ?? $workoutLog->custom_name,
            'completed_at' => $validated['completed_at'] ?? $workoutLog->completed_at,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Replace all exercise logs
        $workoutLog->exerciseLogs()->delete();

        foreach ($validated['exercises'] ?? [] as $exerciseData) {
            foreach ($exerciseData['sets'] ?? [] as $setIndex => $setData) {
                if (empty($setData['reps'])) {
                    continue;
                }

                ExerciseLog::create([
                    'workout_log_id' => $workoutLog->id,
                    'workout_exercise_id' => $exerciseData['workout_exercise_id'] ?? null,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'set_number' => $setIndex + 1,
                    'weight' => $setData['weight'] ?? null,
                    'reps' => $setData['reps'],
                ]);
            }
        }

        return redirect()->route('coach.clients.show', $client)
            ->with('success', 'Workout log updated.');
    }

    public function destroy(User $client, WorkoutLog $workoutLog): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($workoutLog->client_id !== $client->id) {
            abort(403);
        }

        $workoutLog->delete();

        return redirect()->route('coach.clients.show', $client)
            ->with('success', 'Workout log deleted.');
    }
}
```

**Step 5: Add routes to routes/web.php** (inside the `role:coach` group):

```php
Route::get('clients/{client}/workout-logs/create', [Coach\ClientWorkoutLogController::class, 'create'])->name('clients.workout-logs.create');
Route::post('clients/{client}/workout-logs', [Coach\ClientWorkoutLogController::class, 'store'])->name('clients.workout-logs.store');
Route::get('clients/{client}/workout-logs/{workoutLog}/edit', [Coach\ClientWorkoutLogController::class, 'edit'])->name('clients.workout-logs.edit');
Route::put('clients/{client}/workout-logs/{workoutLog}', [Coach\ClientWorkoutLogController::class, 'update'])->name('clients.workout-logs.update');
Route::delete('clients/{client}/workout-logs/{workoutLog}', [Coach\ClientWorkoutLogController::class, 'destroy'])->name('clients.workout-logs.destroy');
```

**Step 6: Create view `resources/views/coach/clients/workout-log-form.blade.php`**

Mirror the structure of `client/log-workout.blade.php` but:
- Wrap with `<x-layouts.coach>`
- Form posts to `coach.clients.workout-logs.store` (create) or `coach.clients.workout-logs.update` (edit)
- Pre-fill values from `$workoutLog` if editing
- Pass `$exercises` as JSON for Alpine.js
- Add a back link to the client show page
- No XP dispatch (coach actions don't award XP to the coach)

**Step 7: Update `coach/clients/show.blade.php`**

In the "Recent Workouts" section, add:
- "Log Workout" button linking to `route('coach.clients.workout-logs.create', $client)`
- "Edit" link per log row to `route('coach.clients.workout-logs.edit', [$client, $log])`
- "Delete" form per log row to `route('coach.clients.workout-logs.destroy', [$client, $log])`

**Step 8: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 9: Run tests**

```bash
php artisan test --compact --filter=CoachClientWorkoutLogTest
```
Expected: PASS.

**Step 10: Commit**

```bash
git add app/Http/Controllers/Coach/ClientWorkoutLogController.php app/Http/Requests/StoreClientWorkoutLogRequest.php resources/views/coach/clients/ routes/web.php tests/Feature/CoachClientWorkoutLogTest.php
git commit -m "feat: coach can create, edit, and delete client workout logs"
```

---

## Task 6: Coach manages nutrition logs for clients

**Files:**
- Create: `app/Http/Controllers/Coach/ClientMealLogController.php`
- Create: `app/Http/Requests/StoreClientMealLogRequest.php`
- Modify: `resources/views/coach/clients/nutrition.blade.php`
- Modify: `routes/web.php`

**Step 1: Write failing tests**

File: `tests/Feature/CoachClientMealLogTest.php`

```bash
php artisan make:test --pest CoachClientMealLogTest
```

```php
<?php

use App\Models\MealLog;
use App\Models\User;

it('coach can log a meal on behalf of a client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();

    $this->actingAs($coach)
        ->post(route('coach.clients.meal-logs.store', $client), [
            'date' => now()->format('Y-m-d'),
            'meal_type' => 'lunch',
            'name' => 'Grilled Chicken',
            'calories' => 350,
            'protein' => 40,
            'carbs' => 10,
            'fat' => 8,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('meal_logs', [
        'client_id' => $client->id,
        'name' => 'Grilled Chicken',
    ]);
});

it('coach can delete a client meal log', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $mealLog = MealLog::factory()->for($client, 'client')->create();

    $this->actingAs($coach)
        ->delete(route('coach.clients.meal-logs.destroy', [$client, $mealLog]))
        ->assertRedirect();

    $this->assertDatabaseMissing('meal_logs', ['id' => $mealLog->id]);
});

it('coach cannot manage meal logs of another coach\'s client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $otherCoach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $otherCoach->id])->create();
    $mealLog = MealLog::factory()->for($client, 'client')->create();

    $this->actingAs($coach)
        ->delete(route('coach.clients.meal-logs.destroy', [$client, $mealLog]))
        ->assertForbidden();
});
```

**Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=CoachClientMealLogTest
```
Expected: FAIL.

**Step 3: Create form request**

```bash
php artisan make:request StoreClientMealLogRequest --no-interaction
```

```php
public function authorize(): bool
{
    return $this->user()->isCoach();
}

public function rules(): array
{
    return [
        'meal_id' => ['nullable', 'exists:meals,id'],
        'date' => ['required', 'date'],
        'meal_type' => ['required', 'string', 'max:50'],
        'name' => ['required', 'string', 'max:255'],
        'calories' => ['required', 'integer', 'min:0'],
        'protein' => ['required', 'numeric', 'min:0'],
        'carbs' => ['required', 'numeric', 'min:0'],
        'fat' => ['required', 'numeric', 'min:0'],
        'notes' => ['nullable', 'string', 'max:1000'],
    ];
}
```

**Step 4: Create `Coach\ClientMealLogController`**

```bash
php artisan make:controller Coach/ClientMealLogController --no-interaction
```

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientMealLogRequest;
use App\Models\Meal;
use App\Models\MealLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ClientMealLogController extends Controller
{
    public function store(StoreClientMealLogRequest $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        if (! empty($validated['meal_id'])) {
            $meal = Meal::findOrFail($validated['meal_id']);
            if ($meal->coach_id !== auth()->id()) {
                abort(403);
            }
        }

        MealLog::create([
            ...$validated,
            'client_id' => $client->id,
        ]);

        return redirect()->route('coach.clients.nutrition', [
            'client' => $client,
            'date' => $validated['date'],
        ])->with('success', 'Meal logged for client.');
    }

    public function destroy(User $client, MealLog $mealLog): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($mealLog->client_id !== $client->id) {
            abort(403);
        }

        $date = $mealLog->date->format('Y-m-d');
        $mealLog->delete();

        return redirect()->route('coach.clients.nutrition', [
            'client' => $client,
            'date' => $date,
        ])->with('success', 'Meal removed.');
    }
}
```

**Step 5: Add routes to routes/web.php**:

```php
Route::post('clients/{client}/meal-logs', [Coach\ClientMealLogController::class, 'store'])->name('clients.meal-logs.store');
Route::delete('clients/{client}/meal-logs/{mealLog}', [Coach\ClientMealLogController::class, 'destroy'])->name('clients.meal-logs.destroy');
Route::get('clients/{client}/nutrition/meals', [Coach\ClientMealLogController::class, 'meals'])->name('clients.nutrition.meals');
```

**Step 6: Update `coach/clients/nutrition.blade.php`**

Add:
- A "Log Meal" form/modal (same fields as the client nutrition form: meal_type, name, date, macros)
- Delete buttons on each meal log row within the daily totals table
- A JSON endpoint for meal autocomplete (`meals` method on controller, optional)

**Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 8: Run tests**

```bash
php artisan test --compact --filter=CoachClientMealLogTest
```
Expected: PASS.

**Step 9: Commit**

```bash
git add app/Http/Controllers/Coach/ClientMealLogController.php app/Http/Requests/StoreClientMealLogRequest.php resources/views/coach/clients/nutrition.blade.php routes/web.php tests/Feature/CoachClientMealLogTest.php
git commit -m "feat: coach can add and delete client meal logs"
```

---

## Task 7: Coach manages check-in / daily logs for clients

**Files:**
- Create: `app/Http/Controllers/Coach/ClientCheckInController.php`
- Create: `resources/views/coach/clients/check-in.blade.php`
- Modify: `resources/views/coach/clients/show.blade.php`
- Modify: `routes/web.php`

**Step 1: Write failing tests**

File: `tests/Feature/CoachClientCheckInTest.php`

```bash
php artisan make:test --pest CoachClientCheckInTest
```

```php
<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;

it('coach can submit a check-in for a client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();
    $metric = TrackingMetric::factory()->create(['coach_id' => $coach->id, 'type' => 'number']);
    ClientTrackingMetric::factory()->create(['client_id' => $client->id, 'tracking_metric_id' => $metric->id]);

    $this->actingAs($coach)
        ->post(route('coach.clients.check-in.store', $client), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [$metric->id => '75'],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('daily_logs', [
        'client_id' => $client->id,
        'tracking_metric_id' => $metric->id,
        'value' => '75',
    ]);
});

it('coach cannot check in for another coach\'s client', function () {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    $otherCoach = User::factory()->state(['role' => 'coach'])->create();
    $client = User::factory()->state(['role' => 'client', 'coach_id' => $otherCoach->id])->create();

    $this->actingAs($coach)
        ->post(route('coach.clients.check-in.store', $client), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
        ])
        ->assertForbidden();
});
```

**Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=CoachClientCheckInTest
```
Expected: FAIL.

**Step 3: Create `Coach\ClientCheckInController`**

```bash
php artisan make:controller Coach/ClientCheckInController --no-interaction
```

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientCheckInController extends Controller
{
    public function show(Request $request, User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $date = $request->get('date', now()->format('Y-m-d'));

        $assignedMetrics = $client->assignedTrackingMetrics()
            ->with('trackingMetric')
            ->get()
            ->pluck('trackingMetric')
            ->filter();

        $existingLogs = $client->dailyLogs()
            ->whereDate('date', $date)
            ->get()
            ->keyBy('tracking_metric_id');

        return view('coach.clients.check-in', compact('client', 'assignedMetrics', 'existingLogs', 'date'));
    }

    public function store(Request $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'metrics' => ['nullable', 'array'],
            'metrics.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignedMetricIds = $client->assignedTrackingMetrics()
            ->pluck('tracking_metric_id')
            ->toArray();

        foreach ($validated['metrics'] ?? [] as $metricId => $value) {
            if (! in_array((int) $metricId, $assignedMetricIds)) {
                continue;
            }

            if ($value === null || $value === '') {
                DailyLog::where('client_id', $client->id)
                    ->where('tracking_metric_id', $metricId)
                    ->whereDate('date', $validated['date'])
                    ->delete();
                continue;
            }

            $log = DailyLog::where('client_id', $client->id)
                ->where('tracking_metric_id', $metricId)
                ->whereDate('date', $validated['date'])
                ->first();

            if ($log) {
                $log->update(['value' => $value]);
            } else {
                DailyLog::create([
                    'client_id' => $client->id,
                    'tracking_metric_id' => $metricId,
                    'date' => $validated['date'],
                    'value' => $value,
                ]);
            }
        }

        return redirect()->route('coach.clients.check-in.show', ['client' => $client, 'date' => $validated['date']])
            ->with('success', 'Check-in saved for client.');
    }
}
```

**Step 4: Add routes to routes/web.php**:

```php
Route::get('clients/{client}/check-in', [Coach\ClientCheckInController::class, 'show'])->name('clients.check-in.show');
Route::post('clients/{client}/check-in', [Coach\ClientCheckInController::class, 'store'])->name('clients.check-in.store');
```

**Step 5: Create view `resources/views/coach/clients/check-in.blade.php`**

Mirror `client/check-in.blade.php` structure:
- Wrap with `<x-layouts.coach>`
- Form posts to `coach.clients.check-in.store`
- Date picker with navigation
- Metric inputs per assigned metric type (number, boolean, text)
- Back link to client show page

**Step 6: Update `coach/clients/show.blade.php`**

In the "Daily Check-ins (Last 7 Days)" section, add a "Log Check-in" button:
```html
<a href="{{ route('coach.clients.check-in.show', $client) }}">Log Check-in</a>
```

**Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 8: Run tests**

```bash
php artisan test --compact --filter=CoachClientCheckInTest
```
Expected: PASS.

**Step 9: Commit**

```bash
git add app/Http/Controllers/Coach/ClientCheckInController.php resources/views/coach/clients/check-in.blade.php resources/views/coach/clients/show.blade.php routes/web.php tests/Feature/CoachClientCheckInTest.php
git commit -m "feat: coach can add and edit client daily check-in metrics"
```

---

## Task 8: Full test run + pint

**Step 1: Run all tests**

```bash
php artisan test --compact
```
Expected: All existing + new tests pass. The 9 pre-existing scaffold failures (Auth/Profile) are expected.

**Step 2: Run pint on all changed files**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 3: Final commit if any pint changes**

```bash
git add -p
git commit -m "style: apply pint formatting"
```
