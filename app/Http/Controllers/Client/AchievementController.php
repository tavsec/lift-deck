<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $achievements = Achievement::where(function ($query) use ($user) {
            $query->whereNull('coach_id')
                ->orWhere('coach_id', $user->coach_id);
        })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $earnedAchievements = $user->achievements()->get();
        $earnedAchievementIds = $earnedAchievements->pluck('id');

        $xpSummary = $user->xpSummary()->with('currentLevel')->first();

        return view('client.achievements', compact('achievements', 'earnedAchievementIds', 'xpSummary'));
    }
}
