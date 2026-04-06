# Coach Metrics Onboarding Popup — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Show a one-time popup on the coach dashboard after first sign-in, asking whether to seed 6 default tracking metrics in the coach's locale.

**Architecture:** Add `metrics_onboarded_at` timestamp to `users` to gate the popup. A dedicated `MetricsSetupController` handles both yes/no actions, setting the timestamp and optionally seeding metrics via `TrackingMetric::seedDefaults()`. The popup is an Alpine.js modal on the dashboard — non-dismissable, shown only when the timestamp is null.

**Tech Stack:** Laravel 12, Blade, Alpine.js, Pest 4

---

### Task 1: Migration — add `metrics_onboarded_at` to `users`

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_add_metrics_onboarded_at_to_users_table.php`
- Modify: `app/Models/User.php`

**Step 1: Create the migration**

```bash
php artisan make:migration add_metrics_onboarded_at_to_users_table --no-interaction
```

**Step 2: Fill the migration**

Open the generated file and replace the `up`/`down` methods:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->timestamp('metrics_onboarded_at')->nullable()->after('locale');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->dropColumn('metrics_onboarded_at');
    });
}
```

**Step 3: Run the migration**

```bash
php artisan migrate --no-interaction
```

Expected: `Migrating: ...add_metrics_onboarded_at... Done.`

**Step 4: Add to `$fillable` and `casts()` in `app/Models/User.php`**

Add `'metrics_onboarded_at'` to the `$fillable` array.

Add to `casts()`:
```php
'metrics_onboarded_at' => 'datetime',
```

**Step 5: Commit**

```bash
git add database/migrations/ app/Models/User.php
git commit -m "feat: add metrics_onboarded_at column to users"
```

---

### Task 2: Update `TrackingMetric::seedDefaults()` with locale support

**Files:**
- Modify: `app/Models/TrackingMetric.php`

**Step 1: Write the failing test first** (see Task 5 — write tests after Task 3 translations are in place)

Skip ahead and return here after Task 3 translations exist.

**Step 2: Replace `seedDefaults()` in `TrackingMetric`**

Replace the existing method with:

```php
/**
 * Seed default metrics for a coach, using their locale for names.
 *
 * @return array<int, self>
 */
public static function seedDefaults(int $coachId, string $locale = 'en'): array
{
    $defaults = [
        [
            'name' => __('coach.default_metrics.weight', locale: $locale),
            'type' => 'number',
            'unit' => 'kg',
            'order' => 1,
        ],
        [
            'name' => __('coach.default_metrics.steps', locale: $locale),
            'type' => 'number',
            'unit' => 'steps',
            'order' => 2,
        ],
        [
            'name' => __('coach.default_metrics.progress_image', locale: $locale),
            'type' => 'image',
            'order' => 3,
        ],
        [
            'name' => __('coach.default_metrics.mood', locale: $locale),
            'type' => 'scale',
            'order' => 4,
        ],
        [
            'name' => __('coach.default_metrics.energy', locale: $locale),
            'type' => 'scale',
            'order' => 5,
        ],
        [
            'name' => __('coach.default_metrics.sleep', locale: $locale),
            'type' => 'scale',
            'order' => 6,
        ],
    ];

    $metrics = [];
    foreach ($defaults as $default) {
        $metrics[] = self::create(array_merge($default, ['coach_id' => $coachId]));
    }

    return $metrics;
}
```

---

### Task 3: Add translations (en / hr / sl)

**Files:**
- Modify: `lang/en/coach.php`
- Modify: `lang/hr/coach.php`
- Modify: `lang/sl/coach.php`

**Step 1: Add to `lang/en/coach.php`**

Append before the closing `];`:

```php
'default_metrics' => [
    'weight' => 'Body Weight',
    'steps' => 'Steps',
    'progress_image' => 'Progress Image',
    'mood' => 'Mood',
    'energy' => 'Energy Level',
    'sleep' => 'Sleep Quality',
],

'metrics_setup' => [
    'title' => 'Set Up Your Tracking Metrics',
    'description' => 'Would you like us to add some default tracking metrics to your account? We\'ll add: Body Weight, Steps, Progress Image, Mood, Energy Level, and Sleep Quality.',
    'yes' => 'Yes, add them',
    'skip' => 'Skip for now',
    'seeded' => 'Default metrics added. You can manage them in the <a href=":url" class="font-semibold underline">Tracking</a> section.',
    'skipped' => 'No problem. You can create metrics anytime in the <a href=":url" class="font-semibold underline">Tracking</a> section.',
],
```

**Step 2: Add to `lang/hr/coach.php`**

```php
'default_metrics' => [
    'weight' => 'Tjelesna težina',
    'steps' => 'Koraci',
    'progress_image' => 'Slika napretka',
    'mood' => 'Raspoloženje',
    'energy' => 'Razina energije',
    'sleep' => 'Kvaliteta sna',
],

'metrics_setup' => [
    'title' => 'Postavi metrike praćenja',
    'description' => 'Želite li da dodamo neke zadane metrike praćenja na vaš račun? Dodat ćemo: tjelesnu težinu, korake, sliku napretka, raspoloženje, razinu energije i kvalitetu sna.',
    'yes' => 'Da, dodaj ih',
    'skip' => 'Preskoči za sada',
    'seeded' => 'Zadane metrike su dodane. Možete ih upravljati u odjeljku <a href=":url" class="font-semibold underline">Praćenje</a>.',
    'skipped' => 'Nema problema. Metrike možete kreirati u bilo koje vrijeme u odjeljku <a href=":url" class="font-semibold underline">Praćenje</a>.',
],
```

**Step 3: Add to `lang/sl/coach.php`**

```php
'default_metrics' => [
    'weight' => 'Telesna teža',
    'steps' => 'Koraki',
    'progress_image' => 'Slika napredka',
    'mood' => 'Razpoloženje',
    'energy' => 'Raven energije',
    'sleep' => 'Kakovost spanja',
],

'metrics_setup' => [
    'title' => 'Nastavi metrike sledenja',
    'description' => 'Ali želite, da dodamo nekaj privzetih metrik sledenja na vaš račun? Dodali bomo: telesno težo, korake, sliko napredka, razpoloženje, raven energije in kakovost spanja.',
    'yes' => 'Da, dodaj jih',
    'skip' => 'Preskoči za zdaj',
    'seeded' => 'Privzete metrike so dodane. Upravljate jih lahko v razdelku <a href=":url" class="font-semibold underline">Sledenje</a>.',
    'skipped' => 'Ni problema. Metrike lahko ustvarite kadarkoli v razdelku <a href=":url" class="font-semibold underline">Sledenje</a>.',
],
```

**Step 4: Commit**

```bash
git add lang/ app/Models/TrackingMetric.php
git commit -m "feat: localize default metric names and add metrics_setup translations"
```

---

### Task 4: Create `MetricsSetupController`

**Files:**
- Create: `app/Http/Controllers/Coach/MetricsSetupController.php`

**Step 1: Generate the controller**

```bash
php artisan make:controller Coach/MetricsSetupController --no-interaction
```

**Step 2: Implement `__invoke`**

Replace the generated file contents with:

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\TrackingMetric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MetricsSetupController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'setup' => ['required', 'boolean'],
        ]);

        $coach = auth()->user();
        $coach->update(['metrics_onboarded_at' => now()]);

        if ($validated['setup']) {
            TrackingMetric::seedDefaults($coach->id, $coach->locale ?? 'en');

            return redirect()->route('coach.dashboard')->with(
                'metrics_setup',
                __('coach.metrics_setup.seeded', ['url' => route('coach.tracking-metrics.index')])
            );
        }

        return redirect()->route('coach.dashboard')->with(
            'metrics_setup',
            __('coach.metrics_setup.skipped', ['url' => route('coach.tracking-metrics.index')])
        );
    }
}
```

---

### Task 5: Register the route

**Files:**
- Modify: `routes/web.php`

**Step 1: Add the route inside the coach middleware group**

Find the existing coach tracking-metrics routes block (around line 88–94). Add directly after the last tracking-metrics route:

```php
Route::post('metrics-setup', [Coach\MetricsSetupController::class, 'store'])->name('metrics-setup');
```

Wait — since this controller uses `__invoke`, use:

```php
Route::post('metrics-setup', Coach\MetricsSetupController::class)->name('metrics-setup');
```

The route name will be `coach.metrics-setup` (it inherits the `coach.` prefix from the group).

---

### Task 6: Add the onboarding popup to the dashboard

**Files:**
- Modify: `resources/views/coach/dashboard.blade.php`

**Step 1: Add flash banner + modal at the top of the view content**

Insert immediately after `<div class="space-y-6">` (line 4):

```blade
{{-- Metrics setup flash banner --}}
@if(session('metrics_setup'))
    <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{!! session('metrics_setup') !!}</p>
            </div>
        </div>
    </div>
@endif

{{-- First-time metrics setup popup --}}
@if(auth()->user()->metrics_onboarded_at === null)
    <div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="metrics-setup-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="metrics-setup-title">
                            {{ __('coach.metrics_setup.title') }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('coach.metrics_setup.description') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6 flex flex-col sm:flex-row gap-3">
                    <form method="POST" action="{{ route('coach.metrics-setup') }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="setup" value="1">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                            {{ __('coach.metrics_setup.yes') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('coach.metrics-setup') }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="setup" value="0">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-700 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                            {{ __('coach.metrics_setup.skip') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
```

**Step 2: Commit**

```bash
git add app/Http/Controllers/Coach/MetricsSetupController.php routes/web.php resources/views/coach/dashboard.blade.php
git commit -m "feat: add metrics setup controller, route, and dashboard popup"
```

---

### Task 7: Write and run tests

**Files:**
- Create: `tests/Feature/Coach/MetricsSetupTest.php`

**Step 1: Generate the test**

```bash
php artisan make:test Coach/MetricsSetupTest --pest --no-interaction
```

**Step 2: Write the tests**

Replace the file content with:

```php
<?php

use App\Models\TrackingMetric;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create(['metrics_onboarded_at' => null]);
});

test('dashboard shows metrics setup popup when metrics_onboarded_at is null', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee(__('coach.metrics_setup.title'));
});

test('dashboard does not show popup when metrics_onboarded_at is set', function () {
    $this->coach->update(['metrics_onboarded_at' => now()]);

    $this->actingAs($this->coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertDontSee(__('coach.metrics_setup.title'));
});

test('choosing yes seeds 6 default metrics and sets metrics_onboarded_at', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('coach.dashboard'));

    $this->coach->refresh();

    expect($this->coach->metrics_onboarded_at)->not->toBeNull();
    expect(TrackingMetric::where('coach_id', $this->coach->id)->count())->toBe(6);
});

test('choosing skip sets metrics_onboarded_at without creating metrics', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.metrics-setup'), ['setup' => '0'])
        ->assertRedirect(route('coach.dashboard'));

    $this->coach->refresh();

    expect($this->coach->metrics_onboarded_at)->not->toBeNull();
    expect(TrackingMetric::where('coach_id', $this->coach->id)->count())->toBe(0);
});

test('yes path seeds metrics with localized names for hr locale', function () {
    $this->coach->update(['locale' => 'hr']);

    $this->actingAs($this->coach)
        ->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('coach.dashboard'));

    expect(TrackingMetric::where('coach_id', $this->coach->id)
        ->where('name', __('coach.default_metrics.weight', locale: 'hr'))
        ->exists()
    )->toBeTrue();
});

test('yes path seeds metrics with localized names for sl locale', function () {
    $this->coach->update(['locale' => 'sl']);

    $this->actingAs($this->coach)
        ->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('coach.dashboard'));

    expect(TrackingMetric::where('coach_id', $this->coach->id)
        ->where('name', __('coach.default_metrics.weight', locale: 'sl'))
        ->exists()
    )->toBeTrue();
});

test('setup route requires authentication', function () {
    $this->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('login'));
});

test('client cannot access coach metrics setup route', function () {
    $client = User::factory()->client()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($client)
        ->post(route('coach.metrics-setup'), ['setup' => '1'])
        ->assertRedirect(route('client.dashboard'));
});
```

**Step 3: Run the tests**

```bash
php artisan test --compact --filter=MetricsSetupTest
```

Expected: All 7 tests pass.

**Step 4: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 5: Commit**

```bash
git add tests/Feature/Coach/MetricsSetupTest.php
git commit -m "test: add coach metrics setup onboarding tests"
```
