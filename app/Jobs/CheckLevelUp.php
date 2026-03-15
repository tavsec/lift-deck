<?php

namespace App\Jobs;

use App\Models\Level;
use App\Models\UserXpSummary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckLevelUp implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
    ) {
        $this->onQueue('loyalty');
    }

    public function handle(): void
    {
        $summary = UserXpSummary::where('user_id', $this->userId)->first();

        if (! $summary) {
            return;
        }

        $newLevel = Level::where('xp_required', '<=', $summary->total_xp)
            ->orderByDesc('xp_required')
            ->first();

        if (! $newLevel) {
            return;
        }

        if ($summary->current_level_id === $newLevel->id) {
            return;
        }

        // Only upgrade, never downgrade
        if ($summary->currentLevel && $summary->currentLevel->level_number >= $newLevel->level_number) {
            return;
        }

        $summary->update(['current_level_id' => $newLevel->id]);
    }
}
