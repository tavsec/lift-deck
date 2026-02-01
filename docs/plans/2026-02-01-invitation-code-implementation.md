# Invitation Code System Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Replace email-based client invitations with a code-based system where coaches generate shareable codes.

**Architecture:** Migration updates the token column, new controller handles client registration via code, onboarding flow guides new clients through profile setup.

**Tech Stack:** Laravel 12, Blade, Tailwind CSS v4

---

## Task 1: Update Database Schema

**Files:**
- Create: `database/migrations/2026_02_01_000001_update_client_invitations_for_codes.php`
- Modify: `app/Models/ClientInvitation.php`

**Step 1: Create migration**

```bash
php artisan make:migration update_client_invitations_for_codes --no-interaction
```

**Step 2: Write migration content**

In the new migration file:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_invitations', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('token', 8)->change();
        });
    }

    public function down(): void
    {
        Schema::table('client_invitations', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('token', 64)->change();
        });
    }
};
```

**Step 3: Update ClientInvitation model token generation**

In `app/Models/ClientInvitation.php`, replace `generateToken()`:

```php
public static function generateToken(): string
{
    $characters = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $token = '';

    for ($i = 0; $i < 8; $i++) {
        $token .= $characters[random_int(0, strlen($characters) - 1)];
    }

    return $token;
}

public static function generateUniqueToken(): string
{
    do {
        $token = self::generateToken();
    } while (self::where('token', $token)->whereNull('accepted_at')->where('expires_at', '>', now())->exists());

    return $token;
}
```

**Step 4: Run migration**

```bash
php artisan migrate
```

**Step 5: Commit**

```bash
git add -A && git commit -m "feat: update client_invitations schema for short codes"
```

---

## Task 2: Update Coach Controller for Code Generation

**Files:**
- Modify: `app/Http/Controllers/Coach/ClientController.php`

**Step 1: Simplify the store method**

Replace the `store()` method in `ClientController.php`:

```php
/**
 * Generate a new invitation code.
 */
public function store(Request $request): RedirectResponse
{
    $coach = auth()->user();

    $invitation = ClientInvitation::create([
        'coach_id' => $coach->id,
        'token' => ClientInvitation::generateUniqueToken(),
        'expires_at' => now()->addDays(7),
    ]);

    return redirect()->route('coach.clients.index')
        ->with('success', 'Invitation code generated!')
        ->with('invitation_code', $invitation->token);
}
```

**Step 2: Commit**

```bash
git add -A && git commit -m "feat: simplify coach invitation to code generation"
```

---

## Task 3: Update Coach Clients Index View with Code Display

**Files:**
- Modify: `resources/views/coach/clients/index.blade.php`

**Step 1: Add modal for displaying generated code**

Add this after the success alert (around line 32), before the Search section:

```blade
@if(session('invitation_code'))
    <div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>

            <div x-show="open" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Invitation Code Generated</h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-4">Share this code with your client to let them register:</p>
                            <div class="bg-gray-100 rounded-lg p-4 mb-4">
                                <p id="invitation-code" class="text-3xl font-mono font-bold tracking-wider text-gray-900">{{ session('invitation_code') }}</p>
                            </div>
                            <button onclick="navigator.clipboard.writeText('{{ session('invitation_code') }}')" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copy Code
                            </button>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-500 mb-2">Or share this link:</p>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly value="{{ url('/join/' . session('invitation_code')) }}" class="flex-1 text-sm rounded-md border-gray-300 bg-gray-50">
                                <button onclick="navigator.clipboard.writeText('{{ url('/join/' . session('invitation_code')) }}')" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6">
                    <button @click="open = false" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
```

**Step 2: Update pending invitations section to show codes**

Replace the pending invitations section (around lines 47-64):

```blade
<!-- Pending Invitations -->
@if($pendingInvitations->count() > 0)
    <div class="bg-yellow-50 rounded-lg shadow p-4">
        <h3 class="text-sm font-medium text-yellow-800 mb-3">Pending Invitations ({{ $pendingInvitations->count() }})</h3>
        <div class="space-y-2">
            @foreach($pendingInvitations as $invitation)
                <div class="flex items-center justify-between bg-white rounded-md p-3 border border-yellow-200">
                    <div class="flex items-center gap-4">
                        <span class="font-mono font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $invitation->token }}</span>
                        <button onclick="navigator.clipboard.writeText('{{ $invitation->token }}')" class="text-gray-400 hover:text-gray-600" title="Copy code">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="text-xs text-gray-500">
                        Expires {{ $invitation->expires_at->diffForHumans() }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
```

**Step 3: Update the "Invite Client" button text**

Change the button text from "Invite Client" to "Generate Code" (around line 11):

```blade
<a href="{{ route('coach.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Generate Code
</a>
```

Also update the empty state button (around line 138).

**Step 4: Commit**

```bash
git add -A && git commit -m "feat: update clients index to display invitation codes"
```

---

## Task 4: Replace Coach Invite Form with Generate Button

**Files:**
- Modify: `resources/views/coach/clients/create.blade.php`

**Step 1: Replace the entire file content**

```blade
<x-layouts.coach>
    <x-slot:title>Generate Invitation Code</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Clients
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Generate Invitation Code</h1>
            <p class="mt-1 text-sm text-gray-500">Create a code that your client can use to register and join your coaching program.</p>
        </div>

        <!-- Generate Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>

                <h2 class="text-lg font-medium text-gray-900 mb-2">Ready to invite a client?</h2>
                <p class="text-sm text-gray-500 mb-6">Click the button below to generate a unique invitation code.</p>

                <form method="POST" action="{{ route('coach.clients.store') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Generate Invitation Code
                    </button>
                </form>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="bg-blue-50 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">How invitation codes work</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>A unique 8-character code will be generated</li>
                                    <li>Share the code or link with your client</li>
                                    <li>The code expires after 7 days</li>
                                    <li>Your client will complete a short onboarding after registering</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
```

**Step 2: Commit**

```bash
git add -A && git commit -m "feat: replace invite form with code generation button"
```

---

## Task 5: Create Client Registration Controller

**Files:**
- Create: `app/Http/Controllers/Auth/ClientRegistrationController.php`

**Step 1: Create the controller**

```bash
php artisan make:controller Auth/ClientRegistrationController --no-interaction
```

**Step 2: Write controller content**

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ClientInvitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ClientRegistrationController extends Controller
{
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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'client',
            'coach_id' => $invitation->coach_id,
        ]);

        $invitation->update(['accepted_at' => now()]);

        event(new Registered($user));

        Auth::login($user);

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
```

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: add client registration controller"
```

---

## Task 6: Create Client Join Views

**Files:**
- Create: `resources/views/auth/join.blade.php`
- Create: `resources/views/auth/join-register.blade.php`

**Step 1: Create code entry view**

Create `resources/views/auth/join.blade.php`:

```blade
<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Join as a Client</h1>
        <p class="mt-2 text-sm text-gray-600">Enter the invitation code from your coach</p>
    </div>

    <form method="GET" action="" id="code-form">
        <div>
            <x-input-label for="code" :value="__('Invitation Code')" />
            <x-text-input
                id="code"
                class="block mt-1 w-full text-center text-2xl font-mono tracking-widest uppercase"
                type="text"
                name="code"
                :value="old('code')"
                required
                autofocus
                maxlength="8"
                placeholder="XXXXXXXX"
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center" id="continue-btn">
                {{ __('Continue') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                Are you a coach?
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500">Register here</a>
            </p>
        </div>
    </form>

    <script>
        document.getElementById('code-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const code = document.getElementById('code').value.toUpperCase();
            if (code.length === 8) {
                window.location.href = '/join/' + code;
            }
        });
    </script>
</x-guest-layout>
```

**Step 2: Create registration form view**

Create `resources/views/auth/join-register.blade.php`:

```blade
<x-guest-layout>
    <div class="text-center mb-6">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Complete Your Registration</h1>
        <p class="mt-2 text-sm text-gray-600">
            You're joining <span class="font-semibold">{{ $invitation->coach->gym_name ?? $invitation->coach->name }}</span>
        </p>
    </div>

    <form method="POST" action="{{ route('join.register') }}">
        @csrf
        <input type="hidden" name="code" value="{{ $code }}">

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('join') }}" class="text-sm text-gray-600 hover:text-gray-500">
                Use a different code
            </a>
        </div>
    </form>
</x-guest-layout>
```

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: add client join views"
```

---

## Task 7: Create Client Onboarding Controller

**Files:**
- Create: `app/Http/Controllers/Client/OnboardingController.php`

**Step 1: Create the controller**

```bash
php artisan make:controller Client/OnboardingController --no-interaction
```

**Step 2: Write controller content**

```php
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
```

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: add client onboarding controller"
```

---

## Task 8: Create Client Welcome and Onboarding Views

**Files:**
- Create: `resources/views/client/welcome.blade.php`
- Create: `resources/views/client/onboarding.blade.php`

**Step 1: Create welcome view**

Create `resources/views/client/welcome.blade.php`:

```blade
<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
            <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome!</h1>

        <p class="text-lg text-gray-600 mb-8">
            You're now connected with<br>
            <span class="font-semibold text-gray-900">{{ $coach->gym_name ?? $coach->name }}</span>
        </p>

        @if($coach->avatar)
            <img src="{{ $coach->avatar }}" alt="{{ $coach->name }}" class="mx-auto h-24 w-24 rounded-full object-cover mb-8">
        @endif

        <p class="text-sm text-gray-500 mb-8">
            Let's set up your profile so your coach can create the perfect program for you.
        </p>

        <a href="{{ route('client.onboarding') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Continue to Setup
        </a>
    </div>
</x-guest-layout>
```

**Step 2: Create onboarding view**

Create `resources/views/client/onboarding.blade.php`:

```blade
<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tell us about yourself</h1>
        <p class="mt-2 text-sm text-gray-600">This helps your coach create the perfect program for you.</p>
    </div>

    <form method="POST" action="{{ route('client.onboarding.store') }}">
        @csrf

        <!-- Goal -->
        <div class="mb-6">
            <x-input-label :value="__('What is your primary goal?')" class="mb-3" />
            <div class="space-y-2">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('goal') === 'fat_loss' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="goal" value="fat_loss" class="text-blue-600 focus:ring-blue-500" {{ old('goal') === 'fat_loss' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Fat Loss</span>
                        <span class="block text-sm text-gray-500">Lose weight and get leaner</span>
                    </span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('goal') === 'strength' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="goal" value="strength" class="text-blue-600 focus:ring-blue-500" {{ old('goal') === 'strength' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Build Strength</span>
                        <span class="block text-sm text-gray-500">Get stronger and build muscle</span>
                    </span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('goal') === 'general_fitness' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="goal" value="general_fitness" class="text-blue-600 focus:ring-blue-500" {{ old('goal') === 'general_fitness' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">General Fitness</span>
                        <span class="block text-sm text-gray-500">Improve overall health and fitness</span>
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('goal')" class="mt-2" />
        </div>

        <!-- Experience Level -->
        <div class="mb-6">
            <x-input-label :value="__('What is your experience level?')" class="mb-3" />
            <div class="space-y-2">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('experience_level') === 'beginner' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="experience_level" value="beginner" class="text-blue-600 focus:ring-blue-500" {{ old('experience_level') === 'beginner' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Beginner</span>
                        <span class="block text-sm text-gray-500">New to working out or less than 6 months</span>
                    </span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('experience_level') === 'intermediate' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="experience_level" value="intermediate" class="text-blue-600 focus:ring-blue-500" {{ old('experience_level') === 'intermediate' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Intermediate</span>
                        <span class="block text-sm text-gray-500">6 months to 2 years of consistent training</span>
                    </span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('experience_level') === 'advanced' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="experience_level" value="advanced" class="text-blue-600 focus:ring-blue-500" {{ old('experience_level') === 'advanced' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Advanced</span>
                        <span class="block text-sm text-gray-500">More than 2 years of serious training</span>
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('experience_level')" class="mt-2" />
        </div>

        <!-- Injuries -->
        <div class="mb-6">
            <x-input-label for="injuries" :value="__('Any injuries or limitations? (optional)')" />
            <textarea id="injuries" name="injuries" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="E.g., bad knee, lower back issues...">{{ old('injuries') }}</textarea>
            <x-input-error :messages="$errors->get('injuries')" class="mt-2" />
        </div>

        <!-- Equipment Access -->
        <div class="mb-6">
            <x-input-label for="equipment_access" :value="__('What equipment do you have access to? (optional)')" />
            <textarea id="equipment_access" name="equipment_access" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="E.g., full gym, home dumbbells only...">{{ old('equipment_access') }}</textarea>
            <x-input-error :messages="$errors->get('equipment_access')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-8">
            <form method="POST" action="{{ route('client.onboarding.skip') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                    Skip for now
                </button>
            </form>

            <x-primary-button>
                {{ __('Complete Setup') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
```

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: add client welcome and onboarding views"
```

---

## Task 9: Add Routes

**Files:**
- Modify: `routes/web.php`

**Step 1: Add join routes (guest only)**

Add after line 9 (after the welcome route):

```php
// Client registration via invitation code
Route::middleware('guest')->group(function () {
    Route::get('join', [App\Http\Controllers\Auth\ClientRegistrationController::class, 'showCodeForm'])->name('join');
    Route::get('join/{code}', [App\Http\Controllers\Auth\ClientRegistrationController::class, 'showRegistrationForm'])->name('join.code');
    Route::post('join', [App\Http\Controllers\Auth\ClientRegistrationController::class, 'register'])->name('join.register');
});
```

**Step 2: Add onboarding routes to client group**

Add inside the client routes group (after line 48):

```php
Route::get('welcome', [Client\OnboardingController::class, 'welcome'])->name('welcome');
Route::get('onboarding', [Client\OnboardingController::class, 'show'])->name('onboarding');
Route::post('onboarding', [Client\OnboardingController::class, 'store'])->name('onboarding.store');
Route::post('onboarding/skip', [Client\OnboardingController::class, 'skip'])->name('onboarding.skip');
```

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: add routes for client join and onboarding"
```

---

## Task 10: Run Pint and Final Verification

**Step 1: Run Laravel Pint**

```bash
vendor/bin/pint --dirty
```

**Step 2: Run tests**

```bash
php artisan test --compact
```

**Step 3: Verify routes are registered**

```bash
php artisan route:list --path=join && php artisan route:list --path=client
```

**Step 4: Final commit if Pint made changes**

```bash
git add -A && git commit -m "style: apply pint formatting"
```

---

## Summary of Files

**Created:**
- `database/migrations/xxxx_update_client_invitations_for_codes.php`
- `app/Http/Controllers/Auth/ClientRegistrationController.php`
- `app/Http/Controllers/Client/OnboardingController.php`
- `resources/views/auth/join.blade.php`
- `resources/views/auth/join-register.blade.php`
- `resources/views/client/welcome.blade.php`
- `resources/views/client/onboarding.blade.php`

**Modified:**
- `app/Models/ClientInvitation.php`
- `app/Http/Controllers/Coach/ClientController.php`
- `resources/views/coach/clients/index.blade.php`
- `resources/views/coach/clients/create.blade.php`
- `routes/web.php`
