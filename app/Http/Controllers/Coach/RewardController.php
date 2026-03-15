<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRewardRequest;
use App\Http\Requests\UpdateRewardRequest;
use App\Models\Reward;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RewardController extends Controller
{
    /**
     * Display a listing of coach and global rewards.
     */
    public function index(): View
    {
        $coachRewards = Reward::where('coach_id', auth()->id())->where('is_active', true)->orderBy('name')->get();
        $globalRewards = Reward::whereNull('coach_id')->where('is_active', true)->orderBy('name')->get();
        $rewards = $coachRewards->merge($globalRewards);

        return view('coach.rewards.index', compact('rewards'));
    }

    /**
     * Show the form for creating a new reward.
     */
    public function create(): View
    {
        return view('coach.rewards.create');
    }

    /**
     * Store a newly created reward.
     */
    public function store(StoreRewardRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['coach_id'] = auth()->id();

        $reward = Reward::create($validated);

        if ($request->hasFile('image')) {
            $reward->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()->route('coach.rewards.index')
            ->with('success', 'Reward created successfully!');
    }

    /**
     * Show the form for editing the specified reward.
     */
    public function edit(Reward $reward): View
    {
        $this->authorizeReward($reward);

        return view('coach.rewards.edit', compact('reward'));
    }

    /**
     * Update the specified reward.
     */
    public function update(UpdateRewardRequest $request, Reward $reward): RedirectResponse
    {
        $this->authorizeReward($reward);

        $reward->update($request->validated());

        if ($request->hasFile('image')) {
            $reward->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()->route('coach.rewards.index')
            ->with('success', 'Reward updated successfully!');
    }

    /**
     * Archive the specified reward.
     */
    public function destroy(Reward $reward): RedirectResponse
    {
        $this->authorizeReward($reward);

        $reward->update(['is_active' => false]);

        return redirect()->route('coach.rewards.index')
            ->with('success', 'Reward archived successfully.');
    }

    /**
     * Authorize that the coach owns this reward (blocks global rewards too).
     */
    private function authorizeReward(Reward $reward): void
    {
        if ($reward->coach_id !== auth()->id()) {
            abort(403);
        }
    }
}
