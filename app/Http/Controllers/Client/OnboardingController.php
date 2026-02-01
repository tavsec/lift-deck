<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Show the welcome page.
     */
    public function welcome(): View
    {
        return view('client.welcome', [
            'coach' => auth()->user()->coach,
        ]);
    }

    /**
     * Show the onboarding form.
     */
    public function show(): View
    {
        return view('client.onboarding');
    }

    /**
     * Store the onboarding data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'goal' => ['required', 'in:fat_loss,strength,general_fitness'],
            'experience_level' => ['required', 'in:beginner,intermediate,advanced'],
            'injuries' => ['nullable', 'string', 'max:1000'],
            'equipment_access' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = auth()->user();

        ClientProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                ...$validated,
                'onboarding_completed_at' => now(),
            ]
        );

        return redirect()->route('client.dashboard')
            ->with('success', 'Welcome! Your profile has been set up.');
    }

    /**
     * Skip onboarding and go to dashboard.
     */
    public function skip(): RedirectResponse
    {
        $user = auth()->user();

        ClientProfile::updateOrCreate(
            ['user_id' => $user->id],
            []
        );

        return redirect()->route('client.dashboard');
    }
}
