# Exercise Progress Modal Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a "Progress" section to the exercise info modal in both the client program view and log-workout view, showing all-time PRs (max weight + estimated 1RM) and range-filtered weight/volume charts fetched via a new JSON endpoint.

**Architecture:** A new `Client\ExerciseProgressController` returns JSON for a single exercise scoped to `auth()->id()`. Both modals get Alpine.js state and methods that fetch on modal open and re-fetch on range change. Chart.js renders two line charts (weight progression, volume progression) inside the modal.

**Tech Stack:** Laravel 12, Pest 4, Alpine.js 3, Chart.js 4

---

### Task 1: ExerciseProgressController — tests + implementation

**Files:**
- Create: `app/Http/Controllers/Client/ExerciseProgressController.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/Client/ExerciseProgressTest.php`

---

**Step 1: Create the test file**

```bash
php artisan make:test --pest Client/ExerciseProgressTest
```

**Step 2: Write the failing tests**

Replace the contents of `tests/Feature/Client/ExerciseProgressTest.php`:

```php
<?php

use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\WorkoutLog;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $this->exercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);
});

it('returns null PRs and empty charts when client has no logs', function () {
    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk()
        ->assertJson([
            'maxWeight' => null,
            'estimated1rm' => null,
            'weightChart' => [],
            'volumeChart' => [],
        ]);
});

it('returns the correct max weight across all sets', function () {
    $log = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 80, 'reps' => 5, 'set_number' => 1]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 100, 'reps' => 3, 'set_number' => 2]);

    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk()
        ->assertJsonPath('maxWeight', 100.0);
});

it('calculates estimated 1rm using epley formula', function () {
    $log = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()]);
    // 100kg × 5 reps → 100 * (1 + 5/30) = 116.7
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 100, 'reps' => 5, 'set_number' => 1]);

    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk()
        ->assertJsonPath('estimated1rm', round(100 * (1 + 5 / 30), 1));
});

it('skips sets with zero reps in 1rm calculation', function () {
    $log = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 150, 'reps' => 0, 'set_number' => 1]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 100, 'reps' => 5, 'set_number' => 2]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk();

    // 150kg at 0 reps is skipped; 100kg × 5 reps wins
    expect($response->json('estimated1rm'))->toBe(round(100 * (1 + 5 / 30), 1));
});

it('limits chart data to the requested range but prs remain all-time', function () {
    $oldLog = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()->subDays(60)]);
    $recentLog = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()->subDays(10)]);
    ExerciseLog::factory()->create(['workout_log_id' => $oldLog->id, 'exercise_id' => $this->exercise->id, 'weight' => 90, 'reps' => 5, 'set_number' => 1]);
    ExerciseLog::factory()->create(['workout_log_id' => $recentLog->id, 'exercise_id' => $this->exercise->id, 'weight' => 100, 'reps' => 5, 'set_number' => 1]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise) . '?range=30')
        ->assertOk();

    expect($response->json('weightChart'))->toHaveCount(1);
    expect($response->json('weightChart.0.weight'))->toBe(100.0);
    expect($response->json('maxWeight'))->toBe(100.0); // all-time, includes old log
});

it('returns all chart data when range is 0', function () {
    $oldLog = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()->subDays(200)]);
    $recentLog = WorkoutLog::factory()->create(['client_id' => $this->client->id, 'completed_at' => now()->subDays(10)]);
    ExerciseLog::factory()->create(['workout_log_id' => $oldLog->id, 'exercise_id' => $this->exercise->id, 'weight' => 90, 'reps' => 5, 'set_number' => 1]);
    ExerciseLog::factory()->create(['workout_log_id' => $recentLog->id, 'exercise_id' => $this->exercise->id, 'weight' => 100, 'reps' => 5, 'set_number' => 1]);

    $response = $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise) . '?range=0')
        ->assertOk();

    expect($response->json('weightChart'))->toHaveCount(2);
});

it('defaults to 90 days for an invalid range value', function () {
    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise) . '?range=999')
        ->assertOk();
});

it('cannot see another clients exercise data', function () {
    $other = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $log = WorkoutLog::factory()->create(['client_id' => $other->id, 'completed_at' => now()]);
    ExerciseLog::factory()->create(['workout_log_id' => $log->id, 'exercise_id' => $this->exercise->id, 'weight' => 150, 'reps' => 5, 'set_number' => 1]);

    $this->actingAs($this->client)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertOk()
        ->assertJsonPath('maxWeight', null);
});

it('cannot be accessed by coaches', function () {
    $this->actingAs($this->coach)
        ->getJson(route('client.exercises.progress', $this->exercise))
        ->assertRedirect();
});
```

**Step 3: Run tests to verify they fail**

```bash
php artisan test --compact --filter=ExerciseProgressTest
```

Expected: all fail with route/controller not found errors.

**Step 4: Create the controller**

```bash
php artisan make:class app/Http/Controllers/Client/ExerciseProgressController.php
```

Replace the contents of `app/Http/Controllers/Client/ExerciseProgressController.php`:

```php
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseProgressController extends Controller
{
    public function __invoke(Request $request, Exercise $exercise): JsonResponse
    {
        $client = auth()->user();
        $range = (int) $request->get('range', 90);

        if (! in_array($range, [30, 90, 365, 0])) {
            $range = 90;
        }

        // PRs — all time, no range filter
        $allLogs = ExerciseLog::query()
            ->whereHas('workoutLog', fn ($q) => $q->where('client_id', $client->id))
            ->where('exercise_id', $exercise->id)
            ->get(['weight', 'reps']);

        $maxWeight = $allLogs->max('weight');

        $estimated1rm = $allLogs
            ->filter(fn ($log) => $log->reps > 0)
            ->map(fn ($log) => (float) $log->weight * (1 + $log->reps / 30))
            ->max();

        // Chart data — range limited
        $chartLogs = ExerciseLog::query()
            ->whereHas('workoutLog', fn ($q) => $q
                ->where('client_id', $client->id)
                ->when($range > 0, fn ($q) => $q->where('completed_at', '>=', now()->subDays($range)->startOfDay()))
            )
            ->where('exercise_id', $exercise->id)
            ->with('workoutLog:id,completed_at')
            ->get();

        $grouped = $chartLogs
            ->groupBy(fn ($log) => $log->workoutLog->completed_at->format('Y-m-d'))
            ->sortKeys();

        $weightChart = $grouped->map(fn ($logs, $date) => [
            'date' => $date,
            'weight' => (float) $logs->max('weight'),
        ])->values()->all();

        $volumeChart = $grouped->map(fn ($logs, $date) => [
            'date' => $date,
            'volume' => (float) $logs->sum(fn ($l) => $l->weight * $l->reps),
        ])->values()->all();

        return response()->json([
            'maxWeight' => $maxWeight !== null ? (float) $maxWeight : null,
            'estimated1rm' => $estimated1rm !== null ? round($estimated1rm, 1) : null,
            'weightChart' => $weightChart,
            'volumeChart' => $volumeChart,
        ]);
    }
}
```

**Step 5: Add the route**

In `routes/web.php`, inside the `client.` named route group (after the `history/{workoutLog}` lines), add:

```php
Route::get('exercises/{exercise}/progress', Client\ExerciseProgressController::class)->name('exercises.progress');
```

**Step 6: Run tests to verify they pass**

```bash
php artisan test --compact --filter=ExerciseProgressTest
```

Expected: all 9 tests pass.

**Step 7: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 8: Commit**

```bash
git add app/Http/Controllers/Client/ExerciseProgressController.php routes/web.php tests/Feature/Client/ExerciseProgressTest.php
git commit -m "feat: add exercise progress JSON endpoint"
```

---

### Task 2: program.blade.php — progress section in exercise modal

**Files:**
- Modify: `resources/views/client/program.blade.php`

The modal currently shows name/muscle group, video embed, and description. We add PRs + charts below.

---

**Step 1: Change the outer `x-data` to use a named function**

Find line 4–8 in `resources/views/client/program.blade.php`:

```html
    <div
    class="space-y-6"
    x-data="{ selectedExercise: null }"
    @keydown.escape.window="selectedExercise = null"
>
```

Replace with:

```html
    <div
    class="space-y-6"
    x-data="exerciseInfoModal()"
    @keydown.escape.window="selectedExercise = null"
>
```

**Step 2: Add `exerciseId` to the `@click` handler**

Find the `@click="selectedExercise = {` block (lines 55–61). Add `exerciseId` as the first property:

```html
                                @click="selectedExercise = {
                                    exerciseId: {{ $workoutExercise->exercise->id }},
                                    name: @js($workoutExercise->exercise->name),
                                    muscleGroup: @js(ucfirst(str_replace('_', ' ', $workoutExercise->exercise->muscle_group))),
                                    description: @js($workoutExercise->exercise->description),
                                    {{-- Uses {{ }} not @js() — single quotes safe in double-quoted HTML attribute; outputs unescaped slashes for test assertions --}}
                                    embedUrl: '{{ $workoutExercise->exercise->getYoutubeEmbedUrl() ?? '' }}',
                                }"
```

**Step 3: Add the progress section to the modal**

Find the closing description block in the modal (lines 179–183):

```html
                <!-- Description -->
                <div class="px-5 pb-8">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('client.program.description') }}</h3>
                    <p x-show="selectedExercise && selectedExercise.description" class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="selectedExercise ? selectedExercise.description : ''"></p>
                    <p x-show="!selectedExercise || !selectedExercise.description" class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('client.program.no_description') }}</p>
                </div>
```

Add the progress section immediately after this block, before the closing `</div>` of the modal content div:

```html
                <!-- Progress Section -->
                <div class="px-5 pb-8 border-t border-gray-100 dark:border-gray-800 pt-5">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('client.exercise_progress.heading') }}</h3>

                    <!-- Range selector -->
                    <div class="flex gap-1 mb-4">
                        <template x-for="r in [30, 90, 365, 0]" :key="r">
                            <button
                                type="button"
                                @click="progressRange = r; selectedExercise && loadProgress(selectedExercise.exerciseId, r)"
                                :class="progressRange === r ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300'"
                                class="px-2.5 py-1 rounded text-xs font-medium transition-colors"
                                x-text="r === 30 ? '30d' : r === 90 ? '90d' : r === 365 ? '1yr' : '{{ __('client.exercise_progress.all_time') }}'"
                            ></button>
                        </template>
                    </div>

                    <!-- Loading spinner -->
                    <div x-show="progressLoading" class="flex items-center justify-center py-8">
                        <svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>

                    <!-- Data -->
                    <template x-if="!progressLoading && progressData">
                        <div class="space-y-4">
                            <!-- PR stats -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('client.exercise_progress.max_weight') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="progressData.maxWeight !== null ? progressData.maxWeight + ' kg' : '—'"></p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('client.exercise_progress.est_1rm') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="progressData.estimated1rm !== null ? progressData.estimated1rm + ' kg' : '—'"></p>
                                </div>
                            </div>

                            <!-- No chart data -->
                            <p x-show="progressData.weightChart.length === 0" class="text-sm text-gray-400 dark:text-gray-500 italic text-center py-4">{{ __('client.exercise_progress.no_data') }}</p>

                            <!-- Charts -->
                            <template x-if="progressData.weightChart.length > 0">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.exercise_progress.weight_chart') }}</p>
                                        <canvas id="progressWeightChart" height="120"></canvas>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.exercise_progress.volume_chart') }}</p>
                                        <canvas id="progressVolumeChart" height="120"></canvas>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
```

**Step 4: Add `@push('scripts')` with Chart.js and the `exerciseInfoModal` function**

At the very end of the file, before `</x-layouts.client>`, add:

```html
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        function exerciseInfoModal() {
            return {
                selectedExercise: null,
                progressData: null,
                progressRange: 90,
                progressLoading: false,
                _progressCharts: [],

                init() {
                    this.$watch('selectedExercise', (val) => {
                        if (val && val.exerciseId) {
                            this.progressRange = 90;
                            this.loadProgress(val.exerciseId, 90);
                        } else {
                            this.progressData = null;
                            this._destroyCharts();
                        }
                    });
                },

                loadProgress(exerciseId, range) {
                    this.progressLoading = true;
                    this.progressData = null;
                    this._destroyCharts();
                    fetch(`/client/exercises/${exerciseId}/progress?range=${range}`)
                        .then(r => r.json())
                        .then(data => {
                            this.progressData = data;
                            this.progressLoading = false;
                            this.$nextTick(() => this._renderCharts(data));
                        });
                },

                _destroyCharts() {
                    this._progressCharts.forEach(c => c.destroy());
                    this._progressCharts = [];
                },

                _renderCharts(data) {
                    if (data.weightChart.length === 0) {
                        return;
                    }

                    const labels = data.weightChart.map(p => p.date);
                    const commonOptions = {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { x: { ticks: { maxTicksLimit: 8 } } },
                    };

                    const wCtx = document.getElementById('progressWeightChart');
                    if (wCtx) {
                        this._progressCharts.push(new Chart(wCtx, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    data: data.weightChart.map(p => p.weight),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    pointRadius: 3,
                                }],
                            },
                            options: commonOptions,
                        }));
                    }

                    const vCtx = document.getElementById('progressVolumeChart');
                    if (vCtx) {
                        this._progressCharts.push(new Chart(vCtx, {
                            type: 'line',
                            data: {
                                labels: data.volumeChart.map(p => p.date),
                                datasets: [{
                                    data: data.volumeChart.map(p => p.volume),
                                    borderColor: 'rgb(16, 185, 129)',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    pointRadius: 3,
                                }],
                            },
                            options: commonOptions,
                        }));
                    }
                },
            };
        }
    </script>
    @endpush
```

**Step 5: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 6: Commit**

```bash
git add resources/views/client/program.blade.php
git commit -m "feat: add exercise progress section to program modal"
```

---

### Task 3: log-workout.blade.php — progress section in exercise modal

**Files:**
- Modify: `resources/views/client/log-workout.blade.php`

The log-workout modal already has a `workoutLogger()` Alpine component (in `@push('scripts')`). The `selectedExercise` state is already there. We add progress state + methods into the same component.

**Note:** In log-workout, exercise objects use `exercise_id` (snake_case), not `exerciseId` like in the program view. The range selector `@click` must use `selectedExercise.exercise_id` accordingly.

---

**Step 1: Add Chart.js CDN in `@push('scripts')`**

Find the `@push('scripts')` section (around line 384). Add the Chart.js CDN **before** the existing `<script>` tag:

```html
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
```

(The `<script>` that was already there continues as normal; just insert the CDN line before it.)

**Step 2: Add progress state to `workoutLogger()` return object**

Inside the `workoutLogger()` function's return object, find:

```js
                selectedExercise: null,
```

Replace with:

```js
                selectedExercise: null,
                progressData: null,
                progressRange: 90,
                progressLoading: false,
                _progressCharts: [],
```

**Step 3: Add `$watch` to `init()`**

Inside the `init()` method of `workoutLogger()`, add at the end of the method:

```js
                    this.$watch('selectedExercise', (val) => {
                        if (val && val.exercise_id) {
                            this.progressRange = 90;
                            this.loadProgress(val.exercise_id, 90);
                        } else {
                            this.progressData = null;
                            this._destroyProgressCharts();
                        }
                    });
```

**Step 4: Add progress methods to `workoutLogger()` return object**

Find the last method in the `workoutLogger()` return object (before the closing `};`). Add these three methods after it:

```js
                loadProgress(exerciseId, range) {
                    this.progressLoading = true;
                    this.progressData = null;
                    this._destroyProgressCharts();
                    fetch(`/client/exercises/${exerciseId}/progress?range=${range}`)
                        .then(r => r.json())
                        .then(data => {
                            this.progressData = data;
                            this.progressLoading = false;
                            this.$nextTick(() => this._renderProgressCharts(data));
                        });
                },

                _destroyProgressCharts() {
                    this._progressCharts.forEach(c => c.destroy());
                    this._progressCharts = [];
                },

                _renderProgressCharts(data) {
                    if (data.weightChart.length === 0) {
                        return;
                    }

                    const labels = data.weightChart.map(p => p.date);
                    const commonOptions = {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { x: { ticks: { maxTicksLimit: 8 } } },
                    };

                    const wCtx = document.getElementById('logProgressWeightChart');
                    if (wCtx) {
                        this._progressCharts.push(new Chart(wCtx, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    data: data.weightChart.map(p => p.weight),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    pointRadius: 3,
                                }],
                            },
                            options: commonOptions,
                        }));
                    }

                    const vCtx = document.getElementById('logProgressVolumeChart');
                    if (vCtx) {
                        this._progressCharts.push(new Chart(vCtx, {
                            type: 'line',
                            data: {
                                labels: data.volumeChart.map(p => p.date),
                                datasets: [{
                                    data: data.volumeChart.map(p => p.volume),
                                    borderColor: 'rgb(16, 185, 129)',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    pointRadius: 3,
                                }],
                            },
                            options: commonOptions,
                        }));
                    }
                },
```

Note: canvas IDs here are `logProgressWeightChart` and `logProgressVolumeChart` (different from program modal IDs) to avoid any potential conflicts.

**Step 5: Add progress section to the log-workout modal**

Find the description section at the end of the log-workout modal (around line 375–379):

```html
                <div class="px-5 pb-8">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('client.log_workout.description') }}</h3>
                    <p x-show="selectedExercise && selectedExercise.description" class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="selectedExercise ? selectedExercise.description : ''"></p>
                    <p x-show="!selectedExercise || !selectedExercise.description" class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('client.log_workout.no_description') }}</p>
                </div>
```

Add the progress section immediately after this block, before the closing `</div>` of the modal content div:

```html
                <!-- Progress Section -->
                <div class="px-5 pb-8 border-t border-gray-100 dark:border-gray-800 pt-5">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('client.exercise_progress.heading') }}</h3>

                    <!-- Range selector -->
                    <div class="flex gap-1 mb-4">
                        <template x-for="r in [30, 90, 365, 0]" :key="r">
                            <button
                                type="button"
                                @click="progressRange = r; selectedExercise && loadProgress(selectedExercise.exercise_id, r)"
                                :class="progressRange === r ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300'"
                                class="px-2.5 py-1 rounded text-xs font-medium transition-colors"
                                x-text="r === 30 ? '30d' : r === 90 ? '90d' : r === 365 ? '1yr' : '{{ __('client.exercise_progress.all_time') }}'"
                            ></button>
                        </template>
                    </div>

                    <!-- Loading spinner -->
                    <div x-show="progressLoading" class="flex items-center justify-center py-8">
                        <svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>

                    <!-- Data -->
                    <template x-if="!progressLoading && progressData">
                        <div class="space-y-4">
                            <!-- PR stats -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('client.exercise_progress.max_weight') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="progressData.maxWeight !== null ? progressData.maxWeight + ' kg' : '—'"></p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('client.exercise_progress.est_1rm') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="progressData.estimated1rm !== null ? progressData.estimated1rm + ' kg' : '—'"></p>
                                </div>
                            </div>

                            <!-- No chart data -->
                            <p x-show="progressData.weightChart.length === 0" class="text-sm text-gray-400 dark:text-gray-500 italic text-center py-4">{{ __('client.exercise_progress.no_data') }}</p>

                            <!-- Charts -->
                            <template x-if="progressData.weightChart.length > 0">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.exercise_progress.weight_chart') }}</p>
                                        <canvas id="logProgressWeightChart" height="120"></canvas>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.exercise_progress.volume_chart') }}</p>
                                        <canvas id="logProgressVolumeChart" height="120"></canvas>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
```

**Step 6: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 7: Commit**

```bash
git add resources/views/client/log-workout.blade.php
git commit -m "feat: add exercise progress section to log-workout modal"
```

---

### Task 4: Translation keys

**Files:**
- Modify: `lang/en/client.php`
- Modify: `lang/sl/client.php`
- Modify: `lang/hr/client.php`

---

**Step 1: Add keys to `lang/en/client.php`**

Find the end of the file (before the closing `];`) and add a new top-level key:

```php
    'exercise_progress' => [
        'heading' => 'My Progress',
        'max_weight' => 'Max Weight',
        'est_1rm' => 'Est. 1RM',
        'no_data' => 'No logged sets yet for this exercise.',
        'weight_chart' => 'Best Set Weight (kg)',
        'volume_chart' => 'Session Volume (kg)',
        'all_time' => 'All',
    ],
```

**Step 2: Add keys to `lang/sl/client.php`**

Same location, same structure:

```php
    'exercise_progress' => [
        'heading' => 'Moj napredek',
        'max_weight' => 'Največja teža',
        'est_1rm' => 'Ocenjen 1RM',
        'no_data' => 'Za to vajo še ni zabeleženih setov.',
        'weight_chart' => 'Najboljši set (kg)',
        'volume_chart' => 'Volumen treninga (kg)',
        'all_time' => 'Vse',
    ],
```

**Step 3: Add keys to `lang/hr/client.php`**

```php
    'exercise_progress' => [
        'heading' => 'Moj napredak',
        'max_weight' => 'Maksimalna težina',
        'est_1rm' => 'Proc. 1RM',
        'no_data' => 'Još nema zabilježenih setova za ovu vježbu.',
        'weight_chart' => 'Najbolji set (kg)',
        'volume_chart' => 'Volumen treninga (kg)',
        'all_time' => 'Sve',
    ],
```

**Step 4: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 5: Run all new tests**

```bash
php artisan test --compact --filter=ExerciseProgressTest
```

Expected: all 9 tests pass.

**Step 6: Commit**

```bash
git add lang/en/client.php lang/sl/client.php lang/hr/client.php
git commit -m "feat: add exercise progress translation keys"
```
