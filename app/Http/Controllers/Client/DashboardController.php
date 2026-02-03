<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $activeProgram = $user->activeProgram()?->load('program');

        return view('client.dashboard', [
            'coach' => $user->coach,
            'activeProgram' => $activeProgram,
        ]);
    }
}
