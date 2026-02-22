<?php

namespace App\Jobs;

use App\Models\Achievement;
use App\Models\DailyLog;
use App\Models\User;
use App\Models\UserXpSummary;
use App\Models\WorkoutLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EvaluateAchievements implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
    ) {
        $this->onQueue('loyalty');
    }

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            return;
        }

        $earnedIds = $user->achievements()->pluck('achievements.id');

        $achievements = Achievement::where('type', 'automatic')
            ->where('is_active', true)
            ->whereNotIn('id', $earnedIds)
            ->where(function ($query) use ($user) {
                $query->whereNull('coach_id')
                    ->orWhere('coach_id', $user->coach_id);
            })
            ->get();

        foreach ($achievements as $achievement) {
            if ($this->isConditionMet($user, $achievement)) {
                $user->achievements()->attach($achievement->id, [
                    'earned_at' => now(),
                ]);

                if ($achievement->xp_reward > 0 || $achievement->points_reward > 0) {
                    $summary = UserXpSummary::firstOrCreate(
                        ['user_id' => $user->id],
                        ['total_xp' => 0, 'available_points' => 0],
                    );

                    if ($achievement->xp_reward > 0) {
                        $summary->increment('total_xp', $achievement->xp_reward);
                    }
                    if ($achievement->points_reward > 0) {
                        $summary->increment('available_points', $achievement->points_reward);
                    }
                }
            }
        }
    }

    private function isConditionMet(User $user, Achievement $achievement): bool
    {
        return match ($achievement->condition_type) {
            'workout_count' => WorkoutLog::where('client_id', $user->id)->count() >= $achievement->condition_value,
            'checkin_count' => DailyLog::where('client_id', $user->id)->distinct('date')->count('date') >= $achievement->condition_value,
            'xp_total' => ($user->xpSummary?->total_xp ?? 0) >= $achievement->condition_value,
            'streak_days' => $this->calculateCurrentStreak($user) >= $achievement->condition_value,
            default => false,
        };
    }

    private function calculateCurrentStreak(User $user): int
    {
        $dates = DailyLog::where('client_id', $user->id)
            ->where('date', '<=', now()->toDateString())
            ->distinct('date')
            ->orderByDesc('date')
            ->pluck('date');

        $streak = 0;
        $expectedDate = now()->startOfDay();

        foreach ($dates as $date) {
            if ($date->startOfDay()->equalTo($expectedDate)) {
                $streak++;
                $expectedDate = $expectedDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
