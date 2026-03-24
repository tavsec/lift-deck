<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClientProgram;
use App\Models\ClientProgramExerciseTarget;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientProgramTargetController extends Controller
{
    public function edit(Program $program, ClientProgram $clientProgram): View
    {
        if ($program->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($clientProgram->program_id !== $program->id) {
            abort(403);
        }

        $program->load('workouts.exercises.exercise');
        $clientProgram->load(['client', 'exerciseTargets']);

        $targetsByExercise = $clientProgram->exerciseTargets
            ->groupBy('workout_exercise_id')
            ->map(fn ($targets) => $targets->keyBy('set_number'));

        return view('coach.programs.targets', compact('program', 'clientProgram', 'targetsByExercise'));
    }

    public function update(Request $request, Program $program, ClientProgram $clientProgram): RedirectResponse
    {
        if ($program->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($clientProgram->program_id !== $program->id) {
            abort(403);
        }

        $program->loadMissing('workouts.exercises');

        $validExercises = $program->workouts
            ->flatMap(fn ($workout) => $workout->exercises)
            ->keyBy('id');

        $validated = $request->validate([
            'targets' => ['nullable', 'array'],
            'targets.*' => ['nullable', 'array'],
            'targets.*.*' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ]);

        foreach ($validated['targets'] ?? [] as $workoutExerciseId => $sets) {
            $workoutExercise = $validExercises->get($workoutExerciseId);

            if (! $workoutExercise) {
                continue;
            }

            foreach ($sets as $setNumber => $weight) {
                if ($setNumber < 1 || $setNumber > $workoutExercise->sets) {
                    continue;
                }

                if ($weight === null) {
                    ClientProgramExerciseTarget::where('client_program_id', $clientProgram->id)
                        ->where('workout_exercise_id', $workoutExerciseId)
                        ->where('set_number', $setNumber)
                        ->delete();

                    continue;
                }

                ClientProgramExerciseTarget::updateOrCreate(
                    [
                        'client_program_id' => $clientProgram->id,
                        'workout_exercise_id' => $workoutExerciseId,
                        'set_number' => $setNumber,
                    ],
                    ['target_weight' => $weight]
                );
            }
        }

        $clientProgram->loadMissing('client');

        return redirect()->route('coach.programs.assignments.targets.edit', [$program, $clientProgram])
            ->with('success', 'Target weights updated for '.$clientProgram->client->name.'.');
    }
}
