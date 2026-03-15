<?php

namespace App\Jobs;

use App\Mail\RewardRedeemedMail;
use App\Models\RewardRedemption;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NotifyCoachOfRedemption implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $redemptionId,
    ) {
        $this->onQueue('loyalty');
    }

    public function handle(): void
    {
        $redemption = RewardRedemption::with(['user.coach', 'reward'])->find($this->redemptionId);

        if (! $redemption) {
            return;
        }

        $coach = $redemption->user->coach;

        if (! $coach) {
            return;
        }

        Mail::to($coach->email)->send(new RewardRedeemedMail($redemption));
    }
}
