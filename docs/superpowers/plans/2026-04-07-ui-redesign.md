# UI Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restyle the entire LiftDeck UI (excluding Filament admin) to a MiniMax-inspired design system — white-dominant canvas, multi-font hierarchy, pill navigation, colorful feature cards, full dark mode, and coach accent-only branding.

**Architecture:** Foundation-first then parallel agents. Tasks 1–4 must run sequentially in the main session (design system → layouts → components → landing page). Tasks 5–7 run in parallel via subagents once the foundation is in place. Task 8 is a final review pass.

**Tech Stack:** Laravel 12, Livewire 4, Blade components, Tailwind CSS v3, Alpine.js v3, Google Fonts (DM Sans, Outfit, Poppins, Roboto), Pest v4

---

## File Map

### Foundation (Tasks 1–3)
| File | Action |
|------|--------|
| `tailwind.config.js` | Modify — update colors, fonts, shadows, extend radius |
| `resources/css/app.css` | Modify — Google Fonts import, CSS custom properties |
| `resources/views/components/layouts/coach.blade.php` | Modify — new shell |
| `resources/views/components/layouts/client.blade.php` | Modify — new shell |
| `resources/views/layouts/guest.blade.php` | Modify — centered card shell |
| `resources/views/components/primary-button.blade.php` | Modify |
| `resources/views/components/secondary-button.blade.php` | Modify |
| `resources/views/components/danger-button.blade.php` | Modify |
| `resources/views/components/text-input.blade.php` | Modify |
| `resources/views/components/input-label.blade.php` | Modify |
| `resources/views/components/input-error.blade.php` | Modify |
| `resources/views/components/auth-session-status.blade.php` | Modify |
| `resources/views/components/modal.blade.php` | Modify |
| `resources/views/components/nav-link.blade.php` | Modify |
| `resources/views/components/application-logo.blade.php` | Modify |

### Landing + Auth (Task 4–5)
| File | Action |
|------|--------|
| `resources/views/welcome.blade.php` | Rewrite |
| `resources/views/auth/login.blade.php` | Modify |
| `resources/views/auth/register.blade.php` | Modify |
| `resources/views/auth/forgot-password.blade.php` | Modify |
| `resources/views/auth/reset-password.blade.php` | Modify |
| `resources/views/auth/verify-email.blade.php` | Modify |
| `resources/views/auth/join.blade.php` | Modify |
| `resources/views/auth/join-register.blade.php` | Modify |
| `resources/views/auth/confirm-password.blade.php` | Modify |

### Client Views (Task 6)
| File | Action |
|------|--------|
| `resources/views/client/dashboard.blade.php` | Modify |
| `resources/views/client/program.blade.php` | Modify |
| `resources/views/client/log.blade.php` | Modify |
| `resources/views/client/log-workout.blade.php` | Modify |
| `resources/views/client/check-in.blade.php` | Modify |
| `resources/views/client/check-in-history.blade.php` | Modify |
| `resources/views/client/nutrition.blade.php` | Modify |
| `resources/views/client/history.blade.php` | Modify |
| `resources/views/client/history-show.blade.php` | Modify |
| `resources/views/client/achievements.blade.php` | Modify |
| `resources/views/client/rewards.blade.php` | Modify |
| `resources/views/client/loyalty.blade.php` | Modify |
| `resources/views/client/messages.blade.php` | Modify |
| `resources/views/client/settings/edit.blade.php` | Modify |
| `resources/views/client/welcome.blade.php` | Modify |
| `resources/views/client/onboarding.blade.php` | Modify |
| `resources/views/components/workout-log-comments.blade.php` | Modify |

### Coach Views (Task 7)
| File | Action |
|------|--------|
| `resources/views/coach/dashboard.blade.php` | Modify |
| `resources/views/coach/clients/index.blade.php` | Modify |
| `resources/views/coach/clients/show.blade.php` | Modify |
| `resources/views/coach/clients/create.blade.php` | Modify |
| `resources/views/coach/clients/create-track-only.blade.php` | Modify |
| `resources/views/coach/clients/edit.blade.php` | Modify |
| `resources/views/coach/clients/analytics.blade.php` | Modify |
| `resources/views/coach/clients/check-in.blade.php` | Modify |
| `resources/views/coach/clients/nutrition.blade.php` | Modify |
| `resources/views/coach/clients/loyalty.blade.php` | Modify |
| `resources/views/coach/clients/workout-log.blade.php` | Modify |
| `resources/views/coach/clients/workout-log-form.blade.php` | Modify |
| `resources/views/coach/programs/index.blade.php` | Modify |
| `resources/views/coach/programs/create.blade.php` | Modify |
| `resources/views/coach/programs/edit.blade.php` | Modify |
| `resources/views/coach/programs/show.blade.php` | Modify |
| `resources/views/coach/programs/assign.blade.php` | Modify |
| `resources/views/coach/programs/targets.blade.php` | Modify |
| `resources/views/coach/exercises/index.blade.php` | Modify |
| `resources/views/coach/exercises/create.blade.php` | Modify |
| `resources/views/coach/exercises/edit.blade.php` | Modify |
| `resources/views/coach/exercises/show.blade.php` | Modify |
| `resources/views/coach/meals/index.blade.php` | Modify |
| `resources/views/coach/meals/create.blade.php` | Modify |
| `resources/views/coach/meals/edit.blade.php` | Modify |
| `resources/views/coach/messages/index.blade.php` | Modify |
| `resources/views/coach/messages/show.blade.php` | Modify |
| `resources/views/coach/rewards/index.blade.php` | Modify |
| `resources/views/coach/rewards/create.blade.php` | Modify |
| `resources/views/coach/rewards/edit.blade.php` | Modify |
| `resources/views/coach/redemptions/index.blade.php` | Modify |
| `resources/views/coach/achievements/index.blade.php` | Modify |
| `resources/views/coach/achievements/create.blade.php` | Modify |
| `resources/views/coach/achievements/edit.blade.php` | Modify |
| `resources/views/coach/branding.blade.php` | Modify |
| `resources/views/coach/settings/edit.blade.php` | Modify |
| `resources/views/coach/plan.blade.php` | Modify |
| `resources/views/coach/subscription.blade.php` | Modify |
| `resources/views/coach/tracking-metrics/index.blade.php` | Modify |
| `resources/views/profile/edit.blade.php` | Modify |
| `resources/views/profile/partials/update-profile-information-form.blade.php` | Modify |
| `resources/views/profile/partials/update-password-form.blade.php` | Modify |
| `resources/views/profile/partials/delete-user-form.blade.php` | Modify |
| `resources/views/dashboard.blade.php` | Modify |

---

## Design Tokens Reference

Keep this open throughout implementation. Every file must use these values — no deviation.

### Colors (Tailwind classes)
```
bg-white                    → primary background (light)
dark:bg-gray-950            → primary background (dark)
bg-gray-50 / dark:bg-gray-900 → secondary background / cards
text-[#222222] dark:text-gray-100 → primary text
text-[#45515e] dark:text-gray-400 → secondary text
text-[#8e8e93] dark:text-gray-500 → muted text
bg-[#1456f0]                → brand blue
bg-[#3b82f6]                → primary-500 (action)
hover:bg-[#2563eb]          → primary-600 (hover)
bg-[#181e25]                → dark CTA / footer
border-gray-200 dark:border-gray-800 → default border
bg-gray-50 dark:bg-gray-900 → subtle section bg
```

### Coach Accent (CSS variable — always use this for active/progress states)
```
style="color: var(--color-primary)"
style="background-color: var(--color-primary)"
style="border-color: var(--color-primary)"
```
Fallback if not set: `#1456f0`.

### Typography classes
```
font-display    → Outfit (headings, hero, card titles)
font-sans       → DM Sans (body, nav, buttons — default)
font-mid        → Poppins (sub-headings, feature labels)
font-mono       → Roboto (stats, metrics)
```

### Border radius
```
rounded         → 4px (tags, badges)
rounded-lg      → 8px (buttons, small cards)
rounded-xl      → 12px (medium panels)
rounded-2xl     → 20px (feature cards)
rounded-3xl     → 24px (hero cards)
rounded-full    → 9999px (pill nav, toggles)
```

### Shadows
```
shadow-card     → rgba(0,0,0,0.08) 0px 4px 6px
shadow-brand    → rgba(44,30,116,0.16) 0px 0px 15px
shadow-elevated → rgba(36,36,36,0.08) 0px 12px 16px -4px
```

### Button patterns
```html
{{-- Primary dark (main CTA) --}}
<button class="inline-flex items-center px-5 py-2.5 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors duration-150">

{{-- Primary blue (brand action) --}}
<button class="inline-flex items-center px-5 py-2.5 bg-[#1456f0] text-white text-sm font-semibold rounded-lg hover:bg-[#2563eb] transition-colors duration-150">

{{-- Secondary --}}
<button class="inline-flex items-center px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-[#333] dark:text-gray-200 text-sm font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-150">

{{-- Danger --}}
<button class="inline-flex items-center px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-500 transition-colors duration-150">
```

### Card pattern
```html
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
```

### Section heading pattern
```html
<h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">Title</h1>
<p class="text-[#45515e] dark:text-gray-400 mt-1">Description</p>
```

### Alert patterns
```html
{{-- Success --}}
<div class="rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
  <p class="text-sm text-green-800 dark:text-green-200">...</p>
</div>

{{-- Error --}}
<div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
  <p class="text-sm text-red-800 dark:text-red-200">...</p>
</div>

{{-- Warning --}}
<div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-4">
  <p class="text-sm text-amber-800 dark:text-amber-200">...</p>
</div>

{{-- Info --}}
<div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
  <p class="text-sm text-blue-800 dark:text-blue-200">...</p>
</div>
```

---

## Task 1: Design System — Tailwind Config + CSS

**Files:**
- Modify: `tailwind.config.js`
- Modify: `resources/css/app.css`

- [ ] **Step 1: Update `tailwind.config.js`**

Replace the entire file contents with:

```js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        screens: {
            sm: '540px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1536px',
        },
        extend: {
            fontFamily: {
                sans: ['DM Sans', 'Helvetica Neue', 'Helvetica', 'Arial', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', 'Helvetica Neue', 'Helvetica', 'Arial', ...defaultTheme.fontFamily.sans],
                mid: ['Poppins', 'Helvetica Neue', 'Helvetica', 'Arial', ...defaultTheme.fontFamily.sans],
                mono: ['Roboto', 'Helvetica Neue', 'Helvetica', 'Arial', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                'brand-blue': '#1456f0',
                'brand-pink': '#ea5ec1',
                'dark-surface': '#181e25',
                'text-primary': '#222222',
                'text-secondary': '#45515e',
                'text-muted': '#8e8e93',
            },
            boxShadow: {
                card: 'rgba(0, 0, 0, 0.08) 0px 4px 6px',
                ambient: 'rgba(0, 0, 0, 0.08) 0px 0px 22.576px',
                brand: 'rgba(44, 30, 116, 0.16) 0px 0px 15px',
                elevated: 'rgba(36, 36, 36, 0.08) 0px 12px 16px -4px',
            },
        },
    },

    plugins: [forms],
};
```

- [ ] **Step 2: Update `resources/css/app.css`**

Replace the top of the file (before `@tailwind base`) with the Google Fonts import and CSS variables, then keep the rest:

```css
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap');

@tailwind base;
@tailwind components;
@tailwind utilities;

[x-cloak] { display: none !important; }

@layer base {
    *,
    ::after,
    ::before,
    ::backdrop,
    ::file-selector-button {
        border-color: theme('colors.gray.200');
    }

    button:not(:disabled),
    [role="button"]:not(:disabled) {
        cursor: pointer;
    }

    :root {
        --color-primary: #1456f0;
        --color-secondary: #2563eb;
    }
}
```

Remove all lines after the `@layer base` block that reference old font/color variables (`sticky`, `navbar-logo`, etc.) — keep only `[x-cloak]` and the base layer.

- [ ] **Step 3: Run existing tests to confirm nothing broke**

```bash
php artisan test --compact
```

Expected: all existing tests pass (no HTML/CSS tests — config changes don't break functionality).

- [ ] **Step 4: Commit**

```bash
git add tailwind.config.js resources/css/app.css
git commit -m "feat: update design system — DM Sans/Outfit/Poppins/Roboto fonts, new color tokens, shadow scale"
```

---

## Task 2: Layout Shells

**Files:**
- Modify: `resources/views/components/layouts/coach.blade.php`
- Modify: `resources/views/components/layouts/client.blade.php`
- Modify: `resources/views/layouts/guest.blade.php`

### 2a — Coach Layout

- [ ] **Step 1: Replace the font `<link>` in `coach.blade.php`**

Find the existing bunny.net font link:
```html
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
```
Remove it — fonts are now loaded via `app.css`.

- [ ] **Step 2: Update the `<body>` tag**

Change:
```html
<body x-data="{ trialBanner: ..., graceBanner: ... }" class="font-sans antialiased bg-gray-50 dark:bg-gray-950">
```
To:
```html
<body x-data="{ trialBanner: {{ auth()->user()?->onTrial() ? 'true' : 'false' }}, graceBanner: {{ session('subscription_grace_days') !== null ? 'true' : 'false' }} }" class="font-sans antialiased bg-gray-50 dark:bg-gray-950">
```
(The `font-sans` now resolves to DM Sans from the updated Tailwind config — no other change needed.)

- [ ] **Step 3: Update the mobile header bar**

Find the mobile header `<div class="md:hidden fixed top-0 ...">` block. Update the inner `flex` container class from:
```html
<div class="flex items-center justify-between px-4 h-14">
```
To:
```html
<div class="flex items-center justify-between px-4 h-14 border-b border-gray-200 dark:border-gray-800">
```

- [ ] **Step 4: Update the desktop sidebar styles**

Find the `<aside ... class="hidden md:flex ...">` element. Update its class to:
```html
class="hidden md:flex md:flex-col md:fixed md:bottom-0 md:left-0 md:w-56 md:bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 {{ (auth()->user()?->onTrial() || session('subscription_grace_days') !== null) ? 'md:top-11' : 'md:top-0' }}"
```

- [ ] **Step 5: Update sidebar nav item active state classes throughout `coach.blade.php`**

Every nav link currently uses `bg-blue-50 dark:bg-blue-900/30` for the active state. Do a find-and-replace across the entire file:

Find: `bg-blue-50 dark:bg-blue-900/30`
Replace with: `bg-[#eff6ff] dark:bg-blue-900/20`

Find (inactive state): `text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100`
Replace with: `text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-[#222222] dark:hover:text-gray-100`

Find (inactive icon): `text-gray-400 dark:text-gray-500`
Replace with: `text-gray-400 dark:text-gray-500` (no change needed here)

- [ ] **Step 6: Update main content wrapper**

Find the `<div class="md:pl-64 ...">` or equivalent main content wrapper and update to:
```html
<div class="md:pl-56 flex flex-col flex-1">
```

Find the main `<main>` tag and ensure it has:
```html
<main class="flex-1 min-h-screen bg-gray-50 dark:bg-gray-950 p-6">
```

- [ ] **Step 7: Update user info section at bottom of sidebar**

Find the sidebar's user info footer. Update the sign-out button:
```html
<button type="submit" class="text-xs text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-300 transition-colors">
    {{ __('coach.layout.nav.sign_out') }}
</button>
```

- [ ] **Step 8: Update the mobile menu overlay**

Find `<div id="mobile-menu" ...>`. The inner white panel:
```html
<div class="fixed inset-y-0 left-0 w-56 bg-white dark:bg-gray-900 shadow-xl border-r border-gray-200 dark:border-gray-800">
```

Apply the same nav link class replacements from Step 5 to the mobile nav items inside this overlay.

### 2b — Client Layout

- [ ] **Step 9: Replace the font `<link>` in `client.blade.php`**

Remove the bunny.net font link — fonts come from `app.css`.

- [ ] **Step 10: Update `<body>` background**

Change:
```html
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-950">
```
To:
```html
<body class="font-sans antialiased bg-white dark:bg-gray-950">
```

- [ ] **Step 11: Update the fixed top header**

Find the top header `<div class="fixed top-0 left-0 right-0 bg-white dark:bg-gray-900 shadow-sm z-40">`. Update to:
```html
<div class="fixed top-0 left-0 right-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 z-40">
```
(Remove `shadow-sm`, add `border-b` instead — cleaner MiniMax look.)

- [ ] **Step 12: Update the bottom nav**

Find `<nav class="fixed bottom-0 ... bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 z-40">`.

Update each tab link. For the **active** tab, replace any `style="color: var(--color-primary)"` patterns. Add a pill indicator above each active icon. Replace the grid of tab links with this pattern (shown for Home tab; repeat for all 6 tabs):

```html
<!-- Home Tab -->
<a href="{{ route('client.dashboard') }}"
   class="relative flex flex-col items-center justify-center py-2 flex-1 {{ request()->routeIs('client.dashboard') ? 'text-[#1456f0]' : 'text-[#8e8e93] dark:text-gray-500' }}"
   {!! request()->routeIs('client.dashboard') ? 'style="color: var(--color-primary)"' : '' !!}>
    @if(request()->routeIs('client.dashboard'))
        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-6 h-0.5 rounded-full" style="background-color: var(--color-primary)"></span>
    @endif
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    <span class="text-[10px] mt-0.5 font-medium">{{ __('client.layout.nav.home') }}</span>
</a>
```

Apply the same pattern to Program, Log, Check-in, Nutrition, and History tabs (use the same SVG icons as currently exist, just update the outer `<a>` classes and add the pill indicator).

### 2c — Guest Layout

- [ ] **Step 13: Rewrite `resources/views/layouts/guest.blade.php`**

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'LiftDeck') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 min-h-screen">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
            <a href="/" class="mb-8">
                <x-application-logo class="h-9 w-auto" />
            </a>
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8"
                 style="box-shadow: rgba(44, 30, 116, 0.12) 0px 0px 24px;">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
```

- [ ] **Step 14: Update `resources/views/components/application-logo.blade.php`**

Replace the SVG with a text logo:
```html
<span {{ $attributes->merge(['class' => 'font-display font-bold text-2xl text-[#222222] dark:text-gray-100 tracking-tight']) }}>
    Lift<span class="text-[#1456f0]">Deck</span>
</span>
```

- [ ] **Step 15: Run auth-related tests**

```bash
php artisan test --compact tests/Feature/Auth/
```

Expected: all auth tests pass (layout changes don't affect auth logic).

- [ ] **Step 16: Commit**

```bash
git add resources/views/components/layouts/coach.blade.php resources/views/components/layouts/client.blade.php resources/views/layouts/guest.blade.php resources/views/components/application-logo.blade.php
git commit -m "feat: restyle coach, client, and guest layout shells"
```

---

## Task 3: Atomic Components

**Files:**
- Modify: `resources/views/components/primary-button.blade.php`
- Modify: `resources/views/components/secondary-button.blade.php`
- Modify: `resources/views/components/danger-button.blade.php`
- Modify: `resources/views/components/text-input.blade.php`
- Modify: `resources/views/components/input-label.blade.php`
- Modify: `resources/views/components/input-error.blade.php`
- Modify: `resources/views/components/auth-session-status.blade.php`
- Modify: `resources/views/components/modal.blade.php`
- Modify: `resources/views/components/nav-link.blade.php`

- [ ] **Step 1: Rewrite `primary-button.blade.php`**

```html
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-[#181e25] dark:bg-gray-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-gray-800 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1456f0] disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150']) }}>
    {{ $slot }}
</button>
```

- [ ] **Step 2: Rewrite `secondary-button.blade.php`**

```html
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg font-semibold text-sm text-[#333333] dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1456f0] disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150']) }}>
    {{ $slot }}
</button>
```

- [ ] **Step 3: Rewrite `danger-button.blade.php`**

```html
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150']) }}>
    {{ $slot }}
</button>
```

- [ ] **Step 4: Rewrite `text-input.blade.php`**

```html
@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge(['class' => 'w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 dark:focus:border-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150']) }}
>
```

- [ ] **Step 5: Rewrite `input-label.blade.php`**

```html
@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1']) }}>
    {{ $value ?? $slot }}
</label>
```

- [ ] **Step 6: Rewrite `input-error.blade.php`**

```html
@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-1.5 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="text-xs text-red-600 dark:text-red-400">{{ $message }}</li>
        @endforeach
    </ul>
@endif
```

- [ ] **Step 7: Rewrite `auth-session-status.blade.php`**

```html
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3']) }}>
        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ $status }}</p>
    </div>
@endif
```

- [ ] **Step 8: Update `modal.blade.php` — card styling only**

Find the inner modal card div:
```html
class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
```
Replace with:
```html
class="mb-6 bg-white dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
style="box-shadow: rgba(44, 30, 116, 0.12) 0px 0px 24px;"
```

Also update the backdrop div:
```html
<div class="absolute inset-0 bg-gray-900/60 dark:bg-gray-950/80"></div>
```

- [ ] **Step 9: Rewrite `nav-link.blade.php`**

(This component is used by the old Breeze nav — keep it functional but update styling.)

```html
@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center text-sm font-medium text-[#222222] dark:text-gray-100 border-b-2 border-[#1456f0] pb-1 transition-colors duration-150'
    : 'inline-flex items-center text-sm font-medium text-[#45515e] dark:text-gray-400 border-b-2 border-transparent pb-1 hover:text-[#222222] dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600 transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
```

- [ ] **Step 10: Run tests**

```bash
php artisan test --compact tests/Feature/Auth/ tests/Feature/ProfileTest.php
```

Expected: all pass.

- [ ] **Step 11: Commit**

```bash
git add resources/views/components/primary-button.blade.php resources/views/components/secondary-button.blade.php resources/views/components/danger-button.blade.php resources/views/components/text-input.blade.php resources/views/components/input-label.blade.php resources/views/components/input-error.blade.php resources/views/components/auth-session-status.blade.php resources/views/components/modal.blade.php resources/views/components/nav-link.blade.php
git commit -m "feat: restyle atomic components — buttons, inputs, alerts, modal"
```

---

## Task 4: Landing Page

**Files:**
- Rewrite: `resources/views/welcome.blade.php`

The current `welcome.blade.php` is large (existing landing page). Rewrite it entirely with the new design. The existing page has sections — map them to the new structure below.

- [ ] **Step 1: Rewrite `resources/views/welcome.blade.php`**

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LiftDeck — Coaching Platform for Fitness Professionals</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-[#222222]">

    {{-- NAVIGATION --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-sm border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center gap-8">
            <a href="/" class="font-display font-bold text-xl tracking-tight flex-shrink-0">
                Lift<span class="text-[#1456f0]">Deck</span>
            </a>
            <nav class="hidden md:flex items-center gap-1 flex-1">
                <a href="#features" class="px-4 py-2 rounded-full text-sm font-medium text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors">Features</a>
                <a href="#pricing" class="px-4 py-2 rounded-full text-sm font-medium text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors">Pricing</a>
                <a href="#coaches" class="px-4 py-2 rounded-full text-sm font-medium text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors">For Coaches</a>
            </nav>
            <div class="flex items-center gap-3 ml-auto">
                @if(Route::has('login'))
                    <a href="{{ route('login') }}" class="text-sm font-medium text-[#45515e] hover:text-[#222222] transition-colors">Sign in</a>
                @endif
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="px-4 py-2.5 bg-[#181e25] text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        Get started free
                    </a>
                @endif
            </div>
        </div>
    </header>

    {{-- HERO --}}
    <section class="pt-24 pb-20 px-6 text-center">
        <div class="max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-blue-50 text-[#1456f0] rounded-full px-4 py-1.5 text-sm font-semibold mb-8 border border-blue-100">
                <span class="w-2 h-2 rounded-full bg-[#1456f0]"></span>
                Built for fitness coaches
            </div>
            <h1 class="font-display text-5xl md:text-[64px] font-medium text-[#181e25] leading-[1.10] tracking-tight mb-6">
                Your coaching.<br>Their <span class="text-[#1456f0]">progress</span>.
            </h1>
            <p class="text-lg md:text-xl text-[#45515e] leading-relaxed max-w-2xl mx-auto mb-10">
                LiftDeck gives fitness coaches a complete platform — programs, check-ins, nutrition, messaging, and rewards — all in one place, accessible from any device.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-[#181e25] text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        Start free trial →
                    </a>
                @endif
                <a href="#features" class="w-full sm:w-auto px-8 py-3.5 bg-gray-100 text-[#333333] text-base font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                    See how it works
                </a>
            </div>
        </div>
    </section>

    {{-- SOCIAL PROOF --}}
    <section class="py-12 bg-gray-50 border-y border-gray-100">
        <div class="max-w-4xl mx-auto px-6">
            <div class="grid grid-cols-3 gap-8 text-center">
                <div>
                    <div class="font-display text-4xl font-semibold text-[#181e25]">500+</div>
                    <div class="text-sm text-[#8e8e93] mt-1.5">Coaches using LiftDeck</div>
                </div>
                <div>
                    <div class="font-display text-4xl font-semibold text-[#181e25]">12k+</div>
                    <div class="text-sm text-[#8e8e93] mt-1.5">Active clients tracked</div>
                </div>
                <div>
                    <div class="font-display text-4xl font-semibold text-[#181e25]">98%</div>
                    <div class="text-sm text-[#8e8e93] mt-1.5">Client retention rate</div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <div class="text-xs font-bold uppercase tracking-widest text-[#8e8e93] mb-3">Everything you need</div>
                <h2 class="font-display text-3xl md:text-[38px] font-semibold text-[#181e25] leading-tight">
                    The complete toolkit<br>for modern coaches
                </h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                {{-- Card 1 --}}
                <div class="rounded-2xl p-7 text-white" style="background: linear-gradient(135deg, #1456f0 0%, #3b82f6 100%); box-shadow: rgba(44,30,116,0.16) 0 0 20px;">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Training Programs</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Build and assign custom programs with exercise libraries, sets, reps, and progression tracking.</p>
                </div>
                {{-- Card 2 --}}
                <div class="rounded-2xl p-7 text-white" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); box-shadow: rgba(44,30,116,0.16) 0 0 20px;">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Client Check-ins</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Collect weekly check-ins with photos, metrics, and mood tracking — all in one dashboard.</p>
                </div>
                {{-- Card 3 --}}
                <div class="rounded-2xl p-7 text-white" style="background: linear-gradient(135deg, #181e25 0%, #2d3a4a 100%); box-shadow: rgba(44,30,116,0.16) 0 0 20px;">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Workout Logging</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Clients log workouts from their phone. You see every rep, set, and personal best in real time.</p>
                </div>
                {{-- Card 4 --}}
                <div class="rounded-2xl p-7 text-white" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%); box-shadow: rgba(44,30,116,0.16) 0 0 20px;">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Nutrition Plans</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Create meal plans and track macros. Keep nutrition and training aligned for every client.</p>
                </div>
                {{-- Card 5 --}}
                <div class="rounded-2xl p-7 text-white" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%); box-shadow: rgba(44,30,116,0.16) 0 0 20px;">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Messaging</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Direct messaging between coach and client — no switching apps, no lost threads.</p>
                </div>
                {{-- Card 6 --}}
                <div class="rounded-2xl p-7 text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); box-shadow: rgba(44,30,116,0.16) 0 0 20px;">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Loyalty & Rewards</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Motivate clients with points, achievements, and redeemable rewards tied to their progress.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="py-20 px-6 bg-gray-50 border-t border-gray-100">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="font-display text-3xl md:text-4xl font-semibold text-[#181e25] mb-4">
                Ready to grow your coaching business?
            </h2>
            <p class="text-[#45515e] text-lg mb-8">
                Join hundreds of coaches already using LiftDeck to deliver better results.
            </p>
            @if(Route::has('register'))
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 bg-[#181e25] text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    Start your free trial →
                </a>
            @endif
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-[#181e25] px-6 py-14">
        <div class="max-w-6xl mx-auto grid md:grid-cols-4 gap-10">
            <div class="md:col-span-1">
                <div class="font-display font-bold text-lg text-white mb-3">
                    Lift<span class="text-[#1456f0]">Deck</span>
                </div>
                <p class="text-sm text-white/50 leading-relaxed">
                    The complete platform for fitness coaches who want to deliver results at scale.
                </p>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Product</div>
                <div class="space-y-3">
                    <a href="#features" class="block text-sm text-white/70 hover:text-white transition-colors">Features</a>
                    <a href="#pricing" class="block text-sm text-white/70 hover:text-white transition-colors">Pricing</a>
                    <a href="{{ route('login') }}" class="block text-sm text-white/70 hover:text-white transition-colors">Sign in</a>
                </div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Company</div>
                <div class="space-y-3">
                    <span class="block text-sm text-white/70">About</span>
                    <span class="block text-sm text-white/70">Blog</span>
                    <span class="block text-sm text-white/70">Contact</span>
                </div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Legal</div>
                <div class="space-y-3">
                    <span class="block text-sm text-white/70">Privacy</span>
                    <span class="block text-sm text-white/70">Terms</span>
                </div>
            </div>
        </div>
        <div class="max-w-6xl mx-auto mt-10 pt-8 border-t border-white/10">
            <p class="text-xs text-white/30">© {{ date('Y') }} LiftDeck. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
```

- [ ] **Step 2: Run landing page test**

```bash
php artisan test --compact tests/Feature/LandingLocaleTest.php
```

Expected: passes (test checks landing page loads and locale switching works).

- [ ] **Step 3: Commit**

```bash
git add resources/views/welcome.blade.php
git commit -m "feat: redesign landing page — hero, feature cards, CTA, dark footer"
```

---

## Task 5: Auth Pages (Agent 1 — can run in parallel with Tasks 6 & 7)

**Context for the agent:** Foundation (Tailwind config, CSS, layouts, atomic components) is complete. The guest layout (`resources/views/layouts/guest.blade.php`) now renders a centered white card with brand-purple glow shadow on `bg-gray-50`. Atomic components (`x-primary-button`, `x-text-input`, `x-input-label`, `x-input-error`, `x-auth-session-status`) have all been updated.

**Your job:** Update each auth view's *inner content* to use the new design tokens. Do **not** change the logic, form actions, route references, or CSRF tokens. Only update visual classes and layout structure.

**Pattern to follow for every auth form:**
```html
<x-guest-layout>
    {{-- Optional status message --}}
    <x-auth-session-status class="mb-6" :status="session('status')" />

    {{-- Page heading --}}
    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-[#222222]">Sign in</h1>
        <p class="text-sm text-[#8e8e93] mt-1">Welcome back to LiftDeck</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <x-input-label for="email" :value="__('auth.login.email')" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" class="mt-1" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password" :value="__('auth.login.password')" />
                <x-text-input id="password" type="password" name="password" class="mt-1" required />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
        </div>

        <div class="flex items-center justify-between mt-5">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#1456f0] focus:ring-[#1456f0]">
                <span class="text-sm text-[#45515e]">{{ __('auth.login.remember_me') }}</span>
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-[#1456f0] hover:underline font-medium">
                    {{ __('auth.login.forgot_password') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full mt-6 justify-center">
            {{ __('auth.login.button') }}
        </x-primary-button>
    </form>
</x-guest-layout>
```

**Files to update** (apply the same heading + form spacing + button pattern, preserving all existing fields and logic):

- [ ] **`resources/views/auth/login.blade.php`** — Heading: "Sign in" / "Welcome back". Keep email, password, remember me, forgot password link.
- [ ] **`resources/views/auth/register.blade.php`** — Heading: "Create your account". Keep name, email, password, password confirmation fields. Link to login at bottom.
- [ ] **`resources/views/auth/forgot-password.blade.php`** — Heading: "Reset your password". Paragraph + email field + submit.
- [ ] **`resources/views/auth/reset-password.blade.php`** — Heading: "Set new password". Email (readonly), password, password_confirmation.
- [ ] **`resources/views/auth/verify-email.blade.php`** — No form, just a paragraph and "Resend verification" link + logout. Wrap in the same card spacing.
- [ ] **`resources/views/auth/confirm-password.blade.php`** — Heading: "Confirm password". Password field + confirm button.
- [ ] **`resources/views/auth/join.blade.php`** — Preserve existing join flow logic. Apply card heading + form spacing.
- [ ] **`resources/views/auth/join-register.blade.php`** — Preserve existing join-register flow. Apply card heading + form spacing.

- [ ] **Run auth tests**

```bash
php artisan test --compact tests/Feature/Auth/
```

Expected: all pass.

- [ ] **Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Commit**

```bash
git add resources/views/auth/
git commit -m "feat: restyle auth pages with new design system"
```

---

## Task 6: Client Views (Agent 2 — parallel with Tasks 5 & 7)

**Context for the agent:** Foundation is complete. The client layout (`resources/views/components/layouts/client.blade.php`) provides the fixed top bar and bottom tab nav — **do not touch those**. Your job is to restyle the `{{ $slot }}` content of each client page only.

**Core patterns for client pages:**

**Page heading:**
```html
<div class="mb-5">
    <h1 class="font-display text-xl font-semibold text-[#222222] dark:text-gray-100">Page Title</h1>
    <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">Optional subtitle</p>
</div>
```

**Content card:**
```html
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5 mb-4">
    ...
</div>
```

**Pill/tag badge:**
```html
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-[#1456f0] dark:bg-blue-900/30 dark:text-blue-400">
    Label
</span>
```

**Progress bar (uses coach accent):**
```html
<div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
    <div class="h-1.5 rounded-full transition-all" style="width: {{ $pct }}%; background-color: var(--color-primary)"></div>
</div>
```

**Section divider:**
```html
<hr class="border-gray-100 dark:border-gray-800 my-4">
```

**Flash/alert (success):**
```html
<div class="rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mb-4">
    <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
</div>
```

**Files to restyle** (apply patterns above, preserve all Livewire attributes, Alpine directives, routes, and logic):

- [ ] `resources/views/client/dashboard.blade.php` — stat cards, program preview card, quick-action buttons
- [ ] `resources/views/client/program.blade.php` — workout cards with pill badges, exercise list items
- [ ] `resources/views/client/log.blade.php` — workout selection cards, log history list
- [ ] `resources/views/client/log-workout.blade.php` — exercise set/rep inputs, complete button (keep all Alpine/Livewire logic)
- [ ] `resources/views/client/check-in.blade.php` — form card with metric inputs, photo upload area
- [ ] `resources/views/client/check-in-history.blade.php` — card grid of past check-ins
- [ ] `resources/views/client/nutrition.blade.php` — macro progress bars using coach accent, meal cards
- [ ] `resources/views/client/history.blade.php` — workout history list, notification badges with coach accent
- [ ] `resources/views/client/history-show.blade.php` — workout detail card, exercise set table
- [ ] `resources/views/client/achievements.blade.php` — achievement cards with gradient/colored icons
- [ ] `resources/views/client/rewards.blade.php` — reward cards, points balance
- [ ] `resources/views/client/loyalty.blade.php` — points history, level progress bar
- [ ] `resources/views/client/messages.blade.php` — message thread list, message bubbles
- [ ] `resources/views/client/settings/edit.blade.php` — settings form sections in cards
- [ ] `resources/views/client/welcome.blade.php` — onboarding welcome card
- [ ] `resources/views/client/onboarding.blade.php` — onboarding steps
- [ ] `resources/views/components/workout-log-comments.blade.php` — comment card

- [ ] **Run client tests**

```bash
php artisan test --compact tests/Feature/Client/
```

Expected: all pass.

- [ ] **Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Commit**

```bash
git add resources/views/client/ resources/views/components/workout-log-comments.blade.php
git commit -m "feat: restyle client views with new design system"
```

---

## Task 7: Coach Views (Agent 3 — parallel with Tasks 5 & 6)

**Context for the agent:** Foundation is complete. The coach layout (`resources/views/components/layouts/coach.blade.php`) provides the top bar and sidebar — **do not touch those**. Your job is to restyle the `{{ $slot }}` content of each coach page only.

**Core patterns for coach pages:**

**Page header:**
```html
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">Page Title</h1>
        <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">Optional subtitle</p>
    </div>
    {{-- Optional action button --}}
    <a href="{{ route('coach.something.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
        + Add New
    </a>
</div>
```

**Data table card:**
```html
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-[#8e8e93] dark:text-gray-400 uppercase tracking-wider">Column</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                <td class="px-5 py-4 text-sm text-[#222222] dark:text-gray-100">Value</td>
            </tr>
        </tbody>
    </table>
</div>
```

**Client avatar + name (common in coach views):**
```html
<div class="flex items-center gap-3">
    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 overflow-hidden"
         style="background-color: var(--color-primary)">
        @if($client->avatar)
            <img src="{{ $client->avatar }}" alt="{{ $client->name }}" class="w-full h-full object-cover">
        @else
            {{ strtoupper(substr($client->name, 0, 1)) }}
        @endif
    </div>
    <div>
        <div class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ $client->name }}</div>
        <div class="text-xs text-[#8e8e93] dark:text-gray-500">{{ $client->email }}</div>
    </div>
</div>
```

**Form section card:**
```html
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6 mb-5">
    <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">Section Title</h2>
    <div class="space-y-4">
        {{-- form fields --}}
    </div>
</div>
```

**Empty state:**
```html
<div class="text-center py-16">
    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-[#8e8e93]" ...></svg>
    </div>
    <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-1">No items yet</h3>
    <p class="text-sm text-[#8e8e93] dark:text-gray-500">Description of what to do.</p>
</div>
```

**Files to restyle:**

- [ ] `resources/views/coach/dashboard.blade.php` — stat cards, activity feed, metrics setup modal
- [ ] `resources/views/coach/clients/index.blade.php` — client table/grid, search, add button
- [ ] `resources/views/coach/clients/show.blade.php` — client profile header, tab navigation, stats
- [ ] `resources/views/coach/clients/create.blade.php` — form card
- [ ] `resources/views/coach/clients/create-track-only.blade.php` — form card
- [ ] `resources/views/coach/clients/edit.blade.php` — form card with sections
- [ ] `resources/views/coach/clients/analytics.blade.php` — charts + metric cards
- [ ] `resources/views/coach/clients/check-in.blade.php` — check-in history cards, photo gallery
- [ ] `resources/views/coach/clients/nutrition.blade.php` — macro cards, meal log
- [ ] `resources/views/coach/clients/loyalty.blade.php` — XP table, level card
- [ ] `resources/views/coach/clients/workout-log.blade.php` — workout log list
- [ ] `resources/views/coach/clients/workout-log-form.blade.php` — log form
- [ ] `resources/views/coach/programs/index.blade.php` — program cards/table
- [ ] `resources/views/coach/programs/create.blade.php` — builder form
- [ ] `resources/views/coach/programs/edit.blade.php` — builder form
- [ ] `resources/views/coach/programs/show.blade.php` — program detail
- [ ] `resources/views/coach/programs/assign.blade.php` — assign form
- [ ] `resources/views/coach/programs/targets.blade.php` — targets form
- [ ] `resources/views/coach/exercises/index.blade.php` — exercise table
- [ ] `resources/views/coach/exercises/create.blade.php` — form card
- [ ] `resources/views/coach/exercises/edit.blade.php` — form card
- [ ] `resources/views/coach/exercises/show.blade.php` — exercise detail
- [ ] `resources/views/coach/meals/index.blade.php` — meal table
- [ ] `resources/views/coach/meals/create.blade.php` — form card
- [ ] `resources/views/coach/meals/edit.blade.php` — form card
- [ ] `resources/views/coach/messages/index.blade.php` — client list with unread badges
- [ ] `resources/views/coach/messages/show.blade.php` — message thread
- [ ] `resources/views/coach/rewards/index.blade.php` — rewards table
- [ ] `resources/views/coach/rewards/create.blade.php` — form card
- [ ] `resources/views/coach/rewards/edit.blade.php` — form card
- [ ] `resources/views/coach/redemptions/index.blade.php` — redemption table
- [ ] `resources/views/coach/achievements/index.blade.php` — achievements table
- [ ] `resources/views/coach/achievements/create.blade.php` — form card
- [ ] `resources/views/coach/achievements/edit.blade.php` — form card
- [ ] `resources/views/coach/branding.blade.php` — color pickers, logo upload, preview card
- [ ] `resources/views/coach/settings/edit.blade.php` — settings form sections
- [ ] `resources/views/coach/plan.blade.php` — pricing cards with gradient accents
- [ ] `resources/views/coach/subscription.blade.php` — subscription status card
- [ ] `resources/views/coach/tracking-metrics/index.blade.php` — metrics table + add form
- [ ] `resources/views/profile/edit.blade.php` — tabbed profile sections
- [ ] `resources/views/profile/partials/update-profile-information-form.blade.php` — form card
- [ ] `resources/views/profile/partials/update-password-form.blade.php` — form card
- [ ] `resources/views/profile/partials/delete-user-form.blade.php` — danger zone card
- [ ] `resources/views/dashboard.blade.php` — apply `<x-layouts.coach>` wrapper if not already; simple placeholder card

- [ ] **Run coach tests**

```bash
php artisan test --compact tests/Feature/Coach/
```

Expected: all pass.

- [ ] **Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Commit**

```bash
git add resources/views/coach/ resources/views/profile/ resources/views/dashboard.blade.php
git commit -m "feat: restyle coach views with new design system"
```

---

## Task 8: Review Pass + Final Tests

**Run after Tasks 5, 6, 7 are all complete.**

- [ ] **Step 1: Run full test suite**

```bash
php artisan test --compact
```

Expected: all tests pass.

- [ ] **Step 2: Build frontend assets**

```bash
npm run build
```

Expected: no build errors.

- [ ] **Step 3: Restyle `resources/views/beta.blade.php` and `resources/views/offline.blade.php`**

For `beta.blade.php` — apply the guest card pattern (centered card on `bg-gray-50`, same shadow). For `offline.blade.php` — apply white background, centered message with `font-display` heading and `text-[#8e8e93]` body.

Also update `resources/views/layouts/app.blade.php` (legacy Breeze layout) to use the DM Sans font and new colors, in case any view still references `<x-app-layout>`.

- [ ] **Step 4: Verify dark mode** — Toggle dark mode on coach dashboard, client dashboard, and login page. Confirm backgrounds, text, borders, and cards render correctly in both modes.

- [ ] **Step 5: Verify coach accent** — As a client user whose coach has a custom `primary_color` set, confirm the bottom nav active tab, progress bars, and CTA button use the custom color rather than `#1456f0`.

- [ ] **Step 6: Run Pint on any remaining dirty files**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 7: Final commit**

```bash
git add -A
git commit -m "feat: complete UI redesign — MiniMax-inspired design system"
```
