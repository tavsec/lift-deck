<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class AnalyticsService
{
    public function getNutritionData(User $client, string $from, string $to): array
    {
        $startDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);

        $dates = collect();
        $dayCount = $startDate->diffInDays($endDate) + 1;
        for ($i = 0; $i < $dayCount; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        $mealLogs = $client->mealLogs()
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->orderBy('date')
            ->get();

        $macroGoals = $client->macroGoals()
            ->whereDate('effective_date', '<=', $to)
            ->orderBy('effective_date')
            ->get();

        $nutritionData = [];
        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;
        $daysWithMeals = 0;
        $daysAdherent = 0;
        $daysWithGoal = 0;

        foreach ($dates as $date) {
            $dayLogs = $mealLogs->filter(fn ($l) => $l->date->format('Y-m-d') === $date);
            $dayCals = (int) $dayLogs->sum('calories');
            $dayProtein = (float) $dayLogs->sum('protein');
            $dayCarbs = (float) $dayLogs->sum('carbs');
            $dayFat = (float) $dayLogs->sum('fat');

            $activeGoal = $macroGoals->filter(fn ($g) => $g->effective_date->format('Y-m-d') <= $date)
                ->sortByDesc('effective_date')
                ->first();

            $goalCalories = $activeGoal?->calories;

            if ($dayLogs->count() > 0) {
                $daysWithMeals++;
                $totalCalories += $dayCals;
                $totalProtein += $dayProtein;
                $totalCarbs += $dayCarbs;
                $totalFat += $dayFat;

                if ($goalCalories) {
                    $daysWithGoal++;
                    $deviation = abs($dayCals - $goalCalories) / $goalCalories;
                    if ($deviation <= 0.10) {
                        $daysAdherent++;
                    }
                }
            }

            $nutritionData[] = [
                'date' => $date,
                'calories' => $dayCals,
                'protein' => round($dayProtein, 1),
                'carbs' => round($dayCarbs, 1),
                'fat' => round($dayFat, 1),
                'goalCalories' => $goalCalories,
            ];
        }

        $nutritionStats = [
            'avgCalories' => $daysWithMeals > 0 ? (int) round($totalCalories / $daysWithMeals) : 0,
            'avgProtein' => $daysWithMeals > 0 ? round($totalProtein / $daysWithMeals, 1) : 0,
            'avgCarbs' => $daysWithMeals > 0 ? round($totalCarbs / $daysWithMeals, 1) : 0,
            'avgFat' => $daysWithMeals > 0 ? round($totalFat / $daysWithMeals, 1) : 0,
            'adherenceRate' => $daysWithGoal > 0 ? round(($daysAdherent / $daysWithGoal) * 100) : null,
            'daysLogged' => $daysWithMeals,
        ];

        return compact('nutritionData', 'nutritionStats');
    }
}
