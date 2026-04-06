# Coach Metrics Onboarding Popup — Design

**Date:** 2026-03-26
**Branch:** 23-add-basic-metrics-on-coach-sign-up

## Overview

When a coach signs in for the first time, show a modal popup asking whether they want default tracking metrics seeded for their account. If yes, create 6 localized metrics. Either way, record the decision so the popup never shows again.

## Data Layer

### Migration
Add `metrics_onboarded_at` (nullable timestamp) to `users`.
- `null` → popup not yet shown
- set to `now()` on either "yes" or "no" response

### Default Metrics (6)
| Name | Type | Unit |
|------|------|------|
| Weight | number | kg |
| Steps | number | steps |
| Progress Image | image | — |
| Mood | scale | — |
| Energy | scale | — |
| Sleep | scale | — |

### `TrackingMetric::seedDefaults(int $coachId, string $locale = 'en')`
Updated to accept locale, resolve metric names via `__()` in the given locale, and create only the 6 metrics above.

## Controller

**`App\Http\Controllers\Coach\MetricsSetupController`** (single `__invoke`)

- Validates `setup` (boolean)
- Always sets `auth()->user()->metrics_onboarded_at = now()`
- If `setup = true`: calls `TrackingMetric::seedDefaults($coach->id, $coach->locale ?? 'en')`
- Redirects to `coach.dashboard` with flash message

### Flash Messages
- **Yes:** "Default metrics added. You can manage them in the Tracking section."
- **No:** "No problem. You can create metrics anytime in the Tracking section."

## Routes

```
POST /coach/metrics-setup  →  Coach\MetricsSetupController  (name: coach.metrics-setup)
```

## Translations

Keys added to `lang/{en,hr,sl}/coach.php` under:

- `coach.metrics_setup.title`
- `coach.metrics_setup.description`
- `coach.metrics_setup.yes`
- `coach.metrics_setup.skip`
- `coach.metrics_setup.seeded` (flash — yes path)
- `coach.metrics_setup.skipped` (flash — no path)
- `coach.default_metrics.weight`
- `coach.default_metrics.steps`
- `coach.default_metrics.progress_image`
- `coach.default_metrics.mood`
- `coach.default_metrics.energy`
- `coach.default_metrics.sleep`

## UI

**Location:** `resources/views/coach/dashboard.blade.php`

Alpine.js modal (`x-data="{ open: true }" x-show="open"`) rendered only when `auth()->user()->metrics_onboarded_at === null`. Modal is not dismissable by clicking outside — the coach must choose one option.

Two buttons:
- **Yes, add them** → `POST /coach/metrics-setup` with `setup=1`
- **Skip for now** → `POST /coach/metrics-setup` with `setup=0`

After redirect, standard flash banner on dashboard confirms the action and links to the Tracking section.
