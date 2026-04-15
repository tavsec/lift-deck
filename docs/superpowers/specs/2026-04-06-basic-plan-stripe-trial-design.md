# Basic Plan Stripe Trial — Design Spec

## Goal

Change the Basic plan trial from a generic server-side trial (just a `trial_ends_at` date on the user) to a real Stripe subscription with a 7-day trial period. This enables coaches to switch plans via the Stripe Customer Portal during their trial.

## Architecture

When a coach selects Basic, they are redirected to Stripe Checkout with `trialDays(7)` on the Basic price — identical to how Advanced and Professional work, just with a trial attached. Stripe collects the card, creates the subscription in `trialing` status, fires `customer.subscription.created`, and Cashier stores it locally. After 7 days Stripe auto-charges €2.50/month. During the trial the coach has a real Stripe subscription and the Customer Portal shows full plan-switching options.

**Tech Stack:** Laravel 12, Cashier v16, Blade, Tailwind CSS v3, Pest v4

---

## Section 1: Flow Change

**Before:**
1. Coach selects Basic → `PlanSelectionController::store()` sets `trial_ends_at = now()->addDays(7)` and `selected_plan = 'basic'` → redirected to dashboard immediately (no Stripe interaction)
2. Trial ends → middleware redirects to `/coach/subscription` to pay
3. No Stripe subscription during trial → Customer Portal shows no plan switcher

**After:**
1. Coach selects Basic → redirected to Stripe Checkout with 7-day trial on the Basic price
2. Coach enters card → Stripe creates subscription with `stripe_status = 'trialing'` → webhook fires → Cashier stores subscription locally → coach redirected to dashboard via `coach.plan.success`
3. Trial ends → Stripe auto-charges €2.50/month, subscription moves to `active`
4. During trial → Customer Portal shows plan switcher ✅

---

## Section 2: Code Changes

### `PlanSelectionController::store()`

Remove the Basic-specific branch that sets `trial_ends_at`. Basic now flows identically to Advanced/Professional: set `selected_plan`, redirect to checkout.

```php
public function store(StorePlanSelectionRequest $request): RedirectResponse
{
    $coach = auth()->user();
    $plan = $request->validated('plan');

    $coach->update(['selected_plan' => $plan]);

    return redirect()->route('coach.subscription.checkout');
}
```

### `SubscriptionController::checkout()`

Basic now goes through Stripe Checkout with `trialDays(7)`:

```php
if ($coach->selected_plan === 'basic') {
    return $coach->newSubscription('default', $plan['stripe_price_id'])
        ->trialDays(7)
        ->checkout($checkoutOptions);
}
```

The existing guard `if ($this->subscriptionService->isActive($coach)) { return redirect()->route('coach.dashboard'); }` is correct — Basic coaches won't be active when they first reach checkout, so it won't block them.

### `SettingsController::editCoach()`

`trialEndsAt` currently reads `$coach->trial_ends_at` (the user model column). For new coaches the trial date lives on the Cashier subscription record, not the user model. Fall back to the subscription's `trial_ends_at`:

```php
'trialEndsAt' => $coach->trial_ends_at ?? $coach->subscription('default')?->trial_ends_at,
```

### `resources/views/coach/plan.blade.php`

- Remove the blue info banner: _"Basic plan includes a 7-day Free Trial — no credit card required."_
- Remove the "No credit card required" sub-text under the Basic button
- Keep the button label "Start Free Trial"
- Add a short note under the Basic button: _"7-day free trial, then €2.50/month"_

---

## Section 3: No Changes Required

- **`SubscriptionService::isActive()`** — `subscribed('default')` already returns true for `trialing` subscriptions (Cashier's `valid()` includes `onTrial()`). The generic `$coach->onTrial()` check stays for test factory compatibility.
- **`UserFactory`** — keeps `trial_ends_at = now()->addDays(14)` so factory-created coaches pass `EnsureCoachSubscribed` middleware in tests without needing a real subscription record.
- **`EnsureCoachSubscribed` middleware** — no change. `isActive()` covers trialing subscriptions via `subscribed()`.
- **`SubscriptionService::currentPlanKey()`** — already reads `subscription->stripe_price` and matches to config, so trialing coaches on Basic will correctly resolve to `'basic'`.

---

## Section 4: Tests

### `tests/Feature/Coach/PlanSelectionTest.php`

Update two tests:

1. **`selecting basic plan sets selected_plan and trial_ends_at and redirects to dashboard`** → rename and change:
   - New name: `selecting basic plan sets selected_plan and redirects to checkout`
   - Assert redirect to `coach.subscription.checkout` (not `coach.dashboard`)
   - Assert `$coach->selected_plan === 'basic'`
   - Assert `$coach->trial_ends_at` is **null** (no longer set directly)

2. **`redirects to dashboard if coach already has active trial`** → this test uses `trial_ends_at` on the user model (factory pattern), which still works via `isActive()` → no change needed.

### `tests/Feature/Coach/SubscriptionSettingsTest.php`

The existing trial test uses the factory's `trial_ends_at` field — still valid for test purposes. No change needed.

---

## Files Changed

- **Modify:** `app/Http/Controllers/Coach/PlanSelectionController.php`
- **Modify:** `app/Http/Controllers/Coach/SubscriptionController.php`
- **Modify:** `app/Http/Controllers/SettingsController.php`
- **Modify:** `resources/views/coach/plan.blade.php`
- **Modify:** `tests/Feature/Coach/PlanSelectionTest.php`
