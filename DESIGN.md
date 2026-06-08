# LiftDeck Design System

## Philosophy: Training Instrument

LiftDeck is designed to feel like a precision training instrument — purpose-built, no decorative noise. The visual language draws from sports equipment: high-contrast volt green (#c6f24e) on deep-dark surfaces, tight border radii, and a typographic system that prioritises legibility at a glance over aesthetic flourish.

Every screen must answer the question a coach or athlete is asking *right now*. Chrome, decorative gradients, and unnecessary animations are removed in favour of data density, clear hierarchy, and fast visual scanning.

---

## Color System

### Volt Accent (theme-independent)

| Token | Value | Usage |
|-------|-------|-------|
| `--volt` | `#c6f24e` | Primary CTA background, active nav indicators, progress fills |
| `--volt-press` | `#b4e438` | Pressed/hover state on volt buttons |
| `--volt-ink` | `#14180a` | Text/icons placed *on top of* a volt background |

In Tailwind: `bg-volt`, `hover:bg-volt-press`, `text-volt-ink`

### Surface & Text (light / dark)

These tokens flip between light and dark themes automatically. Use them via CSS variables — never hard-code a specific hex for surfaces or text.

| Token | Light | Dark | Usage |
|-------|-------|------|-------|
| `--bg` | `#eceef2` | `#0b0d10` | Page background |
| `--surface` | `#ffffff` | `#16191f` | Cards, panels, modals |
| `--surface-2` | `#f3f5f7` | `#1d2027` | Input backgrounds, secondary surfaces |
| `--surface-3` | `#e9ecf0` | `#252a32` | Hover states, inline code backgrounds |
| `--border` | `rgba(18,22,31,.09)` | `rgba(255,255,255,.08)` | Default border / divider |
| `--border-strong` | `rgba(18,22,31,.16)` | `rgba(255,255,255,.16)` | Emphasized borders, focus rings |
| `--text` | `#181b22` | `#f0f2f5` | Primary body text, headings |
| `--text-2` | `#555b66` | `#a4abb6` | Secondary/supporting text |
| `--text-3` | `#8c93a0` | `#6b7280` | Placeholders, disabled text, captions |
| `--volt-text` | `#5c7a10` | `#c6f24e` | Volt-coloured inline text (readable on its background) |

In Tailwind use `bg-[var(--surface)]`, `text-[var(--text)]`, `border-[var(--border)]`.

### How Dark Mode Works

Tailwind's `darkMode: 'class'` strategy is configured. The `.dark` class on `<html>` activates all `dark:` utility variants AND the `--surface`/`--text`/etc. CSS variable overrides defined in `app.css`.

The `[data-theme="dark"]` attribute selector is also supported for any third-party or server-rendered component that sets an attribute instead of a class.

To implement:
- Blade: `<html class="{{ $darkMode ? 'dark' : '' }}">`
- Alpine: `:class="{ dark: isDark }"` on `<html>`
- Never duplicate color values — use the CSS variable tokens listed above

---

## Typography

### Font Families

| Role | Family | Tailwind class | Weights loaded |
|------|--------|----------------|----------------|
| Body (default sans) | Hanken Grotesk | `font-sans` | 400, 500, 600, 700, 800 |
| Display / headings | Space Grotesk | `font-display` | 400, 500, 600, 700 |
| Numbers / code | JetBrains Mono | `font-mono` | 400, 500, 600, 700 |

Legacy families (`DM Sans`, `Outfit`, `Poppins`) remain as fallbacks in the font stack but should not be used in new components. `font-mid` (Poppins) is kept for backward compat only.

### When to Use Each

- **Hanken Grotesk** (`font-sans`): all body copy, labels, form inputs, table cells, nav items. Default — do not set explicitly unless overriding.
- **Space Grotesk** (`font-display`): page titles (`<h1>`), section headings (`<h2>`), marketing hero lines, card stat numbers with labels. Adds just enough personality for headers without interfering with data legibility.
- **JetBrains Mono** (`font-mono`): weight values (kg, reps), numeric stats, macro gram figures, code snippets, timestamps. The fixed-width prevents layout shift as numbers update.

---

## Border Radius Scale

| Token | Value | Tailwind equivalent | Usage |
|-------|-------|---------------------|-------|
| `--r-sm` | `10px` | `rounded-[10px]` | Badges, pills, small chips |
| `--r-md` | `14px` | `rounded-[14px]` | Input fields, inline cards |
| `--r-lg` | `18px` | `rounded-[18px]` | Main content cards |
| `--r-xl` | `24px` | `rounded-[24px]` | Modals, sheets, feature cards |

For Tailwind utility classes consider adding these to `borderRadius` in `tailwind.config.js` if direct `rounded-[...]` arbitrary values become repetitive.

---

## Shadows

| Token | Value | Tailwind class | Usage |
|-------|-------|----------------|-------|
| `--shadow-card` | `0 1px 2px rgba(18,22,31,.04), 0 4px 16px rgba(18,22,31,.045)` | `shadow-[var(--shadow-card)]` | Resting cards, list items |
| `--shadow-pop` | `0 12px 32px rgba(18,22,31,.14), 0 2px 6px rgba(18,22,31,.08)` | `shadow-pop` (Tailwind token) | Dropdowns, modals, popovers, tooltips |

Dark mode values are automatically applied via CSS variable overrides — no `dark:shadow-*` needed when using the variable tokens.

---

## Status / Semantic Colors

These use OKLCH for perceptual uniformity across light and dark rendering.

| Token | Value | Usage |
|-------|-------|-------|
| `--ok` | `oklch(0.72 0.15 150)` | Success states, completed workouts, goals met |
| `--warn` | `oklch(0.74 0.15 75)` | Warnings, approaching limits, amber alerts |
| `--danger` | `oklch(0.62 0.2 25)` | Errors, over-limit macros, failed actions |
| `--info` | `oklch(0.62 0.13 250)` | Informational banners, neutral notifications |

Use via Tailwind arbitrary: `text-[var(--ok)]`, `bg-[var(--danger)]`.

---

## Macro Nutrition Colors

Fixed colors (not theme-flipped) to maintain consistent macro identity across charts and progress bars.

| Token | Value | Nutrient |
|-------|-------|----------|
| `--macro-p` | `#3b82f6` | Protein (blue) |
| `--macro-c` | `#e8b923` | Carbohydrates (amber) |
| `--macro-f` | `#e0533d` | Fat (red) |

---

## Component Patterns

### Cards

```html
<div class="bg-[var(--surface)] border border-[var(--border)] rounded-[18px] shadow-[var(--shadow-card)] p-5">
  ...
</div>
```

- Background: `bg-[var(--surface)]` (flips in dark mode automatically)
- Border: `border border-[var(--border)]`
- Radius: `rounded-[18px]` (`--r-lg`)
- Shadow: `shadow-[var(--shadow-card)]`
- Padding: `p-4` (compact) or `p-5`/`p-6` (standard)

### Buttons

**Volt primary** — main CTA, save/submit:
```html
<button class="bg-volt hover:bg-volt-press text-volt-ink font-semibold rounded-[14px] px-5 py-2.5 transition-colors">
  Save
</button>
```

**Dark secondary** — confirm, proceed without emphasis:
```html
<button class="bg-[var(--surface-2)] hover:bg-[var(--surface-3)] text-[var(--text)] font-medium rounded-[14px] px-5 py-2.5 transition-colors">
  Cancel
</button>
```

**Ghost** — tertiary / destructive:
```html
<button class="text-[var(--text-2)] hover:text-[var(--text)] hover:bg-[var(--surface-3)] rounded-[14px] px-4 py-2 transition-colors">
  Delete
</button>
```

### Pills / Badges

```html
<span class="inline-flex items-center gap-1 bg-[var(--surface-2)] text-[var(--text-2)] text-xs font-medium rounded-[10px] px-2.5 py-1">
  Label
</span>
```

Volt pill (active/selected state):
```html
<span class="inline-flex items-center bg-volt/15 text-[var(--volt-text)] text-xs font-semibold rounded-[10px] px-2.5 py-1">
  Active
</span>
```

### Form Inputs

```html
<input class="w-full bg-[var(--surface-2)] border border-[var(--border)] focus:border-volt focus:ring-1 focus:ring-volt/40 rounded-[14px] px-4 py-2.5 text-[var(--text)] placeholder:text-[var(--text-3)] transition-colors">
```

---

## Dark Mode Implementation

### Setup
```html
<!-- resources/views/layouts/coach.blade.php -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
```

Or toggle dynamically with Alpine:
```html
<html x-data="{ dark: true }" :class="{ dark: dark }">
```

### Rules
1. Never use `bg-white dark:bg-gray-900` patterns — use `bg-[var(--surface)]` instead.
2. Never use `text-gray-900 dark:text-white` — use `text-[var(--text)]`.
3. Always use the CSS variable tokens for surfaces, borders, and text.
4. Volt (`#c6f24e`) and the macro colors are constant — no dark: override needed.
5. Status colors (`--ok`, `--warn`, `--danger`, `--info`) are constant.

---

## Coach App Patterns

### Sidebar Layout

- Sidebar width: **248px** (`w-[248px]`)
- Background: `bg-[var(--surface)]`
- Border right: `border-r border-[var(--border)]`
- Top logo area: `h-16` with `px-5`

### Sidebar Navigation Item

Inactive:
```html
<a class="flex items-center gap-3 px-4 py-2.5 rounded-[10px] text-[var(--text-2)] hover:bg-[var(--surface-2)] hover:text-[var(--text)] transition-colors font-medium text-sm">
```

Active (current page):
```html
<a class="flex items-center gap-3 px-4 py-2.5 rounded-[10px] bg-volt text-volt-ink font-semibold text-sm">
```

The volt background on the active nav item is the primary brand signal in the coach interface.

### Section Labels

```html
<span class="px-4 text-[10px] font-semibold uppercase tracking-widest text-[var(--text-3)] mb-1 block">
  Clients
</span>
```

---

## Client App Patterns

### Bottom Tab Bar

- Height: **64px** (`h-16`)
- Background: `bg-[var(--surface)]`
- Top border: `border-t border-[var(--border)]`
- Six tabs; uses `safe-area-inset-bottom` via `pb-safe` or `pb-[env(safe-area-inset-bottom)]`

### Bottom Tab Item

Inactive:
```html
<button class="flex flex-col items-center justify-center gap-0.5 flex-1 text-[var(--text-3)] transition-colors">
  <!-- icon 22x22 -->
  <span class="text-[10px] font-medium">Label</span>
</button>
```

Active:
```html
<button class="flex flex-col items-center justify-center gap-0.5 flex-1 text-volt transition-colors">
  <!-- icon 22x22 filled with text-volt -->
  <span class="text-[10px] font-semibold text-[var(--volt-text)]">Label</span>
</button>
```

Note: the icon itself uses `text-volt` but the label under it uses `text-[var(--volt-text)]` — on dark backgrounds these are both the bright lime; on light the label uses the darker readable `#5c7a10`.

---

## Tailwind Quick Reference

| Need | Class |
|------|-------|
| Page background | `bg-[var(--bg)]` |
| Card / panel | `bg-[var(--surface)]` |
| Input background | `bg-[var(--surface-2)]` |
| Hover background | `hover:bg-[var(--surface-3)]` |
| Default border | `border border-[var(--border)]` |
| Strong border | `border border-[var(--border-strong)]` |
| Primary text | `text-[var(--text)]` |
| Secondary text | `text-[var(--text-2)]` |
| Muted text | `text-[var(--text-3)]` |
| Volt text on page | `text-[var(--volt-text)]` |
| Volt button bg | `bg-volt hover:bg-volt-press` |
| Text on volt button | `text-volt-ink` |
| Card shadow | `shadow-[var(--shadow-card)]` |
| Modal / pop shadow | `shadow-pop` |
| Success text | `text-[var(--ok)]` |
| Warning text | `text-[var(--warn)]` |
| Error text | `text-[var(--danger)]` |
| Info text | `text-[var(--info)]` |
| Protein color | `text-[var(--macro-p)]` / `bg-[var(--macro-p)]` |
| Carbs color | `text-[var(--macro-c)]` / `bg-[var(--macro-c)]` |
| Fat color | `text-[var(--macro-f)]` / `bg-[var(--macro-f)]` |

---

## File Locations

- CSS variables + font imports: `resources/css/app.css`
- Tailwind tokens (volt colors, font families, shadows): `tailwind.config.js`
- Coach layout: `resources/views/layouts/coach.blade.php`
- Client layout: `resources/views/layouts/client.blade.php`
