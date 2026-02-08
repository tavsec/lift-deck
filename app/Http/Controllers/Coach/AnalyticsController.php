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

        return view('coach.clients.analytics', compact(
            'client',
            'range',
            'from',
            'to',
            'dates',
        ));
    }
}
