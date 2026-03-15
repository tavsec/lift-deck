<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $xpTransactions = $user->xpTransactions()
            ->with('xpEventType')
            ->latest('created_at')
            ->paginate(15, ['*'], 'xp_page');

        $redemptions = $user->rewardRedemptions()
            ->with('reward')
            ->latest()
            ->paginate(15, ['*'], 'redemptions_page');

        $xpSummary = $user->xpSummary()->with('currentLevel')->first();

        return view('client.loyalty', compact('xpTransactions', 'redemptions', 'xpSummary'));
    }
}
