<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class OnboardingChecklistController extends Controller
{
    public function dismiss(): RedirectResponse
    {
        auth()->user()->update(['onboarding_checklist_dismissed_at' => now()]);

        return redirect()->route('coach.dashboard');
    }
}
