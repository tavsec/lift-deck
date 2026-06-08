# Client Design Features Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Implement the missing UI features from the new "training instrument" client design: RPE tracking per set, muscle-keyed exercise thumbnails (ExThumb), and an improved exercise search/picker.

**Architecture:** RPE is a new nullable column on `exercise_logs`; no breaking change. ExThumb is a Blade component backed by an Alpine helper — pure frontend, zero DB impact. The improved exercise picker replaces the inline list in `log-workout.blade.php` with a styled dropdown that reuses ExThumb. All three features are independent and can be merged separately.

**Tech Stack:** Laravel 12, Blade components, Alpine.js v3, Tailwind CSS v3, Pest 4

---

## Task 1: RPE Migration

**Goal:** Add `rpe` (1–10, nullable) to `exercise_logs`.

**Files:**
- Create: `database/migrations/<timestamp>_add_rpe_to_exercise_logs_table.php`

**Step 1: Generate migration**

```bash
php artisan make:migration add_rpe_to_exercise_logs_table --no-interaction
```

**Step 2: Edit the migration**

```php
public function up(): void
{
    Schema::table('exercise_logs', function (Blueprint $table) {
        $table->unsignedTinyInteger('rpe')->nullable()->after('reps');
    });
}

public function down(): void
{
    Schema::table('exercise_logs', function (Blueprint $table) {
        $table->dropColumn('rpe');
    });
}
```

**Step 3: Run migration**

```bash
php artisan migrate --no-interaction
```
Expected: "Migrating: …add_rpe…" → "Migrated"

**Step 4: Commit**

```bash
git add database/migrations/
git commit -m "feat: add rpe column to exercise_logs"
```

---

## Task 2: ExerciseLog Model — add rpe to fillable + cast

**Files:**
- Modify: `app/Models/ExerciseLog.php`

**Step 1: Write failing test**

File: `tests/Unit/ExerciseLogRpeTest.php`

```php
<?php

use App\Models\ExerciseLog;

it('accepts rpe in fillable', function () {
    $log = new ExerciseLog(['rpe' => 7]);
    expect($log->rpe)->toBe(7);
});

it('casts rpe as integer', function () {
    $log = new ExerciseLog(['rpe' => '8']);
    expect($log->rpe)->toBeInt();
});
```

**Step 2: Run test — expect failure**

```bash
php artisan test --compact --filter=ExerciseLogRpe
```
Expected: FAIL — rpe not in fillable.

**Step 3: Update model**

In `app/Models/ExerciseLog.php`, add `'rpe'` to `$fillable` and add to `casts()`:
```php
protected $fillable = [
    'workout_log_id',
    'workout_exercise_id',
    'exercise_id',
    'set_number',
    'weight',
    'reps',
    'rpe',    // ← add
    'notes',
];

protected function casts(): array
{
    return [
        'weight'     => 'decimal:2',
        'reps'       => 'integer',
        'set_number' => 'integer',
        'rpe'        => 'integer',   // ← add
    ];
}
```

**Step 4: Run test — expect pass**

```bash
php artisan test --compact --filter=ExerciseLogRpe
```
Expected: PASS

**Step 5: Pint**

```bash
vendor/bin/pint app/Models/ExerciseLog.php tests/Unit/ExerciseLogRpeTest.php --format agent
```

**Step 6: Commit**

```bash
git add app/Models/ExerciseLog.php tests/Unit/ExerciseLogRpeTest.php
git commit -m "feat: add rpe to ExerciseLog model"
```

---

## Task 3: WorkoutLogController — store RPE

**Files:**
- Modify: `app/Http/Controllers/Client/WorkoutLogController.php`

**Context:** The controller reads `exercises[i][sets][j][weight]` and `exercises[i][sets][j][reps]` from the request when creating `ExerciseLog` records. Find the `store` method and add `rpe` alongside `weight`/`reps`.

**Step 1: Write failing feature test**

File: `tests/Feature/Client/WorkoutLogRpeTest.php`

```php
<?php

use App\Models\ExerciseLog;
use App\Models\User;

it('stores rpe when provided', function () {
    // Use existing test helpers from the codebase.
    // Find a test that creates a workout log (e.g. WorkoutLogTest) and copy its setup.
    // The assertion:
    $log = ExerciseLog::first();
    // After submitting a workout with rpe: 8 for the first set...
    // expect($log->rpe)->toBe(8);
    // Write the full test by examining how WorkoutLogTest sets up the POST request.
});
```

> **Note:** Look at `tests/Feature/Client/` for existing `WorkoutLog*` tests to copy the setup boilerplate. The key assertion is that `ExerciseLog::latest()->first()->rpe === 8` after POSTing with `exercises[0][sets][0][rpe] = 8`.

**Step 2: Run test — expect failure**

```bash
php artisan test --compact --filter=WorkoutLogRpe
```

**Step 3: Update controller**

In `WorkoutLogController@store`, find where `ExerciseLog::create()` or `ExerciseLog::insert()` is called. Add:

```php
'rpe' => isset($set['rpe']) ? (int) $set['rpe'] : null,
```

alongside the existing `weight` and `reps` lines.

**Step 4: Run test — expect pass**

```bash
php artisan test --compact --filter=WorkoutLogRpe
```

**Step 5: Pint + commit**

```bash
vendor/bin/pint app/Http/Controllers/Client/WorkoutLogController.php --format agent
git add app/Http/Controllers/Client/WorkoutLogController.php tests/Feature/Client/WorkoutLogRpeTest.php
git commit -m "feat: store rpe in workout log"
```

---

## Task 4: ExThumb Blade Component

**Goal:** A reusable `<x-ex-thumb>` Blade component that renders a 40×40 (default) gradient tile keyed by muscle group — matching the design's `ExThumb` React component exactly.

**Files:**
- Create: `resources/views/components/ex-thumb.blade.php`

**Step 1: Create the component**

```blade
{{-- Usage: <x-ex-thumb muscle="Back" :size="40" /> --}}
@props(['muscle' => 'default', 'size' => 40])

@php
$themes = [
    'back'        => ['from' => '#3b82f6', 'to' => '#1e40af', 'ic' => 'back'],
    'chest'       => ['from' => '#f0653e', 'to' => '#b8311a', 'ic' => 'chest'],
    'shoulders'   => ['from' => '#a06bff', 'to' => '#6d28d9', 'ic' => 'shoulder'],
    'core'        => ['from' => '#2dd4bf', 'to' => '#0d9488', 'ic' => 'core'],
    'quadriceps'  => ['from' => '#34d27b', 'to' => '#15803d', 'ic' => 'legs'],
    'legs'        => ['from' => '#34d27b', 'to' => '#15803d', 'ic' => 'legs'],
    'glutes'      => ['from' => '#f472b6', 'to' => '#be185d', 'ic' => 'legs'],
    'biceps'      => ['from' => '#f5b53d', 'to' => '#c2790a', 'ic' => 'arm'],
    'triceps'     => ['from' => '#f59e3d', 'to' => '#c2620a', 'ic' => 'arm'],
    'arms'        => ['from' => '#f5b53d', 'to' => '#c2790a', 'ic' => 'arm'],
    'hamstrings'  => ['from' => '#34d27b', 'to' => '#15803d', 'ic' => 'legs'],
    'calves'      => ['from' => '#34d27b', 'to' => '#15803d', 'ic' => 'legs'],
    'default'     => ['from' => '#94a3b8', 'to' => '#475569', 'ic' => 'dumbbell'],
];

$key = strtolower(str_replace(' ', '_', $muscle));
$theme = $themes[$key] ?? $themes['default'];

// SVG path data keyed by glyph name
$glyphs = [
    'back'     => '<path d="M12 3v18" /><path d="M12 6c-2.5 0-5 1.5-5 4M12 6c2.5 0 5 1.5 5 4" /><path d="M7 10c0 3 2 5 5 5s5-2 5-5" />',
    'chest'    => '<path d="M4 8c2-1.5 5-2 8-2s6 .5 8 2" /><path d="M4 8v4c0 3 3.5 5 8 5s8-2 8-5V8" /><path d="M12 6v11" />',
    'shoulder' => '<circle cx="12" cy="8" r="3.2" /><path d="M5 20c.5-4 3-6 7-6s6.5 2 7 6" />',
    'core'     => '<rect x="7" y="4" width="10" height="16" rx="3" /><path d="M7 9h10M7 13h10M12 4v16" />',
    'legs'     => '<path d="M9 3v7l-2 11M15 3v7l2 11" /><path d="M9 10h6" />',
    'arm'      => '<path d="M6 6v5a4 4 0 0 0 4 4h2" /><path d="M12 15a3 3 0 0 0 6 0v-2" /><circle cx="6" cy="5" r="1.5" fill="currentColor" stroke="none" />',
    'dumbbell' => '<path d="M6.5 6.5l11 11" /><path d="M3 10l-1-1a2 2 0 0 1 3-3l1 1M14 21l1 1a2 2 0 0 0 3-3l-1-1" />',
];

$glyph = $glyphs[$theme['ic']] ?? $glyphs['dumbbell'];
$borderRadius = round($size * 0.25);
$iconSize = round($size * 0.56);
$iconOffset = round(($size - $iconSize) / 2);
@endphp

<div
    style="
        width: {{ $size }}px;
        height: {{ $size }}px;
        border-radius: {{ $borderRadius }}px;
        background: linear-gradient(150deg, {{ $theme['from'] }}, {{ $theme['to'] }});
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
        display: grid;
        place-items: center;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.12), inset 0 -10px 18px rgba(0,0,0,.22);
    "
>
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="white"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
        style="width: {{ $iconSize }}px; height: {{ $iconSize }}px; position: relative; z-index: 1; filter: drop-shadow(0 1px 2px rgba(0,0,0,.35));"
    >{!! $glyph !!}</svg>
    <div style="
        position: absolute; inset: 0;
        background: radial-gradient(120% 80% at 25% 15%, rgba(255,255,255,.28), transparent 55%);
        pointer-events: none;
    "></div>
</div>
```

**Step 2: Quick smoke test — verify it renders**

Open any client page in the browser and add temporarily to dashboard:
```blade
<x-ex-thumb muscle="Back" :size="40" />
<x-ex-thumb muscle="Chest" :size="40" />
<x-ex-thumb muscle="default" :size="40" />
```
Verify: three gradient tiles appear. Remove after confirming.

**Step 3: Commit**

```bash
git add resources/views/components/ex-thumb.blade.php
git commit -m "feat: add ExThumb Blade component for muscle thumbnails"
```

---

## Task 5: ExThumb in Alpine (JS helper for x-for loops)

**Goal:** The log-workout page uses Alpine `x-for` to render exercise cards, so `<x-ex-thumb>` (server-side Blade) can't be used inside `x-for`. We need a client-side equivalent — a small Alpine `$data` helper + inline SVG rendered via `x-html`.

**Files:**
- Modify: `resources/views/client/log-workout.blade.php` (add a `<script>` block defining `window.exThumbHtml`)

**Step 1: Add the JS helper at the bottom of the `@push('scripts')` block, before the closing `</script>`**

```javascript
// ExThumb renderer for Alpine x-for loops
window.exThumbHtml = function(muscle, size) {
    size = size || 40;
    const themes = {
        back:       { from: '#3b82f6', to: '#1e40af', ic: 'back' },
        chest:      { from: '#f0653e', to: '#b8311a', ic: 'chest' },
        shoulders:  { from: '#a06bff', to: '#6d28d9', ic: 'shoulder' },
        core:       { from: '#2dd4bf', to: '#0d9488', ic: 'core' },
        quadriceps: { from: '#34d27b', to: '#15803d', ic: 'legs' },
        legs:       { from: '#34d27b', to: '#15803d', ic: 'legs' },
        glutes:     { from: '#f472b6', to: '#be185d', ic: 'legs' },
        biceps:     { from: '#f5b53d', to: '#c2790a', ic: 'arm' },
        triceps:    { from: '#f59e3d', to: '#c2620a', ic: 'arm' },
        arms:       { from: '#f5b53d', to: '#c2790a', ic: 'arm' },
        hamstrings: { from: '#34d27b', to: '#15803d', ic: 'legs' },
        calves:     { from: '#34d27b', to: '#15803d', ic: 'legs' },
    };
    const glyphs = {
        back:     '<path d="M12 3v18"/><path d="M12 6c-2.5 0-5 1.5-5 4M12 6c2.5 0 5 1.5 5 4"/><path d="M7 10c0 3 2 5 5 5s5-2 5-5"/>',
        chest:    '<path d="M4 8c2-1.5 5-2 8-2s6 .5 8 2"/><path d="M4 8v4c0 3 3.5 5 8 5s8-2 8-5V8"/><path d="M12 6v11"/>',
        shoulder: '<circle cx="12" cy="8" r="3.2"/><path d="M5 20c.5-4 3-6 7-6s6.5 2 7 6"/>',
        core:     '<rect x="7" y="4" width="10" height="16" rx="3"/><path d="M7 9h10M7 13h10M12 4v16"/>',
        legs:     '<path d="M9 3v7l-2 11M15 3v7l2 11"/><path d="M9 10h6"/>',
        arm:      '<path d="M6 6v5a4 4 0 0 0 4 4h2"/><path d="M12 15a3 3 0 0 0 6 0v-2"/><circle cx="6" cy="5" r="1.5" fill="white" stroke="none"/>',
        dumbbell: '<path d="M6.5 6.5l11 11"/><path d="M3 10l-1-1a2 2 0 0 1 3-3l1 1M14 21l1 1a2 2 0 0 0 3-3l-1-1"/>',
    };
    const key = (muscle || '').toLowerCase().replace(/\s+/g, '_').replace(/-/g, '_');
    const t = themes[key] || { from: '#94a3b8', to: '#475569', ic: 'dumbbell' };
    const g = glyphs[t.ic] || glyphs.dumbbell;
    const br = Math.round(size * 0.25);
    const ic = Math.round(size * 0.56);
    return `<div style="width:${size}px;height:${size}px;border-radius:${br}px;background:linear-gradient(150deg,${t.from},${t.to});flex-shrink:0;position:relative;overflow:hidden;display:grid;place-items:center;box-shadow:inset 0 0 0 1px rgba(255,255,255,.12),inset 0 -10px 18px rgba(0,0,0,.22)"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:${ic}px;height:${ic}px;position:relative;z-index:1;filter:drop-shadow(0 1px 2px rgba(0,0,0,.35))">${g}</svg><div style="position:absolute;inset:0;background:radial-gradient(120% 80% at 25% 15%,rgba(255,255,255,.28),transparent 55%);pointer-events:none"></div></div>`;
};
```

**Step 2: Commit**

```bash
git add resources/views/client/log-workout.blade.php
git commit -m "feat: add JS exThumbHtml helper for Alpine loops"
```

---

## Task 6: Add ExThumb + RPE to log-workout exercise cards

**Goal:** Update the exercise card in `log-workout.blade.php` to:
1. Show `x-html="exThumbHtml(exercise.muscle_group, 40)"` next to the exercise name
2. Add an `rpe` column in the set table (hidden input + inline 1–10 selector)

**Files:**
- Modify: `resources/views/client/log-workout.blade.php`

**Step 1: Add ExThumb to exercise card header**

Find the exercise card header section (around line 113–128). Replace the drag handle + name section:

```blade
<!-- Before: drag handle then name -->
<div class="flex items-center gap-2">
    <!-- Drag Handle -->
    <div class="drag-handle ...">...</div>
    <div>
        <button ...>...</button>
        <p ...>...</p>
    </div>
</div>
```

Change to:
```blade
<div class="flex items-center gap-2.5">
    <!-- Drag Handle -->
    <div class="drag-handle cursor-grab active:cursor-grabbing text-[#8c93a0] dark:text-[#6b7280] hover:text-[#555b66] touch-none flex-shrink-0">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
        </svg>
    </div>
    <!-- ExThumb -->
    <div x-html="exThumbHtml(exercise.muscle_group, 40)" class="flex-shrink-0"></div>
    <div class="min-w-0">
        <button type="button" class="font-display text-[15px] font-semibold text-[#181b22] dark:text-[#f0f2f5] text-left hover:underline focus:outline-none leading-tight" @click="selectedExercise = exercise" x-text="exercise.name"></button>
        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5" x-show="exercise.prescribed_sets">
            {{ Str::before(__('client.log_workout.prescribed'), ':sets') }}<span x-text="exercise.prescribed_sets"></span>{{ Str::between(__('client.log_workout.prescribed'), ':sets', ':reps') }}<span x-text="exercise.prescribed_reps"></span>{{ Str::after(__('client.log_workout.prescribed'), ':reps') }}
        </p>
    </div>
</div>
```

**Step 2: Add RPE column to set table header**

Find the `<thead>` section. Change from 4 columns (Set, Weight, Reps, -) to 5:

```blade
<tr class="text-left text-xs text-[#8c93a0] dark:text-[#6b7280] border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
    <th class="pb-2 pr-2 w-8">{{ __('client.log_workout.set') }}</th>
    <th class="pb-2 pr-2">{{ __('client.log_workout.weight_kg') }}</th>
    <th class="pb-2 pr-2">{{ __('client.log_workout.reps') }}</th>
    <th class="pb-2 pr-2 w-16 text-center">RPE</th>
    <th class="pb-2 w-6"></th>
</tr>
```

**Step 3: Add RPE input to each set row**

Inside `<template x-for="(set, setIndex) in exercise.sets">`, change the 4-column `<tr>` to 5 columns. Add between the reps `<td>` and the remove `<td>`:

```blade
<td class="py-1.5 pr-2">
    <input
        type="number"
        min="1"
        max="10"
        :name="`exercises[${exerciseIndex}][sets][${setIndex}][rpe]`"
        x-model="set.rpe"
        placeholder="—"
        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm text-center focus:border-[#c6f24e] focus:ring-[#c6f24e]"
        :style="set.rpe ? `border-color: ${rpeColor(set.rpe)}; box-shadow: 0 0 0 1px ${rpeColor(set.rpe)}20` : ''"
    >
</td>
```

**Step 4: Add `rpe` field to Alpine data structures and `rpeColor` helper**

In the `workoutLogger()` Alpine function, update `addSet()` and `addExercise()` to include `rpe: ''`:

```javascript
addSet(exerciseIndex) {
    this.exercises[exerciseIndex].sets.push({ weight: '', reps: '', rpe: '' });
},

addExercise(exercise) {
    this.exercises.push({
        // ... existing fields ...
        sets: [{ weight: '', reps: '', rpe: '' }],
    });
},
```

Also add the `rpeColor` helper method to the Alpine component:
```javascript
rpeColor(rpe) {
    if (!rpe) return '';
    const n = parseInt(rpe);
    if (n <= 3) return 'oklch(0.78 0.15 145)';
    if (n <= 6) return 'oklch(0.82 0.15 90)';
    if (n <= 8) return 'oklch(0.74 0.17 55)';
    return 'oklch(0.66 0.2 28)';
},
```

**Step 5: Verify in browser**
- Open log-workout, verify ExThumb appears on each exercise card
- Verify RPE column shows in the set table
- Verify RPE input color-codes on input (green → amber → orange → red)

**Step 6: Pint + commit**

```bash
vendor/bin/pint resources/views/client/log-workout.blade.php --format agent
git add resources/views/client/log-workout.blade.php
git commit -m "feat: add ExThumb and RPE to workout logger"
```

---

## Task 7: ExThumb on Program page

**Goal:** Add `<x-ex-thumb>` to each exercise row in `program.blade.php`.

**Files:**
- Modify: `resources/views/client/program.blade.php`

**Step 1: Find the exercise row in the `@foreach($workout->exercises as $workoutExercise)` loop**

The current row is:
```blade
<div class="px-5 py-4 flex items-center justify-between">
    <div class="flex-1 min-w-0">
        <button type="button" ...>{{ $workoutExercise->exercise->name }}</button>
        ...
    </div>
    <span ...>{{ $workoutExercise->sets }} sets...</span>
</div>
```

Change to:
```blade
<div class="px-5 py-4 flex items-center gap-3">
    <x-ex-thumb :muscle="ucfirst(str_replace('_', ' ', $workoutExercise->exercise->muscle_group))" :size="40" />
    <div class="flex-1 min-w-0">
        <button type="button" ...>{{ $workoutExercise->exercise->name }}</button>
        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
            {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
            ...
        </p>
    </div>
    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-[rgba(18,22,31,0.06)] dark:bg-[rgba(255,255,255,0.06)] text-[#8c93a0] dark:text-[#6b7280] flex-shrink-0">
        {{ ucfirst(str_replace('_', ' ', $workoutExercise->exercise->muscle_group)) }}
    </span>
</div>
```

**Step 2: Run build to check for class purge issues**

```bash
npm run build 2>&1 | tail -5
```
Expected: `✓ built in Xs`

**Step 3: Commit**

```bash
git add resources/views/client/program.blade.php
git commit -m "feat: add ExThumb to program exercise rows"
```

---

## Task 8: Improved exercise picker (log-workout)

**Goal:** Replace the basic inline list picker with a cleaner full-width search with ExThumb thumbnails, matching the design's SearchSelect component.

**Files:**
- Modify: `resources/views/client/log-workout.blade.php`

**Step 1: Replace the exercise picker section**

Find the `<div x-show="showExercisePicker" x-cloak>` block (around lines 263–302) and replace it with:

```blade
<div x-show="showExercisePicker" x-cloak class="space-y-2">
    <!-- Search header -->
    <div class="flex items-center gap-2 mb-1">
        <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5] flex-1">{{ __('client.log_workout.select_exercise') }}</h3>
        <button type="button" @click="showExercisePicker = false"
            class="p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-[#181b22] dark:hover:text-[#f0f2f5] rounded">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <!-- Search input -->
    <div class="flex items-center gap-2 px-3 h-11 bg-[#f3f5f7] dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-xl focus-within:border-[#c6f24e] focus-within:ring-1 focus-within:ring-[rgba(198,242,78,0.3)] transition-all">
        <svg class="w-4 h-4 text-[#8c93a0] dark:text-[#6b7280] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input
            type="text"
            x-model="exerciseSearch"
            placeholder="{{ __('client.log_workout.search_exercises') }}"
            class="flex-1 border-0 bg-transparent outline-none text-sm text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280]"
            x-ref="exerciseSearchInput"
        >
        <button type="button" x-show="exerciseSearch" @click="exerciseSearch = ''"
            class="text-[#8c93a0] dark:text-[#6b7280]">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <!-- Results list -->
    <div class="max-h-64 overflow-y-auto rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] bg-white dark:bg-[#181b21]">
        <template x-for="exercise in filteredExercises" :key="exercise.id">
            <button
                type="button"
                @click="addExercise(exercise)"
                class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-[rgba(198,242,78,0.08)] dark:hover:bg-[rgba(198,242,78,0.06)] transition-colors border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)] last:border-0 text-left"
            >
                <div x-html="exThumbHtml(exercise.muscle_group, 36)" class="flex-shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5] truncate" x-text="exercise.name"></div>
                    <div class="text-xs text-[#8c93a0] dark:text-[#6b7280]" x-text="exercise.muscle_group.replace(/_/g, ' ')"></div>
                </div>
                <svg class="w-4 h-4 text-[#8c93a0] dark:text-[#6b7280] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </template>
        <div x-show="filteredExercises.length === 0"
            class="px-3 py-6 text-center text-sm text-[#8c93a0] dark:text-[#6b7280]">
            {{ __('client.log_workout.no_exercises_found') }}
        </div>
        <div x-show="filteredExercises.length === 0 && !exercisesLoaded"
            class="px-3 py-6 text-center text-sm text-[#8c93a0] dark:text-[#6b7280]">
            Loading…
        </div>
    </div>
</div>
```

**Step 2: Also update the "Add Exercise" trigger button** (the dashed border button, around line 251–261) to match the design:

```blade
<button
    type="button"
    @click="openExercisePicker()"
    class="w-full flex items-center justify-center gap-2 px-4 py-3.5 border-2 border-dashed border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.14)] rounded-xl text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:border-[#c6f24e] hover:text-[#5c7a10] dark:hover:border-[#c6f24e] dark:hover:text-[#c6f24e] transition-all"
>
    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    {{ __('client.log_workout.add_exercise') }}
</button>
```

**Step 3: Verify in browser**
- Open log-workout, tap "Add Exercise"
- Verify ExThumb appears for each exercise in the list
- Verify search filters by name and muscle group
- Verify clear button (×) works

**Step 4: Pint + commit**

```bash
vendor/bin/pint resources/views/client/log-workout.blade.php --format agent
git add resources/views/client/log-workout.blade.php
git commit -m "feat: improve exercise picker with ExThumb and better search UX"
```

---

## Task 9: ExThumb on log.blade.php (workout select)

**Goal:** Add muscle group color dots or ExThumb to the workout selection cards.

**Files:**
- Modify: `resources/views/client/log.blade.php`

**Step 1: Add a small muscle group chip strip to each workout card**

Find the `@foreach($activeProgram->program->workouts as $workout)` loop. Inside the card, below `<p>Day N · X exercises</p>`, add a strip of unique muscle group thumbnails from the workout's exercises (first 4):

```blade
@php
    $muscles = $workout->exercises->map(fn($we) => ucfirst(str_replace('_', ' ', $we->exercise->muscle_group)))->unique()->take(4)->values();
@endphp
@if($muscles->isNotEmpty())
    <div class="flex gap-1.5 mt-2">
        @foreach($muscles as $muscle)
            <x-ex-thumb :muscle="$muscle" :size="24" />
        @endforeach
        @if($workout->exercises->count() > 4)
            <div class="w-6 h-6 rounded-md bg-[#f3f5f7] dark:bg-[rgba(255,255,255,0.06)] flex items-center justify-center text-[10px] font-bold text-[#8c93a0] dark:text-[#6b7280]">
                +{{ $workout->exercises->count() - 4 }}
            </div>
        @endif
    </div>
@endif
```

**Step 2: Commit**

```bash
git add resources/views/client/log.blade.php
git commit -m "feat: add muscle group thumbnails to workout selection cards"
```

---

## Task 10: Run full test suite

```bash
php artisan test --compact
```

Expect: all pre-existing tests pass + new RPE tests pass. The 9 pre-existing Breeze failures are known and expected.

---

## Task 11: Final build check

```bash
npm run build 2>&1 | tail -6
```

Expect: `✓ built in Xs`, no errors.

---

## Summary of changes

| File | Change |
|------|--------|
| `database/migrations/*_add_rpe_to_exercise_logs_table.php` | New — adds `rpe` tinyint nullable |
| `app/Models/ExerciseLog.php` | Add `rpe` to fillable + cast |
| `app/Http/Controllers/Client/WorkoutLogController.php` | Store `rpe` per set |
| `resources/views/components/ex-thumb.blade.php` | New — muscle-keyed gradient tile |
| `resources/views/client/log-workout.blade.php` | ExThumb on cards, RPE column, improved picker |
| `resources/views/client/program.blade.php` | ExThumb on exercise rows |
| `resources/views/client/log.blade.php` | Muscle thumbnail strip on workout cards |
| `tests/Unit/ExerciseLogRpeTest.php` | New unit tests |
| `tests/Feature/Client/WorkoutLogRpeTest.php` | New feature test |
