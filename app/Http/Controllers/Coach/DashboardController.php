<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $stats = [
            'total_clients' => $user->clients()->count(),
            'active_clients' => $user->clients()->count(), // Will add active filter later
        ];

        return view('coach.dashboard', compact('stats'));
    }
}
