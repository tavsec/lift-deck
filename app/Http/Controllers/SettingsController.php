<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function editCoach(): View
    {
        $coach = auth()->user();

        return view('coach.settings.edit', [
            'user' => $coach,
            'currentPlanKey' => $this->subscriptionService->currentPlanKey($coach),
            'clientCount' => $coach->clients()->count(),
            'clientLimit' => $this->subscriptionService->clientLimit($coach),
            'isOnTrial' => $coach->onTrial(),
            'trialEndsAt' => $coach->trial_ends_at,
            'isInGracePeriod' => $this->subscriptionService->isInGracePeriod($coach),
            'graceDaysRemaining' => $this->subscriptionService->graceDaysRemaining($coach),
            'subscription' => $coach->subscription('default'),
        ]);
    }

    public function editClient(): View
    {
        return view('client.settings.edit', ['user' => auth()->user()]);
    }

    public function updateCoach(UpdateSettingsRequest $request): RedirectResponse
    {
        $this->updateProfile($request);

        return redirect()->route('coach.settings.edit')->with('status', 'profile-updated');
    }

    public function updateClient(UpdateSettingsRequest $request): RedirectResponse
    {
        $this->updateProfile($request);

        return redirect()->route('client.settings.edit')->with('status', 'profile-updated');
    }

    public function updatePasswordCoach(Request $request): RedirectResponse
    {
        $this->changePassword($request);

        return redirect()->route('coach.settings.edit')->with('status', 'password-updated');
    }

    public function updatePasswordClient(Request $request): RedirectResponse
    {
        $this->changePassword($request);

        return redirect()->route('client.settings.edit')->with('status', 'password-updated');
    }

    private function updateProfile(UpdateSettingsRequest $request): void
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($request->hasFile('avatar')) {
            if ($user->getRawOriginal('avatar')) {
                Storage::delete($user->getRawOriginal('avatar'));
            }
            $path = $request->file('avatar')->store('avatars');
            $user->update(['avatar' => $path]);
        } elseif ($request->boolean('remove_avatar') && $user->getRawOriginal('avatar')) {
            Storage::delete($user->getRawOriginal('avatar'));
            $user->update(['avatar' => null]);
        }
    }

    private function changePassword(Request $request): void
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
    }
}
