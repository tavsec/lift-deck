<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Level;
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

        $xpSummary = $user->xpSummary()->with('currentLevel')->first();
        $nextLevel = $xpSummary
            ? Level::where('xp_required', '>', $xpSummary->total_xp)->orderBy('xp_required')->first()
            : Level::orderBy('xp_required')->first();
        $recentAchievements = $user->achievements()->latest('user_achievements.earned_at')->limit(3)->get();

        return view('client.dashboard', [
            'coach' => $user->coach,
            'activeProgram' => $activeProgram,
            'weeklyWorkoutCount' => $weeklyWorkoutCount,
            'weeklyWorkoutTarget' => $weeklyWorkoutTarget,
            'lastWorkout' => $lastWorkout,
            'assignedMetricCount' => $assignedMetricCount,
            'todayLogCount' => $todayLogCount,
            'xpSummary' => $xpSummary,
            'nextLevel' => $nextLevel,
            'recentAchievements' => $recentAchievements,
        ]);
    }
}
