# Sprint 1–4 QA Report

**Date:** 2026-05-02
**Tested:** Sprint 1 (quick-log mechanics, TDEE/macro calculator, Needs-attention dashboard widget) · Sprint 2 (per-client meal prescriptions, custom portion, desktop nav redesign) · Sprint 3 (inline meal-log comments, Open Food Facts search) · Sprint 4 (per-client Day Plans, 4-source item picker, dynamic sections, "Today's Plan" client card)
**Viewports:** 375×812 (mobile) · 1280×800 (desktop)
**Modes:** light + dark

## Executive summary

Sprints 1–4 broadly work end-to-end. Coach-side TDEE calculator, the per-client Plans card, the 4-source plan editor (Library/Custom/OFF/Macros), dynamic sections (incl. "Pre-workout"), client-side "Today's Plan" with grouped sections and one-tap Mark-as-eaten, the meal-log comment thread, and the unread-comment badge that auto-clears on page load all behave correctly. **One real CRITICAL bug**: every page load of `/client/nutrition` throws an Alpine `Unexpected token 'return'` SyntaxError because `@submit="return foodFormReady()"` is invalid inside Alpine's async-function wrapper. **One HIGH** mobile-layout collision: the fixed bottom nav covers the last day-plan item / Mark-as-eaten button. A handful of MEDIUM/LOW polish issues round it out.

## 🔴 Critical bugs (block usage)

### 1. Alpine SyntaxError on every `/client/nutrition` page load

- **Where:** `resources/views/client/nutrition.blade.php:652`
- **Code:** `<form ... @submit="return foodFormReady()">`
- **Steps:** Log in as client → visit `/client/nutrition` → check browser DevTools console
- **Expected:** No JS errors
- **Actual:** Uncaught `SyntaxError: Unexpected token 'return'` thrown by Alpine's `safeAsyncFunction`. `@submit` expression bodies are wrapped in an `async () => { … }` and a bare top-level `return` is illegal there.
- **Impact:** The error fires on every render (even before the OFF Search tab is opened). It ALSO prevents the OFF-Search submit button from gating on `foodFormReady()` — the form may submit even when invalid. A clean console is also a precondition for catching real regressions in Nightwatch / Sentry.
- **Fix:** drop the `return`, or use a callable expression — e.g. `@submit.prevent="if (!foodFormReady()) { $event.preventDefault(); return; } $el.submit()"`, or simpler `@submit="if (!foodFormReady()) $event.preventDefault()"`.

## 🟠 High-priority issues

### 1. Mobile bottom-nav covers the last day-plan item ("Mark as eaten" hidden)

- **Where:** screenshots `24-mobile-client-nutrition.png`, `25-mobile-client-search.png` — viewport 375×812, `/client/nutrition` with assignment present
- **What:** the fixed `bottom-0 md:hidden` nav is rendered on top of the Pre-workout section's button. The viewport fits Breakfast → Dinner; the Pre-workout row's Mark-as-eaten button is occluded.
- **Impact:** clients cannot mark Pre-workout items as eaten without scrolling AND the static nav still covers it. Works on desktop (no fixed nav).
- **Fix:** add `pb-20` (or matching `pb-[5rem]`) to the page wrapper on mobile, OR switch the bottom nav to a non-overlapping layout.

## 🟡 Medium-priority polish

### 1. Cookie-consent banner overlaps editor / nutrition content
- **Where:** `04-plan-editor-blank.png`, `03b-tdee-applied.png`, `05c-fully-built.png`
- The "We use cookies" pop-over sits in the bottom-left and **occludes the Breakfast section** of the day-plan editor and parts of the macro-goal form. It only goes away when the user dismisses it. Consider lowering its z-index footprint or rendering it as a non-blocking toast.

### 2. "Custom macros" placeholder name is opaque to clients
- **Where:** `/client/nutrition` — Today's Plan, Dinner section
- When a coach uses the "Macros only" source without naming the item, the row shows only "Custom macros" + macros, with no hint of *what* the coach wants the client to eat. Encourage coaches to fill a name field, or show "Custom macros (Dinner)" automatically.

### 3. Adherence shows 0% even after marking 1/5 eaten
- **Where:** `/client/nutrition` — "AVG ADHERENCE" stat in the 30-day card
- After Mark-as-eaten on the apple → progress bar correctly reads "1 of 5 completed / 20%", but the AVG ADHERENCE underneath remains 0%. Probably because the metric averages over previous days where no plan existed; consider scoping the rolling window to days that had an assignment, or label the stat as "30-day".

### 4. Active link on desktop client nav (h-12) reads "Home" when the user is on `/client/nutrition`
- **Where:** screenshot `16-desktop-nav.png` — JSON dump from `nav-info.json` shows `activeLink: "Home"` even though "Nutrition" is highlighted blue with the underline
- The visual underline is correct, but no link carries `aria-current="page"`. Screen readers will announce the wrong active item.
- **Fix:** add `aria-current="page"` (or `wire:current`) to the active route.

### 5. OFF results have no nutrition for some entries (UX, not a bug)
- For the "yogurt" search the first result is `Assil vanille (Danone)` — at 200g it returned `191 kcal · P 6.8g · C 28g · F 5.4g`. Reasonable, though P/C/F per 100g for some products show unrealistic values (zeros). Worth showing a "data may be incomplete" caveat when any macro is `null`/`0`.

## 🟢 Low-priority / nice-to-have

- **Truncated meal name** on mobile day-plan list: "Chicken Rice B…" — consider 2-line clamp instead of single-line ellipsis (`24-mobile-client-nutrition.png`).
- **Cookie-preferences link** on the coach dark sidebar overlaps the "Sign out" caption (`27-dark-plan-editor.png`).
- **Header copy:** date input on `/client/nutrition` uses native browser styling — slightly mis-aligned with the LiftDeck design language (rounded vs square corners on Chromium).
- **Confirm dialog** on "Archive Day Plan" still says "Archive this day plan?" — fine, but it doesn't explain that existing assignments are kept (already explained on the form, but double-confirmation always helps).

## What works well

- **TDEE calculator** populates `Calories=2650`, `Protein=135`, `Carbs=393`, `Fat=60` for 75 kg / 178 cm / 30 / male / moderate / maintain — math sanity passes (`cal ≈ 4P + 4C + 9F`).
- **4-source picker** in the day-plan editor: Library (with portion 1.5×), Custom, OFF (yogurt → 200 g), Macros-only — all four save the row with snapshot name + macros. Items end up under their selected sections.
- **Dynamic sections**: defaults Breakfast/Lunch/Dinner/Snack appear on a new plan; "+ Add section" → "Pre-workout" works; rename + remove (when empty) wired up.
- **Client "Today's Plan" card** renders sections in first-seen order Breakfast → Lunch → Snack → Dinner → Pre-workout (matching spec). Each row shows snapshot name + macros + Mark-as-eaten.
- **Mark-as-eaten** persists across reload; macro progress updates immediately; "Logged" pill replaces the button.
- **Inline coach comment** ("Great work!") is visible to the client, the unread badge appears on `/client/nutrition`, and is auto-cleared on the next reload (verified via `data-testid="unread-comments-badge"`).
- **Needs Attention widget** correctly hides itself when no clients meet a flag — fresh test client did NOT appear (good gating).
- **Per-client Day Plans** are at `/coach/clients/{id}/day-plans/{create,edit}`; the old top-level `/coach/day-plans` route is gone (returns 405). Plans card renders in the nutrition page with "+ New plan" link.
- **Section order on the client side** uses first-seen of items, which is exactly the spec (Snack appears before Dinner because the OFF item was added before the Macros item).
- **Dark mode** holds up on both the plan editor and the OFF picker (screenshots `27-dark-plan-editor.png`, `28-dark-off-picker.png`).
- **OFF search** returns 19 results for "yogurt" within ~1.5s — server-side proxy is healthy.
- **Library tab + Custom portion**: typing 0.75 in the Custom portion field correctly produced `Oatmeal Bowl (×0.75) — 240 kcal`.

## Test data used

- Coach: `coach-test-ux@liftdeck.io` (id 124, subscribed, 4 library meals seeded: Oatmeal Bowl, Chicken Rice Bowl, Protein Shake, Salmon Salad)
- Client: `client-test-ux@liftdeck.io` (id 125, linked via `users.coach_id=124`, email-verified)
- Plans created: **Test Plan A** (Breakfast: Chicken Rice Bowl ×1.5; Lunch: Test custom apple [custom]; Snack: Assil vanille (Danone) [OFF]; Dinner: Custom macros [macros only]; Pre-workout: Protein Shake [library])
- Assignment: Test Plan A → today (2026-05-02)
- Logs: Test custom apple (via Mark-as-eaten), QA Quick Lunch (custom)
- Comment: "Great work!" by coach on the QA Quick Lunch entry

## Screenshots

All in `/tmp/qa-screenshots/`:

- `01-coach-dashboard.png` — coach dashboard, Needs-Attention card correctly absent
- `02-coach-client-nutrition.png` — client nutrition page (coach view) before TDEE
- `03a-tdee-form.png` — TDEE form open with profile fields
- `03b-tdee-applied.png` — macros populated after Apply (shows cookie banner overlap)
- `03c-goal-saved.png` — Current Goal card shows the saved values
- `04-plan-editor-blank.png` — fresh plan editor with default 4 sections
- `05a-after-library.png` — Library item added to Breakfast (×1.5)
- `05b-after-off.png` — OFF "Assil vanille" added to Snack
- `05c-fully-built.png` — full plan with 5 items across 5 sections (cookie overlap visible)
- `05d-after-save.png` — redirect to nutrition page after save
- `06a-plans-list.png` / `06b-after-assign.png` — Plan card listing + after assign
- `08-client-nutrition.png` — client view of Today's Plan
- `09a-after-mark-eaten.png` / `09b-after-reload.png` — Mark-as-eaten persistence
- `10a-custom-portion.png` — Library tab with Custom portion 0.75×
- `11a-search-tab.png` — Client OFF Search tab
- `12-after-custom-log.png` — meal log after a Custom quick log
- `13a–13c-coach-meal-logs.png` / `13b/c` — coach adds a comment
- `14a–14c-client-…` — client sees comment + unread badge + auto-clear after reload
- `15-future-date.png` — future-empty date renders without Today's Plan (favorites still appear, which is expected)
- `16-desktop-nav.png` — full desktop client view (centered nav, h-12, Nutrition active)
- `20–25-mobile-…` — mobile coach + client + day-plan editor + OFF picker
- `26-dark-client-nutrition.png` — dark mode client view
- `27-dark-plan-editor.png` — dark mode plan editor (clean)
- `28-dark-off-picker.png` — dark mode OFF picker (search results render)

Supporting JSON: `findings.json`, `plan-builder-state.json`, `nav-info.json`, `today-plan-text.txt`.
