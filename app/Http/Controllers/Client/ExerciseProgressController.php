<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseProgressController extends Controller
{
    public function __invoke(Request $request, Exercise $exercise): JsonResponse
    {
        $user = auth()->user();
        $range = (int) $request->get('range', 90);

        if (! in_array($range, [30, 90, 365, 0])) {
            $range = 90;
        }

        $allLogs = ExerciseLog::query()
            ->whereHas('workoutLog', fn ($q) => $q->where('client_id', $user->id))
            ->where('exercise_id', $exercise->id)
            ->get(['weight', 'reps']);

        $maxWeight = $allLogs->max('weight');

        $estimated1rm = $allLogs
            ->filter(fn ($log) => $log->reps > 0)
            ->map(fn ($log) => (float) $log->weight * (1 + $log->reps / 30))
            ->max();

        $chartLogs = ExerciseLog::query()
            ->whereHas('workoutLog', fn ($q) => $q
                ->where('client_id', $user->id)
                ->when($range > 0, fn ($q) => $q->where('completed_at', '>=', now()->subDays($range)->startOfDay()))
            )
            ->where('exercise_id', $exercise->id)
            ->with('workoutLog:id,completed_at')
            ->get();

        $grouped = $chartLogs
            ->groupBy(fn ($log) => $log->workoutLog->completed_at->format('Y-m-d'))
            ->sortKeys();

        $weightChart = $grouped->map(fn ($logs, $date) => [
            'date' => $date,
            'weight' => (float) $logs->max('weight'),
        ])->values()->all();

        $volumeChart = $grouped->map(fn ($logs, $date) => [
            'date' => $date,
            'volume' => (float) $logs->sum(fn ($l) => $l->weight * $l->reps),
        ])->values()->all();

        return response()->json([
            'maxWeight' => $maxWeight !== null ? (float) $maxWeight : null,
            'estimated1rm' => $estimated1rm !== null ? round($estimated1rm, 1) : null,
            'weightChart' => $weightChart,
            'volumeChart' => $volumeChart,
        ], 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }
}
