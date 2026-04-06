<?php

namespace App\Http\Controllers\Coach;

use App\Exports\CoachAnalyticsExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analyticsService) {}

    public function show(Request $request, User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $range = $request->get('range', '30');
        if ($range === 'custom') {
            $from = $request->get('from', now()->subDays(29)->format('Y-m-d'));
            $to = $request->get('to', now()->format('Y-m-d'));
        } else {
            $days = (int) $range;
            $from = now()->subDays($days - 1)->format('Y-m-d');
            $to = now()->format('Y-m-d');
        }

        ['nutritionData' => $nutritionData, 'nutritionStats' => $nutritionStats] =
            $this->analyticsService->getNutritionData($client, $from, $to);

        [
            'checkInCharts' => $checkInCharts,
            'tableMetrics' => $tableMetrics,
            'checkInTableData' => $checkInTableData,
            'imageMetrics' => $imageMetrics,
            'imageMetricData' => $imageMetricData,
        ] = $this->analyticsService->getCheckInChartData($client, $from, $to);

        $chartMetrics = collect($checkInCharts);

        [
            'exerciseProgressionData' => $exerciseProgressionData,
            'exercisesByMuscleGroup' => $exercisesByMuscleGroup,
            'exerciseTargetHistory' => $exerciseTargetHistory,
        ] = $this->analyticsService->getExerciseProgressionData($client, $from, $to);

        return view('coach.clients.analytics', compact(
            'client',
            'range',
            'from',
            'to',
            'checkInCharts',
            'chartMetrics',
            'tableMetrics',
            'checkInTableData',
            'imageMetrics',
            'imageMetricData',
            'nutritionData',
            'nutritionStats',
            'exerciseProgressionData',
            'exercisesByMuscleGroup',
            'exerciseTargetHistory',
        ));
    }

    public function exportToExcel(Request $request, User $client)
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        return Excel::download(new CoachAnalyticsExport($client), 'analytics.xlsx');
    }
}
