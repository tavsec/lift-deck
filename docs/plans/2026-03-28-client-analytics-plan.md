# Client Analytics Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Show analytics to clients — nutrition charts on the nutrition page, exercise progression on the history page, and daily metrics history accessible from the check-in page.

**Architecture:** Extract all analytics data-prep logic from `Coach\AnalyticsController` into `App\Services\AnalyticsService`. The coach controller is refactored to call the service. Three client controllers (`NutritionController`, `HistoryController`, `CheckInController`) call the relevant service methods. New views/sections embed Chart.js charts identical to the coach experience.

**Tech Stack:** Laravel 12, PHP 8.4, Chart.js 4 (CDN, already used in coach analytics view), Alpine.js 3, Tailwind CSS v3, Pest 4

---

### Task 1: Create AnalyticsService with nutrition method

Extract `getNutritionData` logic from `Coach\AnalyticsController` (lines 92–159).

**Files:**
- Create: `app/Services/AnalyticsService.php`
- Test: `tests/Feature/Services/AnalyticsServiceTest.php`

**Step 1: Create the test file**

```bash
php artisan make:test --pest Services/AnalyticsServiceTest
```

**Step 2: Write the failing test**

In `tests/Feature/Services/AnalyticsServiceTest.php`:

```php
<?php

use App\Models\MacroGoal;
use App\Models\MealLog;
use App\Models\User;
use App\Services\AnalyticsService;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $this->service = new AnalyticsService();
});

it('returns nutrition data for date range', function () {
    MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->format('Y-m-d'),
        'calories' => 2000,
        'protein' => 150,
        'carbs' => 200,
        'fat' => 70,
    ]);

    $result = $this->service->getNutritionData(
        $this->client,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result)->toHaveKeys(['nutritionData', 'nutritionStats']);
    expect($result['nutritionStats']['daysLogged'])->toBe(1);
    expect($result['nutritionStats']['avgCalories'])->toBe(2000);
});

it('returns zero stats when no meals logged', function () {
    $result = $this->service->getNutritionData(
        $this->client,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result['nutritionStats']['daysLogged'])->toBe(0);
    expect($result['nutritionStats']['avgCalories'])->toBe(0);
});
```

**Step 3: Run to confirm failure**

```bash
php artisan test --compact --filter=AnalyticsServiceTest
```
Expected: FAIL — class not found.

**Step 4: Create the service**

```bash
php artisan make:class Services/AnalyticsService
```

In `app/Services/AnalyticsService.php`, add the `getNutritionData` method — copy the nutrition block from `Coach\AnalyticsController::show()` (lines 92–159), wrap it in a method:

```php
<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class AnalyticsService
{
    public function getNutritionData(User $client, string $from, string $to): array
    {
        $startDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);

        $dates = collect();
        $dayCount = $startDate->diffInDays($endDate) + 1;
        for ($i = 0; $i < $dayCount; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        $mealLogs = $client->mealLogs()
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->orderBy('date')
            ->get();

        $macroGoals = $client->macroGoals()
            ->whereDate('effective_date', '<=', $to)
            ->orderBy('effective_date')
            ->get();

        $nutritionData = [];
        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;
        $daysWithMeals = 0;
        $daysAdherent = 0;
        $daysWithGoal = 0;

        foreach ($dates as $date) {
            $dayLogs = $mealLogs->filter(fn ($l) => $l->date->format('Y-m-d') === $date);
            $dayCals = (int) $dayLogs->sum('calories');
            $dayProtein = (float) $dayLogs->sum('protein');
            $dayCarbs = (float) $dayLogs->sum('carbs');
            $dayFat = (float) $dayLogs->sum('fat');

            $activeGoal = $macroGoals->filter(fn ($g) => $g->effective_date->format('Y-m-d') <= $date)
                ->sortByDesc('effective_date')
                ->first();

            $goalCalories = $activeGoal?->calories;

            if ($dayLogs->count() > 0) {
                $daysWithMeals++;
                $totalCalories += $dayCals;
                $totalProtein += $dayProtein;
                $totalCarbs += $dayCarbs;
                $totalFat += $dayFat;

                if ($goalCalories) {
                    $daysWithGoal++;
                    $deviation = abs($dayCals - $goalCalories) / $goalCalories;
                    if ($deviation <= 0.10) {
                        $daysAdherent++;
                    }
                }
            }

            $nutritionData[] = [
                'date' => $date,
                'calories' => $dayCals,
                'protein' => round($dayProtein, 1),
                'carbs' => round($dayCarbs, 1),
                'fat' => round($dayFat, 1),
                'goalCalories' => $goalCalories,
            ];
        }

        $nutritionStats = [
            'avgCalories' => $daysWithMeals > 0 ? round($totalCalories / $daysWithMeals) : 0,
            'avgProtein' => $daysWithMeals > 0 ? round($totalProtein / $daysWithMeals, 1) : 0,
            'avgCarbs' => $daysWithMeals > 0 ? round($totalCarbs / $daysWithMeals, 1) : 0,
            'avgFat' => $daysWithMeals > 0 ? round($totalFat / $daysWithMeals, 1) : 0,
            'adherenceRate' => $daysWithGoal > 0 ? round(($daysAdherent / $daysWithGoal) * 100) : null,
            'daysLogged' => $daysWithMeals,
        ];

        return compact('nutritionData', 'nutritionStats');
    }
}
```

**Step 5: Run tests to confirm passing**

```bash
php artisan test --compact --filter=AnalyticsServiceTest
```
Expected: PASS.

**Step 6: Commit**

```bash
git add app/Services/AnalyticsService.php tests/Feature/Services/AnalyticsServiceTest.php
git commit -m "feat: add AnalyticsService with getNutritionData method"
```

---

### Task 2: Add getCheckInChartData method to AnalyticsService

**Files:**
- Modify: `app/Services/AnalyticsService.php`
- Modify: `tests/Feature/Services/AnalyticsServiceTest.php`

**Step 1: Write the failing test**

Add to `tests/Feature/Services/AnalyticsServiceTest.php`:

```php
it('returns check-in chart data for numeric metrics', function () {
    $metric = \App\Models\TrackingMetric::factory()->number()->create([
        'coach_id' => $this->coach->id,
    ]);
    \App\Models\ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
    ]);
    \App\Models\DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '82.5',
    ]);

    $result = $this->service->getCheckInChartData(
        $this->client,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result)->toHaveKeys(['checkInCharts', 'tableMetrics', 'checkInTableData', 'imageMetrics', 'imageMetricData']);
    expect($result['checkInCharts'])->toHaveCount(1);
    expect($result['checkInCharts'][0]['data'][0]['value'])->toBe(82.5);
});

it('returns empty check-in data when no metrics assigned', function () {
    $result = $this->service->getCheckInChartData(
        $this->client,
        now()->subDays(6)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result['checkInCharts'])->toBeEmpty();
    expect($result['tableMetrics']->count())->toBe(0);
});
```

**Step 2: Run to confirm failure**

```bash
php artisan test --compact --filter="returns check-in chart"
```
Expected: FAIL — method not found.

**Step 3: Add the method to AnalyticsService**

Copy the check-in logic from `Coach\AnalyticsController::show()` (lines 41–90) plus the image metrics block (lines 161–192). Note: the method needs access to the coach's metrics — look up the coach via `$client->coach_id`.

Add to `app/Services/AnalyticsService.php`:

```php
public function getCheckInChartData(User $client, string $from, string $to): array
{
    $startDate = Carbon::parse($from);
    $endDate = Carbon::parse($to);

    $dates = collect();
    $dayCount = $startDate->diffInDays($endDate) + 1;
    for ($i = 0; $i < $dayCount; $i++) {
        $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
    }

    $assignedMetricIds = $client->assignedTrackingMetrics()->pluck('tracking_metric_id');

    $coach = \App\Models\User::find($client->coach_id);
    $assignedMetrics = $coach
        ? $coach->trackingMetrics()
            ->whereIn('id', $assignedMetricIds)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
        : collect();

    $dailyLogs = $client->dailyLogs()
        ->whereIn('tracking_metric_id', $assignedMetricIds)
        ->whereDate('date', '>=', $from)
        ->whereDate('date', '<=', $to)
        ->orderBy('date')
        ->get();

    $chartMetrics = $assignedMetrics->whereIn('type', ['number', 'scale']);
    $tableMetrics = $assignedMetrics->whereIn('type', ['boolean', 'text']);
    $imageMetrics = $assignedMetrics->where('type', 'image');

    $checkInCharts = [];
    foreach ($chartMetrics as $metric) {
        $metricLogs = $dailyLogs->where('tracking_metric_id', $metric->id);
        $dataPoints = [];
        foreach ($metricLogs as $log) {
            $dataPoints[] = [
                'date' => $log->date->format('Y-m-d'),
                'value' => (float) $log->value,
            ];
        }
        $checkInCharts[] = [
            'id' => $metric->id,
            'name' => $metric->name,
            'unit' => $metric->unit,
            'type' => $metric->type,
            'scaleMin' => $metric->scale_min,
            'scaleMax' => $metric->scale_max,
            'data' => $dataPoints,
        ];
    }

    $checkInTableData = [];
    foreach ($dates as $date) {
        $row = ['date' => $date];
        foreach ($tableMetrics as $metric) {
            $log = $dailyLogs->where('tracking_metric_id', $metric->id)
                ->first(fn ($l) => $l->date->format('Y-m-d') === $date);
            $row['metric_' . $metric->id] = $log?->value;
        }
        $checkInTableData[] = $row;
    }

    $imageMetricData = [];
    if ($imageMetrics->isNotEmpty()) {
        $imageLogs = \App\Models\DailyLog::where('client_id', $client->id)
            ->whereIn('tracking_metric_id', $imageMetrics->pluck('id'))
            ->where('value', 'uploaded')
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->with('media')
            ->orderByDesc('date')
            ->get();

        foreach ($imageMetrics as $metric) {
            $metricLogs = $imageLogs->where('tracking_metric_id', $metric->id);
            $photos = [];
            foreach ($metricLogs as $log) {
                $media = $log->getFirstMedia('check-in-image');
                if ($media) {
                    $photos[] = [
                        'date' => $log->date->format('Y-m-d'),
                        'thumbUrl' => route('media.daily-log', [$log, 'thumb']),
                        'fullUrl' => route('media.daily-log', [$log, 'full']),
                    ];
                }
            }
            $imageMetricData[] = [
                'id' => $metric->id,
                'name' => $metric->name,
                'photos' => $photos,
            ];
        }
    }

    return compact('checkInCharts', 'tableMetrics', 'checkInTableData', 'imageMetrics', 'imageMetricData');
}
```

**Step 4: Run tests**

```bash
php artisan test --compact --filter=AnalyticsServiceTest
```
Expected: PASS.

**Step 5: Commit**

```bash
git add app/Services/AnalyticsService.php tests/Feature/Services/AnalyticsServiceTest.php
git commit -m "feat: add getCheckInChartData to AnalyticsService"
```

---

### Task 3: Add getExerciseProgressionData method to AnalyticsService

**Files:**
- Modify: `app/Services/AnalyticsService.php`
- Modify: `tests/Feature/Services/AnalyticsServiceTest.php`

**Step 1: Write the failing test**

Add to `tests/Feature/Services/AnalyticsServiceTest.php`:

```php
it('returns exercise progression data', function () {
    $exercise = \App\Models\Exercise::factory()->create(['coach_id' => $this->coach->id]);
    $workoutLog = \App\Models\WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'completed_at' => now(),
    ]);
    \App\Models\ExerciseLog::factory()->create([
        'workout_log_id' => $workoutLog->id,
        'exercise_id' => $exercise->id,
        'weight' => 100,
        'reps' => 8,
    ]);

    $result = $this->service->getExerciseProgressionData(
        $this->client,
        now()->subDays(29)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result)->toHaveKeys(['exerciseProgressionData', 'exercisesByMuscleGroup', 'exerciseTargetHistory']);
    expect($result['exerciseProgressionData'])->toHaveKey($exercise->id);
});

it('returns empty exercise data when no workouts', function () {
    $result = $this->service->getExerciseProgressionData(
        $this->client,
        now()->subDays(29)->format('Y-m-d'),
        now()->format('Y-m-d')
    );

    expect($result['exerciseProgressionData'])->toBeEmpty();
    expect($result['exercisesByMuscleGroup']->isEmpty())->toBeTrue();
});
```

**Step 2: Run to confirm failure**

```bash
php artisan test --compact --filter="returns exercise progression"
```
Expected: FAIL — method not found.

**Step 3: Add the method to AnalyticsService**

Copy the exercise progression logic from `Coach\AnalyticsController::show()` (lines 194–277):

```php
public function getExerciseProgressionData(User $client, string $from, string $to): array
{
    $workoutLogs = $client->workoutLogs()
        ->whereDate('completed_at', '>=', $from)
        ->whereDate('completed_at', '<=', $to)
        ->with(['exerciseLogs.exercise'])
        ->orderBy('completed_at')
        ->get();

    $exerciseProgressionData = [];
    $exerciseList = [];

    foreach ($workoutLogs as $workoutLog) {
        foreach ($workoutLog->exerciseLogs as $exerciseLog) {
            $exId = $exerciseLog->exercise_id;
            $exercise = $exerciseLog->exercise;

            if (!isset($exerciseList[$exId])) {
                $exerciseList[$exId] = [
                    'id' => $exId,
                    'name' => $exercise->name,
                    'muscleGroup' => $exercise->muscle_group,
                ];
            }

            if (!isset($exerciseProgressionData[$exId])) {
                $exerciseProgressionData[$exId] = [];
            }

            $dateKey = $workoutLog->completed_at->format('Y-m-d');

            if (!isset($exerciseProgressionData[$exId][$dateKey])
                || $exerciseLog->weight > $exerciseProgressionData[$exId][$dateKey]['weight']) {
                $exerciseProgressionData[$exId][$dateKey] = [
                    'date' => $dateKey,
                    'weight' => (float) $exerciseLog->weight,
                    'reps' => $exerciseLog->reps,
                ];
            }
        }
    }

    foreach ($exerciseProgressionData as $exId => $sessions) {
        ksort($sessions);
        $exerciseProgressionData[$exId] = array_values($sessions);
    }

    $exercisesByMuscleGroup = collect($exerciseList)->groupBy('muscleGroup')->sortKeys();

    $exerciseTargetHistory = [];
    $activeClientProgram = $client->activeProgram();

    if ($activeClientProgram) {
        $allTargets = $activeClientProgram->exerciseTargets()
            ->with('workoutExercise')
            ->orderBy('effective_date')
            ->get();

        foreach ($allTargets as $target) {
            if ($target->effective_date === null) {
                continue;
            }

            $exerciseId = $target->workoutExercise->exercise_id;
            $date = $target->effective_date->format('Y-m-d');

            if (!isset($exerciseTargetHistory[$exerciseId][$date])) {
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

    return compact('exerciseProgressionData', 'exercisesByMuscleGroup', 'exerciseTargetHistory');
}
```

**Step 4: Run tests**

```bash
php artisan test --compact --filter=AnalyticsServiceTest
```
Expected: PASS.

**Step 5: Commit**

```bash
git add app/Services/AnalyticsService.php tests/Feature/Services/AnalyticsServiceTest.php
git commit -m "feat: add getExerciseProgressionData to AnalyticsService"
```

---

### Task 4: Refactor Coach AnalyticsController to use the service

**Files:**
- Modify: `app/Http/Controllers/Coach/AnalyticsController.php`
- Test: `tests/Feature/Coach/AnalyticsTest.php` (existing — run to verify no regressions)

**Step 1: Run existing coach analytics tests to establish baseline**

```bash
php artisan test --compact tests/Feature/Coach/AnalyticsTest.php tests/Feature/Coach/AnalyticsImageTest.php
```
Expected: PASS (baseline).

**Step 2: Refactor the controller**

Replace `app/Http/Controllers/Coach/AnalyticsController.php` with service calls. The `show` method becomes:

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Exports\CoachAnalyticsExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analyticsService) {}

    public function show(Request $request, User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $range = $request->get('range', '30');
        if ($range === 'custom') {
            $from = $request->get('from', now()->subDays(29)->format('Y-m-d'));
            $to = $request->get('to', now()->format('Y-m-d'));
        } else {
            $days = (int) $range;
            $from = now()->subDays($days - 1)->format('Y-m-d');
            $to = now()->format('Y-m-d');
        }

        $startDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);

        $dates = collect();
        $dayCount = $startDate->diffInDays($endDate) + 1;
        for ($i = 0; $i < $dayCount; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        [
            'nutritionData' => $nutritionData,
            'nutritionStats' => $nutritionStats,
        ] = $this->analyticsService->getNutritionData($client, $from, $to);

        [
            'checkInCharts' => $checkInCharts,
            'tableMetrics' => $tableMetrics,
            'checkInTableData' => $checkInTableData,
            'imageMetrics' => $imageMetrics,
            'imageMetricData' => $imageMetricData,
        ] = $this->analyticsService->getCheckInChartData($client, $from, $to);

        // chartMetrics is still needed for the view — derive it from checkInCharts
        $chartMetrics = collect($checkInCharts);

        [
            'exerciseProgressionData' => $exerciseProgressionData,
            'exercisesByMuscleGroup' => $exercisesByMuscleGroup,
            'exerciseTargetHistory' => $exerciseTargetHistory,
        ] = $this->analyticsService->getExerciseProgressionData($client, $from, $to);

        return view('coach.clients.analytics', compact(
            'client',
            'range',
            'from',
            'to',
            'dates',
            'checkInCharts',
            'chartMetrics',
            'tableMetrics',
            'checkInTableData',
            'imageMetrics',
            'imageMetricData',
            'nutritionData',
            'nutritionStats',
            'exerciseProgressionData',
            'exercisesByMuscleGroup',
            'exerciseTargetHistory',
        ));
    }

    public function exportToExcel(Request $request, User $client)
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        return Excel::download(new CoachAnalyticsExport($client), 'analytics.xlsx');
    }
}
```

**Step 3: Run coach analytics tests to verify no regressions**

```bash
php artisan test --compact tests/Feature/Coach/AnalyticsTest.php tests/Feature/Coach/AnalyticsImageTest.php
```
Expected: PASS.

**Step 4: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 5: Commit**

```bash
git add app/Http/Controllers/Coach/AnalyticsController.php
git commit -m "refactor: use AnalyticsService in coach AnalyticsController"
```

---

### Task 5: Add nutrition charts to the client nutrition page

**Files:**
- Modify: `app/Http/Controllers/Client/NutritionController.php`
- Modify: `resources/views/client/nutrition.blade.php`
- Modify: `lang/en/client.php` (+ `lang/sl/client.php`, `lang/hr/client.php`)
- Test: `tests/Feature/Client/NutritionTest.php` (existing + new)

**Step 1: Write the failing test**

Open `tests/Feature/Client/NutritionTest.php` and add:

```php
it('passes nutrition chart data to the nutrition view', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    MealLog::factory()->create([
        'client_id' => $client->id,
        'date' => now()->format('Y-m-d'),
        'calories' => 2000,
        'protein' => 150,
        'carbs' => 200,
        'fat' => 70,
    ]);

    $this->actingAs($client)
        ->get(route('client.nutrition'))
        ->assertOk()
        ->assertViewHas('nutritionData')
        ->assertViewHas('nutritionStats');
});
```

**Step 2: Run to confirm failure**

```bash
php artisan test --compact --filter="passes nutrition chart data"
```
Expected: FAIL — view variables not present.

**Step 3: Update NutritionController**

In `app/Http/Controllers/Client/NutritionController.php`:

Add `use App\Services\AnalyticsService;` and inject it:

```php
public function __construct(private readonly AnalyticsService $analyticsService) {}
```

In `index()`, before `return view(...)`, add:

```php
$from = now()->subDays(29)->format('Y-m-d');
$to = now()->format('Y-m-d');

[
    'nutritionData' => $nutritionData,
    'nutritionStats' => $nutritionStats,
] = $this->analyticsService->getNutritionData($user, $from, $to);
```

Update `return view(...)` to include the new variables:

```php
return view('client.nutrition', compact('date', 'macroGoal', 'mealLogs', 'totals', 'nutritionData', 'nutritionStats'));
```

**Step 4: Run the test**

```bash
php artisan test --compact --filter="passes nutrition chart data"
```
Expected: PASS.

**Step 5: Add translation keys**

In `lang/en/client.php`, inside the `'nutrition'` array, add:

```php
'charts_heading' => 'Last 30 Days',
'avg_calories' => 'Avg Calories',
'avg_protein' => 'Avg Protein',
'avg_carbs' => 'Avg Carbs',
'avg_fat' => 'Avg Fat',
'adherence' => 'Adherence',
'calories' => 'Calories',
'macros' => 'Macros',
'no_data' => 'No nutrition data logged in the last 30 days.',
```

Add the same keys (translated) to `lang/sl/client.php` and `lang/hr/client.php`. Check the existing coach analytics translation keys in `lang/*/coach.php` for reference — the wording is similar.

**Step 6: Add charts section to the nutrition blade**

In `resources/views/client/nutrition.blade.php`, add a new section at the bottom of the main `div.space-y-6`, before the closing `</div>`:

```blade
{{-- Nutrition Charts (last 30 days) --}}
@if($nutritionStats['daysLogged'] > 0)
<x-bladewind::card>
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('client.nutrition.charts_heading') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('client.nutrition.calories') }}</h3>
                <div class="h-48">
                    <canvas
                        x-data="clientCaloriesChart({{ json_encode($nutritionData) }})"
                        x-ref="canvas"
                        x-init="renderChart()"
                    ></canvas>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('client.nutrition.macros') }}</h3>
                <div class="h-48">
                    <canvas
                        x-data="clientMacrosChart({{ json_encode($nutritionData) }})"
                        x-ref="canvas"
                        x-init="renderChart()"
                    ></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 p-4 bg-gray-50 dark:bg-gray-950 rounded-lg">
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.nutrition.avg_calories') }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($nutritionStats['avgCalories']) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.nutrition.avg_protein') }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $nutritionStats['avgProtein'] }}g</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.nutrition.avg_carbs') }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $nutritionStats['avgCarbs'] }}g</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.nutrition.avg_fat') }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $nutritionStats['avgFat'] }}g</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.nutrition.adherence') }}</p>
                @if($nutritionStats['adherenceRate'] !== null)
                    <p class="text-lg font-bold {{ $nutritionStats['adherenceRate'] >= 80 ? 'text-green-600' : ($nutritionStats['adherenceRate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $nutritionStats['adherenceRate'] }}%
                    </p>
                @else
                    <p class="text-lg font-bold text-gray-400 dark:text-gray-500">—</p>
                @endif
            </div>
        </div>
    </div>
</x-bladewind::card>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    function chartTheme() {
        const dark = document.documentElement.classList.contains('dark');
        return {
            tickColor: dark ? '#9ca3af' : '#6b7280',
            gridColor: dark ? 'rgba(75, 85, 99, 0.25)' : 'rgba(229, 231, 235, 1)',
            legendColor: dark ? '#d1d5db' : '#374151',
        };
    }

    function clientCaloriesChart(nutritionData) {
        return {
            renderChart() {
                const existing = Chart.getChart(this.$refs.canvas);
                if (existing) existing.destroy();
                const ctx = this.$refs.canvas.getContext('2d');
                const theme = chartTheme();
                const labels = nutritionData.map(d => {
                    const date = new Date(d.date + 'T00:00:00');
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                });
                const bgColors = nutritionData.map(d => {
                    if (!d.goalCalories || d.calories === 0) return 'rgba(209, 213, 219, 0.5)';
                    const dev = Math.abs(d.calories - d.goalCalories) / d.goalCalories;
                    if (dev <= 0.10) return 'rgba(34, 197, 94, 0.7)';
                    if (dev <= 0.25) return 'rgba(234, 179, 8, 0.7)';
                    return 'rgba(239, 68, 68, 0.7)';
                });
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Calories', data: nutritionData.map(d => d.calories), backgroundColor: bgColors, borderRadius: 3 },
                            { label: 'Goal', data: nutritionData.map(d => d.goalCalories), type: 'line', borderColor: 'rgba(107, 114, 128, 0.5)', borderDash: [5, 5], pointRadius: 0, fill: false, borderWidth: 2 }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12, color: theme.legendColor } } },
                        scales: {
                            x: { ticks: { maxTicksLimit: 10, color: theme.tickColor }, grid: { color: theme.gridColor } },
                            y: { beginAtZero: true, ticks: { color: theme.tickColor }, grid: { color: theme.gridColor } }
                        }
                    }
                });
            }
        };
    }

    function clientMacrosChart(nutritionData) {
        return {
            renderChart() {
                const existing = Chart.getChart(this.$refs.canvas);
                if (existing) existing.destroy();
                const ctx = this.$refs.canvas.getContext('2d');
                const theme = chartTheme();
                const labels = nutritionData.map(d => {
                    const date = new Date(d.date + 'T00:00:00');
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                });
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Protein', data: nutritionData.map(d => d.protein), backgroundColor: 'rgba(59, 130, 246, 0.7)', borderRadius: 2 },
                            { label: 'Carbs', data: nutritionData.map(d => d.carbs), backgroundColor: 'rgba(234, 179, 8, 0.7)', borderRadius: 2 },
                            { label: 'Fat', data: nutritionData.map(d => d.fat), backgroundColor: 'rgba(239, 68, 68, 0.7)', borderRadius: 2 },
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12, color: theme.legendColor } } },
                        scales: {
                            x: { stacked: true, ticks: { maxTicksLimit: 10, color: theme.tickColor }, grid: { color: theme.gridColor } },
                            y: { stacked: true, beginAtZero: true, ticks: { color: theme.tickColor }, grid: { color: theme.gridColor } }
                        }
                    }
                });
            }
        };
    }
</script>
@endpush
```

**Step 7: Run all nutrition tests**

```bash
php artisan test --compact tests/Feature/Client/NutritionTest.php
```
Expected: PASS.

**Step 8: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 9: Commit**

```bash
git add app/Http/Controllers/Client/NutritionController.php \
        resources/views/client/nutrition.blade.php \
        lang/en/client.php lang/sl/client.php lang/hr/client.php
git commit -m "feat: add nutrition charts to client nutrition page"
```

---

### Task 6: Add exercise progression to the client history page

**Files:**
- Modify: `app/Http/Controllers/Client/HistoryController.php`
- Modify: `resources/views/client/history.blade.php`
- Modify: `lang/en/client.php` (+ sl, hr)
- Test: `tests/Feature/Client/HistoryAnalyticsTest.php` (new)

**Step 1: Create the test**

```bash
php artisan make:test --pest Client/HistoryAnalyticsTest
```

In `tests/Feature/Client/HistoryAnalyticsTest.php`:

```php
<?php

use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\WorkoutLog;
use App\Models\Exercise;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('passes exercise progression data to history view', function () {
    $exercise = Exercise::factory()->create(['coach_id' => $this->coach->id]);
    $workoutLog = WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'completed_at' => now(),
    ]);
    ExerciseLog::factory()->create([
        'workout_log_id' => $workoutLog->id,
        'exercise_id' => $exercise->id,
        'weight' => 100,
        'reps' => 8,
    ]);

    $this->actingAs($this->client)
        ->get(route('client.history'))
        ->assertOk()
        ->assertViewHas('exerciseProgressionData')
        ->assertViewHas('exercisesByMuscleGroup')
        ->assertViewHas('exerciseTargetHistory');
});

it('passes empty exercise data when no workouts logged', function () {
    $this->actingAs($this->client)
        ->get(route('client.history'))
        ->assertOk()
        ->assertViewHas('exerciseProgressionData', [])
        ->assertViewHas('exerciseTargetHistory', []);
});
```

**Step 2: Run to confirm failure**

```bash
php artisan test --compact tests/Feature/Client/HistoryAnalyticsTest.php
```
Expected: FAIL.

**Step 3: Update HistoryController**

In `app/Http/Controllers/Client/HistoryController.php`, add:

```php
use App\Services\AnalyticsService;
```

Add constructor:

```php
public function __construct(private readonly AnalyticsService $analyticsService) {}
```

In `index()`, before `return view(...)`, add:

```php
[
    'exerciseProgressionData' => $exerciseProgressionData,
    'exercisesByMuscleGroup' => $exercisesByMuscleGroup,
    'exerciseTargetHistory' => $exerciseTargetHistory,
] = $this->analyticsService->getExerciseProgressionData(
    $user,
    now()->subDays(89)->format('Y-m-d'),
    now()->format('Y-m-d')
);
```

Update `return view(...)`:

```php
return view('client.history', [
    'workoutLogs' => $workoutLogs,
    'unreadWorkoutLogIds' => $unreadWorkoutLogIds,
    'exerciseProgressionData' => $exerciseProgressionData,
    'exercisesByMuscleGroup' => $exercisesByMuscleGroup,
    'exerciseTargetHistory' => $exerciseTargetHistory,
]);
```

**Step 4: Add translation keys**

In `lang/en/client.php`, in the `'history'` array add:

```php
'exercise_progress' => 'Exercise Progress',
'select_exercise' => 'Select an exercise',
'start_end' => 'Start → End',
'change' => 'Change',
'sessions' => 'Sessions',
'no_exercise_data' => 'No workout data in the last 90 days.',
```

Add same keys (translated) to `lang/sl/client.php` and `lang/hr/client.php`.

**Step 5: Add exercise progression section to history blade**

In `resources/views/client/history.blade.php`, add the following block after the `<h1>` heading and before the `@if($workoutLogs->count() > 0)` check:

```blade
{{-- Exercise Progression --}}
@if(count($exerciseProgressionData) > 0)
<x-bladewind::card>
    <div x-data="clientExerciseProgression({{ json_encode($exerciseProgressionData) }}, {{ json_encode($exercisesByMuscleGroup) }}, {{ json_encode($exerciseTargetHistory) }})" x-init="init()">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('client.history.exercise_progress') }}</h2>

        <div class="mb-4">
            <select x-model="selectedExercise" @change="updateChart()"
                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <template x-for="(exercises, group) in exerciseGroups" :key="group">
                    <optgroup :label="group">
                        <template x-for="ex in exercises" :key="ex.id">
                            <option :value="ex.id" x-text="ex.name"></option>
                        </template>
                    </optgroup>
                </template>
            </select>
        </div>

        <div class="h-48 mb-4">
            <canvas x-ref="canvas"></canvas>
        </div>

        <div x-show="summary" class="grid grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-950 rounded-lg">
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.history.start_end') }}</p>
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="summary?.startWeight + 'kg → ' + summary?.endWeight + 'kg'"></p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.history.change') }}</p>
                <p class="text-sm font-bold" :class="summary?.change >= 0 ? 'text-green-600' : 'text-red-600'"
                   x-text="(summary?.change >= 0 ? '+' : '') + summary?.change + 'kg (' + (summary?.change >= 0 ? '+' : '') + summary?.changePercent + '%)'"></p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.history.sessions') }}</p>
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="summary?.sessions"></p>
            </div>
        </div>
    </div>
</x-bladewind::card>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    function chartTheme() {
        const dark = document.documentElement.classList.contains('dark');
        return {
            tickColor: dark ? '#9ca3af' : '#6b7280',
            gridColor: dark ? 'rgba(75, 85, 99, 0.25)' : 'rgba(229, 231, 235, 1)',
        };
    }

    function clientExerciseProgression(allData, exerciseGroups, targetHistory = {}) {
        return {
            selectedExercise: '',
            exerciseGroups,
            summary: null,

            init() {
                const firstGroup = Object.values(exerciseGroups)[0];
                if (firstGroup && firstGroup.length > 0) {
                    this.selectedExercise = String(firstGroup[0].id);
                }
                this.$nextTick(() => { if (this.selectedExercise) this.updateChart(); });
            },

            updateChart() {
                const data = allData[this.selectedExercise] || [];
                const existing = Chart.getChart(this.$refs.canvas);
                if (existing) existing.destroy();

                if (data.length === 0) { this.summary = null; return; }

                const startW = data[0].weight;
                const endW = data[data.length - 1].weight;
                const change = Math.round((endW - startW) * 100) / 100;
                const changePercent = startW > 0 ? Math.round((change / startW) * 1000) / 10 : 0;
                this.summary = { startWeight: startW, endWeight: endW, change, changePercent, sessions: data.length };

                const ctx = this.$refs.canvas.getContext('2d');
                const theme = chartTheme();
                const targets = (targetHistory[this.selectedExercise] || []).slice().sort((a, b) => a.date.localeCompare(b.date));
                const hasTargets = targets.length > 0;

                function activeTarget(dateStr) {
                    let result = null;
                    for (const t of targets) { if (t.date <= dateStr) { result = t.target; } else { break; } }
                    return result;
                }

                const logDateSet = new Set(data.map(d => d.date));
                const allDates = [...logDateSet];
                if (hasTargets) { for (const t of targets) { if (!logDateSet.has(t.date)) allDates.push(t.date); } }
                allDates.sort();

                const logByDate = {};
                for (const d of data) { logByDate[d.date] = d; }

                const labels = allDates.map(dateStr => {
                    const date = new Date(dateStr + 'T00:00:00');
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                });

                const datasets = [{
                    label: 'Top Set Weight (kg)',
                    data: allDates.map(dateStr => logByDate[dateStr]?.weight ?? null),
                    borderColor: '#8B5CF6', backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true, tension: 0.3,
                    pointRadius: allDates.map(dateStr => logByDate[dateStr] ? 4 : 0),
                    spanGaps: true,
                }];

                if (hasTargets) {
                    datasets.push({
                        label: 'Target (kg)',
                        data: allDates.map(dateStr => activeTarget(dateStr)),
                        borderColor: '#f59e0b', backgroundColor: 'transparent',
                        borderDash: [5, 5], pointRadius: 0, tension: 0.3,
                    });
                }

                new Chart(ctx, {
                    type: 'line',
                    data: { labels, datasets },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: hasTargets, labels: { color: theme.tickColor, boxWidth: 12, padding: 12 } },
                            tooltip: {
                                filter: (item) => item.parsed.y !== null,
                                callbacks: {
                                    label(ctx) {
                                        if (ctx.datasetIndex === 0) {
                                            const d = logByDate[allDates[ctx.dataIndex]];
                                            return d ? d.weight + 'kg x ' + d.reps + ' reps' : null;
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
</script>
@endpush
```

**Step 6: Run all tests**

```bash
php artisan test --compact tests/Feature/Client/HistoryAnalyticsTest.php
```
Expected: PASS.

**Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 8: Commit**

```bash
git add app/Http/Controllers/Client/HistoryController.php \
        resources/views/client/history.blade.php \
        lang/en/client.php lang/sl/client.php lang/hr/client.php \
        tests/Feature/Client/HistoryAnalyticsTest.php
git commit -m "feat: add exercise progression charts to client history page"
```

---

### Task 7: Add daily metrics history route and page

**Files:**
- Modify: `routes/web.php`
- Modify: `app/Http/Controllers/Client/CheckInController.php`
- Create: `resources/views/client/check-in-history.blade.php`
- Modify: `resources/views/client/check-in.blade.php`
- Modify: `lang/en/client.php` (+ sl, hr)
- Test: `tests/Feature/Client/CheckInHistoryTest.php` (new)

**Step 1: Create the test**

```bash
php artisan make:test --pest Client/CheckInHistoryTest
```

In `tests/Feature/Client/CheckInHistoryTest.php`:

```php
<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('shows the check-in history page', function () {
    $this->actingAs($this->client)
        ->get(route('client.check-in.history'))
        ->assertOk()
        ->assertViewIs('client.check-in-history');
});

it('passes check-in chart data to the view', function () {
    $metric = TrackingMetric::factory()->number()->create(['coach_id' => $this->coach->id]);
    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
    ]);
    DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '80.0',
    ]);

    $this->actingAs($this->client)
        ->get(route('client.check-in.history'))
        ->assertOk()
        ->assertViewHas('checkInCharts')
        ->assertViewHas('tableMetrics');
});

it('accepts range query parameter', function () {
    $this->actingAs($this->client)
        ->get(route('client.check-in.history', ['range' => '7']))
        ->assertOk();
});

it('cannot be accessed by coaches', function () {
    $this->actingAs($this->coach)
        ->get(route('client.check-in.history'))
        ->assertRedirect();
});
```

**Step 2: Run to confirm failure**

```bash
php artisan test --compact tests/Feature/Client/CheckInHistoryTest.php
```
Expected: FAIL — route not found.

**Step 3: Add the route**

In `routes/web.php`, inside the client route group, after the `check-in.store` route line:

```php
Route::get('check-in/history', [Client\CheckInController::class, 'history'])->name('check-in.history');
```

**Step 4: Add the history method to CheckInController**

In `app/Http/Controllers/Client/CheckInController.php`, add:

```php
use App\Services\AnalyticsService;
```

Add constructor:

```php
public function __construct(private readonly AnalyticsService $analyticsService) {}
```

Add new method:

```php
public function history(Request $request): View
{
    $user = auth()->user();

    $range = $request->get('range', '30');
    $days = in_array((int) $range, [7, 14, 30, 90]) ? (int) $range : 30;
    $from = now()->subDays($days - 1)->format('Y-m-d');
    $to = now()->format('Y-m-d');

    [
        'checkInCharts' => $checkInCharts,
        'tableMetrics' => $tableMetrics,
        'checkInTableData' => $checkInTableData,
        'imageMetrics' => $imageMetrics,
        'imageMetricData' => $imageMetricData,
    ] = $this->analyticsService->getCheckInChartData($user, $from, $to);

    return view('client.check-in-history', compact(
        'range',
        'from',
        'to',
        'checkInCharts',
        'tableMetrics',
        'checkInTableData',
        'imageMetrics',
        'imageMetricData',
    ));
}
```

**Step 5: Add translation keys**

In `lang/en/client.php`, add a new `'check_in_history'` section:

```php
'check_in_history' => [
    'heading' => 'My Metrics History',
    'back' => 'Back to Check-in',
    'view_history' => 'View history',
    'time_period' => 'Time Period',
    'last_7_days' => 'Last 7 days',
    'last_14_days' => 'Last 14 days',
    'last_30_days' => 'Last 30 days',
    'last_90_days' => 'Last 90 days',
    'no_metrics' => 'No metrics assigned yet.',
    'no_data' => 'No check-in data for this period.',
    'date' => 'Date',
    'progress_photos' => 'Progress Photos',
    'no_photos' => 'No progress photos for this period.',
],
```

Add same keys (translated) to `lang/sl/client.php` and `lang/hr/client.php`.

**Step 6: Create the check-in-history blade**

Create `resources/views/client/check-in-history.blade.php`:

```blade
<x-layouts.client>
    <x-slot:title>{{ __('client.check_in_history.heading') }}</x-slot:title>

    <div class="py-6 space-y-6">
        <div>
            <a href="{{ route('client.check-in') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('client.check_in_history.back') }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('client.check_in_history.heading') }}</h1>
        </div>

        {{-- Date Range Filter --}}
        <x-bladewind::card>
            <form method="GET" action="{{ route('client.check-in.history') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.check_in_history.time_period') }}</label>
                    <select name="range" onchange="this.form.submit()"
                        class="block rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="7" {{ $range == 7 ? 'selected' : '' }}>{{ __('client.check_in_history.last_7_days') }}</option>
                        <option value="14" {{ $range == 14 ? 'selected' : '' }}>{{ __('client.check_in_history.last_14_days') }}</option>
                        <option value="30" {{ $range == 30 ? 'selected' : '' }}>{{ __('client.check_in_history.last_30_days') }}</option>
                        <option value="90" {{ $range == 90 ? 'selected' : '' }}>{{ __('client.check_in_history.last_90_days') }}</option>
                    </select>
                </div>
            </form>
        </x-bladewind::card>

        @if(count($checkInCharts) === 0 && $tableMetrics->count() === 0 && $imageMetrics->isEmpty())
            <x-bladewind::card>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">{{ __('client.check_in_history.no_metrics') }}</p>
            </x-bladewind::card>
        @else
            {{-- Numeric / Scale Charts --}}
            @if(count($checkInCharts) > 0)
                <x-bladewind::card>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($checkInCharts as $chart)
                            <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-3">
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $chart['name'] }} @if($chart['unit'])({{ $chart['unit'] }})@endif
                                </h3>
                                <div x-data="clientCheckInChart({{ json_encode($chart) }})" x-init="init()">
                                    <canvas x-ref="canvas" height="200"></canvas>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-bladewind::card>
            @endif

            {{-- Boolean / Text Table --}}
            @if($tableMetrics->count() > 0)
                <x-bladewind::card>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('client.check_in_history.date') }}</th>
                                    @foreach($tableMetrics as $metric)
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $metric->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($checkInTableData as $row)
                                    @php
                                        $hasValue = collect($tableMetrics)->contains(fn ($m) => $row['metric_' . $m->id] !== null);
                                    @endphp
                                    @if($hasValue)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($row['date'])->format('M j') }}</td>
                                            @foreach($tableMetrics as $metric)
                                                <td class="px-3 py-2">
                                                    @if($row['metric_' . $metric->id] === null)
                                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                                    @elseif($metric->type === 'boolean')
                                                        @if($row['metric_' . $metric->id] === '1')
                                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        @endif
                                                    @else
                                                        <span title="{{ $row['metric_' . $metric->id] }}">{{ Str::limit($row['metric_' . $metric->id], 30) }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-bladewind::card>
            @endif

            {{-- Progress Photos --}}
            @if($imageMetrics->isNotEmpty())
                @php $hasAnyPhotos = collect($imageMetricData)->contains(fn ($m) => count($m['photos']) > 0); @endphp
                @if($hasAnyPhotos)
                    <x-bladewind::card>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('client.check_in_history.progress_photos') }}</h2>
                        <div class="space-y-6">
                            @foreach($imageMetricData as $metricData)
                                @if(count($metricData['photos']) > 0)
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $metricData['name'] }}</h3>
                                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                            @foreach($metricData['photos'] as $photo)
                                                <div x-data="{ showLightbox: false }" class="relative">
                                                    <button @click="showLightbox = true" class="block w-full aspect-square rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 hover:border-blue-400 transition-colors">
                                                        <img src="{{ $photo['thumbUrl'] }}" alt="{{ $metricData['name'] }} - {{ $photo['date'] }}" class="w-full h-full object-cover">
                                                    </button>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">{{ \Carbon\Carbon::parse($photo['date'])->format('M j') }}</p>
                                                    <div x-show="showLightbox" x-cloak @click.self="showLightbox = false" @keydown.escape.window="showLightbox = false"
                                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
                                                        <div class="relative max-w-4xl max-h-full">
                                                            <img src="{{ $photo['fullUrl'] }}" alt="{{ $metricData['name'] }} - {{ $photo['date'] }}" class="max-w-full max-h-[90vh] rounded-lg">
                                                            <button @click="showLightbox = false" class="absolute top-2 right-2 bg-black/50 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-black/70">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                            <p class="text-white text-center mt-2 text-sm">{{ \Carbon\Carbon::parse($photo['date'])->format('M j, Y') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </x-bladewind::card>
                @endif
            @endif
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        function chartTheme() {
            const dark = document.documentElement.classList.contains('dark');
            return {
                tickColor: dark ? '#9ca3af' : '#6b7280',
                gridColor: dark ? 'rgba(75, 85, 99, 0.25)' : 'rgba(229, 231, 235, 1)',
            };
        }

        function clientCheckInChart(chartData) {
            return {
                init() {
                    const existing = Chart.getChart(this.$refs.canvas);
                    if (existing) existing.destroy();
                    const ctx = this.$refs.canvas.getContext('2d');
                    const theme = chartTheme();
                    const labels = chartData.data.map(d => {
                        const date = new Date(d.date + 'T00:00:00');
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    });
                    const values = chartData.data.map(d => d.value);
                    const yScale = { grid: { color: theme.gridColor }, ticks: { color: theme.tickColor } };
                    if (chartData.type === 'scale') {
                        yScale.min = chartData.scaleMin;
                        yScale.max = chartData.scaleMax;
                    } else {
                        yScale.beginAtZero = false;
                    }
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                data: values,
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true, tension: 0.3, spanGaps: false, pointRadius: 3,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { type: 'category', ticks: { maxTicksLimit: 10, color: theme.tickColor }, grid: { color: theme.gridColor } },
                                y: yScale,
                            },
                        }
                    });
                }
            };
        }
    </script>
    @endpush
</x-layouts.client>
```

**Step 7: Add "View history" link to check-in page**

In `resources/views/client/check-in.blade.php`, find the `<h1>` heading line (line ~7) and add the link after it:

```blade
<div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('client.check_in.heading') }}</h1>
    <a href="{{ route('client.check-in.history') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
        {{ __('client.check_in_history.view_history') }} →
    </a>
</div>
```

Replace the standalone `<h1>` with the above wrapper div.

**Step 8: Run all tests**

```bash
php artisan test --compact tests/Feature/Client/CheckInHistoryTest.php tests/Feature/Client/CheckInTest.php
```
Expected: PASS.

**Step 9: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 10: Commit**

```bash
git add routes/web.php \
        app/Http/Controllers/Client/CheckInController.php \
        resources/views/client/check-in-history.blade.php \
        resources/views/client/check-in.blade.php \
        lang/en/client.php lang/sl/client.php lang/hr/client.php \
        tests/Feature/Client/CheckInHistoryTest.php
git commit -m "feat: add daily metrics history page accessible from check-in tab"
```

---

### Task 8: Final verification

**Step 1: Run all affected tests**

```bash
php artisan test --compact \
    tests/Feature/Services/AnalyticsServiceTest.php \
    tests/Feature/Coach/AnalyticsTest.php \
    tests/Feature/Coach/AnalyticsImageTest.php \
    tests/Feature/Client/NutritionTest.php \
    tests/Feature/Client/HistoryAnalyticsTest.php \
    tests/Feature/Client/CheckInHistoryTest.php \
    tests/Feature/Client/CheckInTest.php
```
Expected: All PASS.

**Step 2: Run pint one final time**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 3: Commit any pint fixes, then done**
