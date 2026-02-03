<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\WorkoutLog;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $workoutLogs = $user->workoutLogs()
            ->with('programWorkout')
            ->latest('completed_at')
            ->paginate(15);

        return view('client.history', [
            'workoutLogs' => $workoutLogs,
        ]);
    }

    public function show(WorkoutLog $workoutLog): View
    {
        if ($workoutLog->client_id !== auth()->id()) {
            abort(403);
        }

        $workoutLog->load([
            'programWorkout',
            'exerciseLogs.exercise',
            'exerciseLogs.workoutExercise',
        ]);

        return view('client.history-show', [
            'workoutLog' => $workoutLog,
        ]);
    }
}
