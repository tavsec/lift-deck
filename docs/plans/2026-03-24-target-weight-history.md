# Target Weight History Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Track target weight changes over time via `effective_date`, display history to both coach and client, and overlay targets as a step-line on the exercise progression analytics chart.

**Architecture:** Add `effective_date` (date) to `client_program_exercise_targets` and update the unique constraint to include it — same pattern as `MacroGoal`. Saving targets always uses today's date; same-day saves update, new-day saves insert. The analytics chart overlays a dashed target step-line alongside logged weights, computed by finding the latest target ≤ each log date.

**Tech Stack:** Laravel 12, Eloquent, Blade + Alpine.js, Chart.js 4, Tailwind CSS v3, Pest 4

---

### Task 1: Migration + Model + Factory

**Files:**
- Create: migration via artisan
- Modify: `app/Models/ClientProgramExerciseTarget.php`
- Modify: `database/factories/ClientProgramExerciseTargetFactory.php`

**Step 1: Create the migration**

```bash
php artisan make:migration add_effective_date_to_client_program_exercise_targets_table --no-interaction
```

Edit the generated migration:

```php
public function up(): void
{
    Schema::table('client_program_exercise_targets', function (Blueprint $table) {
        $table->date('effective_date')->after('set_number');

        $table->dropUnique(['client_program_id', 'workout_exercise_id', 'set_number']);
        $table->unique(['client_program_id', 'workout_exercise_id', 'set_number', 'effective_date']);
    });
}

public function down(): void
{
    Schema::table('client_program_exercise_targets', function (Blueprint $table) {
        $table->dropUnique(['client_program_id', 'workout_exercise_id', 'set_number', 'effective_date']);
        $table->dropColumn('effective_date');
        $table->unique(['client_program_id', 'workout_exercise_id', 'set_number']);
    });
}
```

Run: `php artisan migrate --no-interaction`

**Step 2: Update the model**

In `app/Models/ClientProgramExerciseTarget.php`, add `'effective_date'` to `$fillable` and add its cast:

```php
protected $fillable = [
    'client_program_id',
    'workout_exercise_id',
    'set_number',
    'effective_date',
    'target_weight',
];

protected function casts(): array
{
    return [
        'set_number' => 'integer',
        'effective_date' => 'date',
        'target_weight' => 'decimal:2',
    ];
}
```

**Step 3: Update the factory**

In `database/factories/ClientProgramExerciseTargetFactory.php`, add `'effective_date' => today()` to `definition()`:

```php
public function definition(): array
{
    return [
        'client_program_id' => ClientProgram::factory(),
        'workout_exercise_id' => WorkoutExercise::factory(),
        'set_number' => 1,
        'effective_date' => today(),
        'target_weight' => fake()->randomFloat(2, 20, 200),
    ];
}
```

**Step 4: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 2: Coach targets controller + view

**Files:**
- Modify: `app/Http/Controllers/Coach/ClientProgramTargetController.php`
- Modify: `resources/views/coach/programs/targets.blade.php`

**Step 1: Update `edit()`**

The controller must now pass two collections to the view:
- `$targetsByExercise` — latest target per (workout_exercise_id, set_number) for pre-filling inputs
- `$historyByExercise` — all rows grouped by workout_exercise_id for the history list

Replace the `$targetsByExercise` line in `edit()`:

```php
$clientProgram->load(['client', 'exerciseTargets']);

$targetsByExercise = $clientProgram->exerciseTargets
    ->sortByDesc('effective_date')
    ->groupBy('workout_exercise_id')
    ->map(fn ($targets) => $targets
        ->groupBy('set_number')
        ->map(fn ($setTargets) => $setTargets->first())
    );

$historyByExercise = $clientProgram->exerciseTargets
    ->sortByDesc('effective_date')
    ->groupBy('workout_exercise_id');

return view('coach.programs.targets', compact('program', 'clientProgram', 'targetsByExercise', 'historyByExercise'));
```

**Step 2: Update `update()`**

Change `updateOrCreate` to include `effective_date = today()` in the match key, so each day's save creates a new historical row. Change the delete to only remove today's row (preserving history):

Replace the entire `foreach` loop:

```php
foreach ($validated['targets'] ?? [] as $workoutExerciseId => $sets) {
    $workoutExercise = $validExercises->get($workoutExerciseId);

    if (! $workoutExercise) {
        continue;
    }

    foreach ($sets as $setNumber => $weight) {
        if ($setNumber < 1 || $setNumber > $workoutExercise->sets) {
            continue;
        }

        if ($weight === null) {
            ClientProgramExerciseTarget::where('client_program_id', $clientProgram->id)
                ->where('workout_exercise_id', $workoutExerciseId)
                ->where('set_number', $setNumber)
                ->where('effective_date', today()->toDateString())
                ->delete();

            continue;
        }

        ClientProgramExerciseTarget::updateOrCreate(
            [
                'client_program_id' => $clientProgram->id,
                'workout_exercise_id' => $workoutExerciseId,
                'set_number' => $setNumber,
                'effective_date' => today()->toDateString(),
            ],
            ['target_weight' => $weight]
        );
    }
}
```

**Step 3: Update the targets view**

In `resources/views/coach/programs/targets.blade.php`, after the set inputs `@endfor` inside each exercise row, add a history section. Find the closing `</div>` of the outer exercise row div (the one with `flex flex-col gap-1`) and add before it:

```blade
@if($historyByExercise->has($workoutExercise->id) && $historyByExercise->get($workoutExercise->id)->isNotEmpty())
    <details class="mt-2">
        <summary class="text-xs text-gray-400 dark:text-gray-500 cursor-pointer select-none hover:text-gray-600 dark:hover:text-gray-300">
            Target history
        </summary>
        <div class="mt-2 space-y-1 pl-2 border-l-2 border-gray-200 dark:border-gray-700">
            @foreach($historyByExercise->get($workoutExercise->id)->groupBy(fn ($t) => $t->effective_date->format('Y-m-d'))->sortKeysDesc() as $date => $entries)
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                    @foreach($entries->sortBy('set_number') as $entry)
                        &middot; Set {{ $entry->set_number }}: {{ $entry->target_weight }} kg
                    @endforeach
                </div>
            @endforeach
        </div>
    </details>
@endif
```

**Step 4: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 3: Client program controller + view

**Files:**
- Modify: `app/Http/Controllers/Client/ProgramController.php`
- Modify: `resources/views/client/program.blade.php`

**Step 1: Update the controller**

Replace the entire `index()` method:

```php
public function index(): View
{
    $user = auth()->user();

    $activeProgram = $user->activeProgram()?->load('program.workouts.exercises.exercise');

    $currentTargets = collect();
    $targetHistory = collect();

    if ($activeProgram) {
        $allTargets = $activeProgram->exerciseTargets()
            ->orderByDesc('effective_date')
            ->get();

        $currentTargets = $allTargets
            ->groupBy('workout_exercise_id')
            ->map(fn ($targets) => $targets
                ->groupBy('set_number')
                ->map(fn ($setTargets) => $setTargets->first())
            );

        $targetHistory = $allTargets->groupBy('workout_exercise_id');
    }

    return view('client.program', [
        'activeProgram' => $activeProgram,
        'currentTargets' => $currentTargets,
        'targetHistory' => $targetHistory,
    ]);
}
```

**Step 2: Update the client program view**

In `resources/views/client/program.blade.php`, find the exercise row inside the `@foreach($workout->exercises as $workoutExercise)` loop. Currently it ends with the muscle group badge. After the `<p class="text-sm text-gray-500 ...">{{ sets × reps }}</p>` and before the closing `</div>` of the exercise info div, add:

```blade
@if($currentTargets->has($workoutExercise->id))
    <div class="mt-1 flex flex-wrap gap-1">
        @foreach($currentTargets->get($workoutExercise->id)->sortKeys() as $setNum => $target)
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300">
                Set {{ $setNum }}: {{ $target->target_weight }} kg
            </span>
        @endforeach
    </div>
@endif

@if($targetHistory->has($workoutExercise->id) && $targetHistory->get($workoutExercise->id)->count() > 0)
    <details class="mt-1">
        <summary class="text-xs text-gray-400 dark:text-gray-500 cursor-pointer select-none">Target history</summary>
        <div class="mt-1 space-y-0.5 pl-2 border-l-2 border-gray-200 dark:border-gray-700">
            @foreach($targetHistory->get($workoutExercise->id)->groupBy(fn ($t) => $t->effective_date->format('Y-m-d'))->sortKeysDesc() as $date => $entries)
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-medium">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                    @foreach($entries->sortBy('set_number') as $entry)
                        &middot; Set {{ $entry->set_number }}: {{ $entry->target_weight }} kg
                    @endforeach
                </div>
            @endforeach
        </div>
    </details>
@endif
```

**Step 3: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 4: Analytics — controller + chart

**Files:**
- Modify: `app/Http/Controllers/Coach/AnalyticsController.php`
- Modify: `resources/views/coach/clients/analytics.blade.php`

**Step 1: Load target history in the controller**

In `AnalyticsController::show()`, after the `// --- Exercise Progression ---` block (after line ~238), add:

```php
// --- Exercise Target History ---
$exerciseTargetHistory = [];
$activeClientProgram = $client->activeProgram();

if ($activeClientProgram) {
    $allTargets = $activeClientProgram->exerciseTargets()
        ->with('workoutExercise')
        ->orderBy('effective_date')
        ->get();

    foreach ($allTargets as $target) {
        $exerciseId = $target->workoutExercise->exercise_id;
        $date = $target->effective_date->format('Y-m-d');

        if (! isset($exerciseTargetHistory[$exerciseId][$date])) {
            $exerciseTargetHistory[$exerciseId][$date] = (float) $target->target_weight;
        } else {
            $exerciseTargetHistory[$exerciseId][$date] = max(
                $exerciseTargetHistory[$exerciseId][$date],
                (float) $target->target_weight
            );
        }
    }

    foreach ($exerciseTargetHistory as $exId => $dateMap) {
        $points = [];
        foreach ($dateMap as $date => $weight) {
            $points[] = ['date' => $date, 'target' => $weight];
        }
        $exerciseTargetHistory[$exId] = $points;
    }
}
```

Add `$exerciseTargetHistory` to the `compact()` call in the return statement.

**Step 2: Pass target history to the Alpine component**

In `resources/views/coach/clients/analytics.blade.php`, find line 257:

```blade
<div x-data="exerciseProgression({{ json_encode($exerciseProgressionData) }}, {{ json_encode($exercisesByMuscleGroup) }})" x-init="init()">
```

Change to:

```blade
<div x-data="exerciseProgression({{ json_encode($exerciseProgressionData) }}, {{ json_encode($exercisesByMuscleGroup) }}, {{ json_encode($exerciseTargetHistory) }})" x-init="init()">
```

**Step 3: Update the `exerciseProgression` JS function**

Find `function exerciseProgression(allData, exerciseGroups) {` (line ~445) and replace the entire function with:

```js
function exerciseProgression(allData, exerciseGroups, targetHistory = {}) {
    return {
        selectedExercise: '',
        exerciseGroups,
        summary: null,

        init() {
            const firstGroup = Object.values(exerciseGroups)[0];
            if (firstGroup && firstGroup.length > 0) {
                this.selectedExercise = String(firstGroup[0].id);
            }
            this.$nextTick(() => {
                if (this.selectedExercise) this.updateChart();
            });
        },

        updateChart() {
            const data = allData[this.selectedExercise] || [];
            const existing = Chart.getChart(this.$refs.canvas);
            if (existing) existing.destroy();

            if (data.length === 0) {
                this.summary = null;
                return;
            }

            const startW = data[0].weight;
            const endW = data[data.length - 1].weight;
            const change = Math.round((endW - startW) * 100) / 100;
            const changePercent = startW > 0 ? Math.round((change / startW) * 1000) / 10 : 0;

            this.summary = {
                startWeight: startW,
                endWeight: endW,
                change,
                changePercent,
                sessions: data.length,
            };

            const ctx = this.$refs.canvas.getContext('2d');
            const theme = chartTheme();
            const labels = data.map(d => {
                const date = new Date(d.date + 'T00:00:00');
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });

            const targets = (targetHistory[this.selectedExercise] || [])
                .slice()
                .sort((a, b) => a.date.localeCompare(b.date));

            function activeTarget(dateStr) {
                let result = null;
                for (const t of targets) {
                    if (t.date <= dateStr) { result = t.target; } else { break; }
                }
                return result;
            }

            const targetValues = data.map(d => activeTarget(d.date));
            const hasTargets = targets.length > 0;

            const datasets = [{
                label: 'Top Set Weight (kg)',
                data: data.map(d => d.weight),
                borderColor: '#8B5CF6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
            }];

            if (hasTargets) {
                datasets.push({
                    label: 'Target (kg)',
                    data: targetValues,
                    borderColor: '#f59e0b',
                    backgroundColor: 'transparent',
                    borderDash: [5, 5],
                    pointRadius: 0,
                    tension: 0,
                    stepped: true,
                });
            }

            new Chart(ctx, {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: hasTargets, labels: { color: theme.tickColor, boxWidth: 12, padding: 12 } },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    if (ctx.datasetIndex === 0) {
                                        const d = data[ctx.dataIndex];
                                        return d.weight + 'kg x ' + d.reps + ' reps';
                                    }
                                    return ctx.parsed.y !== null ? 'Target: ' + ctx.parsed.y + 'kg' : null;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { ticks: { maxTicksLimit: 10, color: theme.tickColor }, grid: { color: theme.gridColor } },
                        y: { beginAtZero: false, ticks: { color: theme.tickColor }, grid: { color: theme.gridColor } }
                    }
                }
            });
        }
    };
}
```

**Step 4: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 5: Tests

**Files:**
- Modify: `tests/Feature/Coach/ClientProgramTargetTest.php`

**Step 1: Update all factory calls to include `effective_date`**

Every `ClientProgramExerciseTarget::factory()->create([...])` call in the test file must include `'effective_date' => today()`. Update all existing factory calls.

**Step 2: Update the "set" test to verify `effective_date`**

```php
it('coach can set a target weight for an exercise', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => [1 => '80.00', 2 => '75.00', 3 => '70.00']],
        ])
        ->assertRedirect();

    $target = ClientProgramExerciseTarget::where([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'set_number' => 1,
        'effective_date' => today()->toDateString(),
    ])->first();

    expect($target)->not->toBeNull();
    expect($target->target_weight)->toEqual('80.00');
    expect(ClientProgramExerciseTarget::where('client_program_id', $this->clientProgram->id)->count())->toBe(3);
});
```

**Step 3: Add a history test**

```php
it('saving on a new day creates a new history record instead of overwriting', function () {
    ClientProgramExerciseTarget::factory()->create([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'set_number' => 1,
        'effective_date' => today()->subDay(),
        'target_weight' => 60.00,
    ]);

    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => [1 => '80.00']],
        ])
        ->assertRedirect();

    expect(ClientProgramExerciseTarget::where([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'set_number' => 1,
    ])->count())->toBe(2);

    expect(ClientProgramExerciseTarget::where([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'set_number' => 1,
        'effective_date' => today()->toDateString(),
    ])->first()->target_weight)->toEqual('80.00');
});
```

**Step 4: Add a same-day update test**

```php
it('saving on the same day updates the existing record', function () {
    ClientProgramExerciseTarget::factory()->create([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'set_number' => 1,
        'effective_date' => today(),
        'target_weight' => 60.00,
    ]);

    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => [1 => '75.00']],
        ])
        ->assertRedirect();

    expect(ClientProgramExerciseTarget::where([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'set_number' => 1,
    ])->count())->toBe(1);

    expect(ClientProgramExerciseTarget::where([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'set_number' => 1,
        'effective_date' => today()->toDateString(),
    ])->first()->target_weight)->toEqual('75.00');
});
```

**Step 5: Update the "clear" test — clearing only removes today's row**

```php
it('clears target when an empty value is submitted', function () {
    ClientProgramExerciseTarget::factory()->create([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'set_number' => 1,
        'effective_date' => today(),
        'target_weight' => 60.00,
    ]);

    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => [1 => null]],
        ])
        ->assertRedirect();

    expect(ClientProgramExerciseTarget::where('client_program_id', $this->clientProgram->id)->count())->toBe(0);
});
```

Also add a test that clearing does NOT remove a previous day's row:

```php
it('clearing does not remove historical records from previous days', function () {
    ClientProgramExerciseTarget::factory()->create([
        'client_program_id' => $this->clientProgram->id,
        'workout_exercise_id' => $this->workoutExercise->id,
        'set_number' => 1,
        'effective_date' => today()->subDay(),
        'target_weight' => 60.00,
    ]);

    $this->actingAs($this->coach)
        ->put(route('coach.programs.assignments.targets.update', [$this->program, $this->clientProgram]), [
            'targets' => [$this->workoutExercise->id => [1 => null]],
        ])
        ->assertRedirect();

    // Previous day's record remains
    expect(ClientProgramExerciseTarget::where('client_program_id', $this->clientProgram->id)->count())->toBe(1);
});
```

**Step 6: Run tests**

```bash
php artisan test --compact --filter=ClientProgramTargetTest
```

All tests must pass.

---

### Task 6: Pint + Commit

**Step 1: Run Pint across all dirty files**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 2: Run full target test suite one final time**

```bash
php artisan test --compact --filter=ClientProgramTargetTest
```

**Step 3: Commit**

```bash
git add \
  database/migrations/*effective_date* \
  app/Models/ClientProgramExerciseTarget.php \
  database/factories/ClientProgramExerciseTargetFactory.php \
  app/Http/Controllers/Coach/ClientProgramTargetController.php \
  app/Http/Controllers/Coach/AnalyticsController.php \
  app/Http/Controllers/Client/ProgramController.php \
  resources/views/coach/programs/targets.blade.php \
  resources/views/coach/clients/analytics.blade.php \
  resources/views/client/program.blade.php \
  tests/Feature/Coach/ClientProgramTargetTest.php

git commit -m "feat: track target weight history and overlay on analytics chart"
```
