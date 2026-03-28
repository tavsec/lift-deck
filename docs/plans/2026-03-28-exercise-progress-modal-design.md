# Exercise Progress Modal Design

**Date:** 2026-03-28

## Overview

Add personal records (PRs) and progression charts to the exercise info modal shown in the client program view and log-workout view. When a client opens the modal for any exercise, they see their all-time PRs and a chart of recent weight and volume progression.

## Approach

A new JSON endpoint (`Client\ExerciseProgressController`) returns PR stats and chart data for a given exercise, scoped to the authenticated client. When a modal opens in either view, Alpine.js fetches from this endpoint and renders two Chart.js line charts. The range can be changed via a tab selector, which re-fetches.

## Components

### 1. ExerciseProgressController (`App\Http\Controllers\Client\ExerciseProgressController`)

Single `__invoke` method. Route: `GET /client/exercises/{exercise}/progress?range=90`

**Query parameters:**
- `range`: `30`, `90`, `365`, or `0` (all-time). Defaults to `90`. Invalid values fall back to `90`.

**Response JSON:**
```json
{
  "maxWeight": 120.0,
  "estimated1rm": 138.5,
  "weightChart": [{"date": "2026-01-05", "weight": 100.0}, ...],
  "volumeChart":  [{"date": "2026-01-05", "volume": 2400.0}, ...]
}
```

**PR rules (all-time, not range-limited):**
- `maxWeight` — `MAX(weight)` across all `ExerciseLogs` for this client and exercise
- `estimated1rm` — Epley formula: `weight × (1 + reps / 30)` per set; take the max; skip sets where `reps = 0`
- Both return `null` if the client has never logged this exercise

**Chart data (range-limited):**
- One data point per `WorkoutLog` session (grouped by `completed_at` date)
- `weightChart`: `MAX(weight)` per session
- `volumeChart`: `SUM(weight × reps)` per session
- Sorted ascending by date

**Authorization:** Queries are scoped to `auth()->id()` via the `ExerciseLog` relationship — no explicit policy needed.

### 2. Modal UI (program.blade.php and log-workout.blade.php)

A "Progress" section is added below the existing video/description content in both modals.

**Layout:**
- PR row: two stat boxes side by side — "Max Weight" | "Est. 1RM"
- Range selector: 30d / 90d / 1yr / All time tabs (default 90d)
- Weight chart (Chart.js line)
- Volume chart (Chart.js line)
- Loading spinner while fetching
- "No data yet" empty state if charts are empty

**Alpine.js behaviour:**
- When `selectedExercise` is set (modal opens), immediately fetch progress for that exercise at the default range
- Re-fetch when range tab changes
- Destroy and recreate Chart.js instances on each fetch to avoid canvas reuse errors

## Files Changed

| File | Change |
|------|--------|
| `app/Http/Controllers/Client/ExerciseProgressController.php` | New — JSON endpoint |
| `routes/web.php` | Add `GET client/exercises/{exercise}/progress` route |
| `resources/views/client/program.blade.php` | Add progress section to exercise modal |
| `resources/views/client/log-workout.blade.php` | Add progress section to exercise modal |
| `lang/en/client.php` (+ sl, hr) | Add translation keys for PR labels and range labels |
| `tests/Feature/Client/ExerciseProgressTest.php` | New — endpoint tests |

## Testing

- Returns `null` PRs and empty charts when client has no logs for the exercise
- `maxWeight` returns the highest weight across all sets and sessions
- `estimated1rm` uses Epley formula correctly; skips sets with `reps = 0`
- `range=30` limits chart data to last 30 days; PRs remain all-time
- `range=0` returns all-time chart data
- Invalid range defaults to 90 days
- Cannot see another client's data (scoped to auth user)
