# Client Logging QoL Improvements

## Feature 1: Empty Set Rows

**Problem:** Sets are pre-filled with `weight: 0, reps: 0`, forcing clients to delete them before logging.

**Fix:**
- `LogController::create()`: change set initialization from `['weight' => 0, 'reps' => 0]` to `['weight' => '', 'reps' => '']`
- `workoutLogger.addSet()` in the blade view: change `{ weight: 0, reps: 0 }` to `{ weight: '', reps: '' }`
- Server-side store already skips sets where weight or reps == 0, so empty strings coercing to 0 are safe

## Feature 2: Coach Can Lock Exercise Removal Per Workout Day

**Schema:** Add `lock_exercise_removal` boolean (default `false`) to `program_workouts` table.

**Model:** Add to `ProgramWorkout` fillable + boolean cast.

**Coach UI:** Inline checkbox on each workout day header in the program edit view. Small POST form to a new route that updates the flag.

**Client UI:** `lock_removal` flag passed through `$exercisesData` (same value for all exercises in the workout). Remove button hidden when `exercise.lock_removal` is true.

**Controller:** `LogController::create()` appends `'lock_removal' => $workout->lock_exercise_removal` to each exercise entry.

## Feature 3: Offline State / Progress Auto-Save

**Storage:** `localStorage` keyed by `workout_logger_{workoutId}` (or `workout_logger_custom`). Stores `{ exercises, notes, customName, savedAt }`.

**Auto-save:** Alpine `$watch` on exercises (deep) + notes, debounced 800ms.

**On load:**
1. Check localStorage for matching key
2. If found and `savedAt` within 24h → show restore banner: "You have an unfinished workout from [HH:MM]. Continue where you left off?" with Restore / Start Fresh buttons
3. Restore → replace state, dismiss banner
4. Start Fresh → clear saved entry, dismiss banner

**Offline indicator:** Slim yellow sticky banner: "You're offline — your progress is being saved locally." Toggled by `window online/offline` events.

**On submit:** `@submit` handler calls `clearSavedState()` before form posts to clear localStorage entry.
