# Pennant Feature Flags Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Install Laravel Pennant and gate the loyalty system per-coach, with toggles in the Filament admin and enforcement at coach nav, coach routes, client routes, and client dashboard.

**Architecture:** Pennant scopes flags to the Coach `User` model and stores state in a `features` DB table. All checks read `Feature::for($coach)->active(Loyalty::class)`. A new `EnsureFeatureActive` middleware enforces route-level gates. Blade `@if` conditionals handle nav and dashboard.

**Tech Stack:** Laravel Pennant, Filament v5 (Toggle field), Laravel middleware alias, Blade conditionals, Pest 4 tests.

---

### Task 1: Install Laravel Pennant

**Files:**
- Modify: `composer.json` (via composer)
- Create: `config/pennant.php` (auto-published)
- Create: `database/migrations/XXXX_create_features_table.php` (auto-published)

**Step 1: Install the package**

```bash
composer require laravel/pennant
```

Expected: resolves and installs `laravel/pennant`.

**Step 2: Publish migrations and config**

```bash
php artisan vendor:publish --provider="Laravel\Pennant\PennantServiceProvider" --no-interaction
```

Expected: publishes `config/pennant.php` and a migration for the `features` table.

**Step 3: Run the migration**

```bash
php artisan migrate --no-interaction
```

Expected: `features` table created.

**Step 4: Commit**

```bash
git add composer.json composer.lock config/pennant.php database/migrations/
git commit -m "feat: install Laravel Pennant for feature flags"
```

---

### Task 2: Create the Loyalty Feature class

**Files:**
- Create: `app/Features/Loyalty.php`

**Step 1: Write the failing test**

Create `tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php`:

```php
<?php

use App\Features\Loyalty;
use App\Models\User;
use Laravel\Pennant\Feature;

test('loyalty feature defaults to inactive for new coaches', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);

    expect(Feature::for($coach)->active(Loyalty::class))->toBeFalse();
});
```

**Step 2: Run test to verify it fails**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
```

Expected: FAIL — `App\Features\Loyalty` not found.

**Step 3: Create the feature class**

```bash
mkdir -p app/Features
```

Create `app/Features/Loyalty.php`:

```php
<?php

namespace App\Features;

use App\Models\User;

class Loyalty
{
    public function resolve(User $scope): bool
    {
        return false;
    }
}
```

**Step 4: Run test to verify it passes**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
```

Expected: PASS.

**Step 5: Commit**

```bash
git add app/Features/Loyalty.php tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
git commit -m "feat: add Loyalty feature class for Pennant (defaults to inactive)"
```

---

### Task 3: Create the EnsureFeatureActive middleware

**Files:**
- Create: `app/Http/Middleware/EnsureFeatureActive.php`
- Modify: `bootstrap/app.php`

**Step 1: Write the failing test**

Add to `tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php`:

```php
test('coach with loyalty off gets 403 on loyalty routes', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($coach)
        ->get(route('coach.rewards.index'))
        ->assertForbidden();
});

test('coach with loyalty on can access loyalty routes', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);
    Feature::for($coach)->activate(Loyalty::class);

    $this->actingAs($coach)
        ->get(route('coach.rewards.index'))
        ->assertOk();
});

test('client with coach loyalty off gets 403 on loyalty routes', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $this->actingAs($client)
        ->get(route('client.rewards'))
        ->assertForbidden();
});

test('client with coach loyalty on can access loyalty routes', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);
    Feature::for($coach)->activate(Loyalty::class);

    $this->actingAs($client)
        ->get(route('client.rewards'))
        ->assertOk();
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
```

Expected: new tests fail — routes are currently open without any feature check.

**Step 3: Create the middleware**

```bash
php artisan make:class app/Http/Middleware/EnsureFeatureActive --no-interaction
```

Replace the generated content of `app/Http/Middleware/EnsureFeatureActive.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureActive
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        $coach = $user->isCoach() ? $user : $user->coach;

        if (! $coach || Feature::for($coach)->inactive($feature)) {
            abort(403);
        }

        return $next($request);
    }
}
```

**Step 4: Register the middleware alias in `bootstrap/app.php`**

Find the `->withMiddleware` block and add the alias:

```php
$middleware->alias([
    'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    'feature' => \App\Http\Middleware\EnsureFeatureActive::class,
]);
```

**Step 5: Apply middleware to loyalty routes in `routes/web.php`**

Currently the coach loyalty routes are scattered. Wrap them in a named group:

```php
// Before (in the coach route group, lines ~60-66):
Route::resource('rewards', Coach\RewardController::class)->except(['show']);
Route::resource('achievements', Coach\AchievementController::class)->except(['show']);
Route::post('clients/{client}/achievements/{achievement}/award', [Coach\AchievementController::class, 'award'])->name('clients.achievements.award');

Route::get('redemptions', [Coach\RedemptionController::class, 'index'])->name('redemptions.index');
Route::patch('redemptions/{redemption}', [Coach\RedemptionController::class, 'update'])->name('redemptions.update');
Route::get('clients/{client}/loyalty', [Coach\LoyaltyController::class, 'show'])->name('clients.loyalty');

// After — wrap in middleware group:
Route::middleware('feature:' . \App\Features\Loyalty::class)->group(function (): void {
    Route::resource('rewards', Coach\RewardController::class)->except(['show']);
    Route::resource('achievements', Coach\AchievementController::class)->except(['show']);
    Route::post('clients/{client}/achievements/{achievement}/award', [Coach\AchievementController::class, 'award'])->name('clients.achievements.award');

    Route::get('redemptions', [Coach\RedemptionController::class, 'index'])->name('redemptions.index');
    Route::patch('redemptions/{redemption}', [Coach\RedemptionController::class, 'update'])->name('redemptions.update');
    Route::get('clients/{client}/loyalty', [Coach\LoyaltyController::class, 'show'])->name('clients.loyalty');
});
```

Also wrap the client loyalty routes (lines ~114-117 in the client route group):

```php
// Before:
Route::get('achievements', [Client\AchievementController::class, 'index'])->name('achievements');
Route::get('rewards', [Client\RewardController::class, 'index'])->name('rewards');
Route::post('rewards/{reward}/redeem', [Client\RewardController::class, 'redeem'])->name('rewards.redeem');
Route::get('loyalty', [Client\LoyaltyController::class, 'index'])->name('loyalty');

// After:
Route::middleware('feature:' . \App\Features\Loyalty::class)->group(function (): void {
    Route::get('achievements', [Client\AchievementController::class, 'index'])->name('achievements');
    Route::get('rewards', [Client\RewardController::class, 'index'])->name('rewards');
    Route::post('rewards/{reward}/redeem', [Client\RewardController::class, 'redeem'])->name('rewards.redeem');
    Route::get('loyalty', [Client\LoyaltyController::class, 'index'])->name('loyalty');
});
```

**Step 6: Run tests to verify they pass**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
```

Expected: all pass.

**Step 7: Commit**

```bash
git add app/Http/Middleware/EnsureFeatureActive.php bootstrap/app.php routes/web.php tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
git commit -m "feat: add EnsureFeatureActive middleware and gate loyalty routes"
```

---

### Task 4: Hide coach nav loyalty section when flag is off

**Files:**
- Modify: `resources/views/components/layouts/coach.blade.php`

The Loyalty section appears **twice** in this file — once in the desktop sidebar (around line 141) and once in the mobile drawer (around line 269). Both must be wrapped.

**Step 1: Write the failing test**

Add to `tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php`:

```php
test('coach nav hides loyalty section when flag is off', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertDontSee('coach.rewards.index', false)
        ->assertDontSee('coach.achievements.index', false)
        ->assertDontSee('coach.redemptions.index', false);
});

test('coach nav shows loyalty section when flag is on', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);
    Feature::for($coach)->activate(Loyalty::class);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertSee(route('coach.rewards.index'), false);
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php --filter="coach nav"
```

Expected: FAIL — nav currently always shows loyalty links.

**Step 3: Wrap both loyalty sections in the coach layout**

In `resources/views/components/layouts/coach.blade.php`, find the **desktop sidebar** loyalty section (around line 141) and wrap it:

```blade
{{-- Before --}}
<div class="pt-2 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Loyalty</p>
</div>

<a href="{{ route('coach.rewards.index') }}" ...>Rewards</a>
<a href="{{ route('coach.achievements.index') }}" ...>Achievements</a>
<a href="{{ route('coach.redemptions.index') }}" ...>Redemptions</a>

{{-- After --}}
@feature(\App\Features\Loyalty::class)
<div class="pt-2 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Loyalty</p>
</div>

<a href="{{ route('coach.rewards.index') }}" ...>Rewards</a>
<a href="{{ route('coach.achievements.index') }}" ...>Achievements</a>
<a href="{{ route('coach.redemptions.index') }}" ...>Redemptions</a>
@endfeature
```

Do the same for the **mobile drawer** loyalty section (around line 269).

> **Note:** `@feature` / `@endfeature` is Pennant's built-in Blade directive. It checks against `auth()->user()` by default. Since coaches are always logged in here, this is correct.

**Step 4: Run tests to verify they pass**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
```

Expected: all pass.

**Step 5: Commit**

```bash
git add resources/views/components/layouts/coach.blade.php tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
git commit -m "feat: hide coach loyalty nav section when Loyalty feature is off"
```

---

### Task 5: Hide client dashboard XP card and gate client loyalty routes when flag is off

**Files:**
- Modify: `app/Http/Controllers/Client/DashboardController.php`
- Modify: `resources/views/client/dashboard.blade.php`

**Step 1: Write the failing test**

Add to `tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php`:

```php
test('client dashboard hides XP card when coach loyalty flag is off', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);

    $this->actingAs($client)
        ->get(route('client.dashboard'))
        ->assertDontSee('XP')
        ->assertDontSee('Rewards Shop');
});

test('client dashboard shows XP card when coach loyalty flag is on', function (): void {
    $coach = User::factory()->create(['role' => 'coach']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id]);
    Feature::for($coach)->activate(Loyalty::class);

    $this->actingAs($client)
        ->get(route('client.dashboard'))
        ->assertSee('Rewards Shop');
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php --filter="client dashboard"
```

Expected: FAIL.

**Step 3: Pass `$loyaltyEnabled` from `DashboardController`**

In `app/Http/Controllers/Client/DashboardController.php`, add at the top of `__invoke()` before the XP queries:

```php
use App\Features\Loyalty;
use Laravel\Pennant\Feature;
```

Add a variable **before** the xpSummary queries and only run those queries when loyalty is enabled:

```php
$coach = $user->coach;
$loyaltyEnabled = $coach && Feature::for($coach)->active(Loyalty::class);

$xpSummary = $loyaltyEnabled ? $user->xpSummary()->with('currentLevel')->first() : null;
$nextLevel = $loyaltyEnabled && $xpSummary
    ? Level::where('xp_required', '>', $xpSummary->total_xp)->orderBy('xp_required')->first()
    : ($loyaltyEnabled ? Level::orderBy('xp_required')->first() : null);
$recentAchievements = $loyaltyEnabled ? $user->achievements()->latest('user_achievements.earned_at')->limit(3)->get() : collect();
```

Pass it to the view:

```php
return view('client.dashboard', [
    // ... existing keys ...
    'loyaltyEnabled' => $loyaltyEnabled,
    // keep xpSummary, nextLevel, recentAchievements as they are
]);
```

**Step 4: Wrap the XP card in the view**

In `resources/views/client/dashboard.blade.php`, the XP & Loyalty card is currently gated with `@if($xpSummary || $nextLevel)`. Replace that condition:

```blade
{{-- Before --}}
@if($xpSummary || $nextLevel)

{{-- After --}}
@if($loyaltyEnabled && ($xpSummary || $nextLevel))
```

**Step 5: Run tests to verify they pass**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
```

Expected: all pass.

**Step 6: Commit**

```bash
git add app/Http/Controllers/Client/DashboardController.php resources/views/client/dashboard.blade.php tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php
git commit -m "feat: hide client XP card when coach loyalty feature is off"
```

---

### Task 6: Add feature toggles to the Filament admin coach edit page

**Files:**
- Modify: `app/Filament/Resources/Users/Schemas/UserForm.php`
- Modify: `app/Filament/Resources/Users/Schemas/UserInfolist.php`

**Step 1: Write the failing test**

Create `tests/Feature/FeatureFlags/LoyaltyFeatureFlagAdminTest.php`:

```php
<?php

use App\Features\Loyalty;
use App\Models\User;
use Laravel\Pennant\Feature;

test('admin can enable loyalty for a coach via edit page', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $coach = User::factory()->create(['role' => 'coach']);

    expect(Feature::for($coach)->active(Loyalty::class))->toBeFalse();

    $this->actingAs($admin)
        ->livewire(\App\Filament\Resources\Users\Pages\EditUser::class, ['record' => $coach->getRouteKey()])
        ->fillForm(['feature_loyalty' => true])
        ->call('save');

    expect(Feature::for($coach->fresh())->active(Loyalty::class))->toBeTrue();
});

test('admin can disable loyalty for a coach via edit page', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $coach = User::factory()->create(['role' => 'coach']);
    Feature::for($coach)->activate(Loyalty::class);

    $this->actingAs($admin)
        ->livewire(\App\Filament\Resources\Users\Pages\EditUser::class, ['record' => $coach->getRouteKey()])
        ->fillForm(['feature_loyalty' => false])
        ->call('save');

    expect(Feature::for($coach->fresh())->active(Loyalty::class))->toBeFalse();
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagAdminTest.php
```

Expected: FAIL — no `feature_loyalty` field exists yet.

**Step 3: Add the Toggle to `UserForm`**

In `app/Filament/Resources/Users/Schemas/UserForm.php`, add imports:

```php
use App\Features\Loyalty;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Laravel\Pennant\Feature;
```

At the end of the `->components([...])` array, add:

```php
Section::make('Features')
    ->schema([
        Toggle::make('feature_loyalty')
            ->label('Loyalty System')
            ->helperText('Enables XP, levels, achievements, and rewards for this coach and their clients.')
            ->default(fn($record) => $record ? Feature::for($record)->active(Loyalty::class) : false)
            ->afterStateUpdated(function ($record, bool $state): void {
                if (! $record) {
                    return;
                }
                $state
                    ? Feature::for($record)->activate(Loyalty::class)
                    : Feature::for($record)->deactivate(Loyalty::class);
            })
            ->live()
            ->dehydrated(false),
    ]),
```

**Step 4: Add a read-only Features section to `UserInfolist`**

In `app/Filament/Resources/Users/Schemas/UserInfolist.php`, add imports:

```php
use App\Features\Loyalty;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Schemas\Schema;
use Laravel\Pennant\Feature;
```

At the end of `->components([...])`, add:

```php
Section::make('Features')
    ->schema([
        TextEntry::make('loyalty_feature_status')
            ->label('Loyalty System')
            ->state(fn($record) => Feature::for($record)->active(Loyalty::class) ? 'Enabled' : 'Disabled')
            ->badge()
            ->color(fn(string $state) => $state === 'Enabled' ? 'success' : 'danger'),
    ]),
```

**Step 5: Run tests to verify they pass**

```bash
php artisan test --compact tests/Feature/FeatureFlags/LoyaltyFeatureFlagAdminTest.php
```

Expected: PASS.

**Step 6: Run full feature flags suite**

```bash
php artisan test --compact tests/Feature/FeatureFlags/
```

Expected: all pass.

**Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 8: Commit**

```bash
git add app/Filament/Resources/Users/Schemas/UserForm.php app/Filament/Resources/Users/Schemas/UserInfolist.php tests/Feature/FeatureFlags/LoyaltyFeatureFlagAdminTest.php
git commit -m "feat: add Loyalty feature toggle to Filament coach edit page"
```

---

### Task 7: Run full test suite and verify nothing regressed

**Step 1: Run all tests**

```bash
php artisan test --compact
```

Expected: all existing tests pass plus the new feature flag tests.

**Step 2: Run pint one final time**

```bash
vendor/bin/pint --dirty --format agent
```

Expected: `{"result":"pass"}`.

**Step 3: Commit any pint fixes if needed**

```bash
git add -p
git commit -m "style: pint formatting fixes"
```
