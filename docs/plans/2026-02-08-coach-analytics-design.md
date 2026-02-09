# Coach Client Analytics Dashboard — Design

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** A single unified analytics page per client where the coach can view historical trends for daily check-ins, nutrition, and exercise progression over a configurable time period.

**Architecture:** Server-rendered Blade page with Chart.js visualizations driven by Alpine.js. All data computed server-side, encoded as JSON, and rendered client-side. Global time period filter (presets + custom range) applies to all sections.

**Tech Stack:** Laravel 12, Blade, Alpine.js, Tailwind CSS, Chart.js (CDN)

---

## Route

`GET /coach/clients/{client}/analytics` — named `coach.clients.analytics`

New "Analytics" link on the coach's client detail page.

## Page Layout

Global time period filter at the top: presets (7d, 14d, 30d, 90d) and custom date range. Same pattern as the nutrition page filter.

Three collapsible sections (all expanded by default):

### Section 1: Daily Check-ins

- One Chart.js **line chart per numeric/scale metric** assigned to the client. Laid out in a 2-column grid.
  - Scale metrics: Y-axis fixed to scale range (e.g. 1–5).
  - Number metrics: Y-axis auto-scales.
  - Missing days show gaps (no interpolation).
- **Table below** for boolean and text metrics. Columns: Date + one column per metric. Booleans as green check / red X. Text truncated with expand.
- Data: `DailyLog` joined with `TrackingMetric`, filtered by client's assigned metrics and date range, grouped by metric.
- Empty state: "No check-in data for this period."

### Section 2: Nutrition

Two charts side by side (stacked on mobile):

- **Calories bar chart** — daily calorie totals. Horizontal dashed line for the macro goal (shifts if goal changed mid-period). Bar color: green (within 10% of goal), yellow (10–25% off), red (>25% off). No-meal days show zero-height bar.
- **Macros stacked bar chart** — daily protein, carbs, fat in grams. Three distinct colors.

Summary stats row below:
- Average daily calories
- Adherence rate — % of days where calories within 10% of goal
- Average protein / carbs / fat per day

Data: `MealLog` aggregated by date + `MacroGoal` history mapped per date. Days with no goal set show bars but no goal line.

Empty state: "No nutrition data for this period."

### Section 3: Exercise Progression

- **Exercise dropdown** — populated with exercises the client actually logged in the period, grouped by muscle group.
- **Line chart** — top set weight per workout session for the selected exercise. X-axis: workout date. Y-axis: weight. Tooltip shows reps.
- **Progress summary** below chart:
  - Starting weight vs ending weight
  - Change (absolute + percentage)
  - Total sessions for this exercise

All exercise data pre-loaded as JSON keyed by exercise ID. Alpine.js swaps Chart.js data client-side on dropdown change — no extra server requests.

Empty state: "No workouts logged for this period."

## Data Flow

All data is fetched in a single controller method (`AnalyticsController@show`), computed server-side, and passed to the Blade view as JSON-encoded variables for Chart.js consumption. No API endpoints needed.
