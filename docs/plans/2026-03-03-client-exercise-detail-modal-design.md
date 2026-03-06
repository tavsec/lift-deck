# Client Exercise Detail Modal — Design

**Date:** 2026-03-03

## Problem

Clients viewing their assigned program can see exercise name, sets × reps, rest, and coach notes inline — but have no way to access the exercise description or instructional video assigned by the coach.

## Solution

Add a tap-to-open modal on exercise names in the client program view (`client/program.blade.php`). The modal shows the exercise's description and YouTube video embed so the client knows exactly how to perform the exercise.

## Scope

- Modify: `resources/views/client/program.blade.php`
- No new routes, controllers, or models needed
- Data already available via eager loading: `program.workouts.exercises.exercise`

## UI Design

Exercise names in the workout list become tappable buttons. Tapping opens a mobile-optimised bottom-sheet modal:

```
+-----------------------------+
| Squat                   [X] |
| [Quads badge]               |
|                             |
| [VIDEO EMBED (16:9)         |
|  or No video placeholder]   |
|                             |
| Description:                |
| Keep your back straight...  |
| (or "No description")       |
+-----------------------------+
```

## Interaction

- Tap exercise name → opens modal with that exercise's data
- Tap backdrop or × button → closes modal
- `x-data` Alpine component holds `{ selectedExercise: null }`
- Exercise data passed as JSON via Alpine `@click` handler

## Mobile

- Fixed bottom sheet (`fixed inset-x-0 bottom-0 rounded-t-2xl`) — natural mobile pattern
- Max height with overflow-y-auto for long descriptions
- Video stays 16:9 aspect ratio on all screen widths
- Backdrop (`fixed inset-0 bg-black/50`) closes on click

## Constraints

- Only show video/description if they exist; graceful fallback if neither
- Muscle group shown as badge (read-only)
- No edit/delete actions — client view only
