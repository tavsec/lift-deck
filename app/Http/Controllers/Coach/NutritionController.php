<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\MacroGoal;
use App\Models\User;
use Illuminate\View\View;

class NutritionController extends Controller
{
    public function show(User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $macroGoals = $client->macroGoals()
            ->orderByDesc('effective_date')
            ->get();

        $currentGoal = MacroGoal::activeForClient($client->id, now()->format('Y-m-d'));

        // Last 7 days of meal logs with daily totals
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $mealLogs = $client->mealLogs()
            ->whereIn('date', $dates)
            ->orderBy('date')
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn ($log) => $log->date->format('Y-m-d'));

        $dailyTotals = [];
        foreach ($dates as $date) {
            $logs = $mealLogs->get($date, collect());
            $dailyTotals[$date] = [
                'calories' => $logs->sum('calories'),
                'protein' => $logs->sum('protein'),
                'carbs' => $logs->sum('carbs'),
                'fat' => $logs->sum('fat'),
                'meals' => $logs,
            ];
        }

        return view('coach.clients.nutrition', compact(
            'client',
            'macroGoals',
            'currentGoal',
            'dates',
            'dailyTotals',
        ));
    }
}
