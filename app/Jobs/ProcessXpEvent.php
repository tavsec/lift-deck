<?php

namespace App\Jobs;

use App\Models\UserXpSummary;
use App\Models\XpEventType;
use App\Models\XpTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessXpEvent implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        public int $userId,
        public string $eventKey,
        public ?array $metadata = null,
    ) {
        $this->onQueue('loyalty');
    }

    public function handle(): void
    {
        $eventType = XpEventType::where('key', $this->eventKey)->first();

        if (! $eventType || ! $eventType->is_active) {
            return;
        }

        if ($this->isOnCooldown($eventType)) {
            return;
        }

        XpTransaction::create([
            'user_id' => $this->userId,
            'xp_event_type_id' => $eventType->id,
            'xp_amount' => $eventType->xp_amount,
            'points_amount' => $eventType->points_amount,
            'metadata' => $this->metadata,
            'created_at' => now(),
        ]);

        $summary = UserXpSummary::firstOrCreate(
            ['user_id' => $this->userId],
            ['total_xp' => 0, 'available_points' => 0],
        );

        $summary->increment('total_xp', $eventType->xp_amount);
        $summary->increment('available_points', $eventType->points_amount);

        CheckLevelUp::dispatch($this->userId)->onQueue('loyalty');
        EvaluateAchievements::dispatch($this->userId)->onQueue('loyalty');
    }

    private function isOnCooldown(XpEventType $eventType): bool
    {
        if (! $eventType->cooldown_hours) {
            return false;
        }

        return XpTransaction::where('user_id', $this->userId)
            ->where('xp_event_type_id', $eventType->id)
            ->where('created_at', '>=', now()->subHours($eventType->cooldown_hours))
            ->exists();
    }
}
