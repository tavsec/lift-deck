# Dark Mode Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a user-toggled dark mode preference for all authenticated coach and client views, stored in the database and applied via Tailwind's `dark:` class strategy.

**Architecture:** Add a `dark_mode` boolean column to `users`, toggled via `PATCH /user/dark-mode`. Both layout files conditionally add `class="dark"` to `<html>` and include a sun/moon toggle button. All view chrome and page content get corresponding `dark:` Tailwind utilities.

**Tech Stack:** Laravel 12, Tailwind CSS v3 (`darkMode: 'class'`), Blade layouts (`components/layouts/coach.blade.php`, `components/layouts/client.blade.php`), Pest v4.

---

## Dark Color Mapping Reference

Apply this everywhere throughout tasks 3–8:

| Light class | Add dark counterpart |
|---|---|
| `bg-white` | `dark:bg-gray-800` |
| `bg-gray-50` | `dark:bg-gray-900` |
| `bg-gray-100` | `dark:bg-gray-700` |
| `border-gray-200` | `dark:border-gray-700` |
| `border-gray-300` | `dark:border-gray-600` |
| `text-gray-900` | `dark:text-gray-100` |
| `text-gray-800` | `dark:text-gray-200` |
| `text-gray-700` | `dark:text-gray-300` |
| `text-gray-600` | `dark:text-gray-400` |
| `text-gray-500` | `dark:text-gray-400` |
| `text-gray-400` | `dark:text-gray-500` |
| `divide-gray-100` | `dark:divide-gray-700` |
| `divide-gray-200` | `dark:divide-gray-700` |
| `hover:bg-gray-50` | `dark:hover:bg-gray-700` |
| `hover:bg-gray-100` | `dark:hover:bg-gray-700` |
| `hover:text-gray-900` | `dark:hover:text-gray-100` |
| `bg-blue-50` (active nav) | `dark:bg-blue-900/30` |
| `shadow` / `shadow-sm` | keep as-is (shadows work in dark) |
| `focus:ring-offset-2` | add `dark:focus:ring-offset-gray-800` |

---

### Task 1: Migration and Model

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_add_dark_mode_to_users_table.php`
- Modify: `app/Models/User.php`
- Test: `tests/Feature/DarkModePreferenceTest.php`

**Step 1: Create the migration**

```bash
php artisan make:migration add_dark_mode_to_users_table --no-interaction
```

Edit the generated migration:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->boolean('dark_mode')->default(false)->after('secondary_color');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->dropColumn('dark_mode');
    });
}
```

**Step 2: Run the migration**

```bash
php artisan migrate --no-interaction
```

**Step 3: Update the User model**

In `app/Models/User.php`, add `'dark_mode'` to `$fillable`:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'coach_id',
    'phone',
    'bio',
    'description',
    'welcome_email_text',
    'onboarding_welcome_text',
    'avatar',
    'gym_name',
    'logo',
    'primary_color',
    'secondary_color',
    'dark_mode',
];
```

Add `'dark_mode'` to `casts()`:

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'dark_mode' => 'boolean',
    ];
}
```

**Step 4: Write the failing test**

```bash
php artisan make:test DarkModePreferenceTest --pest --no-interaction
```

```php
<?php

use App\Models\User;

test('user dark_mode defaults to false', function () {
    $user = User::factory()->create();

    expect($user->dark_mode)->toBeFalse();
});

test('user dark_mode can be set to true', function () {
    $user = User::factory()->create();

    $user->update(['dark_mode' => true]);

    expect($user->fresh()->dark_mode)->toBeTrue();
});
```

**Step 5: Run the tests to verify they pass**

```bash
php artisan test --compact --filter=DarkModePreferenceTest
```

Expected: PASS (2 tests)

**Step 6: Commit**

```bash
git add database/migrations/ app/Models/User.php tests/Feature/DarkModePreferenceTest.php
git commit -m "feat: add dark_mode column to users"
```

---

### Task 2: Toggle Controller and Route

**Files:**
- Create: `app/Http/Controllers/UserPreferencesController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/DarkModePreferenceTest.php`

**Step 1: Write the failing tests** (add to existing `DarkModePreferenceTest.php`)

```php
test('authenticated user can toggle dark mode on', function () {
    $user = User::factory()->create(['dark_mode' => false]);

    $this->actingAs($user)
        ->patch(route('user.dark-mode.toggle'))
        ->assertRedirect();

    expect($user->fresh()->dark_mode)->toBeTrue();
});

test('authenticated user can toggle dark mode off', function () {
    $user = User::factory()->create(['dark_mode' => true]);

    $this->actingAs($user)
        ->patch(route('user.dark-mode.toggle'))
        ->assertRedirect();

    expect($user->fresh()->dark_mode)->toBeFalse();
});

test('unauthenticated user cannot toggle dark mode', function () {
    $this->patch(route('user.dark-mode.toggle'))
        ->assertRedirect(route('login'));
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact --filter="can toggle dark mode|unauthenticated user cannot"
```

Expected: FAIL (route not found)

**Step 3: Create the controller**

```bash
php artisan make:controller UserPreferencesController --no-interaction
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class UserPreferencesController extends Controller
{
    public function toggleDarkMode(): RedirectResponse
    {
        auth()->user()->update(['dark_mode' => ! auth()->user()->dark_mode]);

        return back();
    }
}
```

**Step 4: Add the route**

In `routes/web.php`, add inside the `auth` middleware group (after the media route, before `require __DIR__.'/auth.php'`):

```php
Route::middleware('auth')->group(function () {
    Route::get('media/daily-log/{dailyLog}/{conversion?}', [\App\Http\Controllers\MediaController::class, 'dailyLog'])->name('media.daily-log');
    Route::patch('user/dark-mode', [\App\Http\Controllers\UserPreferencesController::class, 'toggleDarkMode'])->name('user.dark-mode.toggle');
});
```

**Step 5: Run tests to verify they pass**

```bash
php artisan test --compact --filter=DarkModePreferenceTest
```

Expected: PASS (5 tests)

**Step 6: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 7: Commit**

```bash
git add app/Http/Controllers/UserPreferencesController.php routes/web.php tests/Feature/DarkModePreferenceTest.php
git commit -m "feat: add dark mode toggle endpoint"
```

---

### Task 3: Client Layout — Dark Mode Chrome

**Files:**
- Modify: `resources/views/components/layouts/client.blade.php`

**Step 1: Update `<html>` tag**

Change:
```html
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
```
To:
```html
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()->dark_mode ? 'dark' : '' }}">
```

**Step 2: Update `<body>` tag**

Change:
```html
<body class="font-sans antialiased bg-gray-50">
```
To:
```html
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
```

**Step 3: Update top header**

Change:
```html
<div class="fixed top-0 left-0 right-0 bg-white shadow-sm z-40">
```
To:
```html
<div class="fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 shadow-sm z-40">
```

Update the logo text span:
```html
<span class="text-lg font-semibold text-gray-900 dark:text-gray-100">...</span>
```

**Step 4: Update header icon buttons** (messages link and logout button)

Change both button/anchor elements from:
```html
class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100"
```
To:
```html
class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700"
```

**Step 5: Add dark mode toggle button to the header**

In the `flex items-center space-x-3` div, add the toggle button before the messages link:

```html
<form method="POST" action="{{ route('user.dark-mode.toggle') }}">
    @csrf
    @method('PATCH')
    <button type="submit" class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700">
        @if(auth()->user()->dark_mode)
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        @else
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        @endif
    </button>
</form>
```

**Step 6: Update bottom navigation bar**

Change:
```html
<nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40">
```
To:
```html
<nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-40">
```

Update inactive nav item text (`text-gray-500`) to add `dark:text-gray-400`.

**Step 7: Verify the layout renders**

```bash
php artisan test --compact --filter=DarkModePreferenceTest
```

Expected: PASS (all 5)

**Step 8: Commit**

```bash
git add resources/views/components/layouts/client.blade.php
git commit -m "feat: add dark mode chrome to client layout"
```

---

### Task 4: Coach Layout — Dark Mode Chrome

**Files:**
- Modify: `resources/views/components/layouts/coach.blade.php`

**Step 1: Update `<html>` and `<body>` tags** (same as Task 3)

```html
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()->dark_mode ? 'dark' : '' }}">
```

```html
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
```

**Step 2: Update mobile header**

```html
<div class="md:hidden fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 shadow-sm z-40">
```

Mobile header button:
```html
class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700"
```

Logo text span in mobile header:
```html
<span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
```

**Step 3: Add toggle button to mobile header**

Add after the hamburger button, inside the `flex items-center justify-between` div:

```html
<form method="POST" action="{{ route('user.dark-mode.toggle') }}">
    @csrf
    @method('PATCH')
    <button type="submit" class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700">
        @if(auth()->user()->dark_mode)
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        @else
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        @endif
    </button>
</form>
```

Note: The mobile header currently has a `<div class="w-10"></div>` spacer on the right for balance. Replace it with the toggle form above.

**Step 4: Update desktop sidebar**

```html
<aside class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:left-0 md:w-64 md:bg-white dark:md:bg-gray-800 md:border-r md:border-gray-200 dark:md:border-gray-700">
```

Sidebar brand section border:
```html
<div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-700">
```

Brand text span:
```html
<span class="text-xl font-bold text-gray-900 dark:text-gray-100">
```

**Step 5: Update sidebar nav links**

For each nav link, update the class logic:

Active state — change `bg-blue-50` to `bg-blue-50 dark:bg-blue-900/30`.

Inactive state — change `text-gray-700 hover:bg-gray-50 hover:text-gray-900` to `text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100`.

Inactive icon color — change `text-gray-400` to `text-gray-400 dark:text-gray-500`.

**Step 6: Update sidebar user info section**

```html
<div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700">
```

User name text:
```html
<p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
```

Sign out button:
```html
<button type="submit" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
```

**Step 7: Add toggle button to sidebar user info section**

In the sidebar user info `flex items-center` div, add before or after the user name/sign out content:

```html
<form method="POST" action="{{ route('user.dark-mode.toggle') }}" class="ml-auto">
    @csrf
    @method('PATCH')
    <button type="submit" class="p-1.5 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
        @if(auth()->user()->dark_mode)
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        @else
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        @endif
    </button>
</form>
```

**Step 8: Update mobile menu overlay and drawer**

Overlay backdrop stays as-is (`bg-gray-600 bg-opacity-75`).

Mobile drawer:
```html
<div class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-xl">
```

Mobile menu header:
```html
<div class="flex items-center justify-between h-14 px-4 border-b border-gray-200 dark:border-gray-700">
```

Mobile menu brand text:
```html
<span class="text-lg font-bold text-gray-900 dark:text-gray-100">
```

Close button:
```html
class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700"
```

Mobile nav links: apply same pattern as desktop sidebar nav links (Step 5).

Mobile user info section: apply same pattern as desktop user info section (Steps 6–7).

**Step 9: Update main content area**

```html
<main class="mt-14 md:mt-0 md:ml-64 min-h-screen dark:bg-gray-900">
```

**Step 10: Run tests**

```bash
php artisan test --compact --filter=DarkModePreferenceTest
```

Expected: PASS

**Step 11: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 12: Commit**

```bash
git add resources/views/components/layouts/coach.blade.php
git commit -m "feat: add dark mode chrome to coach layout"
```

---

### Task 5: BladewindUI Dark Mode CSS Overrides

The client views use `x-bladewind::card` components which load styles from `vendor/bladewind/css/bladewind-ui.min.css`. These won't respond to Tailwind's `dark:` prefix, so we override them with CSS.

**Files:**
- Modify: `resources/css/app.css`

**Step 1: Identify the BladewindUI card wrapper class**

Open `public/vendor/bladewind/css/bladewind-ui.min.css` and search for `.bw-card` or look at the rendered HTML in the browser DevTools for the class applied to `x-bladewind::card` output.

**Step 2: Add dark mode CSS overrides**

Add to the bottom of `resources/css/app.css`:

```css
/* Dark mode overrides for BladewindUI components */
.dark .bw-card {
    @apply bg-gray-800 border-gray-700;
}

.dark .bw-card * {
    @apply border-gray-700;
}
```

If the card class name differs from `.bw-card`, update accordingly based on Step 1 inspection.

**Step 3: Build assets**

```bash
npm run build
```

**Step 4: Verify visually** — Load a client page with dark mode enabled and check that cards have a dark background.

**Step 5: Commit**

```bash
git add resources/css/app.css
git commit -m "feat: add dark mode CSS overrides for BladewindUI"
```

---

### Task 6: Coach Page Views — Dark Mode Classes

Apply the color mapping table at the top of this plan to every file below. The pattern is: **never remove a class, only add the `dark:` counterpart alongside it**.

**Files to update** (all `resources/views/coach/`):

- `coach/dashboard.blade.php`
- `coach/branding.blade.php`
- `coach/clients/index.blade.php`
- `coach/clients/show.blade.php`
- `coach/clients/create.blade.php`
- `coach/clients/edit.blade.php`
- `coach/clients/analytics.blade.php`
- `coach/clients/nutrition.blade.php`
- `coach/clients/workout-log.blade.php`
- `coach/exercises/index.blade.php`
- `coach/exercises/show.blade.php`
- `coach/exercises/create.blade.php`
- `coach/exercises/edit.blade.php`
- `coach/meals/index.blade.php`
- `coach/meals/create.blade.php`
- `coach/meals/edit.blade.php`
- `coach/messages/index.blade.php`
- `coach/messages/show.blade.php`
- `coach/programs/index.blade.php`
- `coach/programs/show.blade.php`
- `coach/programs/create.blade.php`
- `coach/programs/edit.blade.php`
- `coach/programs/assign.blade.php`
- `coach/tracking-metrics/index.blade.php`

**Common patterns to find and update:**

1. Card containers: `class="bg-white rounded-lg shadow p-4"` → add `dark:bg-gray-800`
2. Card containers: `class="bg-white rounded-lg shadow p-6"` → add `dark:bg-gray-800`
3. Section headings: `class="text-lg font-semibold text-gray-900"` → add `dark:text-gray-100`
4. Body text: `class="text-gray-600"` or `text-gray-700` → add corresponding dark variant
5. Muted text: `class="text-gray-500"` or `text-gray-400` → add corresponding dark variant
6. Table headers: `class="bg-gray-50 text-gray-500"` → add `dark:bg-gray-700 dark:text-gray-400`
7. Table rows: `class="bg-white"` or `divide-y divide-gray-200` → add dark variants
8. Form inputs: `class="border-gray-300"` → add `dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100`
9. Hover rows: `class="hover:bg-gray-50"` → add `dark:hover:bg-gray-700`
10. Secondary buttons: `class="bg-white border border-gray-300 text-gray-700"` → add `dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300`
11. Empty state icons: `class="text-gray-400"` → add `dark:text-gray-500`
12. Dividers: `class="divide-y divide-gray-100"` or `divide-gray-200` → add `dark:divide-gray-700`

**Step 1: Update `coach/dashboard.blade.php`** — 6 card containers, headings, list items, empty state icons.

**Step 2: Update remaining coach views** — work through each file, applying all relevant mappings from the table above.

**Step 3: Run existing coach tests**

```bash
php artisan test --compact tests/Feature/Coach/
```

Expected: all PASS (we only added classes, no logic changes)

**Step 4: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 5: Commit**

```bash
git add resources/views/coach/
git commit -m "feat: add dark mode classes to coach views"
```

---

### Task 7: Client Page Views — Dark Mode Classes

Apply the same color mapping to all client views.

**Files to update** (all `resources/views/client/`):

- `client/dashboard.blade.php`
- `client/program.blade.php`
- `client/log.blade.php`
- `client/log-workout.blade.php`
- `client/history.blade.php`
- `client/history-show.blade.php`
- `client/check-in.blade.php`
- `client/nutrition.blade.php`
- `client/messages.blade.php`
- `client/welcome.blade.php`
- `client/onboarding.blade.php`

**Additional client-specific patterns:**

1. Bladewind card inner content (headings, text within `x-bladewind::card`) — update text colors
2. Progress/stat numbers: `class="text-2xl font-bold text-gray-900"` → add `dark:text-gray-100`
3. Badge/pill elements: `class="bg-blue-100 text-blue-800"` → add `dark:bg-blue-900/30 dark:text-blue-300`
4. Message bubbles or chat UI — treat as cards

**Step 1: Update each client view file**, applying the mapping table.

**Step 2: Run existing client tests**

```bash
php artisan test --compact tests/Feature/Client/
```

Expected: all PASS

**Step 3: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 4: Commit**

```bash
git add resources/views/client/
git commit -m "feat: add dark mode classes to client views"
```

---

### Task 8: Shared Component — workout-log-comments

**Files:**
- Modify: `resources/views/components/workout-log-comments.blade.php`

**Step 1: Apply dark mode classes**

Card container:
```html
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
```

Heading:
```html
<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
```

Comment count:
```html
<span class="text-sm font-normal text-gray-500 dark:text-gray-400">
```

Comment user name:
```html
<span class="text-sm font-medium text-gray-900 dark:text-gray-100">
```

Comment timestamp:
```html
<span class="text-xs text-gray-400 dark:text-gray-500">
```

Comment body:
```html
<p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
```

Textarea input:
```html
class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm text-sm focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 @error('body') border-red-300 @enderror"
```

**Step 2: Run tests**

```bash
php artisan test --compact
```

Expected: all PASS

**Step 3: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 4: Final commit**

```bash
git add resources/views/components/workout-log-comments.blade.php
git commit -m "feat: add dark mode classes to workout-log-comments component"
```

---

## Summary of Commits

1. `feat: add dark_mode column to users`
2. `feat: add dark mode toggle endpoint`
3. `feat: add dark mode chrome to client layout`
4. `feat: add dark mode chrome to coach layout`
5. `feat: add dark mode CSS overrides for BladewindUI`
6. `feat: add dark mode classes to coach views`
7. `feat: add dark mode classes to client views`
8. `feat: add dark mode classes to workout-log-comments component`
