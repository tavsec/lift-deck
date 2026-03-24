<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $activeProgram = $user->activeProgram()?->load('program.workouts.exercises.exercise');

        $currentTargets = collect();
        $targetHistory = collect();

        if ($activeProgram) {
            $allTargets = $activeProgram->exerciseTargets()
                ->orderByDesc('effective_date')
                ->get();

            $currentTargets = $allTargets
                ->groupBy('workout_exercise_id')
                ->map(fn ($targets) => $targets
                    ->groupBy('set_number')
                    ->map(fn ($setTargets) => $setTargets->first())
                );

            $targetHistory = $allTargets->groupBy('workout_exercise_id');
        }

        return view('client.program', [
            'activeProgram' => $activeProgram,
            'currentTargets' => $currentTargets,
            'targetHistory' => $targetHistory,
        ]);
    }
}
