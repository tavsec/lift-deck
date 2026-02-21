# Loyalty System Design

**Goal:** Implement a gamification/loyalty system where clients earn XP and redeemable points for fitness activities, progress through levels, unlock achievements, and redeem points for coach-defined rewards.

**Architecture:** Job-based async processing on a dedicated `loyalty` queue. Observers on existing models dispatch `ProcessXpEvent` jobs. Global XP events and levels managed by admin (Filament). Rewards and achievements are hybrid: global (admin) + per-coach. Spatie MediaLibrary for icons/images on S3.

**Tech Stack:** Laravel 12, Filament v5, Blade + Alpine.js, Tailwind CSS v3, Pest 4, Spatie MediaLibrary

---

## Key Design Decisions

1. **Dual currency:** Total XP (never decreases, determines level) + redeemable points (spendable on rewards)
2. **Job-based processing:** All XP/achievement evaluation runs async on a dedicated `loyalty` queue — zero latency impact on user actions
3. **Predefined event types:** Code defines a fixed set of XP triggers; admin configures amounts. Adding new types requires a code change + seeder
4. **Hybrid ownership:** XP events and levels are global (admin). Rewards and achievements can be global (admin) or per-coach
5. **Media via Spatie:** Achievements, levels, and rewards use MediaLibrary collections on S3 — no extra columns needed

---

## Data Model

### `xp_event_types` (admin-managed, global)

| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| key | string unique | Machine key: `workout_logged`, `daily_checkin`, etc. |
| name | string | Display name |
| description | text nullable | |
| xp_amount | integer | XP awarded per occurrence |
| points_amount | integer | Redeemable points awarded |
| is_active | boolean default true | |
| cooldown_hours | integer nullable | Prevent duplicate awards within window |
| timestamps | | |

### `levels` (admin-managed, global)

| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| name | string | "Bronze", "Silver", etc. |
| level_number | integer unique | Ordinal: 1, 2, 3... |
| xp_required | integer | Cumulative XP threshold |
| timestamps | | |

Uses Spatie MediaLibrary collection `icon` for level badge image.

### `xp_transactions` (ledger)

| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| user_id | FK users | The client |
| xp_event_type_id | FK xp_event_types | What triggered it |
| xp_amount | integer | XP earned |
| points_amount | integer | Points earned |
| metadata | json nullable | Context: `{"workout_log_id": 5}` |
| created_at | timestamp | |

### `user_xp_summaries` (cached totals)

| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| user_id | FK users unique | |
| total_xp | integer default 0 | Lifetime XP |
| available_points | integer default 0 | Spendable balance |
| current_level_id | FK levels nullable | |
| timestamps | | |

### `rewards` (global + per-coach)

| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| coach_id | FK users nullable | null = global |
| name | string | |
| description | text nullable | |
| points_cost | integer | |
| stock | integer nullable | null = unlimited |
| is_active | boolean default true | |
| timestamps | | |

Uses Spatie MediaLibrary collection `image` for reward preview.

### `reward_redemptions`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| user_id | FK users | Client |
| reward_id | FK rewards | |
| points_spent | integer | Snapshot at time of redemption |
| status | string | `pending`, `fulfilled`, `rejected` |
| coach_notes | text nullable | |
| timestamps | | |

### `achievements` (global + per-coach)

| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| coach_id | FK users nullable | null = global |
| name | string | |
| description | text nullable | |
| type | string | `automatic` or `manual` |
| condition_type | string nullable | For automatic: `workout_count`, `streak_days`, `xp_total`, `checkin_count` |
| condition_value | integer nullable | Threshold |
| xp_reward | integer default 0 | Bonus XP on unlock |
| points_reward | integer default 0 | Bonus points on unlock |
| is_active | boolean default true | |
| timestamps | | |

Uses Spatie MediaLibrary collection `icon` for achievement badge.

### `user_achievements` (pivot)

| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| user_id | FK users | |
| achievement_id | FK achievements | |
| awarded_by | FK users nullable | null = auto, set = coach |
| earned_at | timestamp | |
| timestamps | | |

---

## Predefined XP Event Types (Seeded)

| Key | Default XP | Default Points | Cooldown | Trigger |
|-----|-----------|----------------|----------|---------|
| `workout_logged` | 20 | 20 | none | WorkoutLog created |
| `daily_checkin` | 10 | 10 | 24h | DailyLog created |
| `meal_logged` | 5 | 5 | none | MealLog created |
| `program_completed` | 100 | 100 | none | ClientProgram status → completed |
| `streak_7_day` | 50 | 50 | 7 days | 7 consecutive daily check-ins |
| `streak_30_day` | 200 | 200 | 30 days | 30 consecutive daily check-ins |

---

## Job Architecture

### Dedicated Queue

Queue name: `loyalty`. Configured in `config/queue.php` using existing driver. Run with:

```
php artisan queue:work --queue=loyalty
```

### Processing Flow

```
Controller action (e.g., workout logged)
  └─ dispatch(ProcessXpEvent::class)->onQueue('loyalty')
       ├─ 1. Check if event type is active
       ├─ 2. Check cooldown (last xp_transaction for user + event type)
       ├─ 3. Create xp_transaction record
       ├─ 4. Atomic increment user_xp_summaries (total_xp, available_points)
       ├─ 5. dispatch(CheckLevelUp::class)->onQueue('loyalty')
       └─ 6. dispatch(EvaluateAchievements::class)->onQueue('loyalty')

CheckLevelUp job
  ├─ Compare total_xp against levels table
  ├─ Update current_level_id if changed
  └─ (future: notify client of level-up)

EvaluateAchievements job
  ├─ Query active automatic achievements not yet earned by user
  ├─ Evaluate each condition against user stats
  ├─ Award matching achievements + bonus XP/points
  └─ Create user_achievements records
```

### Reward Redemption (Synchronous)

```
Client redeems reward
  └─ RewardRedemptionController::store()
       ├─ 1. DB transaction with lock on user_xp_summaries
       ├─ 2. Validate sufficient available_points
       ├─ 3. Decrement available_points
       ├─ 4. Create reward_redemption (status: pending)
       └─ 5. dispatch(NotifyCoachOfRedemption::class)->onQueue('loyalty')
             └─ Send RewardRedeemedMail to coach
```

---

## UI Design

### Admin Panel (Filament v5)

**New resources:**
- `XpEventTypeResource` — CRUD for event types. Key immutable after create.
- `LevelResource` — CRUD for levels. Spatie media upload for icon. Sorted by level_number.
- `AchievementResource` — CRUD for global achievements (coach_id = null). Conditional fields for automatic type.
- `RewardResource` — CRUD for global rewards (coach_id = null). Spatie media upload for image.

**New dashboard widget:**
- "Loyalty Overview" stats: total XP awarded, total redemptions, active users with XP

### Coach Views (Blade, under `/coach/loyalty/`)

| Route | Purpose |
|-------|---------|
| `GET /coach/rewards` | List own + global (read-only) rewards |
| `GET /coach/rewards/create` | Create coach reward |
| `GET /coach/rewards/{reward}/edit` | Edit own reward |
| `DELETE /coach/rewards/{reward}` | Delete own reward |
| `GET /coach/achievements` | List own + global (read-only) achievements |
| `GET /coach/achievements/create` | Create coach achievement |
| `GET /coach/achievements/{achievement}/edit` | Edit own achievement |
| `DELETE /coach/achievements/{achievement}` | Delete own achievement |
| `POST /coach/clients/{client}/achievements/{achievement}/award` | Manually award achievement |
| `GET /coach/redemptions` | List pending/fulfilled redemptions |
| `PATCH /coach/redemptions/{redemption}` | Fulfill or reject |

Global items shown as read-only with "System" label. Manual award from client detail page.

### Client Views (Blade)

**New pages:**
- `GET /client/rewards` — Reward shop grid. Cards with image, name, cost, redeem button. Points balance + level badge at top.
- `GET /client/achievements` — Achievement showcase. Earned highlighted, unearned greyed. Progress bars for automatic achievements.

**Additions to existing views:**
- **Client dashboard** (`/client`): XP progress bar to next level, level badge, points balance, last 3 achievements
- **Client profile** (coach view at `/coach/clients/{id}`): Level badge, XP progress, earned achievements as badges, points balance, redemption history

### Email Notification

`RewardRedeemedMail` — sent to coach on redemption. Contains client name, reward name, points spent, link to redemptions page. Follows existing `WelcomeClientMail` pattern.

---

## Streak Calculation

Streaks are calculated on-the-fly in the `EvaluateAchievements` job by querying `daily_logs`:

```sql
SELECT DISTINCT date FROM daily_logs
WHERE client_id = ? AND date <= CURRENT_DATE
ORDER BY date DESC
```

Count consecutive days backwards from today. If streak >= 7 and `streak_7_day` not awarded within cooldown, award it. Same for 30-day.
