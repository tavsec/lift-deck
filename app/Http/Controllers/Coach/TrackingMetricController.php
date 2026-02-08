<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\TrackingMetric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingMetricController extends Controller
{
    public function index(): View
    {
        $metrics = auth()->user()->trackingMetrics()->get();

        return view('coach.tracking-metrics.index', compact('metrics'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', 'in:number,scale,boolean,text,image'],
            'unit' => ['nullable', 'string', 'max:50'],
            'scale_min' => ['nullable', 'integer', 'min:0'],
            'scale_max' => ['nullable', 'integer', 'min:1'],
        ]);

        $maxOrder = auth()->user()->trackingMetrics()->max('order') ?? 0;

        auth()->user()->trackingMetrics()->create(array_merge($validated, [
            'order' => $maxOrder + 1,
        ]));

        return redirect()->route('coach.tracking-metrics.index')
            ->with('success', 'Metric created successfully.');
    }

    public function update(Request $request, TrackingMetric $trackingMetric): RedirectResponse
    {
        if ($trackingMetric->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', 'in:number,scale,boolean,text,image'],
            'unit' => ['nullable', 'string', 'max:50'],
            'scale_min' => ['nullable', 'integer', 'min:0'],
            'scale_max' => ['nullable', 'integer', 'min:1'],
        ]);

        $trackingMetric->update($validated);

        return redirect()->route('coach.tracking-metrics.index')
            ->with('success', 'Metric updated successfully.');
    }

    public function destroy(TrackingMetric $trackingMetric): RedirectResponse
    {
        if ($trackingMetric->coach_id !== auth()->id()) {
            abort(403);
        }

        $trackingMetric->update(['is_active' => false]);

        return redirect()->route('coach.tracking-metrics.index')
            ->with('success', 'Metric deactivated successfully.');
    }

    public function restore(TrackingMetric $trackingMetric): RedirectResponse
    {
        if ($trackingMetric->coach_id !== auth()->id()) {
            abort(403);
        }

        $trackingMetric->update(['is_active' => true]);

        return redirect()->route('coach.tracking-metrics.index')
            ->with('success', 'Metric reactivated successfully.');
    }

    public function moveUp(TrackingMetric $trackingMetric): RedirectResponse
    {
        if ($trackingMetric->coach_id !== auth()->id()) {
            abort(403);
        }

        $previous = auth()->user()->trackingMetrics()
            ->where('order', '<', $trackingMetric->order)
            ->orderByDesc('order')
            ->first();

        if ($previous) {
            $tempOrder = $trackingMetric->order;
            $trackingMetric->update(['order' => $previous->order]);
            $previous->update(['order' => $tempOrder]);
        }

        return redirect()->route('coach.tracking-metrics.index');
    }

    public function moveDown(TrackingMetric $trackingMetric): RedirectResponse
    {
        if ($trackingMetric->coach_id !== auth()->id()) {
            abort(403);
        }

        $next = auth()->user()->trackingMetrics()
            ->where('order', '>', $trackingMetric->order)
            ->orderBy('order')
            ->first();

        if ($next) {
            $tempOrder = $trackingMetric->order;
            $trackingMetric->update(['order' => $next->order]);
            $next->update(['order' => $tempOrder]);
        }

        return redirect()->route('coach.tracking-metrics.index');
    }
}
