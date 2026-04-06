# Localization Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add English, Slovenian (sl), and Croatian (hr) localization across the entire LiftDeck app — authenticated UI, emails, and the public landing page.

**Architecture:** A `SetLocale` middleware reads `auth()->user()->locale` on every authenticated request and calls `App::setLocale()`. Language is toggled via a persistent `<x-locale-switcher>` component in both nav layouts. The landing page uses URL-based routing (`/en`, `/si`, `/hr`) with IP-based auto-detection on `/` (result cached 30 days per IP). All views use `__()` translation keys stored in `lang/{en,sl,hr}/` files.

**Tech Stack:** Laravel 12, Blade, `App::setLocale()`, `Http::get()` for ip-api.com, Laravel cache.

---

## Context

- **Worktree:** `.worktrees/localization` on branch `feature/localization`
- **Run tests from main project dir:** `php -d memory_limit=512M vendor/bin/pest --compact`
- **Baseline:** 296 passing, 11 failing (pre-existing Breeze scaffold failures — ignore them)
- **Pint:** Run `vendor/bin/pint --dirty --format agent` before each commit

### Key files to know

| File | Role |
|------|------|
| `resources/views/components/layouts/coach.blade.php` | Coach sidebar + mobile header |
| `resources/views/components/layouts/client.blade.php` | Client bottom nav + top header |
| `app/Http/Controllers/UserPreferencesController.php` | Pattern for simple user preference updates (`back()`) |
| `app/Http/Middleware/EnsureUserHasRole.php` | Pattern for middleware |
| `bootstrap/app.php` | Where middleware aliases are registered |
| `app/Models/User.php` | Add `locale` to `$fillable` |
| `app/Mail/WelcomeClientMail.php` | Pattern for mailables |
| `routes/web.php` | All routes — landing page routes at top |

---

## Task 1: Add `locale` column to users

**Files:**
- Create migration via artisan

**Step 1: Generate migration**

```bash
php artisan make:migration add_locale_to_users_table --no-interaction
```

**Step 2: Write the migration**

Edit the generated file in `database/migrations/`:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->string('locale', 2)->default('en')->after('dark_mode');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->dropColumn('locale');
    });
}
```

**Step 3: Run migration**

```bash
php artisan migrate --no-interaction
```

**Step 4: Update User model**

In `app/Models/User.php`, add `'locale'` to the `$fillable` array (after `'dark_mode'`).

**Step 5: Write failing test**

Create `tests/Feature/LocaleTest.php`:

```php
<?php

use App\Models\User;

it('saves locale to user record', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    expect($user->locale)->toBe('en');

    $user->update(['locale' => 'sl']);

    expect($user->fresh()->locale)->toBe('sl');
});
```

**Step 6: Run test to verify it passes**

```bash
php -d memory_limit=512M vendor/bin/pest --compact --filter="saves locale to user record"
```

Expected: PASS

**Step 7: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/migrations/ app/Models/User.php tests/Feature/LocaleTest.php
git commit -m "feat: add locale column to users table"
```

---

## Task 2: SetLocale Middleware

**Files:**
- Create: `app/Http/Middleware/SetLocale.php`
- Modify: `bootstrap/app.php`

**Step 1: Generate middleware**

```bash
php artisan make:middleware SetLocale --no-interaction
```

**Step 2: Implement middleware**

Replace the contents of `app/Http/Middleware/SetLocale.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            App::setLocale(auth()->user()->locale ?? 'en');
        }

        return $next($request);
    }
}
```

**Step 3: Register as global web middleware**

In `bootstrap/app.php`, add it to `withMiddleware`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        'feature' => \App\Http\Middleware\EnsureFeatureActive::class,
    ]);
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
    ]);
    $middleware->trustProxies('*');
})
```

**Step 4: Add test to `tests/Feature/LocaleTest.php`**

```php
it('sets app locale from authenticated user locale', function (): void {
    $user = User::factory()->create(['locale' => 'sl']);

    $this->actingAs($user)
        ->get(route('coach.dashboard'))
        ->assertOk();

    expect(app()->getLocale())->toBe('sl');
});
```

> Note: The `app()->getLocale()` check only works within the same process; this test verifies the middleware fires without errors and the user can access the route. A more reliable check is verifying translated output — we'll add that in the view tasks.

**Step 5: Run tests**

```bash
php -d memory_limit=512M vendor/bin/pest --compact --filter="LocaleTest"
```

Expected: PASS

**Step 6: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Middleware/SetLocale.php bootstrap/app.php tests/Feature/LocaleTest.php
git commit -m "feat: add SetLocale middleware registered on web group"
```

---

## Task 3: Locale switcher controller + route

**Files:**
- Modify: `app/Http/Controllers/UserPreferencesController.php`
- Modify: `routes/web.php`

**Step 1: Add `updateLocale` to UserPreferencesController**

Following the existing `toggleDarkMode` pattern:

```php
/**
 * Update the authenticated user's locale preference.
 */
public function updateLocale(Request $request): RedirectResponse
{
    $request->validate([
        'locale' => ['required', 'string', 'in:en,sl,hr'],
    ]);

    $request->user()->update(['locale' => $request->locale]);

    return back();
}
```

**Step 2: Add route to `routes/web.php`**

Inside the `auth + verified` middleware group (where `user.dark-mode.toggle` is registered), add:

```php
Route::patch('user/locale', [UserPreferencesController::class, 'updateLocale'])->name('user.locale.update');
```

Check `routes/web.php` for the exact location of `user.dark-mode.toggle` to add it alongside.

**Step 3: Add test to `tests/Feature/LocaleTest.php`**

```php
it('updates user locale via patch request', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->patch(route('user.locale.update'), ['locale' => 'sl'])
        ->assertRedirect();

    expect($user->fresh()->locale)->toBe('sl');
});

it('rejects invalid locale values', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->patch(route('user.locale.update'), ['locale' => 'xx'])
        ->assertSessionHasErrors('locale');
});
```

**Step 4: Run tests**

```bash
php -d memory_limit=512M vendor/bin/pest --compact --filter="LocaleTest"
```

Expected: PASS

**Step 5: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/UserPreferencesController.php routes/web.php tests/Feature/LocaleTest.php
git commit -m "feat: add locale update endpoint on UserPreferencesController"
```

---

## Task 4: Locale switcher Blade component

**Files:**
- Create: `resources/views/components/locale-switcher.blade.php`
- Modify: `resources/views/components/layouts/coach.blade.php`
- Modify: `resources/views/components/layouts/client.blade.php`

**Step 1: Create the component**

Create `resources/views/components/locale-switcher.blade.php`:

```blade
@php $currentLocale = auth()->user()->locale ?? 'en'; @endphp

<div class="flex items-center gap-1">
    @foreach(['en' => 'EN', 'sl' => 'SL', 'hr' => 'HR'] as $locale => $label)
        <form method="POST" action="{{ route('user.locale.update') }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="locale" value="{{ $locale }}">
            <button
                type="submit"
                class="text-xs font-semibold px-2 py-1 rounded {{ $currentLocale === $locale ? 'bg-blue-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}"
            >{{ $label }}</button>
        </form>
    @endforeach
</div>
```

**Step 2: Add to coach sidebar (desktop)**

In `resources/views/components/layouts/coach.blade.php`, find the bottom of the desktop sidebar `<aside>` (around line 200+, near logout/user info). Add `<x-locale-switcher />` in the sidebar footer section, alongside the logout button.

Also add to the mobile header section, alongside the dark mode toggle.

**Step 3: Add to client layout**

In `resources/views/components/layouts/client.blade.php`, add `<x-locale-switcher />` in the top header `<div class="flex items-center space-x-3">` alongside the dark mode toggle, before logout.

**Step 4: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/locale-switcher.blade.php resources/views/components/layouts/
git commit -m "feat: add locale switcher component to coach sidebar and client nav"
```

---

## Task 5: Landing page locale routing

**Files:**
- Create: `app/Http/Controllers/LandingLocaleController.php`
- Modify: `routes/web.php`

**Step 1: Generate controller**

```bash
php artisan make:controller LandingLocaleController --no-interaction
```

**Step 2: Implement controller**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class LandingLocaleController extends Controller
{
    /** Maps URL path segments to internal locale codes. */
    private const URL_TO_LOCALE = [
        'en' => 'en',
        'si' => 'sl',
        'hr' => 'hr',
    ];

    /** Maps ISO country codes to internal locale codes. */
    private const COUNTRY_TO_LOCALE = [
        'SI' => 'sl',
        'HR' => 'hr',
    ];

    /** Maps internal locale codes to URL path segments. */
    private const LOCALE_TO_URL = [
        'en' => 'en',
        'sl' => 'si',
        'hr' => 'hr',
    ];

    public function index(Request $request): RedirectResponse
    {
        $locale = $this->detectLocaleFromIp($request->ip());
        $urlPath = self::LOCALE_TO_URL[$locale] ?? 'en';

        return redirect("/{$urlPath}");
    }

    public function show(string $locale): View
    {
        $internalLocale = self::URL_TO_LOCALE[$locale] ?? 'en';
        App::setLocale($internalLocale);

        return view('welcome');
    }

    private function detectLocaleFromIp(string $ip): string
    {
        return Cache::remember("ip_locale_{$ip}", now()->addDays(30), function () use ($ip): string {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");

                if ($response->ok()) {
                    $countryCode = $response->json('countryCode', '');

                    return self::COUNTRY_TO_LOCALE[$countryCode] ?? 'en';
                }
            } catch (\Throwable) {
                // Fall through to default
            }

            return 'en';
        });
    }
}
```

**Step 3: Update `routes/web.php`**

Replace the existing root route:

```php
// Old:
Route::get('/', function () {
    return view('welcome');
});

// New:
Route::get('/', [LandingLocaleController::class, 'index'])->name('landing.detect');
Route::get('/{locale}', [LandingLocaleController::class, 'show'])
    ->where('locale', 'en|si|hr')
    ->name('landing');
```

Add the import at the top: `use App\Http\Controllers\LandingLocaleController;`

**Step 4: Write tests**

Create `tests/Feature/LandingLocaleTest.php`:

```php
<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('redirects root to /en when ip-api returns unknown country', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(['countryCode' => 'US'], 200)]);

    $this->get('/')->assertRedirect('/en');
});

it('redirects root to /si for slovenian IP', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(['countryCode' => 'SI'], 200)]);

    $this->get('/')->assertRedirect('/si');
});

it('redirects root to /hr for croatian IP', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(['countryCode' => 'HR'], 200)]);

    $this->get('/')->assertRedirect('/hr');
});

it('falls back to /en when ip-api request fails', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(null, 500)]);
    Cache::flush();

    $this->get('/')->assertRedirect('/en');
});

it('caches ip locale lookup', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(['countryCode' => 'SI'], 200)]);
    Cache::flush();

    $this->get('/');
    $this->get('/');

    Http::assertSentCount(1);
});

it('serves landing page at /en', function (): void {
    $this->get('/en')->assertOk()->assertViewIs('welcome');
});

it('serves landing page at /si', function (): void {
    $this->get('/si')->assertOk()->assertViewIs('welcome');
});

it('serves landing page at /hr', function (): void {
    $this->get('/hr')->assertOk()->assertViewIs('welcome');
});
```

**Step 5: Run tests**

```bash
php -d memory_limit=512M vendor/bin/pest --compact --filter="LandingLocaleTest"
```

Expected: PASS

**Step 6: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/LandingLocaleController.php routes/web.php tests/Feature/LandingLocaleTest.php
git commit -m "feat: add IP-based landing page locale routing with caching"
```

---

## Task 6: English translation files (source of truth)

**Files:**
- Create: `lang/en/landing.php`
- Create: `lang/en/auth.php`
- Create: `lang/en/coach.php`
- Create: `lang/en/client.php`
- Create: `lang/en/emails.php`
- Create: `lang/en/validation.php`
- Create: `lang/en/pagination.php`

**Step 1: Create the `lang/en/` directory structure**

```bash
mkdir -p lang/en lang/sl lang/hr
```

**Step 2: Create `lang/en/landing.php`**

Read `resources/views/welcome.blade.php` in sections and extract all user-visible strings. Structure:

```php
<?php

return [
    // Hero section
    'hero_title' => 'The Coaching Platform Built for Results',
    'hero_subtitle' => 'Everything your coaching business needs — client management, workout programs, nutrition tracking, and more.',
    'cta_start' => 'Start Free Trial',
    'cta_login' => 'Sign In',

    // Features section — add all headings, descriptions, labels found in the view
    // ... (extract from welcome.blade.php)

    // Navigation
    'nav_features' => 'Features',
    'nav_pricing' => 'Pricing',
    'nav_login' => 'Sign in',
    'nav_signup' => 'Get Started',
];
```

> **Important:** Read `welcome.blade.php` in full (use offset/limit in sections) and extract every hardcoded string. This file is large — read it in chunks of 100 lines.

**Step 3: Create `lang/en/auth.php`**

Read `resources/views/auth/*.blade.php` and extract all strings:

```php
<?php

return [
    'login_title' => 'Sign in to your account',
    'email' => 'Email address',
    'password' => 'Password',
    'remember_me' => 'Remember me',
    'forgot_password' => 'Forgot your password?',
    'sign_in' => 'Sign in',
    'no_account' => "Don't have an account?",
    'register_title' => 'Create your account',
    'name' => 'Name',
    'confirm_password' => 'Confirm password',
    'sign_up' => 'Sign up',
    'join_title' => 'Join your coach',
    'invitation_code' => 'Invitation code',
    'continue' => 'Continue',
    'reset_password_title' => 'Reset your password',
    'send_reset_link' => 'Send reset link',
    'new_password' => 'New password',
    'reset' => 'Reset Password',
    'verify_email_title' => 'Verify your email',
    'verify_email_message' => 'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?',
    'resend_verification' => 'Resend verification email',
    'confirm_password_title' => 'Confirm password',
    'confirm_password_message' => 'This is a secure area of the application. Please confirm your password before continuing.',
    'confirm' => 'Confirm',
    'logout' => 'Log out',
    // Add more as found in the views
];
```

**Step 4: Create `lang/en/coach.php`**

Read coach views and extract strings. Key areas: dashboard, clients list/show, programs, exercises, meals, check-in, tracking, branding, messages, rewards, achievements:

```php
<?php

return [
    // Navigation
    'nav_dashboard' => 'Dashboard',
    'nav_clients' => 'Clients',
    'nav_programs' => 'Programs',
    'nav_exercises' => 'Exercises',
    'nav_meals' => 'Meals',
    'nav_tracking' => 'Tracking',
    'nav_messages' => 'Messages',
    'nav_branding' => 'Branding',
    'nav_loyalty' => 'Loyalty',
    'nav_rewards' => 'Rewards',
    'nav_achievements' => 'Achievements',

    // Common actions
    'create' => 'Create',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'back' => 'Back',
    'confirm_delete' => 'Are you sure you want to delete this?',

    // Dashboard
    'dashboard_title' => 'Dashboard',
    // ... extract from resources/views/coach/dashboard.blade.php

    // Clients
    'clients_title' => 'Clients',
    'add_client' => 'Add Client',
    // ... extract from resources/views/coach/clients/

    // Programs — extract from resources/views/coach/programs/
    // Exercises — extract from resources/views/coach/exercises/
    // Meals — extract from resources/views/coach/meals/
    // etc.
];
```

> **Important:** Read each coach view file and extract every hardcoded string. Do this exhaustively.

**Step 5: Create `lang/en/client.php`**

Read client views and extract strings:

```php
<?php

return [
    // Navigation (bottom tabs)
    'nav_home' => 'Home',
    'nav_program' => 'Program',
    'nav_log' => 'Log',
    'nav_check_in' => 'Check-in',
    'nav_nutrition' => 'Nutrition',
    'nav_history' => 'History',

    // Layout strings
    'unfinished_workout' => 'Unfinished workout:',
    'last_saved_at' => 'Last saved at',
    'continue' => 'Continue',
    'viewing_cached_content' => "You're viewing cached content. Some data may be outdated.",

    // Dashboard — extract from resources/views/client/dashboard.blade.php
    // Program — extract from resources/views/client/program.blade.php
    // Log — extract from resources/views/client/log*.blade.php
    // Check-in — extract from resources/views/client/check-in.blade.php
    // Nutrition — extract from resources/views/client/nutrition.blade.php
    // History — extract from resources/views/client/history*.blade.php
    // Achievements — extract from resources/views/client/achievements*
    // Loyalty — extract from resources/views/client/loyalty.blade.php
    // Rewards — extract from resources/views/client/rewards.blade.php
    // Onboarding — extract from resources/views/client/onboarding.blade.php
    // Messages — extract from resources/views/client/messages.blade.php
    // Welcome — extract from resources/views/client/welcome.blade.php
];
```

**Step 6: Create `lang/en/emails.php`**

Read `resources/views/mail/*.blade.php`:

```php
<?php

return [
    'welcome_client_subject' => "Welcome to :gym_name's coaching!",
    'welcome_client_greeting' => 'Welcome, :name!',
    // ... extract from mail/welcome-client.blade.php

    'reward_redeemed_subject' => ':name redeemed a reward',
    // ... extract from mail/reward-redeemed.blade.php
];
```

**Step 7: Create `lang/en/validation.php`**

Copy from the Laravel default and trim to what's used:

```php
<?php

return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'email' => 'The :attribute field must be a valid email address.',
    'min' => [
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'max' => [
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'confirmed' => 'The :attribute field confirmation does not match.',
    'unique' => 'The :attribute has already been taken.',
    'in' => 'The selected :attribute is invalid.',
    'attributes' => [],
];
```

**Step 8: Create `lang/en/pagination.php`**

```php
<?php

return [
    'previous' => '&laquo; Previous',
    'next' => 'Next &raquo;',
];
```

**Step 9: Commit**

```bash
git add lang/en/
git commit -m "feat: add English translation files"
```

---

## Task 7: Slovenian translation files (`lang/sl/`)

**Files:**
- Create: `lang/sl/landing.php`, `lang/sl/auth.php`, `lang/sl/coach.php`, `lang/sl/client.php`, `lang/sl/emails.php`, `lang/sl/validation.php`, `lang/sl/pagination.php`

**Step 1: Create each file with Slovenian translations**

Copy the structure from `lang/en/` and translate every value. Key translations:

`lang/sl/auth.php` (example):
```php
<?php

return [
    'login_title' => 'Prijavite se v svoj račun',
    'email' => 'E-poštni naslov',
    'password' => 'Geslo',
    'remember_me' => 'Zapomni si me',
    'forgot_password' => 'Ste pozabili geslo?',
    'sign_in' => 'Prijava',
    'no_account' => 'Nimate računa?',
    'register_title' => 'Ustvarite račun',
    'name' => 'Ime',
    'confirm_password' => 'Potrdite geslo',
    'sign_up' => 'Registracija',
    'join_title' => 'Pridružite se trenerju',
    'invitation_code' => 'Koda povabila',
    'continue' => 'Nadaljuj',
    'reset_password_title' => 'Ponastavite geslo',
    'send_reset_link' => 'Pošlji povezavo za ponastavitev',
    'new_password' => 'Novo geslo',
    'reset' => 'Ponastavi geslo',
    'verify_email_title' => 'Potrdite e-pošto',
    'verify_email_message' => 'Hvala za registracijo! Preden začnete, prosimo potrdite vaš e-poštni naslov s klikom na povezavo, ki smo vam jo poslali.',
    'resend_verification' => 'Ponovno pošlji potrditveno e-pošto',
    'confirm_password_title' => 'Potrdite geslo',
    'confirm_password_message' => 'To je varno področje aplikacije. Prosimo potrdite geslo, preden nadaljujete.',
    'confirm' => 'Potrdi',
    'logout' => 'Odjava',
];
```

`lang/sl/coach.php` (key navigation):
```php
<?php

return [
    'nav_dashboard' => 'Nadzorna plošča',
    'nav_clients' => 'Stranke',
    'nav_programs' => 'Programi',
    'nav_exercises' => 'Vaje',
    'nav_meals' => 'Obroki',
    'nav_tracking' => 'Sledenje',
    'nav_messages' => 'Sporočila',
    'nav_branding' => 'Blagovna znamka',
    'nav_loyalty' => 'Zvestoba',
    'nav_rewards' => 'Nagrade',
    'nav_achievements' => 'Dosežki',
    'create' => 'Ustvari',
    'edit' => 'Uredi',
    'delete' => 'Izbriši',
    'save' => 'Shrani',
    'cancel' => 'Prekliči',
    'back' => 'Nazaj',
    'confirm_delete' => 'Ali ste prepričani, da želite izbrisati?',
    // ... translate all keys from lang/en/coach.php
];
```

`lang/sl/client.php` (key navigation):
```php
<?php

return [
    'nav_home' => 'Domov',
    'nav_program' => 'Program',
    'nav_log' => 'Dnevnik',
    'nav_check_in' => 'Prijava',
    'nav_nutrition' => 'Prehrana',
    'nav_history' => 'Zgodovina',
    'unfinished_workout' => 'Nedokončana vadba:',
    'last_saved_at' => 'Zadnjič shranjeno ob',
    'continue' => 'Nadaljuj',
    'viewing_cached_content' => 'Gledate predpomnjeno vsebino. Nekateri podatki so morda zastareli.',
    // ... translate all keys from lang/en/client.php
];
```

`lang/sl/validation.php`:
```php
<?php

return [
    'required' => 'Polje :attribute je obvezno.',
    'string' => 'Polje :attribute mora biti besedilo.',
    'email' => 'Polje :attribute mora biti veljaven e-poštni naslov.',
    'min' => [
        'string' => 'Polje :attribute mora imeti vsaj :min znakov.',
    ],
    'max' => [
        'string' => 'Polje :attribute ne sme presegati :max znakov.',
    ],
    'confirmed' => 'Potrditveno polje :attribute se ne ujema.',
    'unique' => ':attribute je že zaseden.',
    'in' => 'Izbrana vrednost za :attribute ni veljavna.',
    'attributes' => [],
];
```

`lang/sl/pagination.php`:
```php
<?php

return [
    'previous' => '&laquo; Prejšnja',
    'next' => 'Naslednja &raquo;',
];
```

**Step 2: Translate all remaining keys for `landing.php` and `emails.php` in Slovenian**

**Step 3: Commit**

```bash
git add lang/sl/
git commit -m "feat: add Slovenian translation files"
```

---

## Task 8: Croatian translation files (`lang/hr/`)

Same structure as Task 7 but Croatian. Key translations:

`lang/hr/auth.php`:
```php
<?php

return [
    'login_title' => 'Prijavite se na svoj račun',
    'email' => 'E-mail adresa',
    'password' => 'Lozinka',
    'remember_me' => 'Zapamti me',
    'forgot_password' => 'Zaboravili ste lozinku?',
    'sign_in' => 'Prijava',
    'no_account' => 'Nemate račun?',
    'register_title' => 'Stvorite račun',
    'name' => 'Ime',
    'confirm_password' => 'Potvrdite lozinku',
    'sign_up' => 'Registracija',
    'join_title' => 'Pridružite se treneru',
    'invitation_code' => 'Pozivni kod',
    'continue' => 'Nastavi',
    'reset_password_title' => 'Resetirajte lozinku',
    'send_reset_link' => 'Pošalji link za reset',
    'new_password' => 'Nova lozinka',
    'reset' => 'Resetiraj lozinku',
    'verify_email_title' => 'Potvrdite e-mail',
    'verify_email_message' => 'Hvala što ste se registrirali! Prije nego što počnete, molimo potvrdite svoju e-mail adresu klikom na link koji smo vam poslali.',
    'resend_verification' => 'Ponovno pošalji verifikacijski e-mail',
    'confirm_password_title' => 'Potvrdite lozinku',
    'confirm_password_message' => 'Ovo je sigurno područje aplikacije. Molimo potvrdite lozinku prije nastavka.',
    'confirm' => 'Potvrdi',
    'logout' => 'Odjava',
];
```

`lang/hr/coach.php` (navigation):
```php
<?php

return [
    'nav_dashboard' => 'Nadzorna ploča',
    'nav_clients' => 'Klijenti',
    'nav_programs' => 'Programi',
    'nav_exercises' => 'Vježbe',
    'nav_meals' => 'Obroci',
    'nav_tracking' => 'Praćenje',
    'nav_messages' => 'Poruke',
    'nav_branding' => 'Brendiranje',
    'nav_loyalty' => 'Lojalnost',
    'nav_rewards' => 'Nagrade',
    'nav_achievements' => 'Postignuća',
    'create' => 'Stvori',
    'edit' => 'Uredi',
    'delete' => 'Obriši',
    'save' => 'Spremi',
    'cancel' => 'Odustani',
    'back' => 'Natrag',
    'confirm_delete' => 'Jeste li sigurni da želite obrisati?',
    // ... translate all keys
];
```

`lang/hr/client.php`:
```php
<?php

return [
    'nav_home' => 'Početna',
    'nav_program' => 'Program',
    'nav_log' => 'Dnevnik',
    'nav_check_in' => 'Prijava',
    'nav_nutrition' => 'Prehrana',
    'nav_history' => 'Povijest',
    'unfinished_workout' => 'Nedovršeni trening:',
    'last_saved_at' => 'Zadnje spremljeno u',
    'continue' => 'Nastavi',
    'viewing_cached_content' => 'Pregledavate predmemorirani sadržaj. Neki podaci mogu biti zastarjeli.',
    // ... translate all keys
];
```

`lang/hr/validation.php`:
```php
<?php

return [
    'required' => 'Polje :attribute je obavezno.',
    'string' => 'Polje :attribute mora biti tekst.',
    'email' => 'Polje :attribute mora biti valjana e-mail adresa.',
    'min' => [
        'string' => 'Polje :attribute mora imati najmanje :min znakova.',
    ],
    'max' => [
        'string' => 'Polje :attribute ne smije biti duže od :max znakova.',
    ],
    'confirmed' => 'Potvrda polja :attribute se ne podudara.',
    'unique' => ':attribute je već zauzet.',
    'in' => 'Odabrana vrijednost za :attribute nije valjana.',
    'attributes' => [],
];
```

`lang/hr/pagination.php`:
```php
<?php

return [
    'previous' => '&laquo; Prethodna',
    'next' => 'Sljedeća &raquo;',
];
```

**Step 2: Commit**

```bash
git add lang/hr/
git commit -m "feat: add Croatian translation files"
```

---

## Task 9: Update auth views to use `__()`

**Files:**
- Modify: all files in `resources/views/auth/`
- Modify: `resources/views/layouts/guest.blade.php` (if any strings)

**Step 1: For each auth view, replace hardcoded strings**

Example — `resources/views/auth/login.blade.php`:

Before:
```blade
<h1>Sign in to your account</h1>
<label>Email address</label>
```

After:
```blade
<h1>{{ __('auth.login_title') }}</h1>
<label>{{ __('auth.email') }}</label>
```

Work through all 8 auth views systematically.

**Step 2: Run tests to ensure nothing is broken**

```bash
php -d memory_limit=512M vendor/bin/pest --compact tests/Feature/Auth/
```

Expected: same pass/fail count as baseline for auth tests.

**Step 3: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/auth/ resources/views/layouts/
git commit -m "feat: replace hardcoded strings in auth views with translation keys"
```

---

## Task 10: Update coach layout and views to use `__()`

**Files:**
- Modify: `resources/views/components/layouts/coach.blade.php`
- Modify: all views in `resources/views/coach/`

**Step 1: Update coach layout nav labels**

In `resources/views/components/layouts/coach.blade.php`, replace hardcoded nav labels:

```blade
{{-- Before --}}
Dashboard
{{-- After --}}
{{ __('coach.nav_dashboard') }}
```

Apply to all nav items: Clients, Programs, Exercises, Meals, Tracking, Messages, Branding, Loyalty, Rewards, Achievements.

**Step 2: Update each coach view**

Work through each view directory systematically. For each file, replace hardcoded strings with `__('coach.*')` keys. Read each view, identify strings, replace them.

Views to update:
- `resources/views/coach/dashboard.blade.php`
- `resources/views/coach/clients/index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`, etc.
- `resources/views/coach/programs/` (all files)
- `resources/views/coach/exercises/` (all files)
- `resources/views/coach/meals/` (all files)
- `resources/views/coach/tracking-metrics/` (all files)
- `resources/views/coach/messages/` (all files)
- `resources/views/coach/branding.blade.php`
- `resources/views/coach/rewards/`, `achievements/`, `redemptions/`
- `resources/views/coach/check-in.blade.php`

**Step 3: Run coach tests**

```bash
php -d memory_limit=512M vendor/bin/pest --compact tests/Feature/Coach/
```

**Step 4: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/layouts/coach.blade.php resources/views/coach/
git commit -m "feat: replace hardcoded strings in coach views with translation keys"
```

---

## Task 11: Update client layout and views to use `__()`

**Files:**
- Modify: `resources/views/components/layouts/client.blade.php`
- Modify: all views in `resources/views/client/`

**Step 1: Update client layout nav labels**

In `resources/views/components/layouts/client.blade.php`, replace bottom tab labels:

```blade
{{-- Before --}}
<span class="text-xs mt-1 font-medium">Home</span>
{{-- After --}}
<span class="text-xs mt-1 font-medium">{{ __('client.nav_home') }}</span>
```

Apply to: Home, Program, Log, Check-in, Nutrition, History.

Also update the inline strings in the layout (unfinished workout banner, stale cache banner).

**Step 2: Update each client view**

Work through each file systematically:
- `resources/views/client/dashboard.blade.php`
- `resources/views/client/program.blade.php`
- `resources/views/client/log.blade.php`, `log-workout.blade.php`
- `resources/views/client/check-in.blade.php`
- `resources/views/client/nutrition.blade.php`
- `resources/views/client/history.blade.php`, `history-show.blade.php`
- `resources/views/client/achievements.blade.php`, `achievements/`
- `resources/views/client/loyalty.blade.php`
- `resources/views/client/rewards.blade.php`
- `resources/views/client/onboarding.blade.php`
- `resources/views/client/messages.blade.php`
- `resources/views/client/welcome.blade.php`

**Step 3: Run client tests**

```bash
php -d memory_limit=512M vendor/bin/pest --compact tests/Feature/Client/
```

**Step 4: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/layouts/client.blade.php resources/views/client/
git commit -m "feat: replace hardcoded strings in client views with translation keys"
```

---

## Task 12: Update landing page view to use `__()`

**Files:**
- Modify: `resources/views/welcome.blade.php`

**Step 1: Replace all hardcoded strings in `welcome.blade.php`**

Read the file in sections (it is large — read 100 lines at a time using offset/limit). Replace every user-visible string with `__('landing.*')` keys.

**Step 2: Run landing tests**

```bash
php -d memory_limit=512M vendor/bin/pest --compact --filter="LandingLocaleTest"
```

**Step 3: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/welcome.blade.php
git commit -m "feat: replace hardcoded strings in landing page with translation keys"
```

---

## Task 13: Update email views and mailables

**Files:**
- Modify: `resources/views/mail/welcome-client.blade.php`
- Modify: `resources/views/mail/reward-redeemed.blade.php`
- Modify: `app/Mail/WelcomeClientMail.php`
- Modify: `app/Mail/RewardRedeemedMail.php`

**Step 1: Update email views to use `__()`**

Replace strings in both mail views with `__('emails.*')` keys.

**Step 2: Update `WelcomeClientMail` to set locale**

```php
public function envelope(): Envelope
{
    $gymName = $this->coach->gym_name ?? $this->coach->name;

    return new Envelope(
        subject: __('emails.welcome_client_subject', ['gym_name' => $gymName]),
    );
}

public function content(): Content
{
    return new Content(
        view: 'mail.welcome-client',
    );
}
```

Add locale call in constructor or use `locale()` method:

```php
public function __construct(
    public User $client,
    public User $coach,
) {
    $this->locale($this->client->locale ?? 'en');
}
```

**Step 3: Update `RewardRedeemedMail`**

`RewardRedeemedMail` is sent to the coach (notify that a client redeemed). Use the coach's locale:

```php
public function __construct(
    public RewardRedemption $redemption,
) {
    $this->locale($this->redemption->user->coach?->locale ?? 'en');
}

public function envelope(): Envelope
{
    $clientName = $this->redemption->user->name;

    return new Envelope(
        subject: __('emails.reward_redeemed_subject', ['name' => $clientName]),
    );
}
```

**Step 4: Add email locale test to `tests/Feature/LocaleTest.php`**

```php
it('sends welcome email in client locale', function (): void {
    \Illuminate\Support\Facades\Mail::fake();

    $coach = User::factory()->create(['role' => 'coach', 'locale' => 'en']);
    $client = User::factory()->create(['role' => 'client', 'coach_id' => $coach->id, 'locale' => 'sl']);

    \Illuminate\Support\Facades\Mail::to($client->email)->send(new \App\Mail\WelcomeClientMail($client, $coach));

    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\WelcomeClientMail::class, function ($mail) {
        return $mail->locale === 'sl';
    });
});
```

**Step 5: Run tests**

```bash
php -d memory_limit=512M vendor/bin/pest --compact --filter="LocaleTest"
```

**Step 6: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Mail/ resources/views/mail/
git commit -m "feat: localize emails using client/coach locale preference"
```

---

## Task 14: Final verification

**Step 1: Run full test suite**

From the **main project directory** (not the worktree):

```bash
php -d memory_limit=512M vendor/bin/pest --compact
```

Expected: 296+ passing (new tests added), still only 11 pre-existing failures.

**Step 2: Check for any missed hardcoded strings**

```bash
grep -r ">[A-Z][a-z]\+ " resources/views/coach/ resources/views/client/ resources/views/auth/ --include="*.blade.php" | grep -v "{{ __(" | grep -v "@" | head -30
```

Review output and fix any remaining hardcoded strings.

**Step 3: Commit any fixes**

```bash
vendor/bin/pint --dirty --format agent
git add -p
git commit -m "fix: address remaining hardcoded strings found in final review"
```
