<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBrandingRequest;
use App\Models\OnboardingField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandingController extends Controller
{
    public function edit(): View
    {
        $coach = auth()->user();
        $fields = $coach->onboardingFields()->orderBy('order')->get();

        return view('coach.branding', compact('coach', 'fields'));
    }

    public function update(UpdateBrandingRequest $request): RedirectResponse
    {
        $coach = auth()->user();
        $validated = $request->validated();

        // Update branding text fields
        $coach->update([
            'gym_name' => $validated['gym_name'] ?? $coach->gym_name,
            'description' => $validated['description'] ?? null,
            'primary_color' => $validated['primary_color'] ?? null,
            'secondary_color' => $validated['secondary_color'] ?? null,
            'onboarding_welcome_text' => $validated['onboarding_welcome_text'] ?? null,
            'welcome_email_text' => $validated['welcome_email_text'] ?? null,
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($coach->logo) {
                Storage::delete($coach->logo);
            }
            $path = $request->file('logo')->store('logos');
            $coach->update(['logo' => $path]);
        } elseif ($request->boolean('remove_logo') && $coach->logo) {
            Storage::delete($coach->logo);
            $coach->update(['logo' => null]);
        }

        // Sync onboarding fields
        $this->syncOnboardingFields($coach, $validated['fields'] ?? []);

        return redirect()->route('coach.branding.edit')
            ->with('success', 'Branding updated successfully.');
    }

    private function syncOnboardingFields(mixed $coach, array $fields): void
    {
        $coach->onboardingFields()->delete();

        foreach ($fields as $index => $fieldData) {
            $options = null;
            if ($fieldData['type'] === 'select' && ! empty($fieldData['options'])) {
                $options = array_values(array_filter(
                    array_map('trim', explode("\n", $fieldData['options']))
                ));
            }

            OnboardingField::create([
                'coach_id' => $coach->id,
                'label' => $fieldData['label'],
                'type' => $fieldData['type'],
                'options' => $options,
                'is_required' => (bool) ($fieldData['is_required'] ?? false),
                'order' => $index + 1,
            ]);
        }
    }
}
