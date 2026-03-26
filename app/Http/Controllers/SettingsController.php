<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function editCoach(): View
    {
        return view('coach.settings.edit', ['user' => auth()->user()]);
    }

    public function editClient(): View
    {
        return view('client.settings.edit', ['user' => auth()->user()]);
    }

    public function update(UpdateSettingsRequest $request, string $role): RedirectResponse
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

        $redirectRoute = $role === 'coach' ? 'coach.settings.edit' : 'client.settings.edit';

        return redirect()->route($redirectRoute)->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request, string $role): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $redirectRoute = $role === 'coach' ? 'coach.settings.edit' : 'client.settings.edit';

        return redirect()->route($redirectRoute)->with('status', 'password-updated');
    }
}
