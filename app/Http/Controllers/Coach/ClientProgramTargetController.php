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

        // Key targets by workout_exercise_id for easy lookup in the view
        $targetsByExercise = $clientProgram->exerciseTargets
            ->keyBy('workout_exercise_id');

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

        $validated = $request->validate([
            'targets' => ['nullable', 'array'],
            'targets.*' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ]);

        foreach ($validated['targets'] ?? [] as $workoutExerciseId => $weight) {
            if ($weight === null) {
                // Remove target if cleared
                ClientProgramExerciseTarget::where('client_program_id', $clientProgram->id)
                    ->where('workout_exercise_id', $workoutExerciseId)
                    ->delete();

                continue;
            }

            ClientProgramExerciseTarget::updateOrCreate(
                [
                    'client_program_id' => $clientProgram->id,
                    'workout_exercise_id' => $workoutExerciseId,
                ],
                ['target_weight' => $weight]
            );
        }

        return redirect()->route('coach.programs.assignments.targets.edit', [$program, $clientProgram])
            ->with('success', 'Target weights updated for '.$clientProgram->client->name.'.');
    }
}
