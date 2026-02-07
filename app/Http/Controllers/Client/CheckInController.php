<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckInController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $date = $request->get('date', now()->format('Y-m-d'));

        $assignedMetrics = $user->assignedTrackingMetrics()
            ->with('trackingMetric')
            ->get()
            ->pluck('trackingMetric')
            ->filter();

        $existingLogs = $user->dailyLogs()
            ->whereDate('date', $date)
            ->get()
            ->keyBy('tracking_metric_id');

        return view('client.check-in', compact('assignedMetrics', 'existingLogs', 'date'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'metrics' => ['required', 'array'],
            'metrics.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignedMetricIds = $user->assignedTrackingMetrics()
            ->pluck('tracking_metric_id')
            ->toArray();

        foreach ($validated['metrics'] as $metricId => $value) {
            if (! in_array((int) $metricId, $assignedMetricIds)) {
                continue;
            }

            if ($value === null || $value === '') {
                DailyLog::where('client_id', $user->id)
                    ->where('tracking_metric_id', $metricId)
                    ->whereDate('date', $validated['date'])
                    ->delete();

                continue;
            }

            DailyLog::updateOrCreate(
                [
                    'client_id' => $user->id,
                    'tracking_metric_id' => $metricId,
                    'date' => $validated['date'],
                ],
                ['value' => $value],
            );
        }

        return redirect()->route('client.check-in', ['date' => $validated['date']])
            ->with('success', 'Check-in saved!');
    }
}
