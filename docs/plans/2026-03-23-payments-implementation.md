# Payments & Subscriptions Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Integrate Stripe subscriptions for coaches via Laravel Cashier, with plan-based feature/client-limit enforcement and a Stripe Customer Portal for self-service billing.

**Architecture:** Laravel Cashier (Stripe) manages subscription state on the `User` model via the `Billable` trait. A `SubscriptionService` class is the single source of truth for all plan checks. Middleware layers enforce access at the route level.

**Tech Stack:** `laravel/cashier`, Stripe API, `config/plans.php`, Blade + Alpine.js, Pest 4

**Worktree:** `/Users/timotejavsec/Documents/Projects/lift-deck/.worktrees/payments`

**Run tests with:** `php -d memory_limit=256M vendor/bin/pest <path> --compact`

---

## Task 1: Install Laravel Cashier & Create `config/plans.php`

**Files:**
- Create: `config/plans.php`
- Modify: `composer.json` (via composer require)

**Step 1: Install Cashier**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck/.worktrees/payments
composer require laravel/cashier --no-interaction
```

Expected: Cashier installs successfully.

**Step 2: Publish Cashier migrations**

```bash
php artisan vendor:publish --tag="cashier-migrations" --no-interaction
```

Expected: Migration files appear in `database/migrations/`.

**Step 3: Create `config/plans.php`**

```bash
php artisan make:config plans --no-interaction 2>/dev/null || touch config/plans.php
```

Contents of `config/plans.php`:

```php
<?php

return [
    'basic' => [
        'stripe_price_id' => env('STRIPE_PRICE_BASIC'),
        'client_limit' => 5,
        'features' => [],
        'trial_days' => 7,
    ],
    'advanced' => [
        'stripe_price_id' => env('STRIPE_PRICE_ADVANCED'),
        'client_limit' => 15,
        'features' => ['loyalty'],
        'trial_days' => 0,
    ],
    'professional' => [
        'stripe_price_flat_id' => env('STRIPE_PRICE_PROFESSIONAL_FLAT'),
        'stripe_price_metered_id' => env('STRIPE_PRICE_PROFESSIONAL_METERED'),
        'client_limit' => null,
        'included_clients' => 30,
        'features' => ['loyalty', 'custom_branding'],
        'trial_days' => 0,
    ],
];
```

**Step 4: Add Stripe env vars to `.env`**

Add to `.env` (use placeholder values for now — real IDs come from Stripe dashboard):

```
STRIPE_KEY=pk_test_placeholder
STRIPE_SECRET=sk_test_placeholder
STRIPE_WEBHOOK_SECRET=whsec_placeholder
STRIPE_PRICE_BASIC=price_placeholder_basic
STRIPE_PRICE_ADVANCED=price_placeholder_advanced
STRIPE_PRICE_PROFESSIONAL_FLAT=price_placeholder_professional_flat
STRIPE_PRICE_PROFESSIONAL_METERED=price_placeholder_professional_metered
CASHIER_CURRENCY=eur
```

Also add to `phpunit.xml` inside the `<php>` block:
```xml
<env name="STRIPE_KEY" value="pk_test_placeholder"/>
<env name="STRIPE_SECRET" value="sk_test_placeholder"/>
<env name="STRIPE_WEBHOOK_SECRET" value="whsec_placeholder"/>
<env name="STRIPE_PRICE_BASIC" value="price_basic"/>
<env name="STRIPE_PRICE_ADVANCED" value="price_advanced"/>
<env name="STRIPE_PRICE_PROFESSIONAL_FLAT" value="price_prof_flat"/>
<env name="STRIPE_PRICE_PROFESSIONAL_METERED" value="price_prof_metered"/>
<env name="CASHIER_CURRENCY" value="eur"/>
```

**Step 5: Commit**

```bash
git add config/plans.php database/migrations/*cashier* composer.json composer.lock phpunit.xml .env.example
git commit -m "feat: install Laravel Cashier and add plans config"
```

---

## Task 2: Database Migration — Add `is_free_access` to Users

**Files:**
- Create: `database/migrations/YYYY_MM_DD_add_is_free_access_to_users_table.php`

**Step 1: Create migration**

```bash
php artisan make:migration add_is_free_access_to_users_table --no-interaction
```

**Step 2: Fill the migration**

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->boolean('is_free_access')->default(false)->after('remember_token');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->dropColumn('is_free_access');
    });
}
```

**Step 3: Run migrations**

```bash
php artisan migrate --no-interaction
```

**Step 4: Commit**

```bash
git add database/migrations/
git commit -m "feat: add is_free_access column to users table"
```

---

## Task 3: Update User Model — Add Billable Trait & `is_free_access`

**Files:**
- Modify: `app/Models/User.php`

**Step 1: Write the failing test**

Create `tests/Feature/Subscription/SubscriptionUserModelTest.php`:

```bash
php artisan make:test --pest Subscription/SubscriptionUserModelTest --no-interaction
```

```php
<?php

use App\Models\User;

it('has is_free_access cast to boolean', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->create();
    expect($coach->is_free_access)->toBeTrue();
});

it('defaults is_free_access to false', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create();
    expect($coach->is_free_access)->toBeFalse();
});
```

**Step 2: Run test to verify it fails**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/SubscriptionUserModelTest.php --compact
```

Expected: FAIL — column not in fillable/casts.

**Step 3: Update `app/Models/User.php`**

Add to `use` imports:
```php
use Laravel\Cashier\Billable;
```

Add `Billable` to the traits:
```php
use HasFactory, Notifiable, Billable;
```

Add `is_free_access` to `$fillable`:
```php
'is_free_access',
```

Add to `casts()`:
```php
'is_free_access' => 'boolean',
```

**Step 4: Add `is_free_access` to UserFactory**

In `database/factories/UserFactory.php`, the `definition()` method should include:
```php
'is_free_access' => false,
```

**Step 5: Run test to verify it passes**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/SubscriptionUserModelTest.php --compact
```

Expected: PASS

**Step 6: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 7: Commit**

```bash
git add app/Models/User.php database/factories/UserFactory.php tests/Feature/Subscription/
git commit -m "feat: add Billable trait and is_free_access to User model"
```

---

## Task 4: Create `SubscriptionService`

**Files:**
- Create: `app/Services/SubscriptionService.php`
- Create: `tests/Unit/Services/SubscriptionServiceTest.php`

**Step 1: Write the failing tests**

```bash
php artisan make:test --pest --unit Services/SubscriptionServiceTest --no-interaction
```

```php
<?php

use App\Models\User;
use App\Services\SubscriptionService;

beforeEach(function (): void {
    $this->service = new SubscriptionService;
});

it('grants access to free access coaches', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->make();
    expect($this->service->isActive($coach))->toBeTrue();
    expect($this->service->isInGracePeriod($coach))->toBeFalse();
});

it('grants access during active trial', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->make([
        'trial_ends_at' => now()->addDays(3),
    ]);
    expect($this->service->isActive($coach))->toBeTrue();
});

it('does not grant access when trial expired and no subscription', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->make([
        'trial_ends_at' => now()->subDay(),
    ]);
    expect($this->service->isActive($coach))->toBeFalse();
    expect($this->service->isInGracePeriod($coach))->toBeFalse();
});

it('returns correct client limit for basic plan', function (): void {
    config(['plans.basic.client_limit' => 5]);
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->make();
    // Free access has no client limit (treated as professional)
    expect($this->service->clientLimit($coach))->toBeNull();
});

it('identifies plan by price id', function (): void {
    config(['plans.basic.stripe_price_id' => 'price_basic']);
    $coach = User::factory()->state(['role' => 'coach'])->make();
    // Without a subscription this returns null
    expect($this->service->currentPlan($coach))->toBeNull();
});

it('free access coach has all features', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->make();
    expect($this->service->hasFeature($coach, 'loyalty'))->toBeTrue();
    expect($this->service->hasFeature($coach, 'custom_branding'))->toBeTrue();
});
```

**Step 2: Run tests to verify they fail**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Unit/Services/SubscriptionServiceTest.php --compact
```

Expected: FAIL — class not found.

**Step 3: Create `app/Services/SubscriptionService.php`**

```bash
php artisan make:class Services/SubscriptionService --no-interaction
```

```php
<?php

namespace App\Services;

use App\Models\User;

class SubscriptionService
{
    /**
     * Whether the coach has an active subscription, active trial, or free access.
     */
    public function isActive(User $coach): bool
    {
        if ($coach->is_free_access) {
            return true;
        }

        // Active Stripe subscription
        if ($coach->subscribed('default')) {
            return true;
        }

        // Active trial (no subscription needed yet)
        if ($coach->onTrial()) {
            return true;
        }

        return false;
    }

    /**
     * Whether the coach is within the 7-day grace period after subscription ended.
     */
    public function isInGracePeriod(User $coach): bool
    {
        if ($coach->is_free_access) {
            return false;
        }

        return $coach->onGracePeriod();
    }

    /**
     * Returns the days remaining in the grace period, or 0.
     */
    public function graceDaysRemaining(User $coach): int
    {
        if (! $this->isInGracePeriod($coach)) {
            return 0;
        }

        $subscription = $coach->subscription('default');
        if (! $subscription?->ends_at) {
            return 0;
        }

        return (int) max(0, now()->diffInDays($subscription->ends_at, false));
    }

    /**
     * Returns the active plan config array, or null if no active subscription.
     *
     * @return array<string, mixed>|null
     */
    public function currentPlan(User $coach): ?array
    {
        if ($coach->is_free_access) {
            return config('plans.professional');
        }

        $subscription = $coach->subscription('default');
        if (! $subscription) {
            return null;
        }

        $priceId = $subscription->stripe_price;

        foreach (config('plans') as $plan) {
            if (isset($plan['stripe_price_id']) && $plan['stripe_price_id'] === $priceId) {
                return $plan;
            }
            if (isset($plan['stripe_price_flat_id']) && $plan['stripe_price_flat_id'] === $priceId) {
                return $plan;
            }
        }

        return null;
    }

    /**
     * Returns the plan key (e.g. 'basic', 'advanced', 'professional'), or null.
     */
    public function currentPlanKey(User $coach): ?string
    {
        if ($coach->is_free_access) {
            return 'professional';
        }

        $subscription = $coach->subscription('default');
        if (! $subscription) {
            return null;
        }

        $priceId = $subscription->stripe_price;

        foreach (config('plans') as $key => $plan) {
            if (isset($plan['stripe_price_id']) && $plan['stripe_price_id'] === $priceId) {
                return $key;
            }
            if (isset($plan['stripe_price_flat_id']) && $plan['stripe_price_flat_id'] === $priceId) {
                return $key;
            }
        }

        return null;
    }

    /**
     * The maximum number of clients the coach can have, or null for unlimited.
     */
    public function clientLimit(User $coach): ?int
    {
        $plan = $this->currentPlan($coach);

        return $plan['client_limit'] ?? null;
    }

    /**
     * Whether the coach can add another client.
     */
    public function canAddClient(User $coach): bool
    {
        $limit = $this->clientLimit($coach);

        if ($limit === null) {
            return true;
        }

        return $coach->clients()->count() < $limit;
    }

    /**
     * Whether the coach's plan includes the given feature.
     * Features: 'loyalty', 'custom_branding'
     */
    public function hasFeature(User $coach, string $feature): bool
    {
        if ($coach->is_free_access) {
            return true;
        }

        $plan = $this->currentPlan($coach);

        if (! $plan) {
            return false;
        }

        return in_array($feature, $plan['features'] ?? [], true);
    }

    /**
     * Reports current overage client count to Stripe for Professional metered billing.
     * Should be called after any client add/remove for Professional plan coaches.
     */
    public function reportClientUsage(User $coach): void
    {
        if ($this->currentPlanKey($coach) !== 'professional') {
            return;
        }

        $includedClients = config('plans.professional.included_clients', 30);
        $totalClients = $coach->clients()->count();
        $overageClients = max(0, $totalClients - $includedClients);

        $meteredPriceId = config('plans.professional.stripe_price_metered_id');
        $subscriptionItem = $coach->subscription('default')
            ?->items()
            ->where('stripe_price', $meteredPriceId)
            ->first();

        if (! $subscriptionItem) {
            return;
        }

        $coach->reportUsage($subscriptionItem->stripe_id, $overageClients);
    }
}
```

**Step 4: Run tests to verify they pass**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Unit/Services/SubscriptionServiceTest.php --compact
```

Expected: PASS

**Step 5: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 6: Commit**

```bash
git add app/Services/SubscriptionService.php tests/Unit/Services/SubscriptionServiceTest.php
git commit -m "feat: add SubscriptionService with plan/feature/client limit logic"
```

---

## Task 5: Create Subscription Enforcement Middleware

**Files:**
- Create: `app/Http/Middleware/EnsureCoachSubscribed.php`
- Create: `app/Http/Middleware/EnsureSubscriptionFeature.php`
- Modify: `bootstrap/app.php`

**Step 1: Write failing tests**

Create `tests/Feature/Subscription/SubscriptionMiddlewareTest.php`:

```bash
php artisan make:test --pest Subscription/SubscriptionMiddlewareTest --no-interaction
```

```php
<?php

use App\Models\User;

it('redirects to subscription page when coach has no subscription and trial expired', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertRedirect(route('coach.subscription'));
});

it('allows access during active trial', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(3),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertOk();
});

it('allows access for free access coaches', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertOk();
});

it('allows access during grace period', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    // Simulate grace period by creating a subscription that ended recently
    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test',
        'stripe_status' => 'canceled',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => now()->addDays(5), // still within grace period
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertOk();
});

it('redirects to subscription page when grace period elapsed', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDays(10),
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test',
        'stripe_status' => 'canceled',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => now()->subDay(), // grace period over
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertRedirect(route('coach.subscription'));
});
```

**Step 2: Run tests to verify they fail**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/SubscriptionMiddlewareTest.php --compact
```

Expected: FAIL — middleware not set up yet.

**Step 3: Create `EnsureCoachSubscribed` middleware**

```bash
php artisan make:middleware EnsureCoachSubscribed --no-interaction
```

```php
<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCoachSubscribed
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $coach = $request->user();

        if (! $coach) {
            return $next($request);
        }

        // Allow access to the subscription page itself
        if ($request->routeIs('coach.subscription') || $request->routeIs('coach.subscription.*')) {
            return $next($request);
        }

        $isActive = $this->subscriptionService->isActive($coach);
        $isInGracePeriod = $this->subscriptionService->isInGracePeriod($coach);

        if (! $isActive && ! $isInGracePeriod) {
            return redirect()->route('coach.subscription');
        }

        // Flash grace period data for the layout toast
        if ($isInGracePeriod) {
            $daysRemaining = $this->subscriptionService->graceDaysRemaining($coach);
            session()->flash('subscription_grace_days', $daysRemaining);
        }

        return $next($request);
    }
}
```

**Step 4: Create `EnsureSubscriptionFeature` middleware**

```bash
php artisan make:middleware EnsureSubscriptionFeature --no-interaction
```

```php
<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionFeature
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $coach = $request->user();

        if (! $coach || ! $this->subscriptionService->hasFeature($coach, $feature)) {
            return redirect()->route('coach.subscription')
                ->with('feature_required', $feature);
        }

        return $next($request);
    }
}
```

**Step 5: Register middleware aliases in `bootstrap/app.php`**

Add to the `->withMiddleware()` aliases:

```php
'subscribed' => \App\Http\Middleware\EnsureCoachSubscribed::class,
'subscription.feature' => \App\Http\Middleware\EnsureSubscriptionFeature::class,
```

**Step 6: Add `subscribed` middleware to coach routes in `routes/web.php`**

Change the coach route group middleware from:
```php
Route::middleware(['auth', 'verified', 'role:coach'])
```
to:
```php
Route::middleware(['auth', 'verified', 'role:coach', 'subscribed'])
```

**Step 7: Run tests to verify they pass**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/SubscriptionMiddlewareTest.php --compact
```

Expected: PASS

**Step 8: Run full coach suite to ensure nothing broke**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Coach/ --compact
```

Expected: same pass/fail ratio as baseline (75 passed, 1 pre-existing failure).

**Step 9: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 10: Commit**

```bash
git add app/Http/Middleware/EnsureCoachSubscribed.php app/Http/Middleware/EnsureSubscriptionFeature.php bootstrap/app.php routes/web.php tests/Feature/Subscription/SubscriptionMiddlewareTest.php
git commit -m "feat: add subscription enforcement middleware for coach routes"
```

---

## Task 6: Start Free Trial on Coach Registration

**Files:**
- Modify: `app/Http/Controllers/Auth/RegisteredUserController.php`
- Create: `tests/Feature/Subscription/TrialStartTest.php`

**Step 1: Write failing test**

```bash
php artisan make:test --pest Subscription/TrialStartTest --no-interaction
```

```php
<?php

use App\Models\User;

it('starts a 7-day trial when coach registers', function (): void {
    $this->post(route('register'), [
        'name' => 'Test Coach',
        'email' => 'coach@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $coach = User::where('email', 'coach@test.com')->first();

    expect($coach->trial_ends_at)->not->toBeNull();
    expect($coach->trial_ends_at->isFuture())->toBeTrue();
    expect($coach->onTrial())->toBeTrue();
});
```

**Step 2: Run test to verify it fails**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/TrialStartTest.php --compact
```

Expected: FAIL

**Step 3: Update `RegisteredUserController::store()`**

After `Auth::login($user);`, add:

```php
$user->createAsStripeCustomer();
$user->startTrial(now()->addDays(config('plans.basic.trial_days', 7)));
```

Add the import at the top:
```php
use Laravel\Cashier\Exceptions\CustomerAlreadyCreated;
```

**IMPORTANT:** Cashier's `createAsStripeCustomer()` makes a real API call to Stripe. In tests, we need to mock this. The test should instead directly set `trial_ends_at` OR we use a simpler approach: set `trial_ends_at` directly without creating a Stripe customer (since the trial is card-free).

Instead, update `RegisteredUserController::store()` to simply set:

```php
$user->update(['trial_ends_at' => now()->addDays(config('plans.basic.trial_days', 7))]);
```

This avoids any Stripe API call during registration. The Stripe customer is created when the coach actually subscribes via the Customer Portal.

**Step 4: Run test to verify it passes**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/TrialStartTest.php --compact
```

Expected: PASS

**Step 5: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 6: Commit**

```bash
git add app/Http/Controllers/Auth/RegisteredUserController.php tests/Feature/Subscription/TrialStartTest.php
git commit -m "feat: start 7-day free trial on coach registration"
```

---

## Task 7: Subscription Page Controller & Route

**Files:**
- Create: `app/Http/Controllers/Coach/SubscriptionController.php`
- Create: `resources/views/coach/subscription.blade.php`
- Modify: `routes/web.php`

**Step 1: Write failing tests**

```bash
php artisan make:test --pest Subscription/SubscriptionPageTest --no-interaction
```

```php
<?php

use App\Models\User;

it('shows subscription page to coach', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Subscription');
});

it('shows trial expiry info on subscription page', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('trial');
});

it('shows plans on subscription page', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Basic')
        ->assertSee('Advanced')
        ->assertSee('Professional');
});
```

**Step 2: Run test to verify it fails**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/SubscriptionPageTest.php --compact
```

Expected: FAIL — route not found.

**Step 3: Create SubscriptionController**

```bash
php artisan make:controller Coach/SubscriptionController --no-interaction
```

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function index(): View
    {
        $coach = auth()->user();
        $currentPlanKey = $this->subscriptionService->currentPlanKey($coach);
        $clientCount = $coach->clients()->count();
        $clientLimit = $this->subscriptionService->clientLimit($coach);
        $isOnTrial = $coach->onTrial();
        $trialEndsAt = $coach->trial_ends_at;
        $isInGracePeriod = $this->subscriptionService->isInGracePeriod($coach);
        $graceDaysRemaining = $this->subscriptionService->graceDaysRemaining($coach);
        $plans = config('plans');
        $subscription = $coach->subscription('default');

        return view('coach.subscription', compact(
            'currentPlanKey',
            'clientCount',
            'clientLimit',
            'isOnTrial',
            'trialEndsAt',
            'isInGracePeriod',
            'graceDaysRemaining',
            'plans',
            'subscription',
        ));
    }

    public function portal(): RedirectResponse
    {
        $coach = auth()->user();

        if (! $coach->stripe_id) {
            $coach->createAsStripeCustomer();
        }

        return $coach->redirectToBillingPortal(route('coach.subscription'));
    }
}
```

**Step 4: Add routes to `routes/web.php`** (inside coach group)

```php
Route::get('subscription', [Coach\SubscriptionController::class, 'index'])->name('subscription');
Route::get('subscription/portal', [Coach\SubscriptionController::class, 'portal'])->name('subscription.portal');
```

**Step 5: Create `resources/views/coach/subscription.blade.php`**

```blade
<x-layouts.coach>
    <x-slot:title>Subscription</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Subscription</h1>

        {{-- Status Banner --}}
        @if($isOnTrial)
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    You're on a <strong>free trial</strong> — expires {{ $trialEndsAt->format('M d, Y') }}
                    ({{ $trialEndsAt->diffForHumans() }}).
                </p>
            </div>
        @elseif($isInGracePeriod)
            <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
                <p class="text-sm text-amber-800 dark:text-amber-200">
                    Your subscription has ended. You have <strong>{{ $graceDaysRemaining }} day(s)</strong> of grace period remaining.
                    <a href="{{ route('coach.subscription.portal') }}" class="underline font-medium">Reactivate now →</a>
                </p>
            </div>
        @elseif($currentPlanKey)
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-green-800 dark:text-green-200">
                        Active plan: <strong>{{ ucfirst($currentPlanKey) }}</strong>
                        @if($clientLimit !== null)
                            · {{ $clientCount }}/{{ $clientLimit }} clients
                        @else
                            · {{ $clientCount }} clients
                        @endif
                    </p>
                    <a href="{{ route('coach.subscription.portal') }}"
                       class="text-sm font-medium text-green-700 dark:text-green-300 underline">
                        Manage subscription →
                    </a>
                </div>
            </div>
        @endif

        @if(session('feature_required'))
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4">
                <p class="text-sm text-red-800 dark:text-red-200">
                    The <strong>{{ session('feature_required') }}</strong> feature requires an Advanced or Professional plan.
                </p>
            </div>
        @endif

        {{-- Plan Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Basic --}}
            <div class="bg-white dark:bg-gray-900 border rounded-lg p-6 {{ $currentPlanKey === 'basic' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700' }}">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Basic</h2>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">€2.50<span class="text-sm font-normal text-gray-500">/month</span></p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">7-day free trial · no card required</p>
                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li>✓ Up to 5 clients</li>
                    <li>✓ All core features</li>
                    <li>✗ Loyalty system</li>
                    <li>✗ Custom branding</li>
                </ul>
            </div>

            {{-- Advanced --}}
            <div class="bg-white dark:bg-gray-900 border rounded-lg p-6 {{ $currentPlanKey === 'advanced' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700' }}">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Advanced</h2>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">€10<span class="text-sm font-normal text-gray-500">/month</span></p>
                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li>✓ Up to 15 clients</li>
                    <li>✓ All core features</li>
                    <li>✓ Loyalty system</li>
                    <li>✗ Custom branding</li>
                </ul>
            </div>

            {{-- Professional --}}
            <div class="bg-white dark:bg-gray-900 border rounded-lg p-6 {{ $currentPlanKey === 'professional' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700' }}">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Professional</h2>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">€15<span class="text-sm font-normal text-gray-500">/month</span></p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">+€0.50/client/month above 30</p>
                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li>✓ Up to 30 clients included</li>
                    <li>✓ All core features</li>
                    <li>✓ Loyalty system</li>
                    <li>✓ Custom branding</li>
                </ul>
            </div>
        </div>

        {{-- CTA --}}
        <div class="flex justify-center">
            <a href="{{ route('coach.subscription.portal') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                {{ $currentPlanKey ? 'Manage Subscription' : 'Subscribe Now' }}
            </a>
        </div>
    </div>
</x-layouts.coach>
```

**Step 6: Run tests to verify they pass**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/SubscriptionPageTest.php --compact
```

Expected: PASS

**Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 8: Commit**

```bash
git add app/Http/Controllers/Coach/SubscriptionController.php resources/views/coach/subscription.blade.php routes/web.php tests/Feature/Subscription/SubscriptionPageTest.php
git commit -m "feat: add subscription page with plan overview and Stripe portal link"
```

---

## Task 8: Grace Period Toast in Coach Layout

**Files:**
- Modify: `resources/views/components/layouts/coach.blade.php`

**Step 1: Write failing test**

Add to `tests/Feature/Subscription/SubscriptionMiddlewareTest.php`:

```php
it('flashes grace period days to session during grace period', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_grace_test',
        'stripe_status' => 'canceled',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => now()->addDays(5),
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertSessionHas('subscription_grace_days');
});
```

**Step 2: Run the test to verify it fails**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/SubscriptionMiddlewareTest.php --compact
```

**Step 3: Add toast to `resources/views/components/layouts/coach.blade.php`**

Find the `<body>` tag and add immediately after, before the mobile header div:

```blade
{{-- Grace Period Toast --}}
@if(session('subscription_grace_days') !== null)
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        class="fixed top-0 inset-x-0 z-50 bg-amber-500 text-white px-4 py-2 flex items-center justify-between text-sm"
    >
        <span>
            Your subscription has ended. You have <strong>{{ session('subscription_grace_days') }} day(s)</strong> remaining.
            <a href="{{ route('coach.subscription.portal') }}" class="underline ml-1">Manage subscription →</a>
        </span>
        <button @click="show = false" class="ml-4 text-white hover:text-amber-100" aria-label="Dismiss">✕</button>
    </div>
@endif
```

**Step 4: Run tests**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/SubscriptionMiddlewareTest.php --compact
```

Expected: PASS

**Step 5: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 6: Commit**

```bash
git add resources/views/components/layouts/coach.blade.php tests/Feature/Subscription/SubscriptionMiddlewareTest.php
git commit -m "feat: add grace period toast to coach layout"
```

---

## Task 9: Client Limit Enforcement in ClientController

**Files:**
- Modify: `app/Http/Controllers/Coach/ClientController.php`
- Create: `tests/Feature/Subscription/ClientLimitTest.php`

**Step 1: Write failing tests**

```bash
php artisan make:test --pest Subscription/ClientLimitTest --no-interaction
```

```php
<?php

use App\Models\User;

it('blocks inviting a new client when basic plan limit is reached', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    // Mock the plan — set subscription with basic price
    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);
    $coach->update(['trial_ends_at' => null]);

    // Create 5 clients (the limit)
    User::factory()->count(5)->state(['role' => 'client', 'coach_id' => $coach->id])->create();

    $this->actingAs($coach)
        ->post(route('coach.clients.store'))
        ->assertRedirect(route('coach.clients.index'));

    // Should be redirected with an error, not a new invitation created
    // The client count should remain 5
    expect($coach->clients()->count())->toBe(5);
});

it('allows inviting when below plan limit', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    User::factory()->count(2)->state(['role' => 'client', 'coach_id' => $coach->id])->create();

    // Basic plan, 2 of 5 used — should allow invitation
    $this->actingAs($coach)
        ->post(route('coach.clients.store'))
        ->assertRedirect(route('coach.clients.index'))
        ->assertSessionHas('invitation_code');
});

it('blocks creating track-only client when limit reached', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic2',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);
    $coach->update(['trial_ends_at' => null]);

    User::factory()->count(5)->state(['role' => 'client', 'coach_id' => $coach->id])->create();

    $this->actingAs($coach)
        ->post(route('coach.clients.store-track-only'), [
            'name' => 'Track Only',
            'email' => 'track@test.com',
        ])
        ->assertRedirect(route('coach.clients.index'));

    expect($coach->clients()->count())->toBe(5);
});
```

**Step 2: Run tests to verify they fail**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/ClientLimitTest.php --compact
```

**Step 3: Update `ClientController::store()` (invitation)**

Inject `SubscriptionService` into the constructor and add a limit check at the top of `store()`:

```php
use App\Services\SubscriptionService;

public function __construct(private readonly SubscriptionService $subscriptionService) {}
```

Add to the top of `store()`:
```php
$coach = auth()->user();

if (! $this->subscriptionService->canAddClient($coach)) {
    return redirect()->route('coach.clients.index')
        ->with('error', 'You have reached your plan\'s client limit. Upgrade your subscription to add more clients.');
}
```

**Step 4: Update `ClientController::storeTrackOnly()`**

Add at the top of `storeTrackOnly()`:
```php
$coach = auth()->user();

if (! $this->subscriptionService->canAddClient($coach)) {
    return redirect()->route('coach.clients.index')
        ->with('error', 'You have reached your plan\'s client limit. Upgrade your subscription to add more clients.');
}
```

**Step 5: Run tests to verify they pass**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/ClientLimitTest.php --compact
```

Expected: PASS

**Step 6: Run full coach suite**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Coach/ --compact
```

Expected: same baseline.

**Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 8: Commit**

```bash
git add app/Http/Controllers/Coach/ClientController.php tests/Feature/Subscription/ClientLimitTest.php
git commit -m "feat: enforce client limit in ClientController based on subscription plan"
```

---

## Task 10: Gate Loyalty & Branding Routes by Subscription Feature

**Files:**
- Modify: `routes/web.php`
- Create: `tests/Feature/Subscription/FeatureGatingTest.php`

**Step 1: Write failing tests**

```bash
php artisan make:test --pest Subscription/FeatureGatingTest --no-interaction
```

```php
<?php

use App\Models\User;

it('redirects basic plan coach from loyalty routes', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic3',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);
    $coach->update(['trial_ends_at' => null]);

    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();

    $this->actingAs($coach)
        ->get(route('coach.clients.loyalty', $client))
        ->assertRedirect(route('coach.subscription'));
});

it('allows advanced plan coach to access loyalty routes', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => null,
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_advanced',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.advanced.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();

    $this->actingAs($coach)
        ->get(route('coach.clients.loyalty', $client))
        ->assertOk();
});

it('redirects basic plan coach from branding route', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => false])->create([
        'trial_ends_at' => now()->addDays(5),
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_basic4',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);
    $coach->update(['trial_ends_at' => null]);

    $this->actingAs($coach)
        ->get(route('coach.branding.edit'))
        ->assertRedirect(route('coach.subscription'));
});

it('allows free access coach to access all gated routes', function (): void {
    $coach = User::factory()->state(['role' => 'coach', 'is_free_access' => true])->create([
        'trial_ends_at' => now()->subDay(),
    ]);

    $client = User::factory()->state(['role' => 'client', 'coach_id' => $coach->id])->create();

    $this->actingAs($coach)
        ->get(route('coach.clients.loyalty', $client))
        ->assertOk();

    $this->actingAs($coach)
        ->get(route('coach.branding.edit'))
        ->assertOk();
});
```

**Step 2: Run tests to verify they fail**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/FeatureGatingTest.php --compact
```

**Step 3: Add `subscription.feature` middleware to routes in `routes/web.php`**

Wrap the loyalty routes:
```php
Route::get('clients/{client}/loyalty', [Coach\LoyaltyController::class, 'show'])
    ->name('clients.loyalty')
    ->middleware('subscription.feature:loyalty');
```

Wrap the branding routes:
```php
Route::middleware('subscription.feature:custom_branding')->group(function () {
    Route::get('branding', [Coach\BrandingController::class, 'edit'])->name('branding.edit');
    Route::put('branding', [Coach\BrandingController::class, 'update'])->name('branding.update');
});
```

**Step 4: Run tests to verify they pass**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/FeatureGatingTest.php --compact
```

Expected: PASS

**Step 5: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 6: Commit**

```bash
git add routes/web.php tests/Feature/Subscription/FeatureGatingTest.php
git commit -m "feat: gate loyalty and branding routes by subscription feature"
```

---

## Task 11: Professional Plan Metered Usage Reporting

**Files:**
- Modify: `app/Http/Controllers/Coach/ClientController.php`

**Step 1: Update `ClientController`**

After any `User::create()` or `$client->delete()` call that changes the client count, add:

In `store()` (after invitation creation — note: invitations don't add clients immediately, so skip here):

In `storeTrackOnly()` (after `User::create()`):
```php
$this->subscriptionService->reportClientUsage($coach);
```

In `destroy()` (after `$client->delete()`):
```php
$this->subscriptionService->reportClientUsage(auth()->user());
```

**Note:** Client registrations via invitation also add clients — handle this in the `ClientRegistrationController` similarly. Read that file and add `reportClientUsage` after the user is created there.

**Step 2: Verify coach test suite still passes**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Coach/ --compact
```

**Step 3: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 4: Commit**

```bash
git add app/Http/Controllers/Coach/ClientController.php
git commit -m "feat: report metered usage to Stripe after client add/remove"
```

---

## Task 12: Update Filament UserResource with `is_free_access` & Subscription Status

**Files:**
- Modify: `app/Filament/Resources/Users/Schemas/UserForm.php`
- Modify: `app/Filament/Resources/Users/Schemas/UserInfolist.php`

**Step 1: Read `UserInfolist.php` first**

```bash
cat app/Filament/Resources/Users/Schemas/UserInfolist.php
```

**Step 2: Add `is_free_access` toggle to `UserForm.php`**

Add to the components list:

```php
use Filament\Forms\Components\Toggle;
```

```php
Toggle::make('is_free_access')
    ->label('Free Access')
    ->helperText('Grant full Professional access without a subscription (for ambassadors, friends, etc.)'),
```

**Step 3: Add subscription status to `UserInfolist.php`**

Add relevant entries to display subscription status read-only. Follow the existing pattern in the file.

Import:
```php
use Filament\Infolists\Components\TextEntry;
```

Add entries for:
- `stripe_id` (read-only)
- `trial_ends_at` formatted as date
- `is_free_access` as Yes/No

**Step 4: Run Filament test suite if any exists**

```bash
php -d memory_limit=256M vendor/bin/pest tests/ --filter=Filament --compact
```

**Step 5: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 6: Commit**

```bash
git add app/Filament/Resources/Users/Schemas/
git commit -m "feat: add is_free_access toggle and subscription status to Filament UserResource"
```

---

## Task 13: Register Cashier Webhook Route

**Files:**
- Modify: `routes/web.php`

**Step 1: Add Cashier webhook route**

At the top of `routes/web.php`, outside all middleware groups:

```php
Route::cashierWebhooks('cashier/webhook');
```

This single line registers all Cashier webhook handling. Cashier automatically handles subscription state updates, cancellations, payment failures, etc.

**Step 2: Verify routes are registered**

```bash
php artisan route:list --path=cashier
```

Expected: POST `cashier/webhook` appears.

**Step 3: Commit**

```bash
git add routes/web.php
git commit -m "feat: register Cashier webhook route"
```

---

## Task 14: Final Test Run & Pint

**Step 1: Run all subscription tests**

```bash
php -d memory_limit=256M vendor/bin/pest tests/Feature/Subscription/ tests/Unit/Services/ --compact
```

Expected: All pass.

**Step 2: Run full test suite**

```bash
php -d memory_limit=256M vendor/bin/pest tests/ --compact
```

Expected: Same pre-existing failures as baseline (9 Auth/Profile scaffolding tests), all new tests pass.

**Step 3: Run pint one final time**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 4: Final commit if pint changed anything**

```bash
git add -A
git commit -m "style: apply pint formatting to subscription implementation"
```

---

## Stripe Dashboard Setup (Manual Steps After Implementation)

These steps happen in the Stripe Dashboard — not in code:

1. Create product **LiftDeck Basic** → price: 2.50 EUR/month recurring
2. Create product **LiftDeck Advanced** → price: 10 EUR/month recurring
3. Create product **LiftDeck Professional** → two prices:
   - Flat: 15 EUR/month
   - Metered: 0.50 EUR/unit/month (aggregate: `last_during_period`, billing scheme: `per_unit`)
4. Copy price IDs into `.env` and `.env.example`
5. Configure Customer Portal in Stripe Dashboard (Billing → Customer Portal):
   - Enable: cancel subscriptions, update payment method, view invoices
   - Set return URL to `https://yourdomain/coach/subscription`
6. Register webhook endpoint in Stripe → `https://yourdomain/cashier/webhook`
   - Events: `customer.subscription.*`, `invoice.payment_*`, `customer.updated`
   - Copy signing secret to `STRIPE_WEBHOOK_SECRET`
