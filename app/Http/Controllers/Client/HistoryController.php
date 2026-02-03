<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutLogCommentRequest;
use App\Models\WorkoutLog;
use App\Models\WorkoutLogComment;
use App\Notifications\WorkoutLogCommented;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $workoutLogs = $user->workoutLogs()
            ->with('programWorkout')
            ->withCount('comments')
            ->latest('completed_at')
            ->paginate(15);

        // Workout log IDs with unread comments for this client
        $unreadWorkoutLogIds = $user->unreadNotifications
            ->where('type', WorkoutLogCommented::class)
            ->pluck('data.workout_log_id')
            ->filter()
            ->unique();

        return view('client.history', [
            'workoutLogs' => $workoutLogs,
            'unreadWorkoutLogIds' => $unreadWorkoutLogIds,
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
            'comments.user',
        ]);

        // Mark notifications for this workout log as read
        auth()->user()->unreadNotifications
            ->where('type', WorkoutLogCommented::class)
            ->filter(fn ($n) => ($n->data['workout_log_id'] ?? null) === $workoutLog->id)
            ->each->markAsRead();

        return view('client.history-show', [
            'workoutLog' => $workoutLog,
        ]);
    }

    public function comment(StoreWorkoutLogCommentRequest $request, WorkoutLog $workoutLog): RedirectResponse
    {
        if ($workoutLog->client_id !== auth()->id()) {
            abort(403);
        }

        $comment = WorkoutLogComment::create([
            'workout_log_id' => $workoutLog->id,
            'user_id' => auth()->id(),
            'body' => $request->validated('body'),
        ]);

        $workoutLog->load('programWorkout');

        // Notify the coach
        $coach = auth()->user()->coach;
        if ($coach) {
            $coach->notify(new WorkoutLogCommented($comment, $workoutLog));
        }

        return redirect()->route('client.history.show', $workoutLog)
            ->with('success', 'Comment added.');
    }
}
