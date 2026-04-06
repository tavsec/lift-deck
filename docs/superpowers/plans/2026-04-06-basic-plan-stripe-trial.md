# Basic Plan Stripe Trial Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the generic server-side trial (just a `trial_ends_at` date on the user) with a real Stripe subscription trial for the Basic plan, enabling plan switching via the Stripe Customer Portal.

**Architecture:** When a coach selects Basic, they are redirected to Stripe Checkout with `trialDays(7)` — the same flow as Advanced/Professional but with a trial attached. The `PlanSelectionController::store()` is simplified to a single code path for all plans. `SubscriptionController::checkout()` adds trial support driven by the plan's `trial_days` config value. `SettingsController` is updated to fall back to the subscription's `trial_ends_at` when displaying trial info.

**Tech Stack:** Laravel 12, Cashier v16, Blade, Tailwind CSS v3, Pest v4

---

## File Structure

- **Modify:** `app/Http/Controllers/Coach/PlanSelectionController.php` — remove Basic-specific branch, all plans go to checkout
- **Modify:** `app/Http/Controllers/Coach/SubscriptionController.php` — add `trialDays()` for plans with `trial_days > 0` in config
- **Modify:** `app/Http/Controllers/SettingsController.php` — fix `trialEndsAt` to fall back to subscription's `trial_ends_at`
- **Modify:** `resources/views/coach/plan.blade.php` — remove "no credit card required" copy
- **Modify:** `tests/Feature/Coach/PlanSelectionTest.php` — update Basic plan test to expect checkout redirect + no trial_ends_at

---

### Task 1: Simplify PlanSelectionController — all plans go to checkout

**Files:**
- Modify: `app/Http/Controllers/Coach/PlanSelectionController.php:32-48`
- Test: `tests/Feature/Coach/PlanSelectionTest.php`

**Context:**
- `PlanSelectionController::store()` currently has a special branch for Basic that sets `trial_ends_at = now()->addDays(7)` and redirects to the dashboard directly, bypassing Stripe
- All other plans already redirect to `coach.subscription.checkout`
- After this task, all three plans set `selected_plan` and redirect to checkout — the trial is now handled by Stripe in Task 2
- The existing test `selecting basic plan sets selected_plan and trial_ends_at and redirects to dashboard` must be updated

- [ ] **Step 1: Update the failing test first**

In `tests/Feature/Coach/PlanSelectionTest.php`, replace the test at line 31:

```php
it('selecting basic plan sets selected_plan and redirects to checkout', function (): void {
    $coach = User::factory()->state(['role' => 'coach'])->create([
        'trial_ends_at' => null,
        'selected_plan' => null,
    ]);

    $this->actingAs($coach)
        ->post(route('coach.plan.store'), ['plan' => 'basic'])
        ->assertRedirect(route('coach.subscription.checkout'));

    $coach->refresh();
    expect($coach->selected_plan)->toBe('basic');
    expect($coach->trial_ends_at)->toBeNull();
});
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter="selecting basic plan"
```

Expected: FAIL — redirects to `coach.dashboard`, not `coach.subscription.checkout`.

- [ ] **Step 3: Update `PlanSelectionController::store()`**

Replace the `store()` method in `app/Http/Controllers/Coach/PlanSelectionController.php`:

```php
public function store(StorePlanSelectionRequest $request): RedirectResponse
{
    $coach = auth()->user();

    $coach->update(['selected_plan' => $request->validated('plan')]);

    return redirect()->route('coach.subscription.checkout');
}
```

- [ ] **Step 4: Run the full PlanSelectionTest to verify**

```bash
php artisan test --compact tests/Feature/Coach/PlanSelectionTest.php
```

Expected: PASS (all tests). The `redirects to dashboard if coach already has active trial` test still passes because factory coaches still have `trial_ends_at` set, so `isActive()` returns true and `show()` redirects to dashboard before reaching `store()`.

- [ ] **Step 5: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Coach/PlanSelectionController.php tests/Feature/Coach/PlanSelectionTest.php
git commit -m "feat: basic plan now goes through stripe checkout like advanced/professional"
```

---

### Task 2: Add trial support to SubscriptionController::checkout()

**Files:**
- Modify: `app/Http/Controllers/Coach/SubscriptionController.php:60-97`

**Context:**
- `checkout()` currently has a special branch only for `professional` (which needs two prices). All other plans fall through to a single `newSubscription()->checkout()` call with no trial
- The plan config at `config/plans.php` has `'trial_days' => 7` for Basic and `'trial_days' => 0` for Advanced and Professional
- We use the config value to conditionally apply `trialDays()` — no hardcoded plan names
- Stripe Checkout makes a live API call so we cannot test the actual checkout session creation; we test only the guard conditions (already covered by existing tests)
- Update the docblock comment which currently mentions "Basic trial coach needs to subscribe after trial ends" — that's now outdated

- [ ] **Step 1: Update `checkout()` in `app/Http/Controllers/Coach/SubscriptionController.php`**

Replace the full `checkout()` method:

```php
/**
 * Redirects the coach to Stripe Checkout for their selected plan.
 * All three plans (Basic, Advanced, Professional) go through Stripe Checkout.
 * Basic includes a trial period configured via config('plans.basic.trial_days').
 *
 * Stripe Checkout session creation makes a live API call — test the guard
 * conditions only (no selected_plan, already subscribed).
 */
public function checkout(): Response
{
    $coach = auth()->user();

    if (! $coach->selected_plan) {
        return redirect()->route('coach.plan');
    }

    if ($this->subscriptionService->isActive($coach)) {
        return redirect()->route('coach.dashboard');
    }

    $plan = config("plans.{$coach->selected_plan}");

    abort_if($plan === null, 404);

    $checkoutOptions = [
        'success_url' => route('coach.plan.success'),
        'cancel_url'  => route('coach.subscription'),
    ];

    if ($coach->selected_plan === 'professional') {
        return $coach->newSubscription('default', $plan['stripe_price_flat_id'])
            ->meteredPrice($plan['stripe_price_metered_id'])
            ->checkout($checkoutOptions);
    }

    $builder = $coach->newSubscription('default', $plan['stripe_price_id']);

    if (($plan['trial_days'] ?? 0) > 0) {
        $builder->trialDays($plan['trial_days']);
    }

    return $builder->checkout($checkoutOptions);
}
```

- [ ] **Step 2: Run existing checkout-related tests**

```bash
php artisan test --compact tests/Feature/Coach/PlanSelectionTest.php
```

Expected: PASS — the guard condition tests (no selected_plan → plan, already active → dashboard) still pass.

- [ ] **Step 3: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Coach/SubscriptionController.php
git commit -m "feat: apply 7-day stripe trial to basic plan checkout"
```

---

### Task 3: Fix SettingsController — trialEndsAt for subscription-based trials

**Files:**
- Modify: `app/Http/Controllers/SettingsController.php:19-33`
- Test: `tests/Feature/Coach/SubscriptionSettingsTest.php`

**Context:**
- `editCoach()` currently passes `'trialEndsAt' => $coach->trial_ends_at` to the view
- For new Basic coaches the trial date is on the Cashier **subscription** record's `trial_ends_at`, not the user model. The user's `trial_ends_at` will be `null`
- The fix: fall back to `$coach->subscription('default')?->trial_ends_at` when the user-level date is null
- The existing `SubscriptionSettingsTest` uses factory coaches (who have `trial_ends_at` on the user model) — still valid. Add one new test for the subscription-based trial path
- Create a subscription record directly (same pattern used in the grace period test) with a future `trial_ends_at` and `stripe_status = 'trialing'`

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/Coach/SubscriptionSettingsTest.php`:

```php
it('settings subscription card shows trial end date from subscription when user trial_ends_at is null', function (): void {
    $coach = User::factory()->create([
        'role'           => 'coach',
        'trial_ends_at'  => null,
        'is_free_access' => false,
    ]);

    $trialEnd = now()->addDays(6);

    $coach->subscriptions()->create([
        'type'          => 'default',
        'stripe_id'     => 'sub_test_trial',
        'stripe_status' => 'trialing',
        'stripe_price'  => 'price_test',
        'quantity'      => 1,
        'trial_ends_at' => $trialEnd,
        'ends_at'       => null,
    ]);

    $this->actingAs($coach)
        ->get(route('coach.settings.edit'))
        ->assertOk()
        ->assertViewHas('trialEndsAt', fn ($v) => $v?->isSameDay($trialEnd));
});
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter="trial end date from subscription"
```

Expected: FAIL — `trialEndsAt` is `null` because only `$coach->trial_ends_at` is checked.

- [ ] **Step 3: Update `editCoach()` in `app/Http/Controllers/SettingsController.php`**

Change the `trialEndsAt` line from:

```php
'trialEndsAt'       => $coach->trial_ends_at,
```

To:

```php
'trialEndsAt'       => $coach->trial_ends_at ?? $coach->subscription('default')?->trial_ends_at,
```

- [ ] **Step 4: Run test to verify it passes**

```bash
php artisan test --compact tests/Feature/Coach/SubscriptionSettingsTest.php
```

Expected: PASS (6 tests).

- [ ] **Step 5: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/SettingsController.php tests/Feature/Coach/SubscriptionSettingsTest.php
git commit -m "fix: use subscription trial_ends_at as fallback in settings view"
```

---

### Task 4: Update plan selection view — remove no-card copy

**Files:**
- Modify: `resources/views/coach/plan.blade.php`

**Context:**
- The view at `resources/views/coach/plan.blade.php` has two pieces of copy that refer to "no credit card required":
  1. A blue info banner at lines 10–14: `<div class="rounded-md bg-blue-50 ...">...<strong>Basic plan includes a 7-day Free Trial</strong> — no credit card required...</div>`
  2. A sub-text under the Basic button at line 49: `<p ...>No credit card required</p>`
- Remove both. Replace the sub-text under the Basic button with a pricing note: "7-day free trial, then €2.50/month"
- The `PlanSelectionTest` checks `assertSee('Free Trial')` — the button still says "Start Free Trial" so that test keeps passing

- [ ] **Step 1: Remove the blue info banner (lines 10–14)**

Remove this block from `resources/views/coach/plan.blade.php`:

```blade
        <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>Basic plan includes a 7-day Free Trial</strong> — no credit card required. Advanced and Professional plans start immediately with payment.
            </p>
        </div>

```

- [ ] **Step 2: Replace "No credit card required" sub-text under the Basic button**

Replace:

```blade
                    <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">No credit card required</p>
```

With:

```blade
                    <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">7-day free trial, then €2.50/month</p>
```

- [ ] **Step 3: Run the plan selection tests to verify nothing broke**

```bash
php artisan test --compact tests/Feature/Coach/PlanSelectionTest.php
```

Expected: PASS (all tests). The `assertSee('Free Trial')` test passes because the Basic button still says "Start Free Trial".

- [ ] **Step 4: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 5: Commit**

```bash
git add resources/views/coach/plan.blade.php
git commit -m "feat: update basic plan copy — card required for trial"
```
