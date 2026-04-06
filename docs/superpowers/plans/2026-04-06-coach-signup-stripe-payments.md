# Coach Sign-Up with Stripe Payments — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a post-registration plan selection step so coaches pick a plan before accessing the dashboard — Basic starts a 7-day free trial, Advanced/Professional go straight to Stripe Checkout.

**Architecture:** After registration, coaches land on a new `/coach/plan` page to pick their plan. Basic sets `trial_ends_at` locally and grants immediate access. Advanced/Professional set `selected_plan` and redirect to Stripe Checkout (via `SubscriptionController::checkout`). The `EnsureCoachSubscribed` middleware is updated to route new coaches (no `selected_plan`) to the plan page rather than the subscription page.

**Tech Stack:** Laravel 12, Laravel Cashier v16 (Stripe), Blade, Tailwind CSS v3, Pest v4

---

## File Map

| File | Action | Purpose |
|------|--------|---------|
| `database/migrations/XXXX_add_selected_plan_to_users_table.php` | Create | Adds `selected_plan` nullable string column |
| `app/Models/User.php` | Modify | Add `selected_plan` to `$fillable` |
| `database/factories/UserFactory.php` | Modify | Default `selected_plan = 'basic'` so factory coaches bypass plan step |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Modify | Remove trial assignment, redirect to `coach.plan` |
| `app/Http/Middleware/EnsureCoachSubscribed.php` | Modify | Route no-plan coaches to `coach.plan`; allow plan + checkout routes |
| `app/Http/Controllers/Coach/PlanSelectionController.php` | Create | `show`, `store`, `success` actions |
| `app/Http/Requests/StorePlanSelectionRequest.php` | Create | Validates `plan` field |
| `resources/views/coach/plan.blade.php` | Create | Plan picker UI |
| `app/Http/Controllers/Coach/SubscriptionController.php` | Modify | Add `checkout` action |
| `resources/views/coach/subscription.blade.php` | Modify | Smart CTA (subscribe/manage/choose plan) |
| `routes/web.php` | Modify | Add plan + checkout routes; exclude from `subscribed` middleware |
| `tests/Feature/Coach/PlanSelectionTest.php` | Create | Plan selection feature tests |
| `tests/Feature/Auth/RegistrationTest.php` | Modify | Update redirect assertion |
| `tests/Feature/Subscription/TrialStartTest.php` | Modify | Trial now starts on plan selection, not registration |
| `tests/Feature/Subscription/SubscriptionMiddlewareTest.php` | Modify | Add tests for new `selected_plan` routing |
| `tests/Feature/Subscription/SubscriptionPageTest.php` | Modify | Add tests for new CTA logic |
| `README.md` | Modify | Document the coach signup + payments flow |

---

## Task 1: Migration, model, and factory

**Files:**
- Create: `database/migrations/XXXX_add_selected_plan_to_users_table.php`
- Modify: `app/Models/User.php`
- Modify: `database/factories/UserFactory.php`

- [ ] **Step 1: Generate migration**

```bash
php artisan make:migration add_selected_plan_to_users_table --no-interaction
```

- [ ] **Step 2: Write the migration**

Open the generated file and replace its contents with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('selected_plan')->nullable()->after('is_free_access');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('selected_plan');
        });
    }
};
```

- [ ] **Step 3: Add `selected_plan` to `User::$fillable`**

In `app/Models/User.php`, add `'selected_plan'` to the `$fillable` array (after `'is_free_access'`):

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'coach_id',
    'phone',
    'bio',
    'description',
    'welcome_email_text',
    'onboarding_welcome_text',
    'avatar',
    'gym_name',
    'logo',
    'primary_color',
    'secondary_color',
    'dark_mode',
    'is_track_only',
    'is_free_access',
    'selected_plan',
    'trial_ends_at',
];
```

- [ ] **Step 4: Update `UserFactory` to default `selected_plan = 'basic'`**

In `database/factories/UserFactory.php`, add `'selected_plan' => 'basic'` to `definition()`:

```php
public function definition(): array
{
    return [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'email_verified_at' => now(),
        'password' => static::$password ??= Hash::make('password'),
        'remember_token' => Str::random(10),
        'role' => 'coach',
        'dark_mode' => false,
        'is_free_access' => false,
        'selected_plan' => 'basic',
        'trial_ends_at' => now()->addDays(14),
    ];
}
```

- [ ] **Step 5: Run migration**

```bash
php artisan migrate --no-interaction
```

Expected: `Running migrations` with the new migration listed.

- [ ] **Step 6: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 7: Commit**

```bash
git add database/migrations app/Models/User.php database/factories/UserFactory.php
git commit -m "feat: add selected_plan column to users table"
```

---

## Task 2: Update registration controller

**Files:**
- Modify: `app/Http/Controllers/Auth/RegisteredUserController.php`
- Modify: `tests/Feature/Auth/RegistrationTest.php`
- Modify: `tests/Feature/Subscription/TrialStartTest.php`

- [ ] **Step 1: Write the failing tests**

Replace `tests/Feature/Auth/RegistrationTest.php` with:

```php
<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new coach is redirected to plan selection after registration', function () {
    $response = $this->post('/register', [
        'name' => 'Test Coach',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('coach.plan'));
});

test('new coach has no trial_ends_at immediately after registration', function () {
    $this->post('/register', [
        'name' => 'Test Coach',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $coach = \App\Models\User::where('email', 'test@example.com')->first();

    expect($coach->trial_ends_at)->toBeNull();
});
```

Replace `tests/Feature/Subscription/TrialStartTest.php` with:

```php
<?php

use App\Models\User;

it('starts a 7-day trial when coach selects basic plan', function (): void {
    $coach = User::factory()->state([
        'role' => 'coach',
        'trial_ends_at' => null,
        'selected_plan' => null,
    ])->create();

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'basic'])
        ->assertRedirect(route('coach.dashboard'));

    $coach->refresh();

    expect($coach->trial_ends_at)->not->toBeNull();
    expect($coach->trial_ends_at->isFuture())->toBeTrue();
    expect($coach->onTrial())->toBeTrue();
});

it('trial lasts 7 days from plan selection', function (): void {
    $coach = User::factory()->state([
        'role' => 'coach',
        'trial_ends_at' => null,
        'selected_plan' => null,
    ])->create();

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'basic']);

    $coach->refresh();

    expect(now()->diffInDays($coach->trial_ends_at))->toBeBetween(6, 8);
});
```

- [ ] **Step 2: Run tests to see them fail**

```bash
php artisan test --compact --filter="RegistrationTest|TrialStartTest"
```

Expected: FAIL — registration redirects to dashboard, not `coach.plan`; `coach.plan.store` route doesn't exist.

- [ ] **Step 3: Update `RegisteredUserController::store`**

In `app/Http/Controllers/Auth/RegisteredUserController.php`, remove the trial assignment and change the redirect:

```php
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'coach',
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect(route('coach.plan', absolute: false));
}
```

- [ ] **Step 4: Add plan routes (so tests can resolve them — full routing is in Task 4)**

Skip this step for now; routes are added in Task 4. The `TrialStartTest` tests will pass once routes and controller exist.

- [ ] **Step 5: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Auth/RegisteredUserController.php tests/Feature/Auth/RegistrationTest.php tests/Feature/Subscription/TrialStartTest.php
git commit -m "feat: redirect coach to plan selection after registration, remove trial from registration"
```

---

## Task 3: Update `EnsureCoachSubscribed` middleware

**Files:**
- Modify: `app/Http/Middleware/EnsureCoachSubscribed.php`
- Modify: `tests/Feature/Subscription/SubscriptionMiddlewareTest.php`

- [ ] **Step 1: Write the failing tests**

Add these tests to `tests/Feature/Subscription/SubscriptionMiddlewareTest.php`:

```php
it('redirects coach with no selected_plan to plan selection page', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertRedirect(route('coach.plan'));
});

it('redirects coach with selected_plan but expired trial to subscription page', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
        'selected_plan' => 'basic',
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertRedirect(route('coach.subscription'));
});

it('allows coach to access plan selection page with no selected_plan', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.plan'))
        ->assertOk();
});
```

- [ ] **Step 2: Run tests to see them fail**

```bash
php artisan test --compact --filter="SubscriptionMiddlewareTest"
```

Expected: FAIL — `coach.plan` route doesn't exist yet, and middleware doesn't check `selected_plan`.

- [ ] **Step 3: Update `EnsureCoachSubscribed` middleware**

Replace `app/Http/Middleware/EnsureCoachSubscribed.php` with:

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

        // Allow plan selection and checkout routes through unconditionally
        if ($request->routeIs('coach.plan', 'coach.plan.*', 'coach.subscription', 'coach.subscription.*')) {
            return $next($request);
        }

        $isActive = $this->subscriptionService->isActive($coach);
        $isInGracePeriod = $this->subscriptionService->isInGracePeriod($coach);

        if (! $isActive && ! $isInGracePeriod) {
            // Coach has never chosen a plan — send to plan selection
            if (! $coach->selected_plan) {
                return redirect()->route('coach.plan');
            }

            // Coach chose a plan but hasn't paid / trial expired — send to subscription page
            return redirect()->route('coach.subscription');
        }

        if ($isInGracePeriod) {
            $daysRemaining = $this->subscriptionService->graceDaysRemaining($coach);
            session()->flash('subscription_grace_days', $daysRemaining);
        }

        return $next($request);
    }
}
```

- [ ] **Step 4: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Middleware/EnsureCoachSubscribed.php tests/Feature/Subscription/SubscriptionMiddlewareTest.php
git commit -m "feat: route coaches without a selected plan to plan selection page"
```

---

## Task 4: `PlanSelectionController`, form request, routes, and view

**Files:**
- Create: `app/Http/Controllers/Coach/PlanSelectionController.php`
- Create: `app/Http/Requests/StorePlanSelectionRequest.php`
- Create: `resources/views/coach/plan.blade.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/Coach/PlanSelectionTest.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/Coach/PlanSelectionTest.php`:

```php
<?php

use App\Models\User;

it('renders the plan selection page for a new coach', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.plan'))
        ->assertOk()
        ->assertSee('Basic')
        ->assertSee('Advanced')
        ->assertSee('Professional')
        ->assertSee('Free Trial');
});

it('redirects to dashboard if coach already has active trial', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->addDays(5),
        'selected_plan' => 'basic',
    ]);

    $this->actingAs($coach)
        ->get(route('coach.plan'))
        ->assertRedirect(route('coach.dashboard'));
});

it('selecting basic plan sets selected_plan and trial_ends_at and redirects to dashboard', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'basic'])
        ->assertRedirect(route('coach.dashboard'));

    $coach->refresh();
    expect($coach->selected_plan)->toBe('basic');
    expect($coach->trial_ends_at)->not->toBeNull();
    expect($coach->trial_ends_at->isFuture())->toBeTrue();
});

it('selecting advanced plan sets selected_plan and redirects to checkout', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'advanced'])
        ->assertRedirect(route('coach.subscription.checkout'));

    $coach->refresh();
    expect($coach->selected_plan)->toBe('advanced');
    expect($coach->trial_ends_at)->toBeNull();
});

it('selecting professional plan sets selected_plan and redirects to checkout', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'professional'])
        ->assertRedirect(route('coach.subscription.checkout'));

    $coach->refresh();
    expect($coach->selected_plan)->toBe('professional');
});

it('rejects invalid plan values', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'enterprise'])
        ->assertSessionHasErrors('plan');
});

it('success page redirects to dashboard', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => 'advanced',
    ]);

    // Simulate an active subscription (Cashier webhook would have created this)
    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test_success',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.advanced.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.plan.success'))
        ->assertRedirect(route('coach.dashboard'));
});
```

- [ ] **Step 2: Run tests to see them fail**

```bash
php artisan test --compact --filter="PlanSelectionTest"
```

Expected: FAIL — route `coach.plan` doesn't exist.

- [ ] **Step 3: Generate form request**

```bash
php artisan make:request StorePlanSelectionRequest --no-interaction
```

- [ ] **Step 4: Write `StorePlanSelectionRequest`**

Replace `app/Http/Requests/StorePlanSelectionRequest.php` with:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanSelectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'plan' => ['required', 'string', 'in:basic,advanced,professional'],
        ];
    }
}
```

- [ ] **Step 5: Generate controller**

```bash
php artisan make:controller Coach/PlanSelectionController --no-interaction
```

- [ ] **Step 6: Write `PlanSelectionController`**

Replace `app/Http/Controllers/Coach/PlanSelectionController.php` with:

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanSelectionRequest;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanSelectionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function show(): View|RedirectResponse
    {
        $coach = auth()->user();

        if ($this->subscriptionService->isActive($coach)) {
            return redirect()->route('coach.dashboard');
        }

        return view('coach.plan', [
            'plans' => config('plans'),
        ]);
    }

    public function store(StorePlanSelectionRequest $request): RedirectResponse
    {
        $coach = auth()->user();
        $plan = $request->validated()['plan'];

        $coach->update(['selected_plan' => $plan]);

        if ($plan === 'basic') {
            $coach->update(['trial_ends_at' => now()->addDays(config('plans.basic.trial_days', 7))]);

            return redirect()->route('coach.dashboard');
        }

        return redirect()->route('coach.subscription.checkout');
    }

    public function success(): RedirectResponse
    {
        return redirect()->route('coach.dashboard');
    }
}
```

- [ ] **Step 7: Add routes to `routes/web.php`**

In `routes/web.php`, add a new route group **before** the `subscribed` coach group (add it after the `cashier.webhook` route and before the `coach.` group):

```php
// Coach plan selection — auth + role required, but NOT subscribed middleware
Route::middleware(['auth', 'verified', 'role:coach'])
    ->prefix('coach')
    ->name('coach.')
    ->group(function () {
        Route::get('plan', [\App\Http\Controllers\Coach\PlanSelectionController::class, 'show'])->name('plan');
        Route::post('plan', [\App\Http\Controllers\Coach\PlanSelectionController::class, 'store'])->name('plan.store');
        Route::get('plan/success', [\App\Http\Controllers\Coach\PlanSelectionController::class, 'success'])->name('plan.success');
        Route::get('subscription/checkout', [\App\Http\Controllers\Coach\SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    });
```

- [ ] **Step 8: Create the plan selection view**

Create `resources/views/coach/plan.blade.php`:

```blade
<x-layouts.coach>
    <x-slot:title>Choose Your Plan</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Choose Your Plan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select the plan that best fits your coaching needs. You can upgrade at any time.</p>
        </div>

        <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>Basic plan includes a 7-day free trial</strong> — no credit card required. Advanced and Professional plans start immediately with payment.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Basic Plan -->
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Basic</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For coaches just getting started.</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€2.50</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">/mo</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Up to 5 clients
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Programs &amp; workout logs
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Nutrition tracking
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="basic">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Start Free Trial
                        </button>
                    </form>
                    <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">No credit card required</p>
                </div>
            </div>

            <!-- Advanced Plan -->
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Advanced</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For growing coaches.</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€10</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">/mo</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Up to 15 clients
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Everything in Basic
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Loyalty &amp; achievements
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="advanced">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Subscribe Now
                        </button>
                    </form>
                </div>
            </div>

            <!-- Professional Plan -->
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Professional</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For professional coaches at scale.</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€15</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">/mo + metered</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        30 clients included
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Everything in Advanced
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Custom branding
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="professional">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Subscribe Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
```

- [ ] **Step 9: Run tests**

```bash
php artisan test --compact --filter="PlanSelectionTest|TrialStartTest|SubscriptionMiddlewareTest"
```

Expected: All pass except middleware tests that reference `coach.plan` route (which now exists).

- [ ] **Step 10: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 11: Commit**

```bash
git add app/Http/Controllers/Coach/PlanSelectionController.php app/Http/Requests/StorePlanSelectionRequest.php resources/views/coach/plan.blade.php routes/web.php tests/Feature/Coach/PlanSelectionTest.php
git commit -m "feat: add plan selection page and controller for post-registration flow"
```

---

## Task 5: `SubscriptionController::checkout` + update subscription page

**Files:**
- Modify: `app/Http/Controllers/Coach/SubscriptionController.php`
- Modify: `resources/views/coach/subscription.blade.php`
- Modify: `tests/Feature/Subscription/SubscriptionPageTest.php`

- [ ] **Step 1: Write the failing tests**

Add these tests to `tests/Feature/Subscription/SubscriptionPageTest.php`:

```php
it('subscription page shows subscribe button with plan name when coach has selected_plan but no subscription', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
        'selected_plan' => 'advanced',
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Subscribe to Advanced');
});

it('subscription page shows choose a plan link when coach has no selected_plan', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => now()->subDay(),
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription'))
        ->assertOk()
        ->assertSee('Choose a Plan');
});

it('checkout route redirects coach with no selected_plan to plan selection', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription.checkout'))
        ->assertRedirect(route('coach.plan'));
});

it('checkout route redirects already subscribed coach to dashboard', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => 'basic',
    ]);

    $coach->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_already_active',
        'stripe_status' => 'active',
        'stripe_price' => config('plans.basic.stripe_price_id'),
        'quantity' => 1,
        'ends_at' => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.subscription.checkout'))
        ->assertRedirect(route('coach.dashboard'));
});
```

- [ ] **Step 2: Run tests to see them fail**

```bash
php artisan test --compact --filter="SubscriptionPageTest"
```

Expected: FAIL — new assertions don't match current view/controller behaviour.

- [ ] **Step 3: Add `checkout` action to `SubscriptionController`**

In `app/Http/Controllers/Coach/SubscriptionController.php`, add the `checkout` method.

The method must:
1. Redirect to `coach.plan` if `selected_plan` is null
2. Redirect to `coach.dashboard` if already subscribed
3. Build a Stripe Checkout session for the chosen plan

For Basic and Advanced (single price ID):
```php
$coach->newSubscription('default', $priceId)->checkout([...])
```

For Professional (flat + metered):
```php
$coach->newSubscription('default', $flatPriceId)
    ->meteredPrice($meteredPriceId)
    ->checkout([...])
```

Full updated `SubscriptionController.php`:

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

    /**
     * Redirects the coach to the Stripe Customer Portal.
     */
    public function portal(): RedirectResponse
    {
        $coach = auth()->user();

        if (! $coach->stripe_id) {
            $coach->createAsStripeCustomer();
        }

        return $coach->redirectToBillingPortal(route('coach.subscription'));
    }

    /**
     * Redirects the coach to Stripe Checkout for their selected plan.
     * Used both when a new coach picks Advanced/Professional and when a
     * Basic trial coach needs to subscribe after the trial ends.
     */
    public function checkout(): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $coach = auth()->user();

        if (! $coach->selected_plan) {
            return redirect()->route('coach.plan');
        }

        if ($coach->subscribed('default')) {
            return redirect()->route('coach.dashboard');
        }

        $plan = config("plans.{$coach->selected_plan}");

        $checkoutOptions = [
            'success_url' => route('coach.plan.success'),
            'cancel_url' => route('coach.subscription'),
        ];

        if ($coach->selected_plan === 'professional') {
            return $coach->newSubscription('default', $plan['stripe_price_flat_id'])
                ->meteredPrice($plan['stripe_price_metered_id'])
                ->checkout($checkoutOptions);
        }

        return $coach->newSubscription('default', $plan['stripe_price_id'])
            ->checkout($checkoutOptions);
    }
}
```

- [ ] **Step 4: Update subscription page view**

In `resources/views/coach/subscription.blade.php`, find the CTA section at the bottom of the plans grid (the `<!-- CTA -->` div) and replace it:

```blade
                <!-- CTA -->
                <div class="mt-8 flex justify-center">
                    @if($subscription && $subscription->active())
                        <a href="{{ route('coach.subscription.portal') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Manage Subscription
                        </a>
                    @elseif(auth()->user()->selected_plan)
                        <a href="{{ route('coach.subscription.checkout') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Subscribe to {{ ucfirst(auth()->user()->selected_plan) }}
                        </a>
                    @else
                        <a href="{{ route('coach.plan') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Choose a Plan
                        </a>
                    @endif
                </div>
```

- [ ] **Step 5: Run all subscription tests**

```bash
php artisan test --compact --filter="SubscriptionPageTest|PlanSelectionTest|TrialStartTest|SubscriptionMiddlewareTest|RegistrationTest"
```

Expected: All pass.

- [ ] **Step 6: Run the full test suite to catch regressions**

```bash
php artisan test --compact
```

Expected: All tests pass. If any fail due to `selected_plan` being null on existing factory usage, add `'selected_plan' => 'basic'` to the specific factory call in that test, or verify the factory default is applied correctly.

- [ ] **Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/Coach/SubscriptionController.php resources/views/coach/subscription.blade.php tests/Feature/Subscription/SubscriptionPageTest.php
git commit -m "feat: add Stripe Checkout action and update subscription page CTA"
```

---

## Task 6: Update README

**Files:**
- Modify: `README.md`

- [ ] **Step 1: Add LiftDeck section to `README.md`**

Append the following section after the existing Laravel content in `README.md`:

```markdown
---

## LiftDeck

A coaching platform for fitness coaches and their clients. Coaches manage clients, programs, workouts, nutrition, and more. Clients track their training and progress.

### Coach Sign-Up Flow

1. Coach registers at `/register` (name, email, password)
2. Redirected to `/coach/plan` — picks a plan:
   - **Basic (€2.50/mo)** — 7-day free trial, no credit card required. After trial ends, redirected to `/coach/subscription` to subscribe via Stripe Checkout.
   - **Advanced (€10/mo)** — redirected immediately to Stripe Checkout to pay before accessing the dashboard.
   - **Professional (€15/mo + metered)** — redirected immediately to Stripe Checkout.
3. After Stripe Checkout completes, Cashier webhook activates the subscription and the coach is redirected to the dashboard.
4. Abandoned Stripe Checkout → coach lands on `/coach/subscription` with an option to complete payment or switch to the Basic trial.

### Subscription Plans

| Plan         | Price         | Clients   | Features                            |
|--------------|---------------|-----------|-------------------------------------|
| Basic        | €2.50/mo      | Up to 5   | Programs, workout logs, nutrition   |
| Advanced     | €10/mo        | Up to 15  | + Loyalty & achievements            |
| Professional | €15/mo + per-client overage | 30 included (unlimited+) | + Custom branding |

Plans are configured in `config/plans.php`. Stripe price IDs are set via environment variables (`STRIPE_PRICE_BASIC`, `STRIPE_PRICE_ADVANCED`, `STRIPE_PRICE_PROFESSIONAL_FLAT`, `STRIPE_PRICE_PROFESSIONAL_METERED`).

### Required Stripe Webhook Events

The following events must be enabled in the Stripe dashboard and pointed at `/cashier/webhook`:

- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `checkout.session.completed`
- `invoice.payment_succeeded`
- `invoice.payment_failed`

### Environment Variables

```env
STRIPE_KEY=pk_...
STRIPE_SECRET=sk_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_PRICE_BASIC=price_...
STRIPE_PRICE_ADVANCED=price_...
STRIPE_PRICE_PROFESSIONAL_FLAT=price_...
STRIPE_PRICE_PROFESSIONAL_METERED=price_...
```

### Running Locally

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev
```
```

- [ ] **Step 2: Run full test suite one final time**

```bash
php artisan test --compact
```

Expected: All tests pass.

- [ ] **Step 3: Commit**

```bash
git add README.md
git commit -m "docs: document LiftDeck coach signup and Stripe payments flow in README"
```

---

## Self-Review Checklist

- [x] **Migration**: `selected_plan` column added — Task 1
- [x] **Registration redirect**: removed trial, redirect to `coach.plan` — Task 2
- [x] **Middleware**: routes new coaches to `coach.plan`, existing coaches with `selected_plan` to `coach.subscription` — Task 3
- [x] **Plan selection page**: Basic → trial, Advanced/Pro → `subscription.checkout` — Task 4
- [x] **Form validation**: `StorePlanSelectionRequest` validates `in:basic,advanced,professional` — Task 4
- [x] **`success` action**: redirects to dashboard — Task 4
- [x] **`checkout` action**: handles Basic, Advanced, Professional including metered — Task 5
- [x] **Subscription page CTA**: active → portal, selected plan → checkout, no plan → `coach.plan` — Task 5
- [x] **Factory updated**: `selected_plan = 'basic'` default so existing tests pass — Task 1
- [x] **TrialStartTest updated**: trial now set on plan selection, not registration — Task 2
- [x] **README**: documents full sign-up flow, plans, webhook events, env vars — Task 6
