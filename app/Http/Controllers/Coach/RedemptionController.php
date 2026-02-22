<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\RewardRedemption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RedemptionController extends Controller
{
    /**
     * Display a listing of redemptions for the coach's clients.
     */
    public function index(): View
    {
        $redemptions = RewardRedemption::whereHas('user', fn ($q) => $q->where('coach_id', auth()->id()))
            ->with(['user', 'reward'])
            ->latest()
            ->paginate(20);

        return view('coach.redemptions.index', compact('redemptions'));
    }

    /**
     * Update the status of a redemption (fulfill or reject).
     */
    public function update(Request $request, RewardRedemption $redemption): RedirectResponse
    {
        if ($redemption->user->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:fulfilled,rejected'],
            'coach_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $redemption->update($validated);

        return redirect()->route('coach.redemptions.index')
            ->with('success', 'Redemption updated.');
    }
}
