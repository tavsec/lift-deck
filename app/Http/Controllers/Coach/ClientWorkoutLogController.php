<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientWorkoutLogRequest;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientWorkoutLogController extends Controller
{
    public function create(User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $exercises = Exercise::where('is_active', true)
            ->where(function ($query) {
                $query->where('coach_id', auth()->id())
                    ->orWhereNull('coach_id');
            })
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get(['id', 'name', 'muscle_group', 'description', 'video_url']);

        return view('coach.clients.workout-log-form', compact('client', 'exercises'));
    }

    public function store(StoreClientWorkoutLogRequest $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        $workoutLog = WorkoutLog::create([
            'client_id' => $client->id,
            'custom_name' => $validated['custom_name'] ?? 'Custom Workout',
            'completed_at' => $validated['completed_at'] ?? now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['exercises'] ?? [] as $exerciseData) {
            foreach ($exerciseData['sets'] ?? [] as $setIndex => $setData) {
                if (empty($setData['reps'])) {
                    continue;
                }

                ExerciseLog::create([
                    'workout_log_id' => $workoutLog->id,
                    'workout_exercise_id' => $exerciseData['workout_exercise_id'] ?? null,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'set_number' => $setIndex + 1,
                    'weight' => $setData['weight'] ?? null,
                    'reps' => $setData['reps'],
                ]);
            }
        }

        return redirect()->route('coach.clients.show', $client)
            ->with('success', 'Workout logged for client.');
    }

    public function edit(User $client, WorkoutLog $workoutLog): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($workoutLog->client_id !== $client->id) {
            abort(403);
        }

        $workoutLog->load('exerciseLogs.exercise');

        $exercises = Exercise::where('is_active', true)
            ->where(function ($query) {
                $query->where('coach_id', auth()->id())
                    ->orWhereNull('coach_id');
            })
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get(['id', 'name', 'muscle_group', 'description', 'video_url']);

        return view('coach.clients.workout-log-form', compact('client', 'workoutLog', 'exercises'));
    }

    public function update(StoreClientWorkoutLogRequest $request, User $client, WorkoutLog $workoutLog): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($workoutLog->client_id !== $client->id) {
            abort(403);
        }

        $validated = $request->validated();

        $workoutLog->update([
            'custom_name' => $validated['custom_name'] ?? $workoutLog->custom_name,
            'completed_at' => $validated['completed_at'] ?? $workoutLog->completed_at,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Replace all exercise logs
        $workoutLog->exerciseLogs()->delete();

        foreach ($validated['exercises'] ?? [] as $exerciseData) {
            foreach ($exerciseData['sets'] ?? [] as $setIndex => $setData) {
                if (empty($setData['reps'])) {
                    continue;
                }

                ExerciseLog::create([
                    'workout_log_id' => $workoutLog->id,
                    'workout_exercise_id' => $exerciseData['workout_exercise_id'] ?? null,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'set_number' => $setIndex + 1,
                    'weight' => $setData['weight'] ?? null,
                    'reps' => $setData['reps'],
                ]);
            }
        }

        return redirect()->route('coach.clients.show', $client)
            ->with('success', 'Workout log updated.');
    }

    public function destroy(User $client, WorkoutLog $workoutLog): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($workoutLog->client_id !== $client->id) {
            abort(403);
        }

        $workoutLog->delete();

        return redirect()->route('coach.clients.show', $client)
            ->with('success', 'Workout log deleted.');
    }
}
