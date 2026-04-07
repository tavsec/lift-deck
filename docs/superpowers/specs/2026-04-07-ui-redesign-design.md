# UI Redesign — Design Spec
**Date:** 2026-04-07
**Scope:** All authenticated and public UI except Filament admin dashboard
**Inspiration:** MiniMax design system (DESIGN.md)

---

## Overview

Full UI redesign of LiftDeck applying a MiniMax-inspired design language: white-dominant canvas, multi-font system, pill navigation, generous card rounding, and colorful gradient feature cards as the primary visual interest. Both light and dark mode are supported. Coach custom branding is preserved as an accent layer only.

---

## 1. Design System Foundation

### Color Tokens

| Token | Light | Dark | Role |
|-------|-------|------|------|
| `bg-primary` | `#ffffff` | `#111827` | Main page background |
| `bg-secondary` | `#f2f3f5` | `#1f2937` | Cards, panels, sidebar |
| `text-primary` | `#222222` | `#f9fafb` | Body text, headings |
| `text-secondary` | `#45515e` | `#9ca3af` | Sub-text, descriptions |
| `text-muted` | `#8e8e93` | `#6b7280` | Captions, placeholders |
| `brand-blue` | `#1456f0` | `#60a5fa` | Brand identity color |
| `primary-500` | `#3b82f6` | `#3b82f6` | Action buttons |
| `primary-600` | `#2563eb` | `#2563eb` | Hover states |
| `dark-surface` | `#181e25` | `#1d2a3a` | Dark CTAs, footer bg |
| `border-default` | `#e5e7eb` | `#374151` | Component borders |
| `border-subtle` | `#f2f3f5` | `#1f2937` | Section dividers |

### Typography

| Family | Role | Usage |
|--------|------|-------|
| **DM Sans** | UI workhorse | Body, nav, buttons, captions, labels (~70% of text) |
| **Outfit** | Display | Section headings, hero headlines, card titles |
| **Poppins** | Mid-tier | Sub-headings, feature names |
| **Roboto** | Technical | Stats, metrics, data-heavy contexts |

Loaded via Google Fonts (replacing current bunny.net Instrument Sans). Weights: 400, 500, 600, 700.

**Key typographic rules:**
- Universal `1.5` line-height for body text
- `1.10` tight line-height for hero/display headings
- Weight 500 as default for headings (not bold); 600 for section titles; 700 reserved for strong body emphasis
- DM Sans for all functional UI; Outfit for display/branding only

### Border Radius Scale

| Value | Use |
|-------|-----|
| `4px` | Tags, micro badges |
| `8px` | Buttons, small UI cards |
| `12px` | Medium panels, form containers |
| `20px–24px` | Feature cards, content showcase cards |
| `9999px` | Pill nav tabs, toggle buttons |

### Shadow Levels

| Level | Value | Use |
|-------|-------|-----|
| 0 | none | Flat elements, text blocks |
| 1 | `rgba(0,0,0,0.08) 0 4px 6px` | Standard cards, containers |
| 2 | `rgba(0,0,0,0.08) 0 0 22px` | Ambient soft glow |
| 3 | `rgba(44,30,116,0.16) 0 0 15px` | Featured/hero cards (brand purple glow) |
| 4 | `rgba(36,36,36,0.08) 0 12px 16px -4px` | Elevated/hover cards |

### Coach Custom Branding (Accent Layer)

Coaches retain custom `--color-primary` / `--color-secondary` CSS variables, but they apply **only** to accent points in the client-facing UI:
- Active nav tab indicator (bottom nav)
- Progress bars and completion rings
- Notification badge background
- Primary CTA button on client pages
- Input focus ring
- Active tab underline

Falls back to `#1456f0` if not set. The accent does **not** override backgrounds, cards, typography, or layout colors.

---

## 2. Layout Designs

### Coach Layout (`resources/views/components/layouts/coach.blade.php`)

**Desktop:**
- White sticky top bar (64px): logo/gym name left, trial badge + avatar right
- Left sidebar (210px): icon + label nav items, `8px` radius active state on `#eff6ff` background with `#1456f0` text
- Main content area: `#f9fafb` background, `24px` padding
- Nav items: Dashboard, Clients, Programs, Exercises, Meals, Messages, Rewards, Achievements, Settings, Branding, Plan/Subscription

**Mobile (< 768px):**
- Top bar collapses sidebar to hamburger overlay
- Overlay sidebar slides in from left on dark backdrop
- Same nav items, same styling

**Dark mode:** Top bar `#111827`, sidebar `#111827` with `#1f2937` active state, content area `#0f172a`.

### Client Layout (`resources/views/components/layouts/client.blade.php`)

Mobile-first PWA shell:
- **Fixed top bar** (52px): gym logo/name left, locale switcher + dark mode toggle + messages icon + avatar right. White background, `border-bottom: 1px solid #e5e7eb`.
- **Scrollable content area**: `pt-16 pb-20`, `max-w-4xl mx-auto px-4`
- **Fixed bottom nav** (58px): 6 tabs (Home, Program, Log, Check-in, Nutrition, History). Active tab uses `--color-primary` for icon + label + a 3px pill indicator above the icon. Inactive tabs: `#8e8e93`.

**Dark mode:** Top bar and bottom nav `#111827`, active indicator still uses coach accent color.

### Guest / Auth Layout (`resources/views/layouts/guest.blade.php`)

- Full-screen `#f9fafb` background
- Centered card: `max-w-md`, `border-radius: 20px`, `box-shadow: rgba(44,30,116,0.12) 0 0 24px`, `border: 1px solid #f2f3f5`
- LiftDeck logo above the card
- All auth forms render inside the card

**Dark mode:** Background `#0f172a`, card `#111827`, border `#1f2937`.

---

## 3. Atomic Components

### Buttons

| Variant | Bg | Text | Radius | Use |
|---------|-----|------|--------|-----|
| Primary Dark | `#181e25` | `#fff` | `8px` | Main CTA |
| Primary Blue | `#1456f0` | `#fff` | `8px` | Brand action |
| Secondary | `#f0f0f0` | `#333` | `8px` | Secondary actions |
| Pill Nav | `rgba(0,0,0,0.05)` | `#111` | `9999px` | Nav tabs, toggles |
| Danger | `#dc2626` | `#fff` | `8px` | Destructive actions |

### Form Inputs

- Border: `1px solid #e5e7eb`, radius `8px`, padding `9px 12px`
- Focus ring: `2px solid --color-primary` (coach accent, falls back to `#1456f0`)
- Error state: `border-color: #dc2626`
- Dark mode: `bg: #1f2937`, `border: #374151`, `text: #f9fafb`

### Cards

- **Standard:** `bg-white`, `border-radius: 12px`, `border: 1px solid #e5e7eb`, shadow level 1
- **Feature/Showcase:** `border-radius: 20px`, gradient background, shadow level 3 (brand glow)
- **Stat:** `bg-white`, `border-radius: 12px`, shadow level 1, large number + small label
- Dark mode standard: `bg: #1f2937`, `border: #374151`

### Navigation (Coach Sidebar)

- Nav item: `display: flex`, icon (16px) + label, `padding: 8px 10px`, `border-radius: 8px`
- Active: `background: #eff6ff`, `color: #1456f0`
- Inactive: `color: #45515e`, hover `background: #f9fafb`
- Section dividers between groups

### Flash Messages / Alerts

- Success: `bg: #e8ffea`, `border: #86efac`, `text: #166534`
- Error: `bg: #fef2f2`, `border: #fca5a5`, `text: #991b1b`
- Warning: `bg: #fffbeb`, `border: #fcd34d`, `text: #92400e`
- Info: `bg: #eff6ff`, `border: #bfdbfe`, `text: #1e40af`
- All: `border-radius: 8px`, `padding: 12px 16px`

---

## 4. Landing Page (`resources/views/welcome.blade.php`)

Sections in order:

1. **Sticky nav:** Logo left, pill nav links center, "Sign in" text link + dark CTA button right
2. **Hero:** Badge pill + large Outfit headline (58px, weight 500, line-height 1.10) + body copy + two CTA buttons (primary dark + secondary light)
3. **Social proof strip:** 3 stats (`#f9fafb` background) — coaches, clients, retention
4. **Feature cards grid (3×2):** 6 colorful gradient cards (blue, purple, dark, teal, orange, green) on white canvas. Each: `border-radius: 20px`, brand-purple glow shadow, icon box + heading + description in white text
5. **CTA section:** Centered headline + body + single dark CTA button
6. **Footer:** Dark `#181e25` background, 4-column grid, logo + tagline left, product/company/legal links right in `rgba(255,255,255,0.7)`

---

## 5. Remaining Pages

All ~80 views (coach, client, auth) are restyled to the new design system. Structure and functionality are preserved; only classes, layout shell references, and visual styling change.

**Auth pages:** Login, register, forgot-password, reset-password, verify-email, join, join-register, confirm-password — all use the centered guest card layout, DM Sans form labels and inputs, dark primary button.

**Client pages (~15 views):** Dashboard, program, log, check-in, check-in history, nutrition, history, history-show, achievements, rewards, loyalty, messages, settings, welcome, onboarding — all use the client mobile layout, card-based content, coach accent for active/progress states.

**Coach pages (~35 views):** Dashboard, clients (index/show/create/edit/analytics/check-in/nutrition/loyalty/workout-log), programs, exercises, meals, messages, rewards, achievements, redemptions, branding, settings, plan, subscription, tracking-metrics — all use the coach desktop/mobile layout, standard card and table patterns.

---

## 6. Implementation Approach

**Approach B — Foundation + Parallel Agents:**

1. **Foundation (main session):**
   - Update `tailwind.config.js` with new colors, fonts, shadows, border-radius scale
   - Update `resources/css/app.css` with CSS custom properties and Google Fonts imports
   - Rewrite `coach.blade.php`, `client.blade.php`, `guest.blade.php` layouts
   - Rewrite all atomic components: buttons, inputs, cards, nav, alerts, modals, dropdowns
   - Add `.superpowers/` to `.gitignore`

2. **Parallel agents:**
   - Agent 1: Auth pages + landing page (`welcome.blade.php`, all `auth/` views)
   - Agent 2: All client views (`client/` directory)
   - Agent 3: All coach views (`coach/` directory)

3. **Review pass:** Catch any visual inconsistencies, verify dark mode across all pages, confirm coach accent works end-to-end.

---

## 7. Out of Scope

- Filament admin dashboard — untouched
- Backend logic, routes, controllers — no changes
- BladewindUI third-party components — CSS/JS assets remain loaded; individual BladewindUI blade components (e.g. `<x-bladewind.alert>`) are replaced with native Tailwind equivalents in layouts and atomic components, but left in-place on any page where they are deeply embedded in functional logic (to avoid breaking behaviour)
- Mail templates — out of scope for this redesign
