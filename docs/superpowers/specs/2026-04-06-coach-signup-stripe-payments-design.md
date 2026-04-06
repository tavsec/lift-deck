# Coach Sign-Up with Stripe Payments

## Overview

Add plan selection to the coach registration flow. After creating an account, coaches pick a plan before accessing the dashboard. Basic gets a 7-day free trial (no credit card). Advanced and Professional go directly to Stripe Checkout to pay. Payment is collected via Stripe Checkout (hosted by Stripe).

---

## User Flows

### Basic Plan
1. Coach registers (name, email, password)
2. Account created, logged in, redirected to `/coach/plan`
3. Coach selects Basic → `selected_plan = 'basic'`, `trial_ends_at = now() + 7 days` set
4. Redirected to dashboard
5. When trial ends → `EnsureCoachSubscribed` redirects to `/coach/subscription`
6. Subscription page shows "Subscribe to Basic" → Stripe Checkout (Basic plan pre-filled)
7. Payment confirmed → subscription active → access restored

### Advanced / Professional Plan
1. Coach registers → redirected to `/coach/plan`
2. Coach selects Advanced or Professional → `selected_plan` stored → redirected to Stripe Checkout
3. **Pays**: subscription created via Cashier webhook → redirected to dashboard
4. **Abandons**: redirected to `/coach/subscription` (blocked); can switch to Basic from there

---

## Data

**New column: `users.selected_plan`** — nullable string, one of `'basic'`, `'advanced'`, `'professional'`. Set when the coach picks a plan. Persists so the subscription page can pre-fill Stripe Checkout when the trial ends.

---

## Components

### 1. Migration
Add `selected_plan` (nullable string) to `users` table.

### 2. `PlanSelectionController`
Located at `app/Http/Controllers/Coach/PlanSelectionController.php`.

- `show()` — renders plan selection view. Redirects to dashboard if the coach already has an active subscription or trial.
- `store(Request $request)` — validates `plan` input (one of basic/advanced/professional):
  - **Basic**: sets `selected_plan`, sets `trial_ends_at = now()->addDays(7)`, redirects to dashboard
  - **Advanced/Pro**: sets `selected_plan`, redirects to `coach.subscription.checkout`
- `success(Request $request)` — handles Stripe return after successful payment; redirects to dashboard (Cashier webhook has already created the subscription)

### 3. Routes
```
GET  /coach/plan                   coach.plan                  PlanSelectionController@show
POST /coach/plan                   coach.plan.store            PlanSelectionController@store
GET  /coach/plan/success           coach.plan.success          PlanSelectionController@success
GET  /coach/subscription/checkout  coach.subscription.checkout SubscriptionController@checkout
```

All plan routes and the subscription checkout route require `auth`, `verified`, `role:coach` but are excluded from the `subscribed` middleware.

### 4. `SubscriptionController::checkout`
New `GET` action. Reads `auth()->user()->selected_plan`, looks up price ID(s) from `config/plans.php`, creates a Stripe Checkout session via Cashier, and redirects.

- Success URL: `route('coach.plan.success')`
- Cancel URL: `route('coach.subscription')`
- Professional: passes both `stripe_price_flat_id` and `stripe_price_metered_id` as line items

Making this a `GET` route means `PlanSelectionController::store` (Advanced/Pro path) simply sets `selected_plan` and redirects here — no duplicated checkout session logic.

### 5. `EnsureCoachSubscribed` middleware updates
Current behaviour: blocks inactive coaches → redirects to `coach.subscription`.

New behaviour:
- Allow `coach.plan` and `coach.plan.success` routes through (no redirect)
- If coach has no `selected_plan` and is not active → redirect to `coach.plan` (pick a plan first)
- If coach has `selected_plan` but is not active → redirect to `coach.subscription` (existing behaviour)

### 6. Plan selection view
`resources/views/coach/plan.blade.php` — uses authenticated coach layout (or minimal wrapper). Shows three plan cards matching the existing subscription page styling:

- **Basic**: "Start 7-Day Free Trial — no credit card required" CTA
- **Advanced**: "Subscribe Now" CTA → Stripe Checkout
- **Professional**: "Subscribe Now" CTA → Stripe Checkout
- Prominent callout: only Basic includes a free trial

### 7. Subscription page CTA update
`resources/views/coach/subscription.blade.php` — replace the single "Subscribe Now / Manage" CTA:

- Active subscription → "Manage Subscription" → Stripe Portal (unchanged)
- Has `selected_plan`, no active subscription → "Subscribe to [Plan]" → `POST coach.subscription.checkout`
- No `selected_plan` → "Choose a Plan" → `coach.plan`

### 8. `RegisteredUserController` update
Remove the `trial_ends_at` assignment. Change redirect from `coach.dashboard` to `coach.plan`.

---

## Stripe Checkout Details

- Uses Cashier's `$user->checkout([$priceId => 1], [...options])` method
- `success_url` and `cancel_url` passed as options
- `allow_promotion_codes` can be enabled optionally
- Cashier's existing webhook handler (`cashier/webhook`) processes `checkout.session.completed` and `customer.subscription.created` — no custom webhook handling needed

---

## What Is Not Changing

- Stripe Customer Portal link for managing existing subscriptions
- `SubscriptionService` logic (plan detection, feature gating, client limits, metered usage)
- Client registration flow
- Trial period length (7 days, Basic only)
- Cashier webhook route

---

## Testing

- Feature test: Basic plan selection sets `selected_plan` and `trial_ends_at`, redirects to dashboard
- Feature test: Advanced/Pro plan selection sets `selected_plan` and redirects to Stripe Checkout URL
- Feature test: Abandoned checkout (no subscription) → subscription page is shown when accessing coach routes
- Feature test: New coach with no `selected_plan` → redirected to plan page (not subscription page)
- Feature test: `SubscriptionController::checkout` redirects to Stripe Checkout with correct price ID
- Existing subscription middleware and feature gating tests remain unchanged
