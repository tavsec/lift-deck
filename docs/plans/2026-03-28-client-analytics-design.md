# Client Analytics Design

**Date:** 2026-03-28

## Overview

Clients should be able to see their own metrics and charts. Rather than one unified analytics page, the analytics are distributed across existing sections of the client app for contextual relevance.

## Approach

Extract the coach analytics data-preparation logic into a shared `AnalyticsService`, then wire it into existing client controllers and views. The coach experience is unchanged; the service becomes the single source of truth for all chart/metrics logic.

## Components

### 1. AnalyticsService (`App\Services\AnalyticsService`)

Three public methods extracted from `Coach\AnalyticsController`:

- `getNutritionData(User $client, string $from, string $to): array` — returns `nutritionData` and `nutritionStats`
- `getCheckInChartData(User $client, string $from, string $to): array` — returns `checkInCharts`, `tableMetrics`, `checkInTableData`, `imageMetricData`, `imageMetrics`
- `getExerciseProgressionData(User $client, string $from, string $to): array` — returns `exerciseProgressionData`, `exercisesByMuscleGroup`, `exerciseTargetHistory`

The coach's `AnalyticsController` is refactored to call these methods. No behavior change for coaches.

### 2. Nutrition Tab

- `Client\NutritionController` calls `AnalyticsService::getNutritionData()` for the last 30 days.
- The existing nutrition blade gets a new collapsible section at the bottom with calories bar chart, macros stacked bar chart, and the summary stats row (avg calories, protein, carbs, fat, adherence).
- No date range filter — always shows last 30 days to keep it simple.

### 3. History Tab (Exercise Analytics)

- `Client\HistoryController::index()` calls `AnalyticsService::getExerciseProgressionData()` for the last 90 days.
- A new **"Exercise Progress"** section is added above the workout log list on the history page.
- Shows an exercise selector dropdown (grouped by muscle group) with a Chart.js line chart rendered inline.
- Summary row below chart: start weight → end weight, change %, sessions count.

### 4. Daily Metrics History Page

- New route: `GET /client/check-in/history` → `client.check-in.history`
- New method: `Client\CheckInController::history()` calls `AnalyticsService::getCheckInChartData()`.
- Default date range: last 30 days. Range selector: 7 / 14 / 30 / 90 days.
- Shows: number/scale metrics as line charts (grid), boolean/text metrics as table, progress photos if image metrics exist.
- A "View history →" link is added to the top of the existing check-in page.

## Files Changed

| File | Change |
|------|--------|
| `app/Services/AnalyticsService.php` | New — extracted logic |
| `app/Http/Controllers/Coach/AnalyticsController.php` | Refactored to use service |
| `app/Http/Controllers/Client/NutritionController.php` | Pass nutrition chart data to view |
| `app/Http/Controllers/Client/HistoryController.php` | Pass exercise progression data to view |
| `app/Http/Controllers/Client/CheckInController.php` | Add `history()` method |
| `resources/views/client/nutrition.blade.php` | Add charts section |
| `resources/views/client/history.blade.php` | Add exercise progress section |
| `resources/views/client/check-in.blade.php` | Add "View history" link |
| `resources/views/client/check-in-history.blade.php` | New — metrics history page |
| `routes/web.php` | Add `client.check-in.history` route |
| `lang/en/client.php` (+ sl, hr) | Add translation keys |

## Testing

- Feature tests for `Client\CheckInController::history()` (date range, empty state, with data)
- Feature tests for nutrition charts data in `Client\NutritionController`
- Feature tests for exercise progression data in `Client\HistoryController`
- Refactored coach analytics tests to verify service extraction didn't break existing behavior
