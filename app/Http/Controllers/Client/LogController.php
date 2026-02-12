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

        // Pre-build exercise data for Alpine.js
        $exercisesData = $workout->exercises->map(fn ($we) => [
            'workout_exercise_id' => $we->id,
            'exercise_id' => $we->exercise_id,
            'name' => $we->exercise->name,
            'muscle_group' => $we->exercise->muscle_group,
            'prescribed_sets' => $we->sets,
            'prescribed_reps' => $we->reps,
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

        return response()->json($exercises);
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
                if($setData['weight'] == 0 || $setData['reps'] == 0) continue;
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
