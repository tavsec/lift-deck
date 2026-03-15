<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAchievementRequest;
use App\Http\Requests\UpdateAchievementRequest;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserXpSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AchievementController extends Controller
{
    /**
     * Display a listing of coach and global achievements.
     */
    public function index(): View
    {
        $coachAchievements = Achievement::where('coach_id', auth()->id())->where('is_active', true)->orderBy('name')->get();
        $globalAchievements = Achievement::whereNull('coach_id')->where('is_active', true)->orderBy('name')->get();
        $achievements = $coachAchievements->merge($globalAchievements);

        return view('coach.achievements.index', compact('achievements'));
    }

    /**
     * Show the form for creating a new achievement.
     */
    public function create(): View
    {
        return view('coach.achievements.create');
    }

    /**
     * Store a newly created achievement.
     */
    public function store(StoreAchievementRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['coach_id'] = auth()->id();

        $achievement = Achievement::create($validated);

        if ($request->hasFile('icon')) {
            $achievement->addMediaFromRequest('icon')->toMediaCollection('icon');
        }

        return redirect()->route('coach.achievements.index')
            ->with('success', 'Achievement created successfully!');
    }

    /**
     * Show the form for editing the specified achievement.
     */
    public function edit(Achievement $achievement): View
    {
        $this->authorizeAchievement($achievement);

        return view('coach.achievements.edit', compact('achievement'));
    }

    /**
     * Update the specified achievement.
     */
    public function update(UpdateAchievementRequest $request, Achievement $achievement): RedirectResponse
    {
        $this->authorizeAchievement($achievement);

        $achievement->update($request->validated());

        if ($request->hasFile('icon')) {
            $achievement->addMediaFromRequest('icon')->toMediaCollection('icon');
        }

        return redirect()->route('coach.achievements.index')
            ->with('success', 'Achievement updated successfully!');
    }

    /**
     * Archive the specified achievement.
     */
    public function destroy(Achievement $achievement): RedirectResponse
    {
        $this->authorizeAchievement($achievement);

        $achievement->update(['is_active' => false]);

        return redirect()->route('coach.achievements.index')
            ->with('success', 'Achievement archived successfully.');
    }

    /**
     * Manually award an achievement to a client.
     */
    public function award(User $client, Achievement $achievement): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($client->achievements()->where('achievement_id', $achievement->id)->exists()) {
            return back()->with('error', 'Achievement already awarded.');
        }

        $client->achievements()->attach($achievement->id, [
            'awarded_by' => auth()->id(),
            'earned_at' => now(),
        ]);

        if ($achievement->xp_reward > 0 || $achievement->points_reward > 0) {
            $summary = UserXpSummary::firstOrCreate(
                ['user_id' => $client->id],
                ['total_xp' => 0, 'available_points' => 0],
            );

            if ($achievement->xp_reward > 0) {
                $summary->increment('total_xp', $achievement->xp_reward);
            }
            if ($achievement->points_reward > 0) {
                $summary->increment('available_points', $achievement->points_reward);
            }
        }

        return back()->with('success', 'Achievement awarded successfully!');
    }

    /**
     * Authorize that the coach owns this achievement (blocks global achievements too).
     */
    private function authorizeAchievement(Achievement $achievement): void
    {
        if ($achievement->coach_id !== auth()->id()) {
            abort(403);
        }
    }
}
