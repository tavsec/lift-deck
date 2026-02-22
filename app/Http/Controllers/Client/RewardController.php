<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyCoachOfRedemption;
use App\Models\Level;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\UserXpSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RewardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $coachRewards = Reward::where('coach_id', $user->coach_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $globalRewards = Reward::whereNull('coach_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $rewards = $coachRewards->merge($globalRewards);

        $xpSummary = $user->xpSummary()->with('currentLevel')->first();
        $nextLevel = $xpSummary
            ? Level::where('xp_required', '>', $xpSummary->total_xp)->orderBy('xp_required')->first()
            : null;

        return view('client.rewards', compact('rewards', 'xpSummary', 'nextLevel'));
    }

    public function redeem(Reward $reward): RedirectResponse
    {
        $user = auth()->user();

        DB::transaction(function () use ($user, $reward) {
            $summary = UserXpSummary::where('user_id', $user->id)->lockForUpdate()->first();

            if (! $summary || $summary->available_points < $reward->points_cost) {
                abort(403, 'Insufficient points.');
            }

            if (! $reward->hasStock()) {
                abort(403, 'Reward out of stock.');
            }

            $summary->decrement('available_points', $reward->points_cost);

            if ($reward->stock !== null) {
                $reward->decrement('stock');
            }

            $redemption = RewardRedemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_cost,
                'status' => 'pending',
            ]);

            NotifyCoachOfRedemption::dispatch($redemption->id);
        });

        return back()->with('success', 'Reward redeemed successfully!');
    }
}
