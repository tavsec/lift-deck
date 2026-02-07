<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutLogCommentRequest;
use App\Models\ClientInvitation;
use App\Models\ClientTrackingMetric;
use App\Models\MacroGoal;
use App\Models\User;
use App\Models\WorkoutLog;
use App\Models\WorkoutLogComment;
use App\Notifications\WorkoutLogCommented;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request): View
    {
        $coach = auth()->user();

        $query = $coach->clients()->with('clientProfile');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(10);

        $pendingInvitations = $coach->sentInvitations()
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->get();

        // Collect client IDs that have unread workout log comments
        $unreadWorkoutLogIds = $coach->unreadNotifications
            ->where('type', WorkoutLogCommented::class)
            ->pluck('data.workout_log_id')
            ->filter()
            ->unique();

        $clientIdsWithUnread = $unreadWorkoutLogIds->isNotEmpty()
            ? WorkoutLog::whereIn('id', $unreadWorkoutLogIds)->pluck('client_id')->unique()
            : collect();

        return view('coach.clients.index', compact('clients', 'pendingInvitations', 'clientIdsWithUnread'));
    }

    /**
     * Show the form for inviting a new client.
     */
    public function create(): View
    {
        return view('coach.clients.create');
    }

    /**
     * Generate a new invitation code.
     */
    public function store(Request $request): RedirectResponse
    {
        $coach = auth()->user();

        $invitation = ClientInvitation::create([
            'coach_id' => $coach->id,
            'token' => ClientInvitation::generateUniqueToken(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->route('coach.clients.index')
            ->with('success', 'Invitation code generated!')
            ->with('invitation_code', $invitation->token);
    }

    /**
     * Display the specified client.
     */
    public function show(User $client): View
    {
        // Ensure this client belongs to the coach
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $client->load(['clientProfile', 'clientPrograms' => function ($query) {
            $query->active()->with('program');
        }]);

        $activeProgram = $client->clientPrograms->first();

        $recentWorkoutLogs = $client->workoutLogs()
            ->with('programWorkout')
            ->withCount('comments')
            ->latest('completed_at')
            ->limit(5)
            ->get();

        // Workout log IDs with unread comments for this coach
        $unreadWorkoutLogIds = auth()->user()->unreadNotifications
            ->where('type', WorkoutLogCommented::class)
            ->pluck('data.workout_log_id')
            ->filter()
            ->unique();

        // Tracking metrics
        $coachMetrics = auth()->user()->trackingMetrics()->where('is_active', true)->get();
        $assignedMetricIds = $client->assignedTrackingMetrics()->pluck('tracking_metric_id');

        // Last 7 days of daily logs
        $recentDailyLogs = $client->dailyLogs()
            ->with('trackingMetric')
            ->whereDate('date', '>=', now()->subDays(6)->toDateString())
            ->orderBy('date')
            ->get()
            ->groupBy(fn ($log) => $log->date->format('Y-m-d'));

        $currentMacroGoal = MacroGoal::activeForClient($client->id, now()->format('Y-m-d'));
        $todayMealTotals = $client->mealLogs()
            ->whereDate('date', now())
            ->selectRaw('COALESCE(SUM(calories), 0) as calories, COALESCE(SUM(protein), 0) as protein, COALESCE(SUM(carbs), 0) as carbs, COALESCE(SUM(fat), 0) as fat')
            ->first();

        return view('coach.clients.show', compact(
            'client',
            'activeProgram',
            'recentWorkoutLogs',
            'unreadWorkoutLogIds',
            'coachMetrics',
            'assignedMetricIds',
            'recentDailyLogs',
            'currentMacroGoal',
            'todayMealTotals',
        ));
    }

    /**
     * Display a client's workout log detail.
     */
    public function workoutLog(User $client, WorkoutLog $workoutLog): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($workoutLog->client_id !== $client->id) {
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

        return view('coach.clients.workout-log', compact('client', 'workoutLog'));
    }

    /**
     * Store a comment on a client's workout log.
     */
    public function workoutLogComment(StoreWorkoutLogCommentRequest $request, User $client, WorkoutLog $workoutLog): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($workoutLog->client_id !== $client->id) {
            abort(403);
        }

        $comment = WorkoutLogComment::create([
            'workout_log_id' => $workoutLog->id,
            'user_id' => auth()->id(),
            'body' => $request->validated('body'),
        ]);

        $workoutLog->load('programWorkout');

        // Notify the client
        $client->notify(new WorkoutLogCommented($comment, $workoutLog));

        return redirect()->route('coach.clients.workout-log', [$client, $workoutLog])
            ->with('success', 'Comment added.');
    }

    /**
     * Toggle a tracking metric assignment for a client.
     */
    public function toggleMetric(Request $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'tracking_metric_id' => ['required', 'exists:tracking_metrics,id'],
        ]);

        $metric = auth()->user()->trackingMetrics()->findOrFail($validated['tracking_metric_id']);

        $existing = ClientTrackingMetric::where('client_id', $client->id)
            ->where('tracking_metric_id', $metric->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            $maxOrder = $client->assignedTrackingMetrics()->max('order') ?? 0;
            ClientTrackingMetric::create([
                'client_id' => $client->id,
                'tracking_metric_id' => $metric->id,
                'order' => $maxOrder + 1,
            ]);
        }

        return redirect()->route('coach.clients.show', $client)
            ->with('success', $existing ? 'Metric removed from client.' : 'Metric assigned to client.');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $client->load('clientProfile');

        return view('coach.clients.edit', compact('client'));
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $client->update($validated);

        return redirect()->route('coach.clients.show', $client)
            ->with('success', 'Client updated successfully!');
    }

    /**
     * Remove the specified client.
     */
    public function destroy(User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $client->delete();

        return redirect()->route('coach.clients.index')
            ->with('success', 'Client removed successfully.');
    }
}
