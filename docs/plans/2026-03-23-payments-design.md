# Payments & Subscriptions Design

## Overview

Coaches subscribe to one of three plans via Stripe. Laravel Cashier manages the Stripe integration. The app enforces feature and client limits based on the active plan. Stripe's Customer Portal handles all payment management — we never handle card details directly.

---

## Subscription Plans

| Plan | Price | Client Limit | Features |
|---|---|---|---|
| Basic | 2.5 EUR/month | 5 | Core only |
| Advanced | 10 EUR/month | 15 | Core + Loyalty |
| Professional | 15 EUR/month + 0.5 EUR/client/month (above 30) | Unlimited (metered) | Core + Loyalty + Custom Branding |

**Free trial:** Basic plan, 7 days, no credit card required. Starts on coach registration.

**Free access:** Coaches can be granted `is_free_access = true` via Filament admin (for ambassadors, friends, etc.). Bypasses all subscription checks.

---

## Architecture

### Laravel Cashier (Stripe)

- `laravel/cashier` added to the project
- `User` model gets the `Billable` trait
- Cashier webhooks registered at `cashier/webhook`
- Cashier handles: subscription state, trial tracking, grace periods, Customer Portal redirects

### Stripe Products & Prices

- **Basic** — flat 2.5 EUR/month price, 7-day trial via Cashier `trialDays(7)`
- **Advanced** — flat 10 EUR/month price
- **Professional** — two prices on one subscription:
  - Flat 15 EUR/month (`STRIPE_PRICE_PROFESSIONAL_FLAT`)
  - Metered 0.5 EUR/unit/month (`STRIPE_PRICE_PROFESSIONAL_METERED`) — units = clients above 30

### Plan Config (`config/plans.php`)

Source of truth for plan limits and features. Plan identity derived from Cashier subscription's Stripe price ID matched against this config.

```php
return [
    'basic' => [
        'stripe_price_id' => env('STRIPE_PRICE_BASIC'),
        'client_limit' => 5,
        'features' => [],
    ],
    'advanced' => [
        'stripe_price_id' => env('STRIPE_PRICE_ADVANCED'),
        'client_limit' => 15,
        'features' => ['loyalty'],
    ],
    'professional' => [
        'stripe_price_flat_id' => env('STRIPE_PRICE_PROFESSIONAL_FLAT'),
        'stripe_price_metered_id' => env('STRIPE_PRICE_PROFESSIONAL_METERED'),
        'client_limit' => null,
        'included_clients' => 30,
        'features' => ['loyalty', 'custom_branding'],
    ],
];
```

---

## Database Changes

### Cashier migrations (auto-generated)

Adds to `users`: `stripe_id`, `pm_type`, `pm_last_four`, `trial_ends_at`

New tables: `subscriptions`, `subscription_items`

### Our migration

Adds to `users`:
- `is_free_access` (boolean, default false)

---

## Subscription States

| State | Access |
|---|---|
| Active trial | Full access for plan |
| Active subscription | Full access for plan |
| Grace period (7 days after expiry/cancellation) | Read access + toast alert |
| Trial expired (no subscription) | Locked out, redirect to subscription page |
| Subscription expired + grace period elapsed | Locked out, redirect to subscription page |
| `is_free_access = true` | Full Professional-level access, no checks |

---

## Feature Gating — `SubscriptionService`

Single service class at `App\Services\SubscriptionService`:

```
isActive(User $coach): bool
isInGracePeriod(User $coach): bool
currentPlan(User $coach): ?array
canAddClient(User $coach): bool
hasFeature(User $coach, string $feature): bool
reportClientUsage(User $coach): void
```

### Enforcement Points

- **All coach routes** — middleware checks `isActive()` OR `isInGracePeriod()`. If neither, redirect to `/coach/subscription`.
- **`ClientController@store`** — calls `canAddClient()`. If false, returns error with upgrade prompt.
- **Loyalty routes** — middleware checks `hasFeature('loyalty')`. If false, redirect with flash message.
- **Branding routes** — middleware checks `hasFeature('custom_branding')`. If false, redirect with flash message.
- **Metered billing** — after client added/removed on Professional plan, `reportClientUsage()` sends current overage count (clients above 30) to Stripe as a usage record.

---

## Grace Period Alert

Middleware on all coach routes checks `isInGracePeriod()` and flashes a session variable. The coach layout renders a dismissible toast:

> "Your subscription has ended. You have X days remaining. [Manage subscription →]"

The link redirects to the Stripe Customer Portal via Cashier's `redirectToBillingPortal()`.

---

## Coach UI

### `/coach/subscription` page

- Current plan name, status, next billing date, client count vs limit
- Trial users: trial expiry date + "Subscribe now" CTA
- Grace period / expired users: prominent upgrade prompt
- "Manage subscription" button → Stripe Customer Portal

### Upgrade prompts

- **Client limit hit** — inline error on add client form with link to subscription page
- **Feature not in plan** — redirect to subscription page with flash message explaining which plan includes the feature

---

## Filament Admin

Additions to the existing `UserResource`:

- `is_free_access` toggle (editable)
- Subscription status display: plan name, status, trial end date, subscription end date (read-only, from Cashier)

---

## Implementation Steps

1. Install and configure `laravel/cashier`
2. Set up Stripe products and prices, add price IDs to `.env`
3. Create `config/plans.php`
4. Run Cashier migrations + add `is_free_access` migration
5. Add `Billable` trait to `User` model
6. Create `SubscriptionService`
7. Create subscription enforcement middleware
8. Register Cashier webhook route and configure in Stripe dashboard
9. Start Basic trial on coach registration
10. Create `/coach/subscription` page
11. Add grace period toast to coach layout
12. Add upgrade prompts to `ClientController` and gated routes
13. Add Professional metered usage reporting
14. Update Filament `UserResource` with subscription fields and free access toggle
15. Write tests
