# Subscription Settings Card — Design Spec

## Goal

Add a "Subscription" card to the coach settings page (`/coach/settings`) so coaches can see their current subscription status and jump to the Stripe Customer Portal to manage it. Changes made on Stripe are automatically reflected in the app via existing Cashier webhooks.

## Architecture

A new card section is appended to the bottom of `resources/views/coach/settings/edit.blade.php`. The card is purely server-rendered using local Cashier data — no live Stripe API calls. `SettingsController::editCoach()` is updated to inject `SubscriptionService` and pass the necessary subscription variables alongside the existing `user` variable.

**Tech Stack:** Laravel 12, Cashier v16, Blade, Tailwind CSS v3

---

## Section 1: UI

The card is titled "Subscription" and renders one of four states based on the coach's local subscription data:

### State 1 — Free trial
- Blue info tone
- Shows: plan name (e.g. "Basic"), trial end date formatted as "Jan 1, 2026", client usage ("2 / 5 clients")
- "Manage on Stripe" button → `route('coach.subscription.portal')`

### State 2 — Active paid subscription
- Green tone
- Shows: plan name (e.g. "Advanced"), client usage ("3 / 15 clients" or "12 clients (unlimited)")
- "Manage on Stripe" button → `route('coach.subscription.portal')`

### State 3 — Grace period
- Orange warning tone
- Shows: "Your subscription has ended" + days remaining in grace period
- "Manage on Stripe" button → `route('coach.subscription.portal')` (urgent CTA)

### State 4 — No subscription
- Neutral/gray tone
- Shows: "No active subscription"
- "Choose a plan" link → `route('coach.subscription')` (no portal button, coach has no Stripe customer yet)

The "Manage on Stripe" button is shown only for states 1, 2, and 3 (coach exists as a Stripe customer).

---

## Section 2: Controller

`SettingsController::editCoach()` is updated to:

1. Inject `SubscriptionService` via constructor (store as `private readonly`)
2. Pass these additional variables to `coach.settings.edit`:
   - `currentPlanKey` — string|null, e.g. `'basic'`, `'advanced'`, `'professional'`
   - `isOnTrial` — bool
   - `trialEndsAt` — Carbon|null
   - `isInGracePeriod` — bool
   - `graceDaysRemaining` — int
   - `clientCount` — int
   - `clientLimit` — int|null (null = unlimited)

All values come from `SubscriptionService` methods and model properties already used in `SubscriptionController::index()`.

---

## Section 3: Sync Mechanism

No new code needed. Cashier's built-in webhook handler at `POST /cashier/webhook` already listens for `customer.subscription.updated`, `customer.subscription.deleted`, `invoice.payment_succeeded`, and `invoice.payment_failed`. These events update the local `subscriptions` table, so the settings card always reflects the current Stripe state within seconds of any change the coach makes in the portal.

Required webhook events are documented in `README.md` and must be enabled in the Stripe dashboard.

---

## Section 4: Tests

4 Pest feature tests in `tests/Feature/Coach/SubscriptionSettingsTest.php`:

1. **Settings page shows trial status** — coach with `is_free_access` or trial active sees trial state card
2. **Settings page shows active plan** — coach with active Cashier subscription sees plan name and client usage
3. **Settings page shows grace period** — coach with cancelled subscription in grace period sees warning
4. **Settings page shows no subscription** — new coach with no subscription sees "Choose a plan" link

Each test asserts the settings page returns 200 and contains the expected text/element.

---

## Files Changed

- **Modify:** `app/Http/Controllers/SettingsController.php` — inject `SubscriptionService`, pass subscription vars to `editCoach()`
- **Modify:** `resources/views/coach/settings/edit.blade.php` — append subscription card
- **Create:** `tests/Feature/Coach/SubscriptionSettingsTest.php` — 4 state tests
