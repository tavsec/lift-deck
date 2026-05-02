<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkoutLog;
use App\Models\WorkoutLogComment;
use App\Services\SubscriptionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    /**
     * @return array{
     *   show: bool,
     *   completed_count: int,
     *   total_count: int,
     *   steps: array<int, array{label: string, complete: bool, route: string|null, action_label: string|null}>
     * }
     */
    private function buildOnboardingChecklist(User $user): array
    {
        $steps = [
            [
                'label' => __('coach.onboarding_checklist.steps.subscribe'),
                'complete' => true,
                'route' => null,
                'action_label' => null,
            ],
            [
                'label' => __('coach.onboarding_checklist.steps.invite_client'),
                'complete' => $user->clients()->exists(),
                'route' => route('coach.clients.create'),
                'action_label' => __('coach.onboarding_checklist.actions.invite_client'),
            ],
            [
                'label' => __('coach.onboarding_checklist.steps.create_program'),
                'complete' => $user->programs()->exists(),
                'route' => route('coach.programs.create'),
                'action_label' => __('coach.onboarding_checklist.actions.create_program'),
            ],
        ];

        // Branding is gated by the custom_branding feature (Professional only).
        // Hide the step entirely for plans without it so we don't dangle a
        // disabled action that would just bounce off the feature middleware.
        if ($this->subscriptionService->hasFeature($user, 'custom_branding')) {
            $steps[] = [
                'label' => __('coach.onboarding_checklist.steps.setup_branding'),
                'complete' => filled($user->getRawOriginal('gym_name')),
                'route' => route('coach.branding.edit'),
                'action_label' => __('coach.onboarding_checklist.actions.setup_branding'),
            ];
        }

        $completedCount = collect($steps)->filter(fn (array $step) => $step['complete'])->count();
        $allComplete = $completedCount === count($steps);

        return [
            'show' => ! $allComplete && $user->onboarding_checklist_dismissed_at === null,
            'completed_count' => $completedCount,
            'total_count' => count($steps),
            'steps' => $steps,
        ];
    }

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

        $onboardingChecklist = $this->buildOnboardingChecklist($user);

        $needsAttention = $this->clientsNeedingAttention($user);

        return view('coach.dashboard', compact('stats', 'recentWorkoutLogs', 'recentComments', 'onboardingChecklist', 'needsAttention'));
    }

    /**
     * Build a list of clients who may need a check-in, with the FIRST matching
     * flag in priority order: inactive -> off_target -> no_goal. Limited to 5.
     *
     * @return Collection<int, array{client: User, flag: string}>
     */
    private function clientsNeedingAttention(User $coach): Collection
    {
        $today = Carbon::today();
        $threeDayWindowStart = $today->copy()->subDays(2)->startOfDay();
        $fourteenDayWindowStart = $today->copy()->subDays(13)->startOfDay();

        $clients = $coach->clients()
            ->with([
                'macroGoals' => function ($query) use ($today): void {
                    $query->whereDate('effective_date', '<=', $today)
                        ->orderByDesc('effective_date');
                },
                'mealLogs' => function ($query) use ($fourteenDayWindowStart): void {
                    $query->whereDate('date', '>=', $fourteenDayWindowStart)
                        ->orderByDesc('date');
                },
            ])
            ->latest('id')
            ->get();

        $flagged = $clients->map(function (User $client) use ($threeDayWindowStart): ?array {
            $activeGoal = $client->macroGoals->first();

            $logsLast3Days = $client->mealLogs->filter(
                fn ($log) => $log->date->greaterThanOrEqualTo($threeDayWindowStart)
            );

            // Priority 1: inactive (no logs in last 3 days, but has an active goal).
            if ($activeGoal !== null && $logsLast3Days->isEmpty()) {
                return ['client' => $client, 'flag' => 'inactive'];
            }

            // Priority 2: off_target (has logs and active goal, avg daily calories far from target).
            if ($activeGoal !== null && $logsLast3Days->isNotEmpty() && $activeGoal->calories > 0) {
                $totalsByDay = $logsLast3Days
                    ->groupBy(fn ($log) => $log->date->toDateString())
                    ->map(fn (Collection $dayLogs) => (int) $dayLogs->sum('calories'));

                $avg = $totalsByDay->avg();
                $ratio = $avg / (int) $activeGoal->calories;

                if ($ratio < 0.7 || $ratio > 1.3) {
                    return ['client' => $client, 'flag' => 'off_target'];
                }
            }

            // Priority 3: no_goal (logged in last 14 days but no active goal).
            if ($activeGoal === null && $client->mealLogs->isNotEmpty()) {
                return ['client' => $client, 'flag' => 'no_goal'];
            }

            return null;
        })->filter();

        $priority = ['inactive' => 0, 'off_target' => 1, 'no_goal' => 2];

        return $flagged
            ->sortBy(fn (array $row) => $priority[$row['flag']])
            ->values()
            ->take(5);
    }
}
