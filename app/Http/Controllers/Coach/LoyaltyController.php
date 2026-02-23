<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function show(User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $xpTransactions = $client->xpTransactions()
            ->with('xpEventType')
            ->latest('created_at')
            ->paginate(15, ['*'], 'xp_page');

        $redemptions = $client->rewardRedemptions()
            ->with('reward')
            ->latest()
            ->paginate(15, ['*'], 'redemptions_page');

        $xpSummary = $client->xpSummary()->with('currentLevel')->first();

        return view('coach.clients.loyalty', compact('client', 'xpTransactions', 'redemptions', 'xpSummary'));
    }
}
