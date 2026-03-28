<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get nutrition data and stats for a client over a date range.
     *
     * @param  User  $client  The client to retrieve nutrition data for.
     * @param  string  $from  Start date in Y-m-d format.
     * @param  string  $to  End date in Y-m-d format.
     * @return array{
     *     nutritionData: array<int, array{
     *         date: string,
     *         calories: int,
     *         protein: float,
     *         carbs: float,
     *         fat: float,
     *         goalCalories: int|null
     *     }>,
     *     nutritionStats: array{
     *         avgCalories: int,
     *         avgProtein: float,
     *         avgCarbs: float,
     *         avgFat: float,
     *         adherenceRate: int|null,
     *         daysLogged: int
     *     }
     * }
     */
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

        // All goals up to the end date are pre-loaded to avoid N+1 queries in the date loop.
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

                if ($goalCalories !== null) {
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
            'adherenceRate' => $daysWithGoal > 0 ? (int) round(($daysAdherent / $daysWithGoal) * 100) : null,
            'daysLogged' => $daysWithMeals,
        ];

        return compact('nutritionData', 'nutritionStats');
    }

    /**
     * Get check-in chart data for a client over a date range.
     *
     * @param  User  $client  The client to retrieve check-in data for.
     * @param  string  $from  Start date in Y-m-d format.
     * @param  string  $to  End date in Y-m-d format.
     * @return array{
     *     checkInCharts: array<int, array{id: int, name: string, unit: string|null, type: string, scaleMin: int|null, scaleMax: int|null, data: array<int, array{date: string, value: float}>}>,
     *     tableMetrics: \Illuminate\Support\Collection,
     *     checkInTableData: array<int, array<string, mixed>>,
     *     imageMetrics: \Illuminate\Support\Collection,
     *     imageMetricData: array<int, array{id: int, name: string, photos: array<int, array{date: string, thumbUrl: string, fullUrl: string}>}>
     * }
     */
    public function getCheckInChartData(User $client, string $from, string $to): array
    {
        $startDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);

        $dates = collect();
        $dayCount = $startDate->diffInDays($endDate) + 1;
        for ($i = 0; $i < $dayCount; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        $assignedMetricIds = $client->assignedTrackingMetrics()->pluck('tracking_metric_id');

        $coach = \App\Models\User::find($client->coach_id);
        $assignedMetrics = $coach
            ? $coach->trackingMetrics()
                ->whereIn('id', $assignedMetricIds)
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
            : collect();

        $dailyLogs = $client->dailyLogs()
            ->whereIn('tracking_metric_id', $assignedMetricIds)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->orderBy('date')
            ->get();

        $chartMetrics = $assignedMetrics->whereIn('type', ['number', 'scale']);
        $tableMetrics = $assignedMetrics->whereIn('type', ['boolean', 'text']);
        $imageMetrics = $assignedMetrics->where('type', 'image');

        $checkInCharts = [];
        foreach ($chartMetrics as $metric) {
            $metricLogs = $dailyLogs->where('tracking_metric_id', $metric->id);
            $dataPoints = [];
            foreach ($metricLogs as $log) {
                $dataPoints[] = [
                    'date' => $log->date->format('Y-m-d'),
                    'value' => (float) $log->value,
                ];
            }
            $checkInCharts[] = [
                'id' => $metric->id,
                'name' => $metric->name,
                'unit' => $metric->unit,
                'type' => $metric->type,
                'scaleMin' => $metric->scale_min,
                'scaleMax' => $metric->scale_max,
                'data' => $dataPoints,
            ];
        }

        $checkInTableData = [];
        foreach ($dates as $date) {
            $row = ['date' => $date];
            foreach ($tableMetrics as $metric) {
                $log = $dailyLogs->where('tracking_metric_id', $metric->id)
                    ->first(fn ($l) => $l->date->format('Y-m-d') === $date);
                $row['metric_'.$metric->id] = $log?->value;
            }
            $checkInTableData[] = $row;
        }

        $imageMetricData = [];
        if ($imageMetrics->isNotEmpty()) {
            $imageLogs = \App\Models\DailyLog::where('client_id', $client->id)
                ->whereIn('tracking_metric_id', $imageMetrics->pluck('id'))
                ->where('value', 'uploaded')
                ->whereDate('date', '>=', $from)
                ->whereDate('date', '<=', $to)
                ->with('media')
                ->orderByDesc('date')
                ->get();

            foreach ($imageMetrics as $metric) {
                $metricLogs = $imageLogs->where('tracking_metric_id', $metric->id);
                $photos = [];
                foreach ($metricLogs as $log) {
                    $media = $log->getFirstMedia('check-in-image');
                    if ($media) {
                        $photos[] = [
                            'date' => $log->date->format('Y-m-d'),
                            'thumbUrl' => route('media.daily-log', [$log, 'thumb']),
                            'fullUrl' => route('media.daily-log', [$log, 'full']),
                        ];
                    }
                }
                $imageMetricData[] = [
                    'id' => $metric->id,
                    'name' => $metric->name,
                    'photos' => $photos,
                ];
            }
        }

        return compact('checkInCharts', 'tableMetrics', 'checkInTableData', 'imageMetrics', 'imageMetricData');
    }
}
