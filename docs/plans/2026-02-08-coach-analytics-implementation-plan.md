# Coach Client Analytics Dashboard — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Build a unified analytics page per client where the coach views historical trends for daily check-ins, nutrition, and exercise progression over a configurable time period, using Chart.js for visualization.

**Architecture:** Single controller method computes all data server-side, encodes as JSON, and passes to a Blade view. Alpine.js drives Chart.js rendering and section collapse. Global date range filter (presets + custom) applies to all sections.

**Tech Stack:** Laravel 12, Blade, Alpine.js, Tailwind CSS v3, Chart.js (CDN)

---

### Task 1: Route, Controller, and Basic Page Shell

**Files:**
- Create: `app/Http/Controllers/Coach/AnalyticsController.php`
- Modify: `routes/web.php:46` (add route near other coach client routes)
- Modify: `resources/views/coach/clients/show.blade.php` (add Analytics link)
- Create: `resources/views/coach/clients/analytics.blade.php`
- Create: `tests/Feature/Coach/AnalyticsTest.php`

**Step 1: Write the test**

Create `tests/Feature/Coach/AnalyticsTest.php`:

```php
<?php

use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('shows the analytics page', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertViewIs('coach.clients.analytics');
});

it('prevents viewing another coachs client analytics', function () {
    $otherCoach = User::factory()->create(['role' => 'coach']);
    $otherClient = User::factory()->create(['role' => 'client', 'coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $otherClient))
        ->assertForbidden();
});

it('accepts range query parameters', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', [$this->client, 'range' => '30']))
        ->assertOk();
});

it('accepts custom date range parameters', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', [
            $this->client,
            'range' => 'custom',
            'from' => '2026-01-01',
            'to' => '2026-01-31',
        ]))
        ->assertOk();
});
```

**Step 2: Run tests to verify they fail**

Run: `php artisan test --compact tests/Feature/Coach/AnalyticsTest.php`
Expected: FAIL — route not defined

**Step 3: Add the route**

In `routes/web.php`, add this line after the nutrition route (line 46):

```php
Route::get('clients/{client}/analytics', [Coach\AnalyticsController::class, 'show'])->name('clients.analytics');
```

**Step 4: Create the controller**

Create `app/Http/Controllers/Coach/AnalyticsController.php`:

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function show(Request $request, User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        // Date range filter (same pattern as NutritionController)
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

        // Build dates array for the range
        $dates = collect();
        $dayCount = $startDate->diffInDays($endDate) + 1;
        for ($i = 0; $i < $dayCount; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        return view('coach.clients.analytics', compact(
            'client',
            'range',
            'from',
            'to',
            'dates',
        ));
    }
}
```

**Step 5: Create the view shell**

Create `resources/views/coach/clients/analytics.blade.php`:

```blade
<x-layouts.coach>
    <x-slot:title>{{ $client->name }} — Analytics</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to {{ $client->name }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }} — Analytics</h1>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" action="{{ route('coach.clients.analytics', $client) }}" x-data="{ range: '{{ $range }}' }" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Time Period</label>
                    <select name="range" x-model="range" @change="if (range !== 'custom') $el.closest('form').submit()"
                        class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="7">Last 7 days</option>
                        <option value="14">Last 14 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="custom">Custom range</option>
                    </select>
                </div>

                <template x-if="range === 'custom'">
                    <div class="flex items-end gap-2">
                        <div>
                            <label class="block text-xs text-gray-500">From</label>
                            <input type="date" name="from" value="{{ $from }}" class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">To</label>
                            <input type="date" name="to" value="{{ $to }}" class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Apply
                        </button>
                    </div>
                </template>
            </form>
        </div>

        <!-- Daily Check-ins Section (Task 2) -->
        <!-- Nutrition Section (Task 3) -->
        <!-- Exercise Progression Section (Task 4) -->
    </div>
</x-layouts.coach>
```

**Step 6: Add Analytics link to client detail page**

In `resources/views/coach/clients/show.blade.php`, find the "Nutrition" `View Details` link (inside the Nutrition Summary card) and add an Analytics link next to the Edit button in the header actions area. Find the line:

```blade
<a href="{{ route('coach.clients.edit', $client) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
```

Add before it:

```blade
<a href="{{ route('coach.clients.analytics', $client) }}" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md font-medium text-sm text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    Analytics
</a>
```

**Step 7: Run tests**

Run: `php artisan test --compact tests/Feature/Coach/AnalyticsTest.php`
Expected: 4 passing

**Step 8: Commit**

```bash
git add app/Http/Controllers/Coach/AnalyticsController.php resources/views/coach/clients/analytics.blade.php resources/views/coach/clients/show.blade.php routes/web.php tests/Feature/Coach/AnalyticsTest.php
git commit -m "feat: add coach analytics page shell with date range filter"
```

---

### Task 2: Daily Check-ins Section

**Files:**
- Modify: `app/Http/Controllers/Coach/AnalyticsController.php`
- Modify: `resources/views/coach/clients/analytics.blade.php`
- Modify: `tests/Feature/Coach/AnalyticsTest.php`

**Step 1: Write the tests**

Append to `tests/Feature/Coach/AnalyticsTest.php`:

```php
it('displays daily check-in chart data for numeric metrics', function () {
    $metric = \App\Models\TrackingMetric::factory()->number('kg')->create([
        'coach_id' => $this->coach->id,
        'name' => 'Body Weight',
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

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('Body Weight');
});

it('displays boolean and text metrics in a table', function () {
    $boolMetric = \App\Models\TrackingMetric::factory()->boolean()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Took Supplements',
    ]);

    \App\Models\ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $boolMetric->id,
    ]);

    \App\Models\DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $boolMetric->id,
        'date' => now()->format('Y-m-d'),
        'value' => '1',
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('Took Supplements');
});
```

**Step 2: Run tests to verify they fail**

Run: `php artisan test --compact tests/Feature/Coach/AnalyticsTest.php`
Expected: FAIL — "Body Weight" not seen on page (no check-in data loaded yet)

**Step 3: Add check-in data loading to controller**

In `AnalyticsController::show()`, add before the `return view(...)`:

```php
// --- Daily Check-ins ---
$assignedMetricIds = $client->assignedTrackingMetrics()->pluck('tracking_metric_id');
$assignedMetrics = auth()->user()->trackingMetrics()
    ->whereIn('id', $assignedMetricIds)
    ->where('is_active', true)
    ->orderBy('order')
    ->get();

$dailyLogs = $client->dailyLogs()
    ->whereIn('tracking_metric_id', $assignedMetricIds)
    ->whereDate('date', '>=', $from)
    ->whereDate('date', '<=', $to)
    ->orderBy('date')
    ->get();

// Separate chart-able metrics from table metrics
$chartMetrics = $assignedMetrics->whereIn('type', ['number', 'scale']);
$tableMetrics = $assignedMetrics->whereIn('type', ['boolean', 'text']);

// Build chart datasets: keyed by metric ID, each is an array of {date, value}
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

// Build table data for boolean/text metrics
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
```

Update the `return view(...)` to include:

```php
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
));
```

**Step 4: Add check-in section to the Blade view**

Replace the `<!-- Daily Check-ins Section (Task 2) -->` comment with:

```blade
<!-- Daily Check-ins -->
<div x-data="{ open: true }" class="bg-white rounded-lg shadow">
    <button @click="open = !open" class="w-full flex items-center justify-between p-6 text-left">
        <h2 class="text-lg font-medium text-gray-900">Daily Check-ins</h2>
        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-collapse class="px-6 pb-6">
        @if(count($checkInCharts) > 0 || $tableMetrics->count() > 0)
            @if(count($checkInCharts) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    @foreach($checkInCharts as $chart)
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">{{ $chart['name'] }}@if($chart['unit']) ({{ $chart['unit'] }})@endif</h3>
                            <div class="h-48">
                                <canvas
                                    x-data="checkInChart({{ json_encode($chart) }})"
                                    x-ref="canvas"
                                    x-init="renderChart()"
                                ></canvas>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($tableMetrics->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 pr-4 font-medium text-gray-500 text-xs uppercase">Date</th>
                                @foreach($tableMetrics as $metric)
                                    <th class="text-center py-2 px-3 font-medium text-gray-500 text-xs uppercase">{{ $metric->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach(array_reverse($checkInTableData) as $row)
                                @php $hasAnyValue = collect($tableMetrics)->contains(fn ($m) => $row['metric_' . $m->id] !== null); @endphp
                                @if($hasAnyValue)
                                <tr>
                                    <td class="py-2 pr-4 text-gray-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($row['date'])->format('M j') }}</td>
                                    @foreach($tableMetrics as $metric)
                                        <td class="text-center py-2 px-3">
                                            @if($row['metric_' . $metric->id] !== null)
                                                @if($metric->type === 'boolean')
                                                    @if($row['metric_' . $metric->id] === '1' || $row['metric_' . $metric->id] === 'true')
                                                        <span class="text-green-600">
                                                            <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        </span>
                                                    @else
                                                        <span class="text-red-400">
                                                            <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-700 cursor-help" title="{{ $row['metric_' . $metric->id] }}">{{ Str::limit($row['metric_' . $metric->id], 30) }}</span>
                                                @endif
                                            @else
                                                <span class="text-gray-300">&mdash;</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <p class="text-sm text-gray-400 text-center py-8">No check-in data for this period.</p>
        @endif
    </div>
</div>
```

Also add the Chart.js CDN and the Alpine.js component at the bottom of the view (before `</x-layouts.coach>`):

```blade
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('checkInChart', (chartData) => ({
        chart: null,
        renderChart() {
            const ctx = this.$refs.canvas.getContext('2d');
            const isScale = chartData.type === 'scale';

            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.data.map(d => d.date),
                    datasets: [{
                        label: chartData.name,
                        data: chartData.data.map(d => d.value),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.3,
                        spanGaps: false,
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            type: 'category',
                            ticks: {
                                maxTicksLimit: 10,
                                callback: function(val) {
                                    const label = this.getLabelForValue(val);
                                    const d = new Date(label + 'T00:00:00');
                                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                }
                            }
                        },
                        y: isScale ? { min: chartData.scaleMin, max: chartData.scaleMax, ticks: { stepSize: 1 } } : { beginAtZero: false }
                    }
                }
            });
        }
    }));
});
</script>
@endpush
```

**Important:** Check that the coach layout (`resources/views/components/layouts/coach.blade.php`) has a `@stack('scripts')` directive before `</body>`. If not, add it.

**Step 5: Run tests**

Run: `php artisan test --compact tests/Feature/Coach/AnalyticsTest.php`
Expected: 6 passing

**Step 6: Commit**

```bash
git add app/Http/Controllers/Coach/AnalyticsController.php resources/views/coach/clients/analytics.blade.php tests/Feature/Coach/AnalyticsTest.php
git commit -m "feat: add daily check-in charts and table to analytics page"
```

---

### Task 3: Nutrition Section

**Files:**
- Modify: `app/Http/Controllers/Coach/AnalyticsController.php`
- Modify: `resources/views/coach/clients/analytics.blade.php`
- Modify: `tests/Feature/Coach/AnalyticsTest.php`

**Step 1: Write the tests**

Append to `tests/Feature/Coach/AnalyticsTest.php`:

```php
it('displays nutrition chart data', function () {
    \App\Models\MacroGoal::factory()->create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'effective_date' => now()->subDays(10),
        'calories' => 2200,
    ]);

    \App\Models\MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->format('Y-m-d'),
        'calories' => 500,
        'protein' => 40,
        'carbs' => 50,
        'fat' => 15,
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('Calories')
        ->assertSee('Avg. Daily Calories');
});

it('calculates nutrition adherence rate', function () {
    $goal = \App\Models\MacroGoal::factory()->create([
        'client_id' => $this->client->id,
        'coach_id' => $this->coach->id,
        'effective_date' => now()->subDays(10),
        'calories' => 2000,
    ]);

    // Within 10% of 2000 (1800-2200)
    \App\Models\MealLog::factory()->create([
        'client_id' => $this->client->id,
        'date' => now()->format('Y-m-d'),
        'calories' => 1950,
        'protein' => 40,
        'carbs' => 50,
        'fat' => 15,
    ]);

    $response = $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', [$this->client, 'range' => '7']));

    $response->assertOk()
        ->assertSee('Adherence');
});
```

**Step 2: Run tests to verify they fail**

Run: `php artisan test --compact tests/Feature/Coach/AnalyticsTest.php`
Expected: FAIL — "Calories" / "Avg. Daily Calories" not seen

**Step 3: Add nutrition data loading to controller**

In `AnalyticsController::show()`, add after the check-in section code and before `return view(...)`:

```php
// --- Nutrition ---
$mealLogs = $client->mealLogs()
    ->whereDate('date', '>=', $from)
    ->whereDate('date', '<=', $to)
    ->orderBy('date')
    ->get();

$macroGoals = $client->macroGoals()
    ->whereDate('effective_date', '<=', $to)
    ->orderBy('effective_date')
    ->get();

// Build daily nutrition data
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

    // Find the active goal for this date
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
```

Update `return view(...)` to include `'nutritionData', 'nutritionStats'`.

**Step 4: Add nutrition section to the Blade view**

Replace `<!-- Nutrition Section (Task 3) -->` with:

```blade
<!-- Nutrition -->
<div x-data="{ open: true }" class="bg-white rounded-lg shadow">
    <button @click="open = !open" class="w-full flex items-center justify-between p-6 text-left">
        <h2 class="text-lg font-medium text-gray-900">Nutrition</h2>
        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-collapse class="px-6 pb-6">
        @if($nutritionStats['daysLogged'] > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Calories</h3>
                    <div class="h-56">
                        <canvas
                            x-data="caloriesChart({{ json_encode($nutritionData) }})"
                            x-ref="canvas"
                            x-init="renderChart()"
                        ></canvas>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Macros (g)</h3>
                    <div class="h-56">
                        <canvas
                            x-data="macrosChart({{ json_encode($nutritionData) }})"
                            x-ref="canvas"
                            x-init="renderChart()"
                        ></canvas>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase">Avg. Daily Calories</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($nutritionStats['avgCalories']) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase">Avg. Protein</p>
                    <p class="text-lg font-bold text-gray-900">{{ $nutritionStats['avgProtein'] }}g</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase">Avg. Carbs</p>
                    <p class="text-lg font-bold text-gray-900">{{ $nutritionStats['avgCarbs'] }}g</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase">Avg. Fat</p>
                    <p class="text-lg font-bold text-gray-900">{{ $nutritionStats['avgFat'] }}g</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase">Adherence</p>
                    @if($nutritionStats['adherenceRate'] !== null)
                        <p class="text-lg font-bold {{ $nutritionStats['adherenceRate'] >= 80 ? 'text-green-600' : ($nutritionStats['adherenceRate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $nutritionStats['adherenceRate'] }}%</p>
                    @else
                        <p class="text-lg font-bold text-gray-400">—</p>
                    @endif
                </div>
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-8">No nutrition data for this period.</p>
        @endif
    </div>
</div>
```

Add to the `@push('scripts')` block (inside `alpine:init`):

```javascript
Alpine.data('caloriesChart', (nutritionData) => ({
    chart: null,
    renderChart() {
        const ctx = this.$refs.canvas.getContext('2d');
        const labels = nutritionData.map(d => d.date);
        const calories = nutritionData.map(d => d.calories);
        const goals = nutritionData.map(d => d.goalCalories);

        // Color bars based on adherence
        const bgColors = nutritionData.map(d => {
            if (!d.goalCalories || d.calories === 0) return 'rgba(209, 213, 219, 0.5)';
            const dev = Math.abs(d.calories - d.goalCalories) / d.goalCalories;
            if (dev <= 0.10) return 'rgba(34, 197, 94, 0.7)';
            if (dev <= 0.25) return 'rgba(234, 179, 8, 0.7)';
            return 'rgba(239, 68, 68, 0.7)';
        });

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Calories',
                        data: calories,
                        backgroundColor: bgColors,
                        borderRadius: 3,
                    },
                    {
                        label: 'Goal',
                        data: goals,
                        type: 'line',
                        borderColor: 'rgba(107, 114, 128, 0.5)',
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                        borderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12 } } },
                scales: {
                    x: { ticks: { maxTicksLimit: 10, callback: function(val) {
                        const label = this.getLabelForValue(val);
                        const d = new Date(label + 'T00:00:00');
                        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }}},
                    y: { beginAtZero: true }
                }
            }
        });
    }
}));

Alpine.data('macrosChart', (nutritionData) => ({
    chart: null,
    renderChart() {
        const ctx = this.$refs.canvas.getContext('2d');
        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: nutritionData.map(d => d.date),
                datasets: [
                    { label: 'Protein', data: nutritionData.map(d => d.protein), backgroundColor: 'rgba(59, 130, 246, 0.7)', borderRadius: 2 },
                    { label: 'Carbs', data: nutritionData.map(d => d.carbs), backgroundColor: 'rgba(234, 179, 8, 0.7)', borderRadius: 2 },
                    { label: 'Fat', data: nutritionData.map(d => d.fat), backgroundColor: 'rgba(239, 68, 68, 0.7)', borderRadius: 2 },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12 } } },
                scales: {
                    x: { stacked: true, ticks: { maxTicksLimit: 10, callback: function(val) {
                        const label = this.getLabelForValue(val);
                        const d = new Date(label + 'T00:00:00');
                        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }}},
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    }
}));
```

**Step 5: Run tests**

Run: `php artisan test --compact tests/Feature/Coach/AnalyticsTest.php`
Expected: 8 passing

**Step 6: Commit**

```bash
git add app/Http/Controllers/Coach/AnalyticsController.php resources/views/coach/clients/analytics.blade.php tests/Feature/Coach/AnalyticsTest.php
git commit -m "feat: add nutrition charts and adherence stats to analytics page"
```

---

### Task 4: Exercise Progression Section

**Files:**
- Modify: `app/Http/Controllers/Coach/AnalyticsController.php`
- Modify: `resources/views/coach/clients/analytics.blade.php`
- Modify: `tests/Feature/Coach/AnalyticsTest.php`

**Step 1: Write the tests**

Append to `tests/Feature/Coach/AnalyticsTest.php`:

```php
it('displays exercise progression data', function () {
    $exercise = \App\Models\Exercise::create([
        'name' => 'Bench Press',
        'muscle_group' => 'Chest',
        'is_active' => true,
    ]);

    $workoutLog = \App\Models\WorkoutLog::factory()->create([
        'client_id' => $this->client->id,
        'completed_at' => now(),
        'custom_name' => 'Push Day',
        'client_program_id' => null,
        'program_workout_id' => null,
    ]);

    \App\Models\ExerciseLog::factory()->create([
        'workout_log_id' => $workoutLog->id,
        'exercise_id' => $exercise->id,
        'workout_exercise_id' => null,
        'set_number' => 1,
        'weight' => 80.00,
        'reps' => 8,
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('Bench Press');
});

it('shows empty state when no workouts exist', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('No workouts logged for this period');
});
```

**Step 2: Run tests to verify they fail**

Run: `php artisan test --compact tests/Feature/Coach/AnalyticsTest.php`
Expected: FAIL — "Bench Press" / "No workouts logged" not seen

**Step 3: Add exercise data loading to controller**

In `AnalyticsController::show()`, add after the nutrition section code and before `return view(...)`:

```php
// --- Exercise Progression ---
$workoutLogs = $client->workoutLogs()
    ->whereDate('completed_at', '>=', $from)
    ->whereDate('completed_at', '<=', $to)
    ->with(['exerciseLogs.exercise'])
    ->orderBy('completed_at')
    ->get();

// Collect exercises the client logged, grouped by muscle group
$exerciseProgressionData = [];
$exerciseList = [];

foreach ($workoutLogs as $workoutLog) {
    foreach ($workoutLog->exerciseLogs as $exerciseLog) {
        $exId = $exerciseLog->exercise_id;
        $exercise = $exerciseLog->exercise;

        if (! isset($exerciseList[$exId])) {
            $exerciseList[$exId] = [
                'id' => $exId,
                'name' => $exercise->name,
                'muscleGroup' => $exercise->muscle_group,
            ];
        }

        if (! isset($exerciseProgressionData[$exId])) {
            $exerciseProgressionData[$exId] = [];
        }

        $dateKey = $workoutLog->completed_at->format('Y-m-d');

        // Track top set (highest weight) per session per exercise
        if (! isset($exerciseProgressionData[$exId][$dateKey])
            || $exerciseLog->weight > $exerciseProgressionData[$exId][$dateKey]['weight']) {
            $exerciseProgressionData[$exId][$dateKey] = [
                'date' => $dateKey,
                'weight' => (float) $exerciseLog->weight,
                'reps' => $exerciseLog->reps,
            ];
        }
    }
}

// Convert to indexed arrays and sort by date
foreach ($exerciseProgressionData as $exId => $sessions) {
    ksort($sessions);
    $exerciseProgressionData[$exId] = array_values($sessions);
}

// Group exercise list by muscle group for dropdown
$exercisesByMuscleGroup = collect($exerciseList)->groupBy('muscleGroup')->sortKeys();
```

Update `return view(...)` to include `'exerciseProgressionData', 'exercisesByMuscleGroup'`.

**Step 4: Add exercise progression section to the Blade view**

Replace `<!-- Exercise Progression Section (Task 4) -->` with:

```blade
<!-- Exercise Progression -->
<div x-data="{ open: true }" class="bg-white rounded-lg shadow">
    <button @click="open = !open" class="w-full flex items-center justify-between p-6 text-left">
        <h2 class="text-lg font-medium text-gray-900">Exercise Progression</h2>
        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-collapse class="px-6 pb-6">
        @if(count($exerciseProgressionData) > 0)
            <div x-data="exerciseProgression({{ json_encode($exerciseProgressionData) }}, {{ json_encode($exercisesByMuscleGroup) }})">
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Exercise</label>
                    <select x-model="selectedExercise" @change="updateChart()" class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <template x-for="(exercises, group) in exerciseGroups" :key="group">
                            <optgroup :label="group">
                                <template x-for="ex in exercises" :key="ex.id">
                                    <option :value="ex.id" x-text="ex.name"></option>
                                </template>
                            </optgroup>
                        </template>
                    </select>
                </div>

                <div class="h-56 mb-4">
                    <canvas x-ref="canvas"></canvas>
                </div>

                <!-- Progress Summary -->
                <div x-show="summary" class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <p class="text-xs text-gray-500 uppercase">Start &rarr; End</p>
                        <p class="text-sm font-bold text-gray-900" x-text="summary?.startWeight + 'kg → ' + summary?.endWeight + 'kg'"></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 uppercase">Change</p>
                        <p class="text-sm font-bold" :class="summary?.change >= 0 ? 'text-green-600' : 'text-red-600'"
                           x-text="(summary?.change >= 0 ? '+' : '') + summary?.change + 'kg (' + (summary?.change >= 0 ? '+' : '') + summary?.changePercent + '%)'"></p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 uppercase">Sessions</p>
                        <p class="text-sm font-bold text-gray-900" x-text="summary?.sessions"></p>
                    </div>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-8">No workouts logged for this period.</p>
        @endif
    </div>
</div>
```

Add to the `@push('scripts')` block (inside `alpine:init`):

```javascript
Alpine.data('exerciseProgression', (allData, exerciseGroups) => ({
    selectedExercise: Object.values(exerciseGroups)[0]?.[0]?.id?.toString() ?? '',
    exerciseGroups,
    chart: null,
    summary: null,

    init() {
        this.$nextTick(() => {
            if (this.selectedExercise) this.updateChart();
        });
    },

    updateChart() {
        const data = allData[this.selectedExercise] || [];
        if (this.chart) this.chart.destroy();

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
        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(d => d.date),
                datasets: [{
                    label: 'Top Set Weight (kg)',
                    data: data.map(d => d.weight),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const d = data[ctx.dataIndex];
                                return d.weight + 'kg × ' + d.reps + ' reps';
                            }
                        }
                    }
                },
                scales: {
                    x: { ticks: { maxTicksLimit: 10, callback: function(val) {
                        const label = this.getLabelForValue(val);
                        const d = new Date(label + 'T00:00:00');
                        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }}},
                    y: { beginAtZero: false }
                }
            }
        });
    }
}));
```

**Step 5: Run tests**

Run: `php artisan test --compact tests/Feature/Coach/AnalyticsTest.php`
Expected: 10 passing

**Step 6: Run full test suite**

Run: `php artisan test --compact`
Expected: All nutrition + analytics tests pass (pre-existing Auth/Profile failures are known)

**Step 7: Commit**

```bash
git add app/Http/Controllers/Coach/AnalyticsController.php resources/views/coach/clients/analytics.blade.php tests/Feature/Coach/AnalyticsTest.php
git commit -m "feat: add exercise progression charts to analytics page"
```

---

### Task 5: Scripts Stack and Final Polish

**Files:**
- Modify: `resources/views/components/layouts/coach.blade.php` (ensure `@stack('scripts')` exists)
- Run: `vendor/bin/pint --dirty`
- Run full test suite

**Step 1: Check coach layout for scripts stack**

Read `resources/views/components/layouts/coach.blade.php` and verify there's a `@stack('scripts')` before `</body>`. If not present, add it.

**Step 2: Run pint**

Run: `vendor/bin/pint --dirty`

**Step 3: Run full test suite**

Run: `php artisan test --compact`
Expected: All analytics + nutrition tests pass

**Step 4: Commit if any changes**

```bash
git add -A
git commit -m "chore: add scripts stack to coach layout and format code"
```
