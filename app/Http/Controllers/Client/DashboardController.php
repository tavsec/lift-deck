<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $activeProgram = $user->activeProgram()?->load('program');

        $weeklyWorkoutCount = $user->workoutLogs()
            ->where('completed_at', '>=', now()->startOfWeek())
            ->count();

        $weeklyWorkoutTarget = $activeProgram?->program?->workouts()?->count() ?? 0;

        $lastWorkout = $user->workoutLogs()
            ->with('programWorkout')
            ->latest('completed_at')
            ->first();

        // Check-in status
        $assignedMetricCount = $user->assignedTrackingMetrics()->count();
        $todayLogCount = $assignedMetricCount > 0
            ? $user->dailyLogs()->whereDate('date', now()->toDateString())->count()
            : 0;

        return view('client.dashboard', [
            'coach' => $user->coach,
            'activeProgram' => $activeProgram,
            'weeklyWorkoutCount' => $weeklyWorkoutCount,
            'weeklyWorkoutTarget' => $weeklyWorkoutTarget,
            'lastWorkout' => $lastWorkout,
            'assignedMetricCount' => $assignedMetricCount,
            'todayLogCount' => $todayLogCount,
        ]);
    }
}
