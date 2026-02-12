# Exercise Reordering & Previous Workout Data

## Overview

Two features for the client workout logging form:
1. Clients can reorder exercises during logging (drag-and-drop + arrow buttons)
2. Each exercise shows data from the client's previous session

## Feature 1: Exercise Reordering

### Approach
- Session-only reorder (no backend persistence of client order)
- SortableJS library for drag-and-drop (touch + mouse)
- Up/down arrow buttons as accessibility fallback

### Frontend Changes (`log-workout.blade.php`)

**SortableJS setup:**
- Install `sortablejs` via npm, import in `app.js`
- Initialize Sortable on the exercises container via `x-init`
- On drag end, sync DOM order back to Alpine `exercises` array

**UI per exercise card:**
- Left: drag handle (6-dot grip icon)
- Right (next to remove button): up/down arrow buttons
- Up disabled on first exercise, down disabled on last

**New Alpine.js methods:**
- `moveExerciseUp(index)` — swap with previous
- `moveExerciseDown(index)` — swap with next
- `onSortEnd(oldIndex, newIndex)` — called by SortableJS, splices array

### Backend Changes
None. The form already submits exercises as an indexed array.

## Feature 2: Previous Workout Data

### Data Lookup Logic

**For program workouts (`LogController::create()`):**
1. Find the most recent completed `WorkoutLog` for same `program_workout_id` + `client_id`
2. Get `ExerciseLog` entries from that log, grouped by `exercise_id`
3. For exercises not found in step 2, fall back to most recent `ExerciseLog` for that `exercise_id` across all client workout logs

**For the exercises endpoint (`LogController::exercises()`):**
- Extend response to include `previous_sets` per exercise
- Query: most recent `ExerciseLog` entries for each exercise, for the current client
- Used by both custom workouts and program workouts when adding new exercises via picker

### Data Shape

Each exercise in Alpine state gets `previous_sets`:
```js
{
  exercise_id: 12,
  name: "Bench Press",
  previous_sets: [
    { weight: 80, reps: 10 },
    { weight: 85, reps: 8 }
  ]
}
```

### Previous Data Sources by Scenario

| Scenario | Source |
|----------|--------|
| Program workout, existing exercise | Last log of same `program_workout_id` |
| Program workout, newly added exercise | Exercises endpoint (last log of any workout) |
| Custom workout | Exercises endpoint (last log of any workout) |

### Frontend Display

Above the sets table in each exercise card:
```
Last session: 80kg × 10, 85kg × 8, 85kg × 7
```
- `text-xs text-gray-500`, only shown when `previous_sets` is non-empty
- Comma-separated `{weight}kg × {reps}` per set

## Files to Modify

- `package.json` — add `sortablejs`
- `resources/js/app.js` — import SortableJS
- `resources/views/client/log-workout.blade.php` — reorder UI + previous data display
- `app/Http/Controllers/Client/LogController.php` — previous data queries in `create()` and `exercises()`

## Files NOT Modified

- No new database tables or migrations
- No changes to models or form request validation
- No changes to the `store()` method
