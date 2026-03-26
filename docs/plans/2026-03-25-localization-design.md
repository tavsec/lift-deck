# Localization Design — 2026-03-25

## Overview

Add English, Slovenian (`sl`), and Croatian (`hr`) localization to LiftDeck. Authenticated users select their language via a persistent nav switcher; the choice is stored on their user record and applied to emails. The public landing page uses URL-based locale routing (`/en`, `/sl`, `/hr`) with IP-based auto-detection on the root `/`.

---

## Languages

| Locale | Language   | Landing URL path |
|--------|------------|-----------------|
| `en`   | English    | `/en`           |
| `sl`   | Slovenian  | `/si`           |
| `hr`   | Croatian   | `/hr`           |

> The landing page uses `/si` (Slovenia country code) as the URL path, but the internal locale identifier is the ISO 639-1 code `sl`. A mapping array handles the translation between the two.

---

## 1. Database

Add a `locale` column to the `users` table:

- Type: `string`, length 2
- Nullable: no, default `'en'`
- Allowed values: `en`, `sl`, `hr`

Migration: `php artisan make:migration add_locale_to_users_table`

---

## 2. SetLocale Middleware

A `App\Http\Middleware\SetLocale` middleware registered globally in `bootstrap/app.php`:

- Authenticated request: reads `auth()->user()->locale`, calls `App::setLocale()`
- Guest request: uses `config('app.locale')` (`en`)

This ensures every request — including API-style Livewire requests — runs in the correct locale.

---

## 3. Language Switcher Component

`<x-locale-switcher>` Blade component rendered in:
- Coach sidebar (next to user info or at bottom)
- Client bottom nav bar

Renders three buttons/links (EN / SL / HR). Clicking one POSTs to `LocaleController@update`.

**`LocaleController@update`:**
1. Validates `locale` is in `['en', 'sl', 'hr']`
2. Updates `auth()->user()->update(['locale' => $locale])`
3. Returns `redirect()->back()`

Route: `POST /locale` (inside `auth` middleware group).

---

## 4. Email Localization

All `Mailable` classes that receive a `User` instance call `->locale($user->locale)` in their `content()` or `envelope()`. Laravel renders the mail using that locale's `lang/` files.

Existing mailables must be audited and updated to accept a `User` and call `->locale()`.

---

## 5. Landing Page Routing

### Root `/`

`LandingLocaleController@index`:
1. Gets the visitor's IP (`$request->ip()`)
2. Checks cache key `"ip_locale_{$ip}"` (TTL: 30 days)
3. Cache miss: calls `Http::get("http://ip-api.com/json/{$ip}")`, reads `countryCode`
4. Maps country code → locale: `'SI' => 'sl'`, `'HR' => 'hr'`, default `'en'`
5. Caches the result
6. Redirects to `route('landing', ['locale' => $urlPath])` where `$urlPath` maps `sl → si`, `hr → hr`, `en → en`

On any failure (local dev, API down, timeout): falls back to `en`.

### `GET /{locale}` (where `{locale}` is `en|si|hr`)

`LandingLocaleController@show`:
1. Maps URL path to locale code: `si → sl`, `hr → hr`, `en → en`
2. Calls `App::setLocale($locale)`
3. Returns `view('welcome')`

Route constraint: `->where('locale', 'en|si|hr')`

---

## 6. Translation Files

Structure under `lang/`:

```
lang/
  en/
    auth.php
    landing.php
    coach.php
    client.php
    emails.php
    validation.php
    pagination.php
  sl/
    (same keys)
  hr/
    (same keys)
```

All views use `__('section.key')`. The `welcome.blade.php` uses `__('landing.*)` keys.

### Key namespaces

| File           | Covers                                              |
|----------------|-----------------------------------------------------|
| `landing.php`  | All public landing page strings                     |
| `auth.php`     | Login, register, join, password reset               |
| `coach.php`    | All coach dashboard, clients, programs, exercises   |
| `client.php`   | All client dashboard, workouts, nutrition, check-ins|
| `emails.php`   | Email subject lines and body copy                   |
| `validation.php` | Validation error messages                        |
| `pagination.php` | Pagination labels                                |

---

## 7. Filament Admin

Admin panel remains English only. No Filament-specific translation files will be added.

---

## Implementation Steps

1. Migration — add `locale` to `users`
2. Update `User` model — add `locale` to `$fillable`, cast not needed (plain string)
3. Create `SetLocale` middleware — register globally in `bootstrap/app.php`
4. Create `LocaleController` — `update` action + route
5. Create `<x-locale-switcher>` component — add to coach sidebar + client bottom nav
6. Create `LandingLocaleController` — `index` (IP detect + redirect) + `show` (render landing)
7. Update landing routes in `web.php`
8. Extract all strings from `welcome.blade.php` into `lang/en/landing.php` and translate to `sl`/`hr`
9. Extract all strings from auth views → `lang/en/auth.php`
10. Extract all strings from coach views → `lang/en/coach.php`
11. Extract all strings from client views → `lang/en/client.php`
12. Extract email strings → `lang/en/emails.php`
13. Update all `Mailable` classes to call `->locale($user->locale)`
14. Copy `lang/en/validation.php` and `pagination.php`, translate to `sl`/`hr`
15. Write tests — locale switching, landing redirect, IP detection fallback
