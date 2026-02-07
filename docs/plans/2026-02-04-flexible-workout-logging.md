# Flexible Workout Logging

## Overview
Allow clients to modify program workouts (add/remove exercises, adjust sets) when logging, and log completely custom workouts not tied to any program.

## Data Model Changes

### Migration: make program references nullable, add custom_name
- `workout_logs.program_workout_id` — nullable (null = custom workout)
- `workout_logs.client_program_id` — nullable (null = custom workout)
- `workout_logs.custom_name` — new nullable string column for custom workout name
- `exercise_logs.workout_exercise_id` — nullable (null = client-added exercise)

## UI Flow

### Log page (client/log.blade.php)
- Existing program workout list stays
- New "Custom Workout" button below the list

### Log workout form (client/log-workout.blade.php)
Alpine.js-powered dynamic form:
- **Program-based**: Pre-populated with program exercises, each with "Remove" button. "Add Exercise" button at bottom.
- **Custom**: Starts empty with workout name input. Add exercises via picker.
- **Exercise picker**: Searchable select from coach's exercise library, grouped by muscle group.
- **Set management**: +/- buttons per exercise to add/remove sets.

### Controller changes
- New `LogController::createCustom()` for custom workout form
- New API endpoint to fetch coach exercises (JSON)
- Updated `LogController::store()` to handle both types
- Updated `StoreWorkoutLogRequest` for flexible validation

## Key Decisions
- Changes are log-only, never modify the program template
- Client picks exercises from coach's library only (no free-text)
- `workout_exercise_id` is null for client-added exercises
