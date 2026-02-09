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

        // Eager load media for image metrics
        $imageLogIds = $existingLogs->filter(fn ($log) => $log->value === 'uploaded')->pluck('id');
        if ($imageLogIds->isNotEmpty()) {
            $logsWithMedia = DailyLog::whereIn('id', $imageLogIds)->with('media')->get()->keyBy('id');
            foreach ($existingLogs as $metricId => $log) {
                if ($logsWithMedia->has($log->id)) {
                    $existingLogs[$metricId] = $logsWithMedia[$log->id];
                }
            }
        }

        return view('client.check-in', compact('assignedMetrics', 'existingLogs', 'date'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'metrics' => ['nullable', 'array'],
            'metrics.*' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['nullable', 'string'],
        ]);

        $assignedMetrics = $user->assignedTrackingMetrics()
            ->with('trackingMetric')
            ->get();

        $assignedMetricIds = $assignedMetrics->pluck('tracking_metric_id')->toArray();
        $imageMetricIds = $assignedMetrics
            ->filter(fn ($am) => $am->trackingMetric?->type === 'image')
            ->pluck('tracking_metric_id')
            ->toArray();

        // Handle regular metrics
        foreach (($validated['metrics'] ?? []) as $metricId => $value) {
            if (! in_array((int) $metricId, $assignedMetricIds)) {
                continue;
            }

            if (in_array((int) $metricId, $imageMetricIds)) {
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

        // Handle image removals
        foreach (($validated['remove_images'] ?? []) as $metricId => $flag) {
            if (! in_array((int) $metricId, $imageMetricIds)) {
                continue;
            }

            $log = DailyLog::where('client_id', $user->id)
                ->where('tracking_metric_id', $metricId)
                ->whereDate('date', $validated['date'])
                ->first();

            if ($log) {
                $log->clearMediaCollection('check-in-image');
                $log->delete();
            }
        }

        // Handle image uploads
        foreach (($validated['images'] ?? []) as $metricId => $file) {
            if (! in_array((int) $metricId, $imageMetricIds)) {
                continue;
            }

            if (! $file) {
                continue;
            }

            $log = DailyLog::where('client_id', $user->id)
                ->where('tracking_metric_id', $metricId)
                ->whereDate('date', $validated['date'])
                ->first();

            if (! $log) {
                $log = DailyLog::create([
                    'client_id' => $user->id,
                    'tracking_metric_id' => $metricId,
                    'date' => $validated['date'],
                    'value' => 'uploaded',
                ]);
            } else {
                $log->update(['value' => 'uploaded']);
            }

            $log->addMedia($file)
                ->toMediaCollection('check-in-image');
        }

        return redirect()->route('client.check-in', ['date' => $validated['date']])
            ->with('success', 'Check-in saved!');
    }
}
