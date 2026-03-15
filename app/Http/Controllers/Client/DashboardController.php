<?php

namespace App\Http\Controllers\Client;

use App\Features\Loyalty;
use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\View\View;
use Laravel\Pennant\Feature;

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

        $coach = $user->coach;
        $loyaltyEnabled = $coach && Feature::for($coach)->active(Loyalty::class);

        $xpSummary = $loyaltyEnabled ? $user->xpSummary()->with('currentLevel')->first() : null;
        $nextLevel = $loyaltyEnabled && $xpSummary
            ? Level::where('xp_required', '>', $xpSummary->total_xp)->orderBy('xp_required')->first()
            : ($loyaltyEnabled ? Level::orderBy('xp_required')->first() : null);
        $recentAchievements = $loyaltyEnabled
            ? $user->achievements()->latest('user_achievements.earned_at')->limit(3)->get()
            : collect();

        return view('client.dashboard', [
            'coach' => $coach,
            'loyaltyEnabled' => $loyaltyEnabled,
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
