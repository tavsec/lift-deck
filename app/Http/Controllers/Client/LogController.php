<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutLogRequest;
use App\Models\ExerciseLog;
use App\Models\ProgramWorkout;
use App\Models\WorkoutLog;
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

        return view('client.log-workout', [
            'workout' => $workout,
            'activeProgram' => $activeProgram,
        ]);
    }

    public function store(StoreWorkoutLogRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $activeProgram = $user->activeProgram();

        if (! $activeProgram) {
            return redirect()->route('client.log')
                ->with('error', 'No active program found.');
        }

        $validated = $request->validated();

        $workout = ProgramWorkout::findOrFail($validated['program_workout_id']);

        // Ensure the workout belongs to the client's active program
        if ($workout->program_id !== $activeProgram->program_id) {
            abort(403);
        }

        $workoutLog = WorkoutLog::create([
            'client_id' => $user->id,
            'client_program_id' => $activeProgram->id,
            'program_workout_id' => $workout->id,
            'completed_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['exercises'] as $exerciseData) {
            foreach ($exerciseData['sets'] as $setIndex => $setData) {
                ExerciseLog::create([
                    'workout_log_id' => $workoutLog->id,
                    'workout_exercise_id' => $exerciseData['workout_exercise_id'],
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
