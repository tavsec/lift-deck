<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\TrackingMetric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MetricsSetupController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'setup' => ['required', 'boolean'],
        ]);

        $coach = auth()->user();
        $coach->update(['metrics_onboarded_at' => now()]);

        if ($validated['setup']) {
            TrackingMetric::seedDefaults($coach->id, $coach->locale ?? 'en');

            return redirect()->route('coach.dashboard')->with(
                'metrics_setup',
                __('coach.metrics_setup.seeded', ['url' => route('coach.tracking-metrics.index')])
            );
        }

        return redirect()->route('coach.dashboard')->with(
            'metrics_setup',
            __('coach.metrics_setup.skipped', ['url' => route('coach.tracking-metrics.index')])
        );
    }
}
