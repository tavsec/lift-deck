# Coach Register Page Redesign — Design Spec

## Goal

Replace the plain 4-field Breeze registration form with a visually polished 3-step wizard that collects coaching context alongside credentials, fully localized in English, Croatian, and Slovenian.

## Architecture

Single Blade view (`resources/views/auth/register.blade.php`) rewritten as a standalone full-viewport HTML document. Alpine.js manages step transitions client-side. The form submits in a single POST to the existing `POST /register` route. No new routes, no new models, no migrations.

## Layout

Split-screen: dark left panel (~38% width) fixed across all steps, light right panel (~62%) cycling through wizard steps.

**Left panel (fixed):**
- LiftDeck wordmark + sub-label
- Headline: `__('register.panel.heading')`
- 4 feature bullets (programs, nutrition, messaging, loyalty)
- Trial note: `__('register.panel.trial_note')`

**Right panel:**
- Step indicator (3 dots with connecting lines, completed steps show ✓)
- Step label, title, subtitle
- Step content
- Back / Continue actions at the bottom

**Mobile (<768px):** Left panel hidden. Right panel takes full width with a small LiftDeck wordmark at top.

## Steps

### Step 1 — Who are you coaching?
**Required to advance** (at least one card must be selected).

Three clickable cards stored in Alpine as `coachingType` (values: `solo`, `growing`, `gym`). Submitted as hidden input. Not persisted to the database — used as a UX signal on the plan selection page.

| Value | Label key | Sub-label key |
|-------|-----------|---------------|
| `solo` | `register.step1.solo` | `register.step1.solo_sub` |
| `growing` | `register.step1.growing` | `register.step1.growing_sub` |
| `gym` | `register.step1.gym` | `register.step1.gym_sub` |

### Step 2 — Tell us about yourself
**All fields optional.** A "Skip this step" link advances directly to step 3.

| Field | Input type | Maps to | Max length |
|-------|-----------|---------|------------|
| Your name | text | `users.name` | 255 |
| Gym or business name | text | `users.gym_name` | 255 |
| Coaching niche | text | `users.bio` | 255 |
| Current number of clients | number (min 0) | not persisted | — |
| Tools currently used | checkbox pills (Sheets, Excel, WhatsApp, Other) | not persisted | — |

### Step 3 — Create your account
**All fields required.**

| Field | Input type | Validation |
|-------|-----------|-----------|
| Email | email | required, email, max:255, unique:users |
| Password | password | required, min:8, confirmed |
| Confirm password | password | — |

## Alpine.js Component

```js
x-data="{
  step: 1,
  coachingType: '',
  name: '',
  gymName: '',
  bio: '',
  goTo(n) { this.step = n; },
  canAdvance1() { return this.coachingType !== ''; }
}"
```

Step transitions use `x-show` with `x-transition:enter` / `x-transition:leave` (fade + slight slide). Back/forward buttons call `goTo(n)`.

## Controller Changes

`app/Http/Controllers/Auth/RegisteredUserController.php` — `store()` currently saves `name`, `email`, `password`, `role`. Add optional saving of `gym_name` and `bio`:

```php
$user = User::create([
    'name'     => $request->name,
    'email'    => $request->email,
    'password' => Hash::make($request->password),
    'role'     => 'coach',
    'gym_name' => $request->gym_name,
    'bio'      => $request->bio,
]);
```

Validation rules to add (nullable, so registration without them still works):
```php
'gym_name' => ['nullable', 'string', 'max:255'],
'bio'      => ['nullable', 'string', 'max:255'],
```

## Localization

New `register` key added to `lang/{en,hr,sl}/auth.php`:

```php
'register' => [
    'panel' => [
        'heading'    => 'Stop coaching from spreadsheets. Run everything in LiftDeck.',
        'trial_note' => '7-day free trial · No credit card required',
        'feature_*'  => '...',
    ],
    'step1' => [ 'title', 'subtitle', 'solo', 'solo_sub', 'growing', 'growing_sub', 'gym', 'gym_sub' ],
    'step2' => [ 'title', 'subtitle', 'skip', 'name', 'gym_name', 'bio', 'client_count', 'tools', 'tools_*' ],
    'step3' => [ 'title', 'subtitle', 'email', 'password', 'password_confirm', 'submit' ],
    'actions' => [ 'back', 'continue', 'signin' ],
],
```

Croatian (`hr`) and Slovenian (`sl`) translations provided for all keys.

## Testing

Update `tests/Feature/Auth/RegistrationTest.php`:
- Existing test: registration succeeds with only email + password (name optional now)
- New test: `gym_name` and `bio` are saved when provided
- New test: registration succeeds without `gym_name` and `bio` (confirms nullable)

## Files Changed

| File | Change |
|------|--------|
| `resources/views/auth/register.blade.php` | Full rewrite — standalone HTML, Alpine wizard |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Add `gym_name` + `bio` to create + validation |
| `lang/en/auth.php` | Add `register.*` keys |
| `lang/hr/auth.php` | Add `register.*` keys (Croatian) |
| `lang/sl/auth.php` | Add `register.*` keys (Slovenian) |
| `tests/Feature/Auth/RegistrationTest.php` | Update + add tests |
