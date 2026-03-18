<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientCheckInController extends Controller
{
    public function show(Request $request, User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $date = $request->get('date', now()->format('Y-m-d'));

        $assignedMetrics = $client->assignedTrackingMetrics()
            ->with('trackingMetric')
            ->get()
            ->pluck('trackingMetric')
            ->filter();

        $existingLogs = $client->dailyLogs()
            ->whereDate('date', $date)
            ->get()
            ->keyBy('tracking_metric_id');

        return view('coach.clients.check-in', compact('client', 'assignedMetrics', 'existingLogs', 'date'));
    }

    public function store(Request $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'metrics' => ['nullable', 'array'],
            'metrics.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignedMetricIds = $client->assignedTrackingMetrics()
            ->pluck('tracking_metric_id')
            ->toArray();

        foreach ($validated['metrics'] ?? [] as $metricId => $value) {
            if (! in_array((int) $metricId, $assignedMetricIds)) {
                continue;
            }

            if ($value === null || $value === '') {
                DailyLog::where('client_id', $client->id)
                    ->where('tracking_metric_id', $metricId)
                    ->whereDate('date', $validated['date'])
                    ->delete();

                continue;
            }

            $log = DailyLog::where('client_id', $client->id)
                ->where('tracking_metric_id', $metricId)
                ->whereDate('date', $validated['date'])
                ->first();

            if ($log) {
                $log->update(['value' => $value]);
            } else {
                DailyLog::create([
                    'client_id' => $client->id,
                    'tracking_metric_id' => $metricId,
                    'date' => $validated['date'],
                    'value' => $value,
                ]);
            }
        }

        return redirect()->route('coach.clients.check-in.show', [
            'client' => $client,
            'date' => $validated['date'],
        ])->with('success', 'Check-in saved for client.');
    }
}
