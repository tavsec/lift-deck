<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\MacroGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NutritionController extends Controller
{
    public function show(Request $request, User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $macroGoals = $client->macroGoals()
            ->orderByDesc('effective_date')
            ->get();

        $currentGoal = MacroGoal::activeForClient($client->id, now()->format('Y-m-d'));

        // Date range filter
        $range = $request->get('range', '7');
        if ($range === 'custom') {
            $from = $request->get('from', now()->subDays(6)->format('Y-m-d'));
            $to = $request->get('to', now()->format('Y-m-d'));
        } else {
            $days = (int) $range;
            $from = now()->subDays($days - 1)->format('Y-m-d');
            $to = now()->format('Y-m-d');
        }

        $startDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);
        $dayCount = $startDate->diffInDays($endDate) + 1;

        $dates = collect();
        for ($i = 0; $i < $dayCount; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        $mealLogs = $client->mealLogs()
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
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
            'range',
            'from',
            'to',
        ));
    }
}
