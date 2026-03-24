# Client Target Weights Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Allow coaches to set and update per-exercise target weights for each client program assignment.

**Architecture:** New `client_program_exercise_targets` table ties a `ClientProgram` (the specific assignment) to a `WorkoutExercise` with a `target_weight`. A dedicated controller handles a single targets page per assignment, linked from the program show page's assigned clients list.

**Tech Stack:** Laravel 12, Eloquent, Blade + Tailwind CSS v3, Pest 4

---

### Task 1: Migration + Model + Factory

**Files:**
- Create: migration via artisan (see step 1)
- Create: `app/Models/ClientProgramExerciseTarget.php`
- Create: `database/factories/ClientProgramExerciseTargetFactory.php`
- Modify: `app/Models/ClientProgram.php`
- Modify: `app/Models/WorkoutExercise.php`

**Step 1: Create the migration**

```bash
php artisan make:migration create_client_program_exercise_targets_table --no-interaction
```

Edit the generated migration file to:

```php
public function up(): void
{
    Schema::create('client_program_exercise_targets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_program_id')->constrained('client_programs')->cascadeOnDelete();
        $table->foreignId('workout_exercise_id')->constrained('workout_exercises')->cascadeOnDelete();
        $table->decimal('target_weight', 8, 2);
        $table->timestamps();

        $table->unique(['client_program_id', 'workout_exercise_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('client_program_exercise_targets');
}
```

Run: `php artisan migrate --no-interaction`

**Step 2: Create the model**

```bash
php artisan make:model ClientProgramExerciseTarget --no-interaction
```

Replace the generated file contents with:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProgramExerciseTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_program_id',
        'workout_exercise_id',
        'target_weight',
    ];

    protected function casts(): array
    {
        return [
            'target_weight' => 'decimal:2',
        ];
    }

    public function clientProgram(): BelongsTo
    {
        return $this->belongsTo(ClientProgram::class);
    }

    public function workoutExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutExercise::class);
    }
}
```

**Step 3: Create the factory**

```bash
php artisan make:factory ClientProgramExerciseTargetFactory --no-interaction
```

Edit `database/factories/ClientProgramExerciseTargetFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\ClientProgram;
use App\Models\WorkoutExercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientProgramExerciseTarget>
 */
class ClientProgramExerciseTargetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_program_id' => ClientProgram::factory(),
            'workout_exercise_id' => WorkoutExercise::factory(),
            'target_weight' => fake()->randomFloat(2, 20, 200),
        ];
    }
}
```

**Step 4: Add relationship to ClientProgram**

In `app/Models/ClientProgram.php`, add the import and relationship method:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

// Add this method to the class:
public function exerciseTargets(): HasMany
{
    return $this->hasMany(ClientProgramExerciseTarget::class);
}
```

**Step 5: Add relationship to WorkoutExercise**

In `app/Models/WorkoutExercise.php`, add:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

// Add this method to the class:
public function clientProgramTargets(): HasMany
{
    return $this->hasMany(ClientProgramExerciseTarget::class);
}
```

---

### Task 2: Controller + Routes

**Files:**
- Create: `app/Http/Controllers/Coach/ClientProgramTargetController.php`
- Modify: `routes/web.php`

**Step 1: Create the controller**

```bash
php artisan make:controller Coach/ClientProgramTargetController --no-interaction
```

Replace the file contents with:

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClientProgram;
use App\Models\ClientProgramExerciseTarget;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientProgramTargetController extends Controller
{
    public function edit(Program $program, ClientProgram $clientProgram): View
    {
        if ($program->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($clientProgram->program_id !== $program->id) {
            abort(403);
        }

        $program->load('workouts.exercises.exercise');
        $clientProgram->load(['client', 'exerciseTargets']);

        // Key targets by workout_exercise_id for easy lookup in the view
        $targetsByExercise = $clientProgram->exerciseTargets
            ->keyBy('workout_exercise_id');

        return view('coach.programs.targets', compact('program', 'clientProgram', 'targetsByExercise'));
    }

    public function update(Request $request, Program $program, ClientProgram $clientProgram): RedirectResponse
    {
        if ($program->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($clientProgram->program_id !== $program->id) {
            abort(403);
        }

        $validated = $request->validate([
            'targets' => ['nullable', 'array'],
            'targets.*' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ]);

        foreach ($validated['targets'] ?? [] as $workoutExerciseId => $weight) {
            if ($weight === null || $weight === '') {
                // Remove target if cleared
                ClientProgramExerciseTarget::where('client_program_id', $clientProgram->id)
                    ->where('workout_exercise_id', $workoutExerciseId)
                    ->delete();
                continue;
            }

            ClientProgramExerciseTarget::updateOrCreate(
                [
                    'client_program_id' => $clientProgram->id,
                    'workout_exercise_id' => $workoutExerciseId,
                ],
                ['target_weight' => $weight]
            );
        }

        return redirect()->route('coach.programs.assignments.targets.edit', [$program, $clientProgram])
            ->with('success', 'Target weights updated for ' . $clientProgram->client->name . '.');
    }
}
```

**Step 2: Register routes**

In `routes/web.php`, after the existing `programs.assign.store` line, add:

```php
Route::get('programs/{program}/assignments/{clientProgram}/targets', [Coach\ClientProgramTargetController::class, 'edit'])->name('programs.assignments.targets.edit');
Route::put('programs/{program}/assignments/{clientProgram}/targets', [Coach\ClientProgramTargetController::class, 'update'])->name('programs.assignments.targets.update');
```

---

### Task 3: View

**Files:**
- Create: `resources/views/coach/programs/targets.blade.php`
- Modify: `resources/views/coach/programs/show.blade.php`

**Step 1: Create the targets view**

Create `resources/views/coach/programs/targets.blade.php`:

```blade
<x-layouts.coach>
    <x-slot:title>Target Weights – {{ $clientProgram->client->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Program
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Target Weights</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Set target weights for <span class="font-medium text-gray-700 dark:text-gray-300">{{ $clientProgram->client->name }}</span> on <span class="font-medium text-gray-700 dark:text-gray-300">{{ $program->name }}</span>. Leave blank to remove a target.
            </p>
        </div>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('coach.programs.assignments.targets.update', [$program, $clientProgram]) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @foreach($program->workouts as $workout)
                @if($workout->exercises->count() > 0)
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $workout->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Day {{ $workout->day_number }} &middot; {{ $workout->exercises->count() }} exercises</p>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($workout->exercises as $workoutExercise)
                                <div class="px-6 py-4 flex items-center justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $workoutExercise->exercise->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <input
                                            type="number"
                                            name="targets[{{ $workoutExercise->id }}]"
                                            value="{{ old('targets.' . $workoutExercise->id, $targetsByExercise->get($workoutExercise->id)?->target_weight) }}"
                                            min="0"
                                            max="9999.99"
                                            step="0.5"
                                            placeholder="—"
                                            class="w-28 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-right @error('targets.' . $workoutExercise->id) border-red-300 @enderror"
                                        >
                                        <span class="text-sm text-gray-500 dark:text-gray-400 w-6">kg</span>
                                    </div>
                                </div>
                                @error('targets.' . $workoutExercise->id)
                                    <p class="px-6 pb-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save Targets
                </button>
            </div>
        </form>
    </div>
</x-layouts.coach>
```

**Step 2: Add "Set Targets" link to programs.show assigned clients section**

In `resources/views/coach/programs/show.blade.php`, replace the assigned client anchor tag (the `<a href="{{ route('coach.clients.show', ...) }}" ...>` block inside the foreach) to add a targets button alongside it. Change the `@foreach($assignedClients as $assignment)` block to:

```blade
@foreach($assignedClients as $assignment)
    <div class="inline-flex items-center gap-2">
        <a href="{{ route('coach.clients.show', $assignment->client) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-950 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                <span class="text-sm font-medium text-blue-700">{{ strtoupper(substr($assignment->client->name, 0, 1)) }}</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $assignment->client->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    @if($assignment->isActive())
                        <span class="text-green-600">Active</span>
                    @elseif($assignment->isPaused())
                        <span class="text-yellow-600">Paused</span>
                    @else
                        <span class="text-gray-600 dark:text-gray-400">Completed</span>
                    @endif
                    - Started {{ $assignment->started_at->format('M d, Y') }}
                </p>
            </div>
        </a>
        <a href="{{ route('coach.programs.assignments.targets.edit', [$program, $assignment]) }}" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-700 rounded-md text-xs font-medium text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
            Targets
        </a>
    </div>
@endforeach
```

---

### Task 4: Tests

**Files:**
- Create: `tests/Feature/Coach/ClientProgramTargetTest.php`

**Step 1: Generate the test file**

```bash
php artisan make:test --pest Coach/ClientProgramTargetTest --no-interaction
```

**Step 2: Write the tests**

Replace the generated file with:

```php
<?php

use App\Models\ClientProgram;
use App\Models\ClientProgramExerciseTarget;
use App\Models\Exercise;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WorkoutExercise;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);

    $this->program = Program::factory()->create(['coach_id' => $this->coach->id]);
    $this->workout = ProgramWorkout::factory()->create(['program_id' => $this->program->id]);
    $this->exercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);
    $this->workoutExercise = WorkoutExercise::factory()->create([
        'program_workout_id' => $this->workout->id,
        'exercise_id' => $this->exercise->id,
    ]);

    $this->clientProgram = ClientProgram::factory()->create([
        'client_id' => $this->client->id,
        'program_id' => $this->program->id,
    ]);
});

it('coach can view the targets edit page', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.programs.assignments.targets.edit', [$this->program, $this->clientProgram]))
        ->assertOk()
        ->assertSee($this->exercise->name)
        ->assertSee($this->client->name);
});

it('coach can set a target weight for an exercise', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => '80.00'],
        ])
        ->assertRedirect();

    expect(ClientProgramExerciseTarget::where([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
    ])->value('target_weight'))->toEqual('80.00');
});

it('coach can update an existing target weight', function () {
    ClientProgramExerciseTarget::factory()->create([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'target_weight' => 60.00,
    ]);

    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => '90.00'],
        ])
        ->assertRedirect();

    expect(ClientProgramExerciseTarget::where([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
    ])->value('target_weight'))->toEqual('90.00');

    // Only one record should exist
    expect(ClientProgramExerciseTarget::where('client_program_id', $this->clientProgram->id)->count())->toBe(1);
});

it('clears target when an empty value is submitted', function () {
    ClientProgramExerciseTarget::factory()->create([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'target_weight' => 60.00,
    ]);

    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => null],
        ])
        ->assertRedirect();

    expect(ClientProgramExerciseTarget::where('client_program_id', $this->clientProgram->id)->count())->toBe(0);
});

it('another coach cannot view targets for someone elses program', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($otherCoach)
        ->get(route('coach.programs.assignments.targets.edit', [$this->program, $this->clientProgram]))
        ->assertForbidden();
});

it('another coach cannot update targets for someone elses program', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($otherCoach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => '80.00'],
        ])
        ->assertForbidden();
});

it('rejects negative target weight', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => '-5'],
        ])
        ->assertSessionHasErrors('targets.' . $this->workoutExercise->id);
});
```

**Step 3: Run the tests**

```bash
php artisan test --compact --filter=ClientProgramTargetTest
```

Expected: all 7 tests pass.

---

### Task 5: Pint + Commit

**Step 1: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 2: Commit**

```bash
git add \
  database/migrations/*client_program_exercise_targets* \
  app/Models/ClientProgramExerciseTarget.php \
  app/Models/ClientProgram.php \
  app/Models/WorkoutExercise.php \
  database/factories/ClientProgramExerciseTargetFactory.php \
  app/Http/Controllers/Coach/ClientProgramTargetController.php \
  routes/web.php \
  resources/views/coach/programs/targets.blade.php \
  resources/views/coach/programs/show.blade.php \
  tests/Feature/Coach/ClientProgramTargetTest.php

git commit -m "feat: add per-client target weights for program exercises"
```
