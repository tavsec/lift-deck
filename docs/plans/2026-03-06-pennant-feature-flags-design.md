# Feature Flags with Laravel Pennant

**Date:** 2026-03-06
**Status:** Approved

## Goal

Enable admins to turn individual features on or off per coach via the Filament admin panel. Starting with the loyalty system (XP, levels, achievements, rewards). Flags are off by default — coaches must be explicitly opted in.

## Architecture

### Package

Install `laravel/pennant`. Use the default DB driver — flags stored in a `features` table keyed by scope.

### Scope

The scope is always the **coach User model**. Clients inherit their coach's flags — there is no per-client override.

- Coach checking their own flag: `Feature::for(auth()->user())->active(Loyalty::class)`
- Client checking their coach's flag: `Feature::for(auth()->user()->coach)->active(Loyalty::class)`

### Feature Definition

One class per feature in `app/Features/`. Resolving to `false` means opt-in (off by default).

```php
// app/Features/Loyalty.php
namespace App\Features;

use App\Models\User;

class Loyalty
{
    public function resolve(User $scope): bool
    {
        return false; // OFF by default
    }
}
```

### Storage

Pennant writes to `features` table:

| scope_type | scope_id | name | value |
|---|---|---|---|
| App\Models\User | 5 | App\Features\Loyalty | true |

No migration needed to add new flags — just add a new class.

## Filament Admin UI

On the **EditUser** page, a "Features" section with `Toggle` fields:

- Each toggle calls `Feature::for($record)->activate/deactivate(Loyalty::class)` via `afterStateUpdated`
- `dehydrated(false)` excludes it from Eloquent save
- `->live()` ensures immediate response
- Initial state loaded from Pennant via `->default(fn($record) => Feature::for($record)->active(Loyalty::class))`

On the **ViewUser** infolist, a read-only "Features" section with `TextEntry` badges showing Enabled/Disabled per flag.

## Enforcement Points

All four enforce the same check — if the flag is off, hide or block.

| Point | What happens |
|---|---|
| **Coach nav** | Loyalty section (Rewards, Achievements, Redemptions) wrapped in `@if(Feature::for(auth()->user())->active(Loyalty::class))` |
| **Coach routes** | A new `EnsureFeatureActive` middleware applied to a route group covering all loyalty coach routes — returns 403 if flag is off |
| **Client nav** | Loyalty links (Rewards Shop, Achievements, Points History) wrapped in `@if(Feature::for(auth()->user()->coach)->active(Loyalty::class))` |
| **Client dashboard** | `DashboardController` passes `$loyaltyEnabled` boolean; XP/level card wrapped in `@if($loyaltyEnabled)` |

### Middleware

```php
// app/Http/Middleware/EnsureFeatureActive.php
class EnsureFeatureActive
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $coach = auth()->user()->isCoach()
            ? auth()->user()
            : auth()->user()->coach;

        if (!$coach || Feature::for($coach)->inactive($feature)) {
            abort(403);
        }

        return $next($request);
    }
}
```

Registered as `feature` alias in `bootstrap/app.php`.

## Testing

### `tests/Feature/FeatureFlags/LoyaltyFeatureFlagTest.php`

- Coach with flag OFF: loyalty routes return 403
- Coach with flag ON: loyalty routes are accessible
- Client with coach flag OFF: loyalty routes return 403, dashboard has no XP card
- Client with coach flag ON: loyalty routes accessible, dashboard shows XP card
- Flag defaults to OFF for new coach (no stored Pennant record)

### `tests/Feature/FeatureFlags/LoyaltyFeatureFlagAdminTest.php`

- Admin can enable loyalty for a coach (Filament edit action)
- Admin can disable loyalty for a coach (Filament edit action)
- Toggle persists to `features` table

## Future Flags

Add a new class to `app/Features/`, register it in the Filament "Features" section with a new `Toggle`. No DB migrations required.
