<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\WorkoutLog;
use App\Models\WorkoutLogComment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $clientIds = $user->clients()->pluck('id');

        $stats = [
            'total_clients' => $clientIds->count(),
            'active_clients' => $clientIds->count(),
            'unread_messages' => $user->unreadMessagesCount(),
            'programs' => $user->programs()->count(),
        ];

        // Recent workout logs from all clients
        $recentWorkoutLogs = WorkoutLog::whereIn('client_id', $clientIds)
            ->with(['client', 'programWorkout'])
            ->latest('completed_at')
            ->limit(10)
            ->get();

        // Recent comments from clients on workout logs
        $recentComments = WorkoutLogComment::whereIn('user_id', $clientIds)
            ->with(['user', 'workoutLog.client', 'workoutLog.programWorkout'])
            ->latest()
            ->limit(10)
            ->get();

        return view('coach.dashboard', compact('stats', 'recentWorkoutLogs', 'recentComments'));
    }
}
