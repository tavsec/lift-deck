<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeClientMail;
use App\Models\ClientInvitation;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ClientRegistrationController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    /**
     * Show the code entry form.
     */
    public function showCodeForm(): View
    {
        return view('auth.join');
    }

    /**
     * Show the registration form with pre-filled code.
     */
    public function showRegistrationForm(string $code): View|RedirectResponse
    {
        $invitation = $this->findValidInvitation($code);

        if (! $invitation) {
            return redirect()->route('join')
                ->withErrors(['code' => 'Invalid or expired invitation code.']);
        }

        return view('auth.join-register', [
            'invitation' => $invitation,
            'code' => $code,
        ]);
    }

    /**
     * Handle client registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:8'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $invitation = $this->findValidInvitation($validated['code']);

        if (! $invitation) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'Invalid or expired invitation code.']);
        }

        if ($invitation->track_only_client_id) {
            $user = User::findOrFail($invitation->track_only_client_id);
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_track_only' => false,
            ]);
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'client',
                'coach_id' => $invitation->coach_id,
            ]);
            event(new Registered($user));

            // Track-only clients are already counted in the coach's client list, so upgrading
            // them does not add a new client — only genuinely new clients trigger usage reporting.
            $this->subscriptionService->reportClientUsage($invitation->coach);
        }

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        Mail::to($user)->send(new WelcomeClientMail($user, $invitation->coach));

        return redirect()->route('client.welcome');
    }

    /**
     * Find a valid (non-expired, non-accepted) invitation by code.
     */
    protected function findValidInvitation(string $code): ?ClientInvitation
    {
        return ClientInvitation::where('token', strtoupper($code))
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();
    }
}
