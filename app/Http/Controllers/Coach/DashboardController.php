<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkoutLog;
use App\Models\WorkoutLogComment;
use Illuminate\View\View;

class DashboardController extends Controller
{
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
            [
                'label' => __('coach.onboarding_checklist.steps.setup_branding'),
                'complete' => filled($user->getRawOriginal('gym_name')),
                'route' => route('coach.branding.edit'),
                'action_label' => __('coach.onboarding_checklist.actions.setup_branding'),
            ],
        ];

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

        return view('coach.dashboard', compact('stats', 'recentWorkoutLogs', 'recentComments', 'onboardingChecklist'));
    }
}
