# Dark Mode Design

**Date:** 2026-02-27
**Scope:** Coach and client app sections only (landing page and Filament admin excluded)

## Overview

Implement a user-toggled dark mode for authenticated coach and client views. The preference is persisted in the database and applied server-side via Tailwind's `dark:` class strategy.

## Architecture

### Database

- Migration: `add_dark_mode_to_users_table` — adds `dark_mode` boolean column, default `false`
- User model: add `'dark_mode' => 'boolean'` to `casts()`

### Backend

- Route: `PATCH /user/dark-mode` (authenticated, web middleware)
- Controller: `UserPreferencesController@updateDarkMode` — toggles `auth()->user()->dark_mode` and redirects back

### Layouts

Both layout files receive:
- `<html class="{{ auth()->user()->dark_mode ? 'dark' : '' }}">` to activate Tailwind's class-based dark mode
- A sun/moon toggle button that submits a form to `PATCH /user/dark-mode`
- All chrome classes updated with `dark:` counterparts

**Toggle placement:**
- **Client layout** — top header, alongside existing message + logout icons
- **Coach desktop sidebar** — user info section at the bottom
- **Coach mobile header** — next to the hamburger button
- **Coach mobile drawer** — user info section at the bottom

### Dark Color Mapping

| Light class | Dark equivalent |
|---|---|
| `bg-white` | `dark:bg-gray-800` |
| `bg-gray-50` | `dark:bg-gray-900` |
| `border-gray-200` | `dark:border-gray-700` |
| `text-gray-900` | `dark:text-gray-100` |
| `text-gray-700` | `dark:text-gray-300` |
| `text-gray-500`, `text-gray-600` | `dark:text-gray-400` |
| `hover:bg-gray-50`, `hover:bg-gray-100` | `dark:hover:bg-gray-700` |
| `bg-blue-50` (active nav item) | `dark:bg-blue-900/30` |

### View Scope

- `resources/views/components/layouts/client.blade.php`
- `resources/views/components/layouts/coach.blade.php`
- All views under `resources/views/coach/`
- All views under `resources/views/client/`
- Shared components used only in coach/client contexts

**Excluded:** `welcome.blade.php`, auth views (`layouts/guest.blade.php`), Filament admin

## Decisions

- **Trigger:** User toggle button (not OS preference)
- **Storage:** Database (`users.dark_mode` boolean)
- **Strategy:** Tailwind `darkMode: 'class'` (already configured in `tailwind.config.js`)
- **Toggle mechanism:** HTML form `PATCH` request → redirect back (no JS required, works with Octane)
- **Auth pages:** Remain light-mode (user is unauthenticated; no preference to read)
