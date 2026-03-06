# Client Exercise Detail Modal — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Let clients tap any exercise name in their program view to see a bottom-sheet modal with the exercise description and YouTube video.

**Architecture:** Pure frontend change — the program view already eagerly loads full exercise data. Add an Alpine.js component with `selectedExercise` state to the existing `client/program.blade.php`. Exercise names become buttons that set `selectedExercise` and open the modal. The modal renders conditionally based on that state.

**Tech Stack:** Alpine.js v3, Tailwind CSS v3, Blade, no new routes or PHP needed.

---

### Task 1: Write the failing feature test

**Files:**
- Create: `tests/Feature/Client/ExerciseDetailModalTest.php`

**Step 1: Create the test file**

Run:
```bash
php artisan make:test --pest Client/ExerciseDetailModalTest
```

**Step 2: Write the test**

Open `tests/Feature/Client/ExerciseDetailModalTest.php` and replace its contents with:

```php
<?php

use App\Models\ClientProgram;
use App\Models\Exercise;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WorkoutExercise;

beforeEach(function () {
    $coach = User::factory()->create(['role' => 'coach']);

    $this->exercise = Exercise::factory()->create([
        'coach_id' => $coach->id,
        'name' => 'Barbell Squat',
        'description' => 'Keep your back straight and core tight.',
        'muscle_group' => 'quads',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ]);

    $program = Program::factory()->create(['coach_id' => $coach->id]);
    $workout = ProgramWorkout::factory()->create([
        'program_id' => $program->id,
        'name' => 'Day 1',
        'day_number' => 1,
    ]);
    WorkoutExercise::factory()->create([
        'program_workout_id' => $workout->id,
        'exercise_id' => $this->exercise->id,
        'sets' => 4,
        'reps' => 8,
    ]);

    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);
    ClientProgram::factory()->create([
        'user_id' => $this->client->id,
        'program_id' => $program->id,
        'started_at' => now(),
    ]);
});

it('renders exercise description and video embed url in the program page', function () {
    $response = $this->actingAs($this->client)->get(route('client.program'));

    $response->assertOk();
    $response->assertSee('Barbell Squat');
    $response->assertSee('Keep your back straight and core tight.');
    $response->assertSee('youtube.com/embed/dQw4w9WgXcQ');
});

it('shows a placeholder when exercise has no description or video', function () {
    $coach = User::factory()->create(['role' => 'coach']);
    $exercise = Exercise::factory()->create([
        'coach_id' => $coach->id,
        'name' => 'Plank',
        'description' => null,
        'muscle_group' => 'core',
        'video_url' => null,
    ]);

    $program = Program::factory()->create(['coach_id' => $coach->id]);
    $workout = ProgramWorkout::factory()->create(['program_id' => $program->id, 'day_number' => 1]);
    WorkoutExercise::factory()->create([
        'program_workout_id' => $workout->id,
        'exercise_id' => $exercise->id,
        'sets' => 3,
        'reps' => 60,
    ]);

    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);
    ClientProgram::factory()->create([
        'user_id' => $client->id,
        'program_id' => $program->id,
        'started_at' => now(),
    ]);

    $response = $this->actingAs($client)->get(route('client.program'));

    $response->assertOk();
    $response->assertSee('Plank');
    $response->assertSee('No description provided');
    $response->assertSee('No video available');
});
```

**Step 3: Run it to confirm it fails**

```bash
php artisan test --compact --filter=ExerciseDetailModalTest
```

Expected: FAIL — `assertSee('No description provided')` fails because the program view doesn't show that text yet.

---

### Task 2: Implement the modal in the program view

**Files:**
- Modify: `resources/views/client/program.blade.php`

**Step 1: Wrap the top-level div in Alpine component state**

Change the opening div from:
```blade
<div class="space-y-6">
```
to:
```blade
<div
    class="space-y-6"
    x-data="{ selectedExercise: null }"
    @keydown.escape.window="selectedExercise = null"
>
```

**Step 2: Make exercise names tappable**

Find the paragraph tag that displays the exercise name inside the `@foreach($workout->exercises as $workoutExercise)` loop:

```blade
<p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $workoutExercise->exercise->name }}</p>
```

Replace it with a button that sets `selectedExercise`:

```blade
<button
    type="button"
    class="text-sm font-medium text-gray-900 dark:text-gray-100 text-left hover:underline focus:outline-none"
    @click="selectedExercise = {
        name: @js($workoutExercise->exercise->name),
        muscleGroup: @js(ucfirst(str_replace('_', ' ', $workoutExercise->exercise->muscle_group))),
        description: @js($workoutExercise->exercise->description),
        embedUrl: @js($workoutExercise->exercise->getYoutubeEmbedUrl()),
    }"
>
    {{ $workoutExercise->exercise->name }}
</button>
```

**Step 3: Add the modal at the bottom, before the closing `</div>` of the Alpine component**

Before the final `</div>` that closes the `x-data` wrapper (after all the `@if/$activeProgram/` blocks), add:

```blade
<!-- Exercise Detail Modal -->
<template x-if="selectedExercise">
    <div class="fixed inset-0 z-50 flex items-end justify-center">
        <!-- Backdrop -->
        <div
            class="absolute inset-0 bg-black/50"
            @click="selectedExercise = null"
        ></div>

        <!-- Bottom sheet -->
        <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 rounded-t-2xl shadow-xl overflow-y-auto max-h-[85vh]">
            <!-- Handle bar -->
            <div class="flex justify-center pt-3 pb-1">
                <div class="w-10 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></div>
            </div>

            <!-- Header -->
            <div class="flex items-start justify-between px-5 pt-3 pb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="selectedExercise.name"></h2>
                    <span
                        class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                        x-text="selectedExercise.muscleGroup"
                    ></span>
                </div>
                <button
                    type="button"
                    @click="selectedExercise = null"
                    class="p-2 -mr-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md"
                    aria-label="Close"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Video -->
            <div class="px-5 pb-4">
                <template x-if="selectedExercise.embedUrl">
                    <div class="aspect-video rounded-lg overflow-hidden bg-black">
                        <iframe
                            :src="selectedExercise.embedUrl"
                            class="w-full h-full"
                            :title="selectedExercise.name"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                </template>
                <template x-if="!selectedExercise.embedUrl">
                    <div class="aspect-video rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No video available</p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Description -->
            <div class="px-5 pb-8">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                <template x-if="selectedExercise.description">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="selectedExercise.description"></p>
                </template>
                <template x-if="!selectedExercise.description">
                    <p class="text-sm text-gray-400 dark:text-gray-500 italic">No description provided</p>
                </template>
            </div>
        </div>
    </div>
</template>
```

---

### Task 3: Run pint and tests

**Step 1: Format code with pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 2: Run the feature tests**

```bash
php artisan test --compact --filter=ExerciseDetailModalTest
```

Expected: Both tests PASS.

**Step 3: Commit**

```bash
git add resources/views/client/program.blade.php tests/Feature/Client/ExerciseDetailModalTest.php
git commit -m "feat: add exercise detail modal for clients on program view"
```
