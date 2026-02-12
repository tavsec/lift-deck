<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientProfile;
use App\Models\OnboardingResponse;
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
     * Show the onboarding form with dynamic fields.
     */
    public function show(): View
    {
        $coach = auth()->user()->coach;
        $fields = $coach->onboardingFields()->orderBy('order')->get();

        return view('client.onboarding', compact('fields'));
    }

    /**
     * Store the onboarding responses.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $coach = $user->coach;
        $fields = $coach->onboardingFields()->get();

        // Build validation rules dynamically
        $rules = [];
        foreach ($fields as $field) {
            $fieldRules = [];
            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            $fieldRules[] = 'string';
            $fieldRules[] = 'max:2000';

            if ($field->type === 'select' && $field->options) {
                $fieldRules[] = 'in:'.implode(',', $field->options);
            }

            $rules["fields.{$field->id}"] = $fieldRules;
        }

        $validated = $request->validate($rules);

        // Save responses
        foreach ($fields as $field) {
            $value = $validated['fields'][$field->id] ?? null;
            if ($value !== null && $value !== '') {
                OnboardingResponse::updateOrCreate(
                    ['client_id' => $user->id, 'onboarding_field_id' => $field->id],
                    ['value' => $value]
                );
            }
        }

        // Mark onboarding complete
        ClientProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['onboarding_completed_at' => now()]
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
