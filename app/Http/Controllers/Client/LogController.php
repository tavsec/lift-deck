<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutLogRequest;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\ProgramWorkout;
use App\Models\WorkoutLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $activeProgram = $user->activeProgram()?->load('program.workouts.exercises.exercise');

        return view('client.log', [
            'activeProgram' => $activeProgram,
        ]);
    }

    public function create(ProgramWorkout $workout): View
    {
        $user = auth()->user();
        $activeProgram = $user->activeProgram();

        // Ensure the workout belongs to the client's active program
        if (! $activeProgram || $workout->program_id !== $activeProgram->program_id) {
            abort(403);
        }

        $workout->load('exercises.exercise');

        // Find the most recent log for this same workout
        $previousLog = WorkoutLog::where('client_id', $user->id)
            ->where('program_workout_id', $workout->id)
            ->latest('completed_at')
            ->first();

        $previousSets = collect();
        if ($previousLog) {
            $previousSets = $previousLog->exerciseLogs
                ->groupBy('exercise_id')
                ->map(fn ($logs) => $logs->sortBy('set_number')->values()->map(fn ($log) => [
                    'weight' => $log->weight,
                    'reps' => $log->reps,
                ])->all());
        }

        // Batch fallback: for exercises missing from the same-workout log,
        // find their most recent log from ANY workout in one query
        $allExerciseIds = $workout->exercises->pluck('exercise_id');
        $missingExerciseIds = $allExerciseIds->diff($previousSets->keys());

        if ($missingExerciseIds->isNotEmpty()) {
            $fallbackLogs = ExerciseLog::whereIn('exercise_id', $missingExerciseIds)
                ->whereHas('workoutLog', fn ($q) => $q->where('client_id', $user->id))
                ->orderByDesc('created_at')
                ->get()
                ->groupBy('exercise_id')
                ->map(function ($logs) {
                    // Take only the sets from the most recent workout log
                    $latestLogId = $logs->first()->workout_log_id;

                    return $logs->where('workout_log_id', $latestLogId)
                        ->sortBy('set_number')
                        ->values()
                        ->map(fn ($log) => [
                            'weight' => $log->weight,
                            'reps' => $log->reps,
                        ])->all();
                });

            $previousSets = $previousSets->merge($fallbackLogs);
        }

        // Pre-build exercise data for Alpine.js
        $exercisesData = $workout->exercises->map(fn ($we) => [
            'workout_exercise_id' => $we->id,
            'exercise_id' => $we->exercise_id,
            'name' => $we->exercise->name,
            'muscle_group' => $we->exercise->muscle_group,
            'prescribed_sets' => $we->sets,
            'prescribed_reps' => $we->reps,
            'previous_sets' => $previousSets->get($we->exercise_id, []),
            'sets' => collect(range(1, $we->sets))->map(fn ($i) => [
                'weight' => 0,
                'reps' => 0,
            ])->values()->all(),
        ])->values()->all();

        return view('client.log-workout', [
            'workout' => $workout,
            'activeProgram' => $activeProgram,
            'exercisesData' => $exercisesData,
            'isCustom' => false,
        ]);
    }

    public function createCustom(): View
    {
        return view('client.log-workout', [
            'workout' => null,
            'activeProgram' => null,
            'exercisesData' => [],
            'isCustom' => true,
        ]);
    }

    /**
     * Search exercises from the client's coach's library.
     */
    public function exercises(): JsonResponse
    {
        $user = auth()->user();
        $coachId = $user->coach_id;

        if (! $coachId) {
            return response()->json([]);
        }

        $exercises = Exercise::where('is_active', true)
            ->where(function ($query) use ($coachId) {
                $query->where('coach_id', $coachId)
                    ->orWhereNull('coach_id');
            })
            ->orderBy('muscle_group')
            ->orderBy('name')
            ->get(['id', 'name', 'muscle_group']);

        // Get the most recent workout_log_id per exercise for this client
        $lastLogIds = ExerciseLog::query()
            ->selectRaw('exercise_id, MAX(workout_log_id) as last_log_id')
            ->whereHas('workoutLog', fn ($q) => $q->where('client_id', $user->id))
            ->whereIn('exercise_id', $exercises->pluck('id'))
            ->groupBy('exercise_id')
            ->pluck('last_log_id', 'exercise_id');

        $previousSets = collect();
        if ($lastLogIds->isNotEmpty()) {
            $previousSets = ExerciseLog::query()
                ->whereIn('workout_log_id', $lastLogIds->values())
                ->whereIn('exercise_id', $lastLogIds->keys())
                ->where(function ($q) use ($lastLogIds) {
                    foreach ($lastLogIds as $exerciseId => $logId) {
                        $q->orWhere(function ($q2) use ($exerciseId, $logId) {
                            $q2->where('exercise_id', $exerciseId)
                                ->where('workout_log_id', $logId);
                        });
                    }
                })
                ->orderBy('set_number')
                ->get()
                ->groupBy('exercise_id')
                ->map(fn ($logs) => $logs->map(fn ($log) => [
                    'weight' => $log->weight,
                    'reps' => $log->reps,
                ])->values()->all());
        }

        $result = $exercises->map(fn ($exercise) => [
            'id' => $exercise->id,
            'name' => $exercise->name,
            'muscle_group' => $exercise->muscle_group,
            'previous_sets' => $previousSets->get($exercise->id, []),
        ]);

        return response()->json($result);
    }

    public function store(StoreWorkoutLogRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        $isCustom = empty($validated['program_workout_id']);

        if ($isCustom) {
            $workoutLog = WorkoutLog::create([
                'client_id' => $user->id,
                'client_program_id' => null,
                'program_workout_id' => null,
                'custom_name' => $validated['custom_name'],
                'completed_at' => $validated['completed_at'] ?? now(),
                'notes' => $validated['notes'] ?? null,
            ]);
        } else {
            $activeProgram = $user->activeProgram();

            if (! $activeProgram) {
                return redirect()->route('client.log')
                    ->with('error', 'No active program found.');
            }

            $workout = ProgramWorkout::findOrFail($validated['program_workout_id']);

            if ($workout->program_id !== $activeProgram->program_id) {
                abort(403);
            }

            $workoutLog = WorkoutLog::create([
                'client_id' => $user->id,
                'client_program_id' => $activeProgram->id,
                'program_workout_id' => $workout->id,
                'completed_at' => $validated['completed_at'] ?? now(),
                'notes' => $validated['notes'] ?? null,
            ]);
        }

        foreach ($validated['exercises'] as $exerciseData) {
            foreach ($exerciseData['sets'] as $setIndex => $setData) {
                if ($setData['weight'] == 0 || $setData['reps'] == 0) {
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

        return redirect()->route('client.history')
            ->with('success', 'Workout logged!');
    }
}
