<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
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

        $startDate = Carbon::parse($from);
        $endDate = Carbon::parse($to);

        $dates = collect();
        $dayCount = $startDate->diffInDays($endDate) + 1;
        for ($i = 0; $i < $dayCount; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        // --- Daily Check-ins ---
        $assignedMetricIds = $client->assignedTrackingMetrics()->pluck('tracking_metric_id');
        $assignedMetrics = auth()->user()->trackingMetrics()
            ->whereIn('id', $assignedMetricIds)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $dailyLogs = $client->dailyLogs()
            ->whereIn('tracking_metric_id', $assignedMetricIds)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->orderBy('date')
            ->get();

        $chartMetrics = $assignedMetrics->whereIn('type', ['number', 'scale']);
        $tableMetrics = $assignedMetrics->whereIn('type', ['boolean', 'text']);

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

        return view('coach.clients.analytics', compact(
            'client',
            'range',
            'from',
            'to',
            'dates',
            'checkInCharts',
            'chartMetrics',
            'tableMetrics',
            'checkInTableData',
        ));
    }
}
