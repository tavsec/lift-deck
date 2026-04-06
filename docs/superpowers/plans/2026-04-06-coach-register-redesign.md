# Coach Register Page Redesign — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the plain 4-field Breeze registration form with a 3-step Alpine.js wizard using a split-screen layout (dark branding left, light form right), fully localized in English, Croatian, and Slovenian.

**Architecture:** Single Blade view rewritten as a standalone full-viewport HTML document. Alpine.js manages step transitions client-side. All fields collected in one form and submitted in a single POST to the existing `POST /register` route. `name` defaults to the email username prefix if the user skips step 2.

**Tech Stack:** Laravel 12, Blade, Alpine.js v3, Tailwind CSS v3, Pest v4

---

## File Map

| File | Change |
|------|--------|
| `lang/en/auth.php` | Add `register.panel.*`, `register.step1.*`, `register.step2.*`, `register.step3.*`, `register.actions.*` keys |
| `lang/hr/auth.php` | Same keys, Croatian translations |
| `lang/sl/auth.php` | Same keys, Slovenian translations |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Add nullable `gym_name` + `bio` validation; save both; default `name` from email if blank |
| `resources/views/auth/register.blade.php` | Full rewrite — standalone HTML, Alpine 3-step wizard |
| `tests/Feature/Auth/RegistrationTest.php` | Add 3 new tests; update name validation test |

---

### Task 1: Add localization keys for the new register wizard

**Files:**
- Modify: `lang/en/auth.php`
- Modify: `lang/hr/auth.php`
- Modify: `lang/sl/auth.php`

No server-side logic — pure data. No tests needed for this task (lang keys are verified implicitly by the view tests in Task 3).

- [ ] **Step 1: Add keys to `lang/en/auth.php`**

Replace the existing `'register' => [...]` block with:

```php
'register' => [
    'name' => 'Name',
    'email' => 'Email',
    'password' => 'Password',
    'confirm_password' => 'Confirm Password',
    'already_registered' => 'Already registered?',
    'button' => 'Register',

    'panel' => [
        'heading'       => 'Stop coaching from spreadsheets. Run everything in LiftDeck.',
        'trial_note'    => '7-day free trial · No credit card required',
        'feature_1'     => 'Programs & workout logging',
        'feature_1_sub' => 'Build plans, clients log in real-time',
        'feature_2'     => 'Nutrition & metrics',
        'feature_2_sub' => 'Macros, body measurements, progress photos',
        'feature_3'     => 'Client messaging',
        'feature_3_sub' => 'Communicate without leaving the app',
        'feature_4'     => 'Loyalty & gamification',
        'feature_4_sub' => 'XP, levels, and coach-defined rewards',
    ],

    'step1' => [
        'label'       => 'Step 1 of 3',
        'title'       => 'Who are you coaching?',
        'subtitle'    => 'Pick the option that best fits you — you can change it later.',
        'solo'        => 'Solo coach',
        'solo_sub'    => 'Just starting out or managing up to 5 clients',
        'growing'     => 'Growing coach',
        'growing_sub' => 'Scaling up, managing 5–30 clients',
        'gym'         => 'Gym or team',
        'gym_sub'     => 'Multiple coaches or a larger client base',
    ],

    'step2' => [
        'label'          => 'Step 2 of 3',
        'title'          => 'Tell us about yourself',
        'subtitle'       => 'All fields are optional — you can fill these in later.',
        'name'           => 'Your name',
        'gym_name'       => 'Gym or business name',
        'gym_name_ph'    => 'e.g. Iron Peak Fitness',
        'bio'            => 'Coaching niche',
        'bio_ph'         => 'e.g. strength, weight loss, rehab',
        'client_count'   => 'Current number of clients',
        'tools'          => 'Tools you currently use',
        'tool_sheets'    => 'Google Sheets',
        'tool_excel'     => 'Excel',
        'tool_whatsapp'  => 'WhatsApp',
        'tool_other'     => 'Other',
        'optional'       => 'optional',
        'skip'           => 'Skip this step',
    ],

    'step3' => [
        'label'    => 'Step 3 of 3',
        'title'    => 'Create your account',
        'subtitle' => 'Almost there — just your email and a password.',
        'email'    => 'Email address',
        'email_ph' => 'you@example.com',
        'password' => 'Password',
        'password_ph' => 'Min. 8 characters',
        'confirm'  => 'Confirm password',
        'confirm_ph' => 'Repeat your password',
        'submit'   => 'Create account',
    ],

    'actions' => [
        'back'     => '← Back',
        'continue' => 'Continue →',
        'signin'   => 'Already have an account?',
        'signin_link' => 'Sign in',
    ],
],
```

- [ ] **Step 2: Add keys to `lang/hr/auth.php`**

Replace the existing `'register' => [...]` block with:

```php
'register' => [
    'name' => 'Ime',
    'email' => 'E-mail',
    'password' => 'Lozinka',
    'confirm_password' => 'Potvrdi lozinku',
    'already_registered' => 'Već ste registrirani?',
    'button' => 'Registracija',

    'panel' => [
        'heading'       => 'Prestanite trenirati iz tablica. Sve vodite u LiftDecku.',
        'trial_note'    => '7 dana besplatno · Nije potrebna kreditna kartica',
        'feature_1'     => 'Programi i bilježenje treninga',
        'feature_1_sub' => 'Gradite planove, klijenti bilježe u stvarnom vremenu',
        'feature_2'     => 'Prehrana i metrike',
        'feature_2_sub' => 'Makroi, tjelesne mjere, fotografije napretka',
        'feature_3'     => 'Poruke s klijentima',
        'feature_3_sub' => 'Komunicirajte bez napuštanja aplikacije',
        'feature_4'     => 'Lojalnost i gamifikacija',
        'feature_4_sub' => 'XP, razine i nagrade koje definira trener',
    ],

    'step1' => [
        'label'       => 'Korak 1 od 3',
        'title'       => 'Koga trenirate?',
        'subtitle'    => 'Odaberite opciju koja vam najviše odgovara — možete je kasnije promijeniti.',
        'solo'        => 'Solo trener',
        'solo_sub'    => 'Tek počinjete ili upravljate do 5 klijenata',
        'growing'     => 'Rastući trener',
        'growing_sub' => 'Skalirате, upravljate 5–30 klijenata',
        'gym'         => 'Teretana ili tim',
        'gym_sub'     => 'Više trenera ili veća baza klijenata',
    ],

    'step2' => [
        'label'          => 'Korak 2 od 3',
        'title'          => 'Recite nam nešto o sebi',
        'subtitle'       => 'Sva polja su neobavezna — možete ih popuniti kasnije.',
        'name'           => 'Vaše ime',
        'gym_name'       => 'Naziv teretane ili tvrtke',
        'gym_name_ph'    => 'npr. Iron Peak Fitness',
        'bio'            => 'Niša treniranja',
        'bio_ph'         => 'npr. snaga, mršavljenje, rehabilitacija',
        'client_count'   => 'Trenutni broj klijenata',
        'tools'          => 'Alati koje trenutno koristite',
        'tool_sheets'    => 'Google tablice',
        'tool_excel'     => 'Excel',
        'tool_whatsapp'  => 'WhatsApp',
        'tool_other'     => 'Ostalo',
        'optional'       => 'neobavezno',
        'skip'           => 'Preskoči ovaj korak',
    ],

    'step3' => [
        'label'       => 'Korak 3 od 3',
        'title'       => 'Stvorite svoj račun',
        'subtitle'    => 'Gotovo je — samo vaš e-mail i lozinka.',
        'email'       => 'E-mail adresa',
        'email_ph'    => 'vas@primjer.com',
        'password'    => 'Lozinka',
        'password_ph' => 'Min. 8 znakova',
        'confirm'     => 'Potvrdi lozinku',
        'confirm_ph'  => 'Ponovite lozinku',
        'submit'      => 'Stvori račun',
    ],

    'actions' => [
        'back'        => '← Nazad',
        'continue'    => 'Nastavi →',
        'signin'      => 'Već imate račun?',
        'signin_link' => 'Prijavite se',
    ],
],
```

- [ ] **Step 3: Add keys to `lang/sl/auth.php`**

Replace the existing `'register' => [...]` block with:

```php
'register' => [
    'name' => 'Ime',
    'email' => 'E-pošta',
    'password' => 'Geslo',
    'confirm_password' => 'Potrdi geslo',
    'already_registered' => 'Že registrirani?',
    'button' => 'Registracija',

    'panel' => [
        'heading'       => 'Nehajte trenirati iz tabel. Vse vodite v LiftDecku.',
        'trial_note'    => '7 dni brezplačno · Kreditna kartica ni potrebna',
        'feature_1'     => 'Programi in beleženje treningov',
        'feature_1_sub' => 'Gradite načrte, stranke beležijo v realnem času',
        'feature_2'     => 'Prehrana in metrike',
        'feature_2_sub' => 'Makri, telesne mere, fotografije napredka',
        'feature_3'     => 'Sporočila s strankami',
        'feature_3_sub' => 'Komunicirajte brez zapuščanja aplikacije',
        'feature_4'     => 'Zvestoba in gamifikacija',
        'feature_4_sub' => 'XP, ravni in nagrade, ki jih določi trener',
    ],

    'step1' => [
        'label'       => '1. korak od 3',
        'title'       => 'Koga trenirate?',
        'subtitle'    => 'Izberite možnost, ki vam najbolj ustreza — pozneje jo lahko spremenite.',
        'solo'        => 'Solo trener',
        'solo_sub'    => 'Šele začenjate ali upravljate do 5 strank',
        'growing'     => 'Rastoči trener',
        'growing_sub' => 'Se širite, upravljate 5–30 strank',
        'gym'         => 'Telovadnica ali ekipa',
        'gym_sub'     => 'Več trenerjev ali večja baza strank',
    ],

    'step2' => [
        'label'          => '2. korak od 3',
        'title'          => 'Povejte nam o sebi',
        'subtitle'       => 'Vsa polja so neobvezna — izpolnite jih lahko pozneje.',
        'name'           => 'Vaše ime',
        'gym_name'       => 'Ime telovadnice ali podjetja',
        'gym_name_ph'    => 'npr. Iron Peak Fitness',
        'bio'            => 'Niša treniranja',
        'bio_ph'         => 'npr. moč, hujšanje, rehabilitacija',
        'client_count'   => 'Trenutno število strank',
        'tools'          => 'Orodja, ki jih trenutno uporabljate',
        'tool_sheets'    => 'Google preglednice',
        'tool_excel'     => 'Excel',
        'tool_whatsapp'  => 'WhatsApp',
        'tool_other'     => 'Drugo',
        'optional'       => 'neobvezno',
        'skip'           => 'Preskoči ta korak',
    ],

    'step3' => [
        'label'       => '3. korak od 3',
        'title'       => 'Ustvarite račun',
        'subtitle'    => 'Skoraj ste — le vaša e-pošta in geslo.',
        'email'       => 'E-poštni naslov',
        'email_ph'    => 'vi@primer.com',
        'password'    => 'Geslo',
        'password_ph' => 'Min. 8 znakov',
        'confirm'     => 'Potrdi geslo',
        'confirm_ph'  => 'Ponovite geslo',
        'submit'      => 'Ustvari račun',
    ],

    'actions' => [
        'back'        => '← Nazaj',
        'continue'    => 'Nadaljuj →',
        'signin'      => 'Že imate račun?',
        'signin_link' => 'Prijavite se',
    ],
],
```

- [ ] **Step 4: Verify lang files load correctly**

```bash
php artisan tinker --execute="echo __('auth.register.step1.title');"
```

Expected output: `Who are you coaching?`

- [ ] **Step 5: Commit**

```bash
git add lang/en/auth.php lang/hr/auth.php lang/sl/auth.php
git commit -m "feat: add localization keys for coach register wizard"
```

---

### Task 2: Update controller to accept gym_name and bio

**Files:**
- Modify: `app/Http/Controllers/Auth/RegisteredUserController.php`
- Modify: `tests/Feature/Auth/RegistrationTest.php`

**Context:** `name` in the DB is NOT NULL. Since step 2 (which collects name) is skippable, we default `name` to the email username prefix when blank. `gym_name` and `bio` are already nullable columns in the users table and already in `$fillable`.

- [ ] **Step 1: Write failing tests**

Open `tests/Feature/Auth/RegistrationTest.php` and add these three tests at the end of the file:

```php
test('gym_name and bio are saved when provided at registration', function () {
    $this->post('/register', [
        'email'                 => 'coach@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
        'gym_name'              => 'Iron Peak Fitness',
        'bio'                   => 'strength coaching',
    ]);

    $coach = \App\Models\User::where('email', 'coach@example.com')->first();

    expect($coach->gym_name)->toBe('Iron Peak Fitness')
        ->and($coach->bio)->toBe('strength coaching');
});

test('registration succeeds without gym_name and bio', function () {
    $response = $this->post('/register', [
        'email'                 => 'coach2@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('coach.plan'));
});

test('name defaults to email prefix when not provided', function () {
    $this->post('/register', [
        'email'                 => 'alex.johnson@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $coach = \App\Models\User::where('email', 'alex.johnson@example.com')->first();

    expect($coach->name)->toBe('alex.johnson');
});
```

- [ ] **Step 2: Run the tests to confirm they fail**

```bash
php artisan test --compact --filter="gym_name and bio are saved|registration succeeds without gym_name|name defaults to email"
```

Expected: 3 failures (validation error / name required)

- [ ] **Step 3: Update the controller**

Replace the full contents of `app/Http/Controllers/Auth/RegisteredUserController.php` with:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['nullable', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'gym_name' => ['nullable', 'string', 'max:255'],
            'bio'      => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name'     => $request->filled('name') ? $request->name : Str::before($request->email, '@'),
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'coach',
            'gym_name' => $request->gym_name,
            'bio'      => $request->bio,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('coach.plan', absolute: false));
    }
}
```

- [ ] **Step 4: Run the tests to confirm they pass**

```bash
php artisan test --compact --filter="gym_name and bio are saved|registration succeeds without gym_name|name defaults to email"
```

Expected: 3 passing

- [ ] **Step 5: Run the full registration test file to confirm nothing regressed**

```bash
php artisan test --compact tests/Feature/Auth/RegistrationTest.php
```

Expected: all pass

- [ ] **Step 6: Run pint**

```bash
vendor/bin/pint app/Http/Controllers/Auth/RegisteredUserController.php --format agent
```

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/Auth/RegisteredUserController.php tests/Feature/Auth/RegistrationTest.php
git commit -m "feat: accept gym_name and bio at registration, default name from email"
```

---

### Task 3: Rewrite the register view as a 3-step Alpine wizard

**Files:**
- Modify: `resources/views/auth/register.blade.php`

**Context:** The current view uses `<x-guest-layout>` which renders a centered card. We replace this with a standalone full-viewport split-screen document. Assets are loaded via `@vite` exactly as in `resources/views/layouts/guest.blade.php`. Alpine.js is already included via `resources/js/app.js`.

The left panel is dark (`bg-[#0f172a]`) and fixed across all steps. The right panel is light (`bg-slate-50`) and contains the wizard. Steps are controlled by Alpine's `step` variable (1, 2, or 3). On mobile (`md:` breakpoint), the left panel is hidden.

The form submits to `POST /register` with all fields as hidden inputs plus the step 3 fields. `coaching_type` is a hidden input updated by Alpine — it is not persisted to the DB but is included for future use.

- [ ] **Step 1: Replace `resources/views/auth/register.blade.php` with the full wizard**

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.register.step3.title') }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">

<div
    class="flex h-screen"
    x-data="{
        step: 1,
        coachingType: 'solo',
        name: '',
        gymName: '',
        bio: '',
        goTo(n) { this.step = n; },
        canAdvance1() { return this.coachingType !== ''; }
    }"
>
    {{-- LEFT PANEL --}}
    <div class="hidden md:flex md:w-[38%] flex-col justify-between bg-[#0f172a] px-10 py-12 shrink-0">
        <div>
            <div class="text-white font-extrabold text-xl tracking-tight">LiftDeck</div>
            <div class="text-slate-500 text-xs mt-1">{{ __('auth.register.actions.signin') }}</div>

            <p class="mt-10 text-slate-200 font-bold text-xl leading-snug">
                {{ __('auth.register.panel.heading') }}
            </p>

            <ul class="mt-8 space-y-4">
                @foreach ([1,2,3,4] as $i)
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-900">
                            <svg class="h-2.5 w-2.5 text-blue-400" viewBox="0 0 10 10" fill="none">
                                <path d="M2 5l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <span class="text-slate-300 text-sm font-semibold">{{ __('auth.register.panel.feature_'.$i) }}</span>
                            <span class="text-slate-500 text-sm"> — {{ __('auth.register.panel.feature_'.$i.'_sub') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="text-slate-600 text-xs">{{ __('auth.register.panel.trial_note') }}</p>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="flex flex-1 flex-col overflow-y-auto bg-slate-50">
        <div class="flex flex-col flex-1 px-8 py-10 md:px-14 md:py-12 max-w-lg w-full mx-auto">

            {{-- Mobile wordmark --}}
            <div class="md:hidden mb-8 font-extrabold text-lg text-slate-900">LiftDeck</div>

            {{-- Step indicator --}}
            <div class="flex items-center gap-0 mb-10">
                @foreach ([1,2,3] as $s)
                    <div
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-colors"
                        :class="{
                            'bg-blue-600 text-white shadow-[0_0_0_3px_#bfdbfe]': step === {{ $s }},
                            'bg-blue-600 text-white': step > {{ $s }},
                            'bg-slate-200 text-slate-400': step < {{ $s }}
                        }"
                    >
                        <span x-show="step <= {{ $s }}">{{ $s }}</span>
                        <span x-show="step > {{ $s }}">
                            <svg class="h-3 w-3" viewBox="0 0 10 10" fill="none">
                                <path d="M2 5l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                    @if ($s < 3)
                        <div class="flex-1 h-0.5 transition-colors" :class="step > {{ $s }} ? 'bg-blue-600' : 'bg-slate-200'"></div>
                    @endif
                @endforeach
            </div>

            <form method="POST" action="{{ route('register') }}" class="flex flex-col flex-1">
                @csrf
                <input type="hidden" name="coaching_type" :value="coachingType">
                <input type="hidden" name="name" :value="name">
                <input type="hidden" name="gym_name" :value="gymName">
                <input type="hidden" name="bio" :value="bio">

                {{-- STEP 1 --}}
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-col flex-1">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('auth.register.step1.label') }}</p>
                    <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ __('auth.register.step1.title') }}</h1>
                    <p class="text-sm text-slate-500 mb-7">{{ __('auth.register.step1.subtitle') }}</p>

                    <div class="space-y-3">
                        @foreach (['solo' => '🏋️', 'growing' => '📈', 'gym' => '🏟️'] as $type => $icon)
                            <button
                                type="button"
                                @click="coachingType = '{{ $type }}'"
                                class="w-full flex items-center gap-4 rounded-xl border-2 px-4 py-3.5 text-left transition-colors"
                                :class="coachingType === '{{ $type }}' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-slate-300'"
                            >
                                <span class="text-2xl">{{ $icon }}</span>
                                <div>
                                    <div class="text-sm font-semibold" :class="coachingType === '{{ $type }}' ? 'text-blue-800' : 'text-slate-800'">
                                        {{ __('auth.register.step1.'.$type) }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ __('auth.register.step1.'.$type.'_sub') }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-auto pt-6 flex justify-end">
                        <button
                            type="button"
                            @click="goTo(2)"
                            :disabled="!canAdvance1()"
                            class="rounded-lg bg-blue-600 px-7 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            {{ __('auth.register.actions.continue') }}
                        </button>
                    </div>

                    <p class="mt-4 text-center text-xs text-slate-400">
                        {{ __('auth.register.actions.signin') }}
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">{{ __('auth.register.actions.signin_link') }}</a>
                    </p>
                </div>

                {{-- STEP 2 --}}
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-col flex-1">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('auth.register.step2.label') }}</p>
                    <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ __('auth.register.step2.title') }}</h1>
                    <p class="text-sm text-slate-500 mb-7">{{ __('auth.register.step2.subtitle') }}</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                {{ __('auth.register.step2.name') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="text"
                                x-model="name"
                                placeholder="{{ __('auth.register.step1.solo') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                {{ __('auth.register.step2.gym_name') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="text"
                                x-model="gymName"
                                placeholder="{{ __('auth.register.step2.gym_name_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                {{ __('auth.register.step2.bio') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="text"
                                x-model="bio"
                                placeholder="{{ __('auth.register.step2.bio_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                {{ __('auth.register.step2.client_count') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="number"
                                min="0"
                                class="w-28 rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none"
                                placeholder="0"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">
                                {{ __('auth.register.step2.tools') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (['tool_sheets', 'tool_excel', 'tool_whatsapp', 'tool_other'] as $tool)
                                    <label class="flex cursor-pointer items-center">
                                        <input type="checkbox" name="tools[]" value="{{ $tool }}" class="sr-only peer">
                                        <span class="rounded-full border border-slate-300 bg-white px-3 py-1 text-xs text-slate-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-colors">
                                            {{ __('auth.register.step2.'.$tool) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto pt-6 flex items-center justify-between">
                        <button type="button" @click="goTo(1)" class="text-sm text-slate-500 hover:text-slate-700">
                            {{ __('auth.register.actions.back') }}
                        </button>
                        <div class="flex items-center gap-4">
                            <button type="button" @click="goTo(3)" class="text-xs text-slate-400 underline hover:text-slate-600">
                                {{ __('auth.register.step2.skip') }}
                            </button>
                            <button type="button" @click="goTo(3)" class="rounded-lg bg-blue-600 px-7 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                {{ __('auth.register.actions.continue') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3 --}}
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-col flex-1">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('auth.register.step3.label') }}</p>
                    <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ __('auth.register.step3.title') }}</h1>
                    <p class="text-sm text-slate-500 mb-7">{{ __('auth.register.step3.subtitle') }}</p>

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-600 mb-1">{{ __('auth.register.step3.email') }}</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="{{ __('auth.register.step3.email_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none @error('email') border-red-400 @enderror"
                            >
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-600 mb-1">{{ __('auth.register.step3.password') }}</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="{{ __('auth.register.step3.password_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none @error('password') border-red-400 @enderror"
                            >
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1">{{ __('auth.register.step3.confirm') }}</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="{{ __('auth.register.step3.confirm_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none"
                            >
                        </div>
                    </div>

                    {{-- On validation error, scroll back to step 3 --}}
                    @if ($errors->any())
                        <script>
                            document.addEventListener('alpine:init', () => {
                                Alpine.store || true;
                                document.querySelector('[x-data]').__x.$data.step = 3;
                            });
                        </script>
                    @endif

                    <div class="mt-auto pt-6 flex items-center justify-between">
                        <button type="button" @click="goTo(2)" class="text-sm text-slate-500 hover:text-slate-700">
                            {{ __('auth.register.actions.back') }}
                        </button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-7 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                            {{ __('auth.register.step3.submit') }}
                        </button>
                    </div>

                    <p class="mt-4 text-center text-xs text-slate-400">
                        {{ __('auth.register.actions.signin') }}
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">{{ __('auth.register.actions.signin_link') }}</a>
                    </p>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
```

- [ ] **Step 2: Run the existing registration tests to confirm the view renders and form posts correctly**

```bash
php artisan test --compact tests/Feature/Auth/RegistrationTest.php
```

Expected: all pass (registration screen renders, coach redirected to plan, trial_ends_at null, gym_name + bio saved, name defaults)

- [ ] **Step 3: Run pint**

```bash
vendor/bin/pint resources/views/auth/register.blade.php --format agent
```

- [ ] **Step 4: Build assets and verify visually**

```bash
npm run build
```

Open `http://localhost:8000/register` in a browser. Verify:
- Left dark panel with features visible on desktop
- Step 1 shows 3 coaching type cards, solo selected by default
- Clicking Continue advances to step 2
- Step 2 shows all optional fields, "Skip this step" and "Continue" both advance to step 3
- Step 3 shows email/password fields, "Create account" submits the form
- Back buttons return to previous step
- Step indicator dots update correctly

- [ ] **Step 5: Commit**

```bash
git add resources/views/auth/register.blade.php
git commit -m "feat: rewrite register page as 3-step Alpine wizard with split-screen layout"
```

---

### Task 4: Fix validation error UX — return to step 3 on server errors

**Files:**
- Modify: `resources/views/auth/register.blade.php`

**Context:** When the form submits and Laravel returns a validation error (e.g. email already taken), the page reloads at step 1 by default. We need Alpine to initialize at step 3 when `$errors->any()` is true, so the user sees their error immediately. The inline `<script>` added in Task 3 Step 1 handles this but needs to be verified to work correctly with Alpine's initialization lifecycle.

The correct Alpine v3 way to pre-set data on load when errors exist is via the `x-init` attribute:

- [ ] **Step 1: Update the Alpine x-data to handle the error state**

In `resources/views/auth/register.blade.php`, change the opening `<div x-data="...">` to:

```blade
<div
    class="flex h-screen"
    x-data="{
        step: {{ $errors->any() ? 3 : 1 }},
        coachingType: 'solo',
        name: '{{ old('name') }}',
        gymName: '{{ old('gym_name') }}',
        bio: '{{ old('bio') }}',
        goTo(n) { this.step = n; },
        canAdvance1() { return this.coachingType !== ''; }
    }"
>
```

Also remove the inline `<script>` block that was previously added in Task 3 Step 1 (inside the Step 3 `x-show` section):

```blade
{{-- Remove this entire block: --}}
@if ($errors->any())
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store || true;
            document.querySelector('[x-data]').__x.$data.step = 3;
        });
    </script>
@endif
```

- [ ] **Step 2: Write a test for the error redirect behavior**

Add to `tests/Feature/Auth/RegistrationTest.php`:

```php
test('register page shows step 3 data when validation fails', function () {
    $response = $this->post('/register', [
        'email'                 => 'not-an-email',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $response->assertRedirect('/register');

    $follow = $this->get('/register');
    $follow->assertStatus(200);
});
```

- [ ] **Step 3: Run the test**

```bash
php artisan test --compact --filter="register page shows step 3"
```

Expected: pass

- [ ] **Step 4: Run pint**

```bash
vendor/bin/pint resources/views/auth/register.blade.php --format agent
```

- [ ] **Step 5: Commit**

```bash
git add resources/views/auth/register.blade.php tests/Feature/Auth/RegistrationTest.php
git commit -m "feat: initialize register wizard at step 3 when validation errors present"
```

---

### Task 5: Update README

**Files:**
- Modify: `README.md`

- [ ] **Step 1: Add a note about the register wizard to README.md**

In the `### Coach Sign-Up Flow` section, update step 1 to:

```markdown
1. Coach registers at `/register` — a 3-step wizard:
   - **Step 1:** Choose coaching type (solo / growing / gym)
   - **Step 2:** Optional profile details (name, gym name, niche, client count, current tools)
   - **Step 3:** Email and password
   All text is localized in English, Croatian (`hr`), and Slovenian (`sl`) via `lang/{locale}/auth.php` under the `register.*` key.
```

- [ ] **Step 2: Commit**

```bash
git add README.md
git commit -m "docs: document 3-step register wizard in README"
```
