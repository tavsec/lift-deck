# Loyalty System Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Implement a gamification/loyalty system with XP, levels, redeemable points, achievements, and rewards — configurable by admins (global) and coaches (per-coach).

**Architecture:** Job-based async processing on a dedicated `loyalty` queue. Observers dispatch `ProcessXpEvent` jobs. Global XP events and levels managed by admin (Filament v5). Rewards and achievements are hybrid: global (admin) + per-coach (Blade views). Spatie MediaLibrary for icons/images on S3.

**Tech Stack:** Laravel 12, Filament v5, Blade + Alpine.js, Tailwind CSS v3, Pest 4, Spatie MediaLibrary

---

## Codebase Conventions Reference

- **Form Requests:** Array-based rules, `authorize()` checks role (see `app/Http/Requests/StoreMealRequest.php`)
- **Controller auth checks:** `if ($thing->coach_id !== auth()->id()) { abort(403); }` or FormRequest authorize
- **Routes:** Coach grouped under `coach.` prefix with `role:coach` middleware. Client under `client.` with `role:client`. (see `routes/web.php`)
- **Coach views:** `<x-layouts.coach>` wrapper, `<x-slot:title>`, flash via `session('success')`, inline SVG icons, blue theme
- **Client views:** `<x-layouts.client>` wrapper, `<x-bladewind::card>` for cards, `<x-bladewind::button>` for buttons
- **Models:** `$fillable`, `casts()` method, PHPDoc on relationships, return type hints
- **Factories:** `fake()` helper, named states (`->coach()`, `->client()`, `->inactive()`), `@extends` PHPDoc
- **Tests:** Pest 4, `beforeEach` for setup, `it('description')` syntax, `$this->actingAs()`, named routes, `expect()` assertions
- **Filament v5:** `Filament\Schemas\Schema`, decomposed Form/Infolist/Table classes, `Heroicon::` enum for icons
- **Spatie Media:** `implements HasMedia`, `use InteractsWithMedia`, `registerMediaCollections()`, `->singleFile()->useDisk(config('filesystems.default'))`
- **Soft delete pattern:** `is_active => false` instead of actual delete
- **Pint:** Run `vendor/bin/pint --dirty --format agent` before committing

---

### Task 1: Migrations — Create all loyalty system tables

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_xp_event_types_table.php`
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_levels_table.php`
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_xp_transactions_table.php`
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_user_xp_summaries_table.php`
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_rewards_table.php`
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_reward_redemptions_table.php`
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_achievements_table.php`
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_user_achievements_table.php`

**Step 1: Create migrations using artisan**

```bash
php artisan make:migration create_xp_event_types_table --no-interaction
php artisan make:migration create_levels_table --no-interaction
php artisan make:migration create_xp_transactions_table --no-interaction
php artisan make:migration create_user_xp_summaries_table --no-interaction
php artisan make:migration create_rewards_table --no-interaction
php artisan make:migration create_reward_redemptions_table --no-interaction
php artisan make:migration create_achievements_table --no-interaction
php artisan make:migration create_user_achievements_table --no-interaction
```

**Step 2: Write each migration**

`xp_event_types`:
```php
public function up(): void
{
    Schema::create('xp_event_types', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();
        $table->string('name');
        $table->text('description')->nullable();
        $table->integer('xp_amount');
        $table->integer('points_amount');
        $table->boolean('is_active')->default(true);
        $table->integer('cooldown_hours')->nullable();
        $table->timestamps();
    });
}
```

`levels`:
```php
public function up(): void
{
    Schema::create('levels', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('level_number')->unique();
        $table->integer('xp_required');
        $table->timestamps();
    });
}
```

`xp_transactions`:
```php
public function up(): void
{
    Schema::create('xp_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('xp_event_type_id')->constrained()->cascadeOnDelete();
        $table->integer('xp_amount');
        $table->integer('points_amount');
        $table->json('metadata')->nullable();
        $table->timestamp('created_at');

        $table->index(['user_id', 'xp_event_type_id', 'created_at']);
    });
}
```

`user_xp_summaries`:
```php
public function up(): void
{
    Schema::create('user_xp_summaries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
        $table->integer('total_xp')->default(0);
        $table->integer('available_points')->default(0);
        $table->foreignId('current_level_id')->nullable()->constrained('levels')->nullOnDelete();
        $table->timestamps();
    });
}
```

`rewards`:
```php
public function up(): void
{
    Schema::create('rewards', function (Blueprint $table) {
        $table->id();
        $table->foreignId('coach_id')->nullable()->constrained('users')->cascadeOnDelete();
        $table->string('name');
        $table->text('description')->nullable();
        $table->integer('points_cost');
        $table->integer('stock')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();

        $table->index('coach_id');
    });
}
```

`reward_redemptions`:
```php
public function up(): void
{
    Schema::create('reward_redemptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('reward_id')->constrained()->cascadeOnDelete();
        $table->integer('points_spent');
        $table->string('status')->default('pending');
        $table->text('coach_notes')->nullable();
        $table->timestamps();

        $table->index(['user_id', 'status']);
    });
}
```

`achievements`:
```php
public function up(): void
{
    Schema::create('achievements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('coach_id')->nullable()->constrained('users')->cascadeOnDelete();
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('type');
        $table->string('condition_type')->nullable();
        $table->integer('condition_value')->nullable();
        $table->integer('xp_reward')->default(0);
        $table->integer('points_reward')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();

        $table->index('coach_id');
    });
}
```

`user_achievements`:
```php
public function up(): void
{
    Schema::create('user_achievements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
        $table->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('earned_at');
        $table->timestamps();

        $table->unique(['user_id', 'achievement_id']);
    });
}
```

**Step 3: Run migrations**

```bash
php artisan migrate --no-interaction
```
Expected: All 8 tables created successfully.

**Step 4: Run pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/migrations/
git commit -m "feat: add loyalty system migrations (xp, levels, rewards, achievements)"
```

---

### Task 2: Models + Factories — XpEventType, Level, XpTransaction, UserXpSummary

**Files:**
- Create: `app/Models/XpEventType.php`
- Create: `app/Models/Level.php`
- Create: `app/Models/XpTransaction.php`
- Create: `app/Models/UserXpSummary.php`
- Create: `database/factories/XpEventTypeFactory.php`
- Create: `database/factories/LevelFactory.php`
- Create: `database/factories/XpTransactionFactory.php`
- Create: `database/factories/UserXpSummaryFactory.php`

**Step 1: Create models with factories**

```bash
php artisan make:model XpEventType -f --no-interaction
php artisan make:model Level -f --no-interaction
php artisan make:model XpTransaction -f --no-interaction
php artisan make:model UserXpSummary -f --no-interaction
```

**Step 2: Write XpEventType model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XpEventType extends Model
{
    /** @use HasFactory<\Database\Factories\XpEventTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'xp_amount',
        'points_amount',
        'is_active',
        'cooldown_hours',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
```

**Step 3: Write Level model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Level extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\LevelFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'level_number',
        'xp_required',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
            ->useDisk(config('filesystems.default'));
    }
}
```

**Step 4: Write XpTransaction model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class XpTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\XpTransactionFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'xp_event_type_id',
        'xp_amount',
        'points_amount',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user who earned this XP.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event type that triggered this transaction.
     */
    public function xpEventType(): BelongsTo
    {
        return $this->belongsTo(XpEventType::class);
    }
}
```

**Step 5: Write UserXpSummary model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserXpSummary extends Model
{
    /** @use HasFactory<\Database\Factories\UserXpSummaryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_xp',
        'available_points',
        'current_level_id',
    ];

    /**
     * Get the user this summary belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the current level.
     */
    public function currentLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'current_level_id');
    }
}
```

**Step 6: Write factories**

`XpEventTypeFactory`:
```php
public function definition(): array
{
    return [
        'key' => fake()->unique()->slug(2),
        'name' => fake()->words(3, true),
        'description' => null,
        'xp_amount' => fake()->numberBetween(5, 50),
        'points_amount' => fake()->numberBetween(5, 50),
        'is_active' => true,
        'cooldown_hours' => null,
    ];
}
```

`LevelFactory`:
```php
public function definition(): array
{
    return [
        'name' => fake()->word(),
        'level_number' => fake()->unique()->numberBetween(1, 100),
        'xp_required' => fake()->numberBetween(100, 10000),
    ];
}
```

`XpTransactionFactory`:
```php
public function definition(): array
{
    return [
        'user_id' => User::factory()->client(),
        'xp_event_type_id' => XpEventType::factory(),
        'xp_amount' => fake()->numberBetween(5, 50),
        'points_amount' => fake()->numberBetween(5, 50),
        'metadata' => null,
        'created_at' => now(),
    ];
}
```

`UserXpSummaryFactory`:
```php
public function definition(): array
{
    return [
        'user_id' => User::factory()->client(),
        'total_xp' => 0,
        'available_points' => 0,
        'current_level_id' => null,
    ];
}
```

**Step 7: Add relationships to User model**

Add to `app/Models/User.php`:
```php
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Get the XP summary for this user.
 */
public function xpSummary(): HasOne
{
    return $this->hasOne(UserXpSummary::class);
}

/**
 * Get XP transactions for this user.
 */
public function xpTransactions(): HasMany
{
    return $this->hasMany(XpTransaction::class);
}

/**
 * Get achievements earned by this user.
 */
public function achievements(): BelongsToMany
{
    return $this->belongsToMany(Achievement::class, 'user_achievements')
        ->withPivot(['awarded_by', 'earned_at'])
        ->withTimestamps();
}

/**
 * Get reward redemptions for this user.
 */
public function rewardRedemptions(): HasMany
{
    return $this->hasMany(RewardRedemption::class);
}
```

**Step 8: Run pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Models/ database/factories/
git commit -m "feat: add XP/level core models and factories"
```

---

### Task 3: Models + Factories — Reward, RewardRedemption, Achievement, UserAchievement

**Files:**
- Create: `app/Models/Reward.php`
- Create: `app/Models/RewardRedemption.php`
- Create: `app/Models/Achievement.php`
- Create: `database/factories/RewardFactory.php`
- Create: `database/factories/RewardRedemptionFactory.php`
- Create: `database/factories/AchievementFactory.php`

**Step 1: Create models with factories**

```bash
php artisan make:model Reward -f --no-interaction
php artisan make:model RewardRedemption -f --no-interaction
php artisan make:model Achievement -f --no-interaction
```

**Step 2: Write Reward model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Reward extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\RewardFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'points_cost',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the coach who created this reward (null = global).
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get redemptions for this reward.
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Check if this reward is a global (system) reward.
     */
    public function isGlobal(): bool
    {
        return $this->coach_id === null;
    }

    /**
     * Check if this reward has available stock.
     */
    public function hasStock(): bool
    {
        if ($this->stock === null) {
            return true;
        }

        return $this->stock > 0;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk(config('filesystems.default'));
    }
}
```

**Step 3: Write RewardRedemption model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardRedemption extends Model
{
    /** @use HasFactory<\Database\Factories\RewardRedemptionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_id',
        'points_spent',
        'status',
        'coach_notes',
    ];

    /**
     * Get the client who redeemed.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reward that was redeemed.
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}
```

**Step 4: Write Achievement model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Achievement extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\AchievementFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'type',
        'condition_type',
        'condition_value',
        'xp_reward',
        'points_reward',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the coach who created this achievement (null = global).
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get users who have earned this achievement.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['awarded_by', 'earned_at'])
            ->withTimestamps();
    }

    /**
     * Check if this is an automatic achievement.
     */
    public function isAutomatic(): bool
    {
        return $this->type === 'automatic';
    }

    /**
     * Check if this is a global (system) achievement.
     */
    public function isGlobal(): bool
    {
        return $this->coach_id === null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
            ->useDisk(config('filesystems.default'));
    }
}
```

**Step 5: Write factories**

`RewardFactory`:
```php
public function definition(): array
{
    return [
        'coach_id' => User::factory()->coach(),
        'name' => fake()->words(3, true),
        'description' => fake()->sentence(),
        'points_cost' => fake()->numberBetween(50, 500),
        'stock' => null,
        'is_active' => true,
    ];
}

public function global(): static
{
    return $this->state(fn () => ['coach_id' => null]);
}

public function inactive(): static
{
    return $this->state(fn () => ['is_active' => false]);
}
```

`RewardRedemptionFactory`:
```php
public function definition(): array
{
    return [
        'user_id' => User::factory()->client(),
        'reward_id' => Reward::factory(),
        'points_spent' => fake()->numberBetween(50, 500),
        'status' => 'pending',
        'coach_notes' => null,
    ];
}

public function fulfilled(): static
{
    return $this->state(fn () => ['status' => 'fulfilled']);
}

public function rejected(): static
{
    return $this->state(fn () => ['status' => 'rejected']);
}
```

`AchievementFactory`:
```php
public function definition(): array
{
    return [
        'coach_id' => User::factory()->coach(),
        'name' => fake()->words(3, true),
        'description' => fake()->sentence(),
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => fake()->numberBetween(5, 50),
        'xp_reward' => fake()->numberBetween(10, 100),
        'points_reward' => fake()->numberBetween(10, 100),
        'is_active' => true,
    ];
}

public function manual(): static
{
    return $this->state(fn () => [
        'type' => 'manual',
        'condition_type' => null,
        'condition_value' => null,
    ]);
}

public function global(): static
{
    return $this->state(fn () => ['coach_id' => null]);
}
```

**Step 6: Run pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Models/ database/factories/
git commit -m "feat: add reward, redemption, and achievement models with factories"
```

---

### Task 4: Seeder — XP event types and default levels

**Files:**
- Create: `database/seeders/XpEventTypeSeeder.php`
- Create: `database/seeders/LevelSeeder.php`

**Step 1: Create seeders**

```bash
php artisan make:seeder XpEventTypeSeeder --no-interaction
php artisan make:seeder LevelSeeder --no-interaction
```

**Step 2: Write XpEventTypeSeeder**

```php
<?php

namespace Database\Seeders;

use App\Models\XpEventType;
use Illuminate\Database\Seeder;

class XpEventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $eventTypes = [
            ['key' => 'workout_logged', 'name' => 'Workout Logged', 'description' => 'Awarded when a workout session is completed.', 'xp_amount' => 20, 'points_amount' => 20, 'cooldown_hours' => null],
            ['key' => 'daily_checkin', 'name' => 'Daily Check-in', 'description' => 'Awarded for logging daily metrics.', 'xp_amount' => 10, 'points_amount' => 10, 'cooldown_hours' => 24],
            ['key' => 'meal_logged', 'name' => 'Meal Logged', 'description' => 'Awarded for logging a meal.', 'xp_amount' => 5, 'points_amount' => 5, 'cooldown_hours' => null],
            ['key' => 'program_completed', 'name' => 'Program Completed', 'description' => 'Awarded when a training program is completed.', 'xp_amount' => 100, 'points_amount' => 100, 'cooldown_hours' => null],
            ['key' => 'streak_7_day', 'name' => '7-Day Streak', 'description' => 'Awarded for 7 consecutive days of daily check-ins.', 'xp_amount' => 50, 'points_amount' => 50, 'cooldown_hours' => 168],
            ['key' => 'streak_30_day', 'name' => '30-Day Streak', 'description' => 'Awarded for 30 consecutive days of daily check-ins.', 'xp_amount' => 200, 'points_amount' => 200, 'cooldown_hours' => 720],
        ];

        foreach ($eventTypes as $eventType) {
            XpEventType::updateOrCreate(
                ['key' => $eventType['key']],
                $eventType,
            );
        }
    }
}
```

**Step 3: Write LevelSeeder**

```php
<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['level_number' => 1, 'name' => 'Beginner', 'xp_required' => 0],
            ['level_number' => 2, 'name' => 'Bronze', 'xp_required' => 100],
            ['level_number' => 3, 'name' => 'Silver', 'xp_required' => 500],
            ['level_number' => 4, 'name' => 'Gold', 'xp_required' => 1500],
            ['level_number' => 5, 'name' => 'Platinum', 'xp_required' => 5000],
            ['level_number' => 6, 'name' => 'Diamond', 'xp_required' => 10000],
        ];

        foreach ($levels as $level) {
            Level::updateOrCreate(
                ['level_number' => $level['level_number']],
                $level,
            );
        }
    }
}
```

**Step 4: Run seeders**

```bash
php artisan db:seed --class=XpEventTypeSeeder --no-interaction
php artisan db:seed --class=LevelSeeder --no-interaction
```

**Step 5: Run pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/seeders/
git commit -m "feat: add XP event type and level seeders"
```

---

### Task 5: Jobs — ProcessXpEvent, CheckLevelUp, EvaluateAchievements

**Files:**
- Create: `app/Jobs/ProcessXpEvent.php`
- Create: `app/Jobs/CheckLevelUp.php`
- Create: `app/Jobs/EvaluateAchievements.php`
- Test: `tests/Feature/Jobs/ProcessXpEventTest.php`
- Test: `tests/Feature/Jobs/CheckLevelUpTest.php`
- Test: `tests/Feature/Jobs/EvaluateAchievementsTest.php`

**Step 1: Create job classes**

```bash
php artisan make:job ProcessXpEvent --no-interaction
php artisan make:job CheckLevelUp --no-interaction
php artisan make:job EvaluateAchievements --no-interaction
```

**Step 2: Write the tests first**

Create `tests/Feature/Jobs/ProcessXpEventTest.php`:
```php
<?php

use App\Jobs\CheckLevelUp;
use App\Jobs\EvaluateAchievements;
use App\Jobs\ProcessXpEvent;
use App\Models\User;
use App\Models\UserXpSummary;
use App\Models\XpEventType;
use App\Models\XpTransaction;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    $this->eventType = XpEventType::factory()->create([
        'key' => 'workout_logged',
        'xp_amount' => 20,
        'points_amount' => 20,
        'is_active' => true,
        'cooldown_hours' => null,
    ]);
});

it('creates an xp transaction and updates summary', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    $this->assertDatabaseHas('xp_transactions', [
        'user_id' => $this->client->id,
        'xp_event_type_id' => $this->eventType->id,
        'xp_amount' => 20,
        'points_amount' => 20,
    ]);

    $summary = UserXpSummary::where('user_id', $this->client->id)->first();
    expect($summary->total_xp)->toBe(20);
    expect($summary->available_points)->toBe(20);
});

it('dispatches CheckLevelUp and EvaluateAchievements jobs', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    Queue::assertPushed(CheckLevelUp::class);
    Queue::assertPushed(EvaluateAchievements::class);
});

it('skips inactive event types', function () {
    $this->eventType->update(['is_active' => false]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    expect(XpTransaction::count())->toBe(0);
});

it('respects cooldown period', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    $this->eventType->update(['cooldown_hours' => 24]);

    XpTransaction::create([
        'user_id' => $this->client->id,
        'xp_event_type_id' => $this->eventType->id,
        'xp_amount' => 20,
        'points_amount' => 20,
        'created_at' => now()->subHours(1),
    ]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    expect(XpTransaction::count())->toBe(1);
});

it('allows event after cooldown expires', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    $this->eventType->update(['cooldown_hours' => 24]);

    XpTransaction::create([
        'user_id' => $this->client->id,
        'xp_event_type_id' => $this->eventType->id,
        'xp_amount' => 20,
        'points_amount' => 20,
        'created_at' => now()->subHours(25),
    ]);

    (new ProcessXpEvent($this->client->id, 'workout_logged'))->handle();

    expect(XpTransaction::count())->toBe(2);
});

it('stores metadata when provided', function () {
    Queue::fake([CheckLevelUp::class, EvaluateAchievements::class]);

    (new ProcessXpEvent($this->client->id, 'workout_logged', ['workout_log_id' => 5]))->handle();

    $transaction = XpTransaction::first();
    expect($transaction->metadata)->toBe(['workout_log_id' => 5]);
});
```

Create `tests/Feature/Jobs/CheckLevelUpTest.php`:
```php
<?php

use App\Jobs\CheckLevelUp;
use App\Models\Level;
use App\Models\User;
use App\Models\UserXpSummary;

beforeEach(function () {
    $this->client = User::factory()->client()->create();

    Level::factory()->create(['level_number' => 1, 'name' => 'Beginner', 'xp_required' => 0]);
    Level::factory()->create(['level_number' => 2, 'name' => 'Bronze', 'xp_required' => 100]);
    Level::factory()->create(['level_number' => 3, 'name' => 'Silver', 'xp_required' => 500]);
});

it('assigns correct level based on total xp', function () {
    $summary = UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 150,
        'available_points' => 150,
    ]);

    (new CheckLevelUp($this->client->id))->handle();

    $summary->refresh();
    expect($summary->currentLevel->name)->toBe('Bronze');
});

it('upgrades level when xp crosses threshold', function () {
    $beginner = Level::where('level_number', 1)->first();
    $summary = UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 500,
        'available_points' => 500,
        'current_level_id' => $beginner->id,
    ]);

    (new CheckLevelUp($this->client->id))->handle();

    $summary->refresh();
    expect($summary->currentLevel->name)->toBe('Silver');
});

it('does not downgrade level', function () {
    $bronze = Level::where('level_number', 2)->first();
    $summary = UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 50,
        'available_points' => 50,
        'current_level_id' => $bronze->id,
    ]);

    (new CheckLevelUp($this->client->id))->handle();

    $summary->refresh();
    expect($summary->currentLevel->name)->toBe('Bronze');
});
```

Create `tests/Feature/Jobs/EvaluateAchievementsTest.php`:
```php
<?php

use App\Jobs\EvaluateAchievements;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserXpSummary;
use App\Models\WorkoutLog;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    UserXpSummary::create([
        'user_id' => $this->client->id,
        'total_xp' => 0,
        'available_points' => 0,
    ]);
});

it('awards workout count achievement when threshold met', function () {
    $achievement = Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 3,
        'xp_reward' => 50,
        'points_reward' => 50,
    ]);

    WorkoutLog::factory()->count(3)->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();

    expect($this->client->achievements)->toHaveCount(1);
    expect($this->client->achievements->first()->id)->toBe($achievement->id);
});

it('does not award achievement when threshold not met', function () {
    Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 10,
    ]);

    WorkoutLog::factory()->count(3)->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();

    expect($this->client->achievements)->toHaveCount(0);
});

it('does not double-award achievements', function () {
    $achievement = Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
    ]);

    WorkoutLog::factory()->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();
    (new EvaluateAchievements($this->client->id))->handle();

    expect($this->client->achievements)->toHaveCount(1);
});

it('awards xp and points bonus for achievement', function () {
    Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
        'xp_reward' => 50,
        'points_reward' => 50,
    ]);

    WorkoutLog::factory()->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();

    $summary = $this->client->xpSummary->fresh();
    expect($summary->total_xp)->toBe(50);
    expect($summary->available_points)->toBe(50);
});

it('only evaluates achievements visible to user (global + coach)', function () {
    $otherCoach = User::factory()->coach()->create();

    Achievement::factory()->global()->create([
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
    ]);

    Achievement::factory()->create([
        'coach_id' => $this->coach->id,
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
    ]);

    Achievement::factory()->create([
        'coach_id' => $otherCoach->id,
        'type' => 'automatic',
        'condition_type' => 'workout_count',
        'condition_value' => 1,
    ]);

    WorkoutLog::factory()->create(['client_id' => $this->client->id]);

    (new EvaluateAchievements($this->client->id))->handle();

    expect($this->client->achievements)->toHaveCount(2);
});
```

**Step 3: Run tests to verify they fail**

```bash
php artisan test --compact --filter=ProcessXpEventTest
php artisan test --compact --filter=CheckLevelUpTest
php artisan test --compact --filter=EvaluateAchievementsTest
```
Expected: FAIL — jobs don't have implementation yet.

**Step 4: Implement ProcessXpEvent job**

```php
<?php

namespace App\Jobs;

use App\Models\UserXpSummary;
use App\Models\XpEventType;
use App\Models\XpTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessXpEvent implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        public int $userId,
        public string $eventKey,
        public ?array $metadata = null,
    ) {
        $this->onQueue('loyalty');
    }

    public function handle(): void
    {
        $eventType = XpEventType::where('key', $this->eventKey)->first();

        if (! $eventType || ! $eventType->is_active) {
            return;
        }

        if ($this->isOnCooldown($eventType)) {
            return;
        }

        XpTransaction::create([
            'user_id' => $this->userId,
            'xp_event_type_id' => $eventType->id,
            'xp_amount' => $eventType->xp_amount,
            'points_amount' => $eventType->points_amount,
            'metadata' => $this->metadata,
            'created_at' => now(),
        ]);

        $summary = UserXpSummary::firstOrCreate(
            ['user_id' => $this->userId],
            ['total_xp' => 0, 'available_points' => 0],
        );

        $summary->increment('total_xp', $eventType->xp_amount);
        $summary->increment('available_points', $eventType->points_amount);

        CheckLevelUp::dispatch($this->userId)->onQueue('loyalty');
        EvaluateAchievements::dispatch($this->userId)->onQueue('loyalty');
    }

    private function isOnCooldown(XpEventType $eventType): bool
    {
        if (! $eventType->cooldown_hours) {
            return false;
        }

        return XpTransaction::where('user_id', $this->userId)
            ->where('xp_event_type_id', $eventType->id)
            ->where('created_at', '>=', now()->subHours($eventType->cooldown_hours))
            ->exists();
    }
}
```

**Step 5: Implement CheckLevelUp job**

```php
<?php

namespace App\Jobs;

use App\Models\Level;
use App\Models\UserXpSummary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckLevelUp implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
    ) {
        $this->onQueue('loyalty');
    }

    public function handle(): void
    {
        $summary = UserXpSummary::where('user_id', $this->userId)->first();

        if (! $summary) {
            return;
        }

        $newLevel = Level::where('xp_required', '<=', $summary->total_xp)
            ->orderByDesc('xp_required')
            ->first();

        if (! $newLevel) {
            return;
        }

        if ($summary->current_level_id === $newLevel->id) {
            return;
        }

        // Only upgrade, never downgrade
        if ($summary->currentLevel && $summary->currentLevel->level_number >= $newLevel->level_number) {
            return;
        }

        $summary->update(['current_level_id' => $newLevel->id]);
    }
}
```

**Step 6: Implement EvaluateAchievements job**

```php
<?php

namespace App\Jobs;

use App\Models\Achievement;
use App\Models\DailyLog;
use App\Models\User;
use App\Models\UserXpSummary;
use App\Models\WorkoutLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EvaluateAchievements implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
    ) {
        $this->onQueue('loyalty');
    }

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            return;
        }

        $earnedIds = $user->achievements()->pluck('achievements.id');

        $achievements = Achievement::where('type', 'automatic')
            ->where('is_active', true)
            ->whereNotIn('id', $earnedIds)
            ->where(function ($query) use ($user) {
                $query->whereNull('coach_id')
                    ->orWhere('coach_id', $user->coach_id);
            })
            ->get();

        foreach ($achievements as $achievement) {
            if ($this->isConditionMet($user, $achievement)) {
                $user->achievements()->attach($achievement->id, [
                    'earned_at' => now(),
                ]);

                if ($achievement->xp_reward > 0 || $achievement->points_reward > 0) {
                    $summary = UserXpSummary::firstOrCreate(
                        ['user_id' => $user->id],
                        ['total_xp' => 0, 'available_points' => 0],
                    );

                    if ($achievement->xp_reward > 0) {
                        $summary->increment('total_xp', $achievement->xp_reward);
                    }
                    if ($achievement->points_reward > 0) {
                        $summary->increment('available_points', $achievement->points_reward);
                    }
                }
            }
        }
    }

    private function isConditionMet(User $user, Achievement $achievement): bool
    {
        return match ($achievement->condition_type) {
            'workout_count' => WorkoutLog::where('client_id', $user->id)->count() >= $achievement->condition_value,
            'checkin_count' => DailyLog::where('client_id', $user->id)->distinct('date')->count('date') >= $achievement->condition_value,
            'xp_total' => ($user->xpSummary?->total_xp ?? 0) >= $achievement->condition_value,
            'streak_days' => $this->calculateCurrentStreak($user) >= $achievement->condition_value,
            default => false,
        };
    }

    private function calculateCurrentStreak(User $user): int
    {
        $dates = DailyLog::where('client_id', $user->id)
            ->where('date', '<=', now()->toDateString())
            ->distinct('date')
            ->orderByDesc('date')
            ->pluck('date');

        $streak = 0;
        $expectedDate = now()->startOfDay();

        foreach ($dates as $date) {
            if ($date->startOfDay()->equalTo($expectedDate)) {
                $streak++;
                $expectedDate = $expectedDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
```

**Step 7: Run tests**

```bash
php artisan test --compact --filter=ProcessXpEventTest
php artisan test --compact --filter=CheckLevelUpTest
php artisan test --compact --filter=EvaluateAchievementsTest
```
Expected: All PASS.

**Step 8: Run pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Jobs/ tests/Feature/Jobs/
git commit -m "feat: add loyalty job pipeline (ProcessXpEvent, CheckLevelUp, EvaluateAchievements)"
```

---

### Task 6: Job — NotifyCoachOfRedemption + RewardRedeemedMail

**Files:**
- Create: `app/Jobs/NotifyCoachOfRedemption.php`
- Create: `app/Mail/RewardRedeemedMail.php`
- Create: `resources/views/mail/reward-redeemed.blade.php`
- Test: `tests/Feature/Jobs/NotifyCoachOfRedemptionTest.php`

**Step 1: Create files**

```bash
php artisan make:job NotifyCoachOfRedemption --no-interaction
php artisan make:mail RewardRedeemedMail --no-interaction
```

**Step 2: Write test**

Create `tests/Feature/Jobs/NotifyCoachOfRedemptionTest.php`:
```php
<?php

use App\Jobs\NotifyCoachOfRedemption;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

it('sends reward redeemed email to coach', function () {
    Mail::fake();

    $coach = User::factory()->coach()->create();
    $client = User::factory()->client()->create(['coach_id' => $coach->id]);
    $reward = Reward::factory()->create(['coach_id' => $coach->id]);
    $redemption = RewardRedemption::factory()->create([
        'user_id' => $client->id,
        'reward_id' => $reward->id,
        'points_spent' => 100,
    ]);

    (new NotifyCoachOfRedemption($redemption->id))->handle();

    Mail::assertSent(\App\Mail\RewardRedeemedMail::class, function ($mail) use ($coach) {
        return $mail->hasTo($coach->email);
    });
});
```

**Step 3: Run test to verify it fails**

```bash
php artisan test --compact --filter=NotifyCoachOfRedemptionTest
```

**Step 4: Implement RewardRedeemedMail**

```php
<?php

namespace App\Mail;

use App\Models\RewardRedemption;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RewardRedeemedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RewardRedemption $redemption,
    ) {}

    public function envelope(): Envelope
    {
        $clientName = $this->redemption->user->name;

        return new Envelope(
            subject: "{$clientName} redeemed a reward",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.reward-redeemed',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
```

**Step 5: Create email view**

`resources/views/mail/reward-redeemed.blade.php`:
```blade
<x-mail::message>
# Reward Redeemed

**{{ $redemption->user->name }}** has redeemed the reward **"{{ $redemption->reward->name }}"** for **{{ $redemption->points_spent }} points**.

<x-mail::button :url="route('coach.redemptions.index')">
View Redemptions
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
```

**Step 6: Implement NotifyCoachOfRedemption job**

```php
<?php

namespace App\Jobs;

use App\Mail\RewardRedeemedMail;
use App\Models\RewardRedemption;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NotifyCoachOfRedemption implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $redemptionId,
    ) {
        $this->onQueue('loyalty');
    }

    public function handle(): void
    {
        $redemption = RewardRedemption::with(['user.coach', 'reward'])->find($this->redemptionId);

        if (! $redemption) {
            return;
        }

        $coach = $redemption->user->coach;

        if (! $coach) {
            return;
        }

        Mail::to($coach->email)->send(new RewardRedeemedMail($redemption));
    }
}
```

**Step 7: Run test**

```bash
php artisan test --compact --filter=NotifyCoachOfRedemptionTest
```
Expected: PASS.

**Step 8: Run pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Jobs/ app/Mail/ resources/views/mail/ tests/Feature/Jobs/
git commit -m "feat: add reward redemption notification job and mailable"
```

---

### Task 7: Dispatch XP events from existing controllers

**Files:**
- Modify: `app/Http/Controllers/Client/LogController.php` (workout logged)
- Modify: `app/Http/Controllers/Client/CheckInController.php` (daily check-in + streaks)
- Modify: `app/Http/Controllers/Client/NutritionController.php` (meal logged)
- Modify: `app/Http/Controllers/Coach/ProgramController.php` (program completed — when coach marks complete, or wherever status changes)
- Test: `tests/Feature/Loyalty/XpDispatchTest.php`

**Step 1: Write test**

Create `tests/Feature/Loyalty/XpDispatchTest.php`:
```php
<?php

use App\Jobs\ProcessXpEvent;
use App\Models\ClientProgram;
use App\Models\DailyLog;
use App\Models\Meal;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\TrackingMetric;
use App\Models\User;
use App\Models\ClientTrackingMetric;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake([ProcessXpEvent::class]);
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
});

it('dispatches XP event when workout is logged', function () {
    $program = Program::factory()->create(['coach_id' => $this->coach->id]);
    $workout = ProgramWorkout::factory()->create(['program_id' => $program->id]);
    $clientProgram = ClientProgram::factory()->create([
        'client_id' => $this->client->id,
        'program_id' => $program->id,
        'status' => 'active',
    ]);

    $this->actingAs($this->client)
        ->post(route('client.log.store'), [
            'program_workout_id' => $workout->id,
            'exercises' => [],
        ]);

    Queue::assertPushed(ProcessXpEvent::class, function ($job) {
        return $job->eventKey === 'workout_logged' && $job->userId === $this->client->id;
    });
});

it('dispatches XP event when daily check-in is submitted', function () {
    $metric = TrackingMetric::factory()->create(['coach_id' => $this->coach->id, 'type' => 'number']);
    ClientTrackingMetric::create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
        'order' => 1,
    ]);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'metrics' => [
                $metric->id => '75',
            ],
        ]);

    Queue::assertPushed(ProcessXpEvent::class, function ($job) {
        return $job->eventKey === 'daily_checkin';
    });
});

it('dispatches XP event when meal is logged', function () {
    $meal = Meal::factory()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->client)
        ->post(route('client.nutrition.store'), [
            'meal_id' => $meal->id,
            'date' => now()->toDateString(),
            'meal_type' => 'lunch',
        ]);

    Queue::assertPushed(ProcessXpEvent::class, function ($job) {
        return $job->eventKey === 'meal_logged';
    });
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact --filter=XpDispatchTest
```

**Step 3: Add dispatch calls to controllers**

In each controller's `store` method, add after the successful creation:

`app/Http/Controllers/Client/LogController.php` — after `WorkoutLog::create(...)`:
```php
use App\Jobs\ProcessXpEvent;

ProcessXpEvent::dispatch(auth()->id(), 'workout_logged', ['workout_log_id' => $workoutLog->id]);
```

`app/Http/Controllers/Client/CheckInController.php` — after saving check-in data:
```php
use App\Jobs\ProcessXpEvent;

ProcessXpEvent::dispatch(auth()->id(), 'daily_checkin');
```

Also dispatch streak checks — add after the daily_checkin dispatch:
```php
ProcessXpEvent::dispatch(auth()->id(), 'streak_7_day');
ProcessXpEvent::dispatch(auth()->id(), 'streak_30_day');
```

`app/Http/Controllers/Client/NutritionController.php` — after `MealLog::create(...)`:
```php
use App\Jobs\ProcessXpEvent;

ProcessXpEvent::dispatch(auth()->id(), 'meal_logged', ['meal_log_id' => $mealLog->id]);
```

For program completion, find where `ClientProgram` status is updated to `completed` and add:
```php
use App\Jobs\ProcessXpEvent;

ProcessXpEvent::dispatch($clientProgram->client_id, 'program_completed', ['client_program_id' => $clientProgram->id]);
```

**Step 4: Run tests**

```bash
php artisan test --compact --filter=XpDispatchTest
```
Expected: PASS. Note: some tests may need adjustment based on exact controller request formats — read each controller first and adjust the test payloads.

**Step 5: Run pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/ tests/Feature/Loyalty/
git commit -m "feat: dispatch XP events from workout, check-in, nutrition, and program controllers"
```

---

### Task 8: Filament Resources — XpEventType, Level, Achievement, Reward + Widget

**Files:**
- Create: Filament resource files for each model (follow existing UserResource pattern)
- Create: `app/Filament/Widgets/LoyaltyOverview.php`
- Modify: `app/Providers/Filament/AdminPanelProvider.php`

**Step 1: Create Filament resources**

```bash
php artisan make:filament-resource XpEventType --no-interaction
php artisan make:filament-resource Level --no-interaction
php artisan make:filament-resource Achievement --no-interaction
php artisan make:filament-resource Reward --no-interaction
```

**Step 2: Configure each resource**

Follow the existing `UserResource` decomposed pattern. For each resource:
- Main resource class sets `$model`, `$navigationIcon`, delegates to Schema/Table classes
- Form schema class defines form fields
- Table class defines columns, filters, actions

**XpEventTypeResource** key form fields:
```php
TextInput::make('key')->required()->disabledOn('edit'),
TextInput::make('name')->required(),
Textarea::make('description'),
TextInput::make('xp_amount')->numeric()->required(),
TextInput::make('points_amount')->numeric()->required(),
TextInput::make('cooldown_hours')->numeric()->nullable(),
Toggle::make('is_active')->default(true),
```

**LevelResource** key form fields:
```php
TextInput::make('name')->required(),
TextInput::make('level_number')->numeric()->required()->unique(ignoreRecord: true),
TextInput::make('xp_required')->numeric()->required(),
SpatieMediaLibraryFileUpload::make('icon')->collection('icon'),
```

**AchievementResource** — scope to global only (`whereNull('coach_id')` in `getEloquentQuery()`):
```php
TextInput::make('name')->required(),
Textarea::make('description'),
Select::make('type')->options(['automatic' => 'Automatic', 'manual' => 'Manual'])->required(),
Select::make('condition_type')
    ->options([
        'workout_count' => 'Workout Count',
        'checkin_count' => 'Check-in Count',
        'xp_total' => 'Total XP',
        'streak_days' => 'Streak Days',
    ])
    ->visible(fn (callable $get) => $get('type') === 'automatic'),
TextInput::make('condition_value')->numeric()
    ->visible(fn (callable $get) => $get('type') === 'automatic'),
TextInput::make('xp_reward')->numeric()->default(0),
TextInput::make('points_reward')->numeric()->default(0),
Toggle::make('is_active')->default(true),
SpatieMediaLibraryFileUpload::make('icon')->collection('icon'),
```

**RewardResource** — scope to global only:
```php
TextInput::make('name')->required(),
Textarea::make('description'),
TextInput::make('points_cost')->numeric()->required(),
TextInput::make('stock')->numeric()->nullable(),
Toggle::make('is_active')->default(true),
SpatieMediaLibraryFileUpload::make('image')->collection('image'),
```

**Step 3: Create LoyaltyOverview widget**

```php
<?php

namespace App\Filament\Widgets;

use App\Models\RewardRedemption;
use App\Models\UserXpSummary;
use App\Models\XpTransaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoyaltyOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Loyalty';

    protected function getStats(): array
    {
        return [
            Stat::make('Total XP Awarded', XpTransaction::sum('xp_amount')),
            Stat::make('Total Redemptions', RewardRedemption::count()),
            Stat::make('Active Users', UserXpSummary::where('total_xp', '>', 0)->count()),
        ];
    }
}
```

**Step 4: Register widget in AdminPanelProvider**

Add `LoyaltyOverview::class` to the `->widgets([...])` array in `app/Providers/Filament/AdminPanelProvider.php`.

**Step 5: Run pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/ app/Providers/
git commit -m "feat: add Filament resources for XP events, levels, achievements, rewards + loyalty widget"
```

---

### Task 9: Coach Controllers + Form Requests — Rewards CRUD

**Files:**
- Create: `app/Http/Controllers/Coach/RewardController.php`
- Create: `app/Http/Requests/StoreRewardRequest.php`
- Create: `app/Http/Requests/UpdateRewardRequest.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Coach/RewardTest.php`

**Step 1: Write test**

Create `tests/Feature/Coach/RewardTest.php`:
```php
<?php

use App\Models\Reward;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
});

it('shows the rewards index with coach and global rewards', function () {
    Reward::factory()->create(['coach_id' => $this->coach->id]);
    Reward::factory()->global()->create();

    $this->actingAs($this->coach)
        ->get(route('coach.rewards.index'))
        ->assertOk()
        ->assertViewIs('coach.rewards.index');
});

it('shows the create reward form', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.rewards.create'))
        ->assertOk();
});

it('creates a reward', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.rewards.store'), [
            'name' => 'Free PT Session',
            'description' => 'One free personal training session',
            'points_cost' => 500,
            'stock' => 10,
        ])
        ->assertRedirect(route('coach.rewards.index'));

    $this->assertDatabaseHas('rewards', [
        'coach_id' => $this->coach->id,
        'name' => 'Free PT Session',
        'points_cost' => 500,
    ]);
});

it('updates a reward', function () {
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->put(route('coach.rewards.update', $reward), [
            'name' => 'Updated Reward',
            'points_cost' => 300,
        ])
        ->assertRedirect(route('coach.rewards.index'));

    expect($reward->fresh()->name)->toBe('Updated Reward');
});

it('archives a reward on destroy', function () {
    $reward = Reward::factory()->create(['coach_id' => $this->coach->id, 'is_active' => true]);

    $this->actingAs($this->coach)
        ->delete(route('coach.rewards.destroy', $reward))
        ->assertRedirect(route('coach.rewards.index'));

    expect($reward->fresh()->is_active)->toBeFalse();
});

it('prevents editing another coachs reward', function () {
    $otherCoach = User::factory()->coach()->create();
    $reward = Reward::factory()->create(['coach_id' => $otherCoach->id]);

    $this->actingAs($this->coach)
        ->get(route('coach.rewards.edit', $reward))
        ->assertForbidden();
});

it('prevents editing global rewards', function () {
    $reward = Reward::factory()->global()->create();

    $this->actingAs($this->coach)
        ->get(route('coach.rewards.edit', $reward))
        ->assertForbidden();
});

it('validates required fields', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.rewards.store'), [])
        ->assertSessionHasErrors(['name', 'points_cost']);
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --compact --filter=RewardTest
```

**Step 3: Create form requests**

`app/Http/Requests/StoreRewardRequest.php`:
```php
public function authorize(): bool
{
    return $this->user()->isCoach();
}

public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'points_cost' => ['required', 'integer', 'min:1'],
        'stock' => ['nullable', 'integer', 'min:0'],
        'image' => ['nullable', 'image', 'max:2048'],
    ];
}
```

`app/Http/Requests/UpdateRewardRequest.php` — same rules.

**Step 4: Create RewardController**

Follow `MealController` pattern exactly. Key differences:
- `index()` queries both `auth()->user()->rewards()` AND `Reward::whereNull('coach_id')->where('is_active', true)`
- `edit()`/`update()`/`destroy()` check `$reward->coach_id !== auth()->id()` (also forbid if `coach_id` is null — global rewards)
- Handle Spatie media upload for `image` collection

**Step 5: Add routes to `routes/web.php`**

Inside the coach group:
```php
Route::resource('rewards', Coach\RewardController::class)->except(['show']);
```

**Step 6: Run tests**

```bash
php artisan test --compact --filter=RewardTest
```
Expected: PASS.

**Step 7: Run pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/ routes/web.php tests/Feature/Coach/
git commit -m "feat: add coach reward CRUD controller with tests"
```

---

### Task 10: Coach Controllers — Achievements CRUD + Manual Award

**Files:**
- Create: `app/Http/Controllers/Coach/AchievementController.php`
- Create: `app/Http/Requests/StoreAchievementRequest.php`
- Create: `app/Http/Requests/UpdateAchievementRequest.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Coach/AchievementTest.php`

Follow same pattern as Task 9 for CRUD. Additional route and test for manual awarding:

```php
// Route
Route::post('clients/{client}/achievements/{achievement}/award', [Coach\AchievementController::class, 'award'])->name('clients.achievements.award');
```

Manual award test:
```php
it('manually awards an achievement to a client', function () {
    $achievement = Achievement::factory()->manual()->create(['coach_id' => $this->coach->id]);
    $client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    UserXpSummary::create(['user_id' => $client->id, 'total_xp' => 0, 'available_points' => 0]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.achievements.award', [$client, $achievement]))
        ->assertRedirect();

    expect($client->achievements)->toHaveCount(1);
    expect($client->achievements->first()->pivot->awarded_by)->toBe($this->coach->id);
});

it('prevents awarding to another coachs client', function () {
    $otherCoach = User::factory()->coach()->create();
    $client = User::factory()->client()->create(['coach_id' => $otherCoach->id]);
    $achievement = Achievement::factory()->manual()->create(['coach_id' => $this->coach->id]);

    $this->actingAs($this->coach)
        ->post(route('coach.clients.achievements.award', [$client, $achievement]))
        ->assertForbidden();
});
```

Award method in controller:
```php
public function award(User $client, Achievement $achievement): RedirectResponse
{
    if ($client->coach_id !== auth()->id()) {
        abort(403);
    }

    if ($client->achievements()->where('achievement_id', $achievement->id)->exists()) {
        return back()->with('error', 'Achievement already awarded.');
    }

    $client->achievements()->attach($achievement->id, [
        'awarded_by' => auth()->id(),
        'earned_at' => now(),
    ]);

    if ($achievement->xp_reward > 0 || $achievement->points_reward > 0) {
        $summary = UserXpSummary::firstOrCreate(
            ['user_id' => $client->id],
            ['total_xp' => 0, 'available_points' => 0],
        );

        if ($achievement->xp_reward > 0) {
            $summary->increment('total_xp', $achievement->xp_reward);
        }
        if ($achievement->points_reward > 0) {
            $summary->increment('available_points', $achievement->points_reward);
        }
    }

    return back()->with('success', 'Achievement awarded successfully!');
}
```

**Run pint and commit after tests pass.**

---

### Task 11: Coach Controller — Redemptions Management

**Files:**
- Create: `app/Http/Controllers/Coach/RedemptionController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Coach/RedemptionTest.php`

**Key functionality:**
- `index()` — list all redemptions for the coach's clients. Query: `RewardRedemption::whereHas('user', fn($q) => $q->where('coach_id', auth()->id()))->with('user', 'reward')->latest()->paginate(20)`
- `update()` — change status to `fulfilled` or `rejected`, add optional `coach_notes`

**Routes:**
```php
Route::get('redemptions', [Coach\RedemptionController::class, 'index'])->name('redemptions.index');
Route::patch('redemptions/{redemption}', [Coach\RedemptionController::class, 'update'])->name('redemptions.update');
```

**Tests should cover:** listing, fulfilling, rejecting, adding notes, authorization (can't manage other coach's client redemptions).

**Run pint and commit after tests pass.**

---

### Task 12: Client Controller — Rewards Shop + Redemption

**Files:**
- Create: `app/Http/Controllers/Client/RewardController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Client/RewardRedemptionTest.php`

**Key functionality:**
- `index()` — show available rewards (global + coach's). Include user's `available_points` and `current_level`
- `redeem()` — synchronous redemption with DB transaction + lock on `user_xp_summaries`

**Routes:**
```php
Route::get('rewards', [Client\RewardController::class, 'index'])->name('rewards');
Route::post('rewards/{reward}/redeem', [Client\RewardController::class, 'redeem'])->name('rewards.redeem');
```

**Redeem method:**
```php
public function redeem(Reward $reward): RedirectResponse
{
    $user = auth()->user();

    DB::transaction(function () use ($user, $reward) {
        $summary = UserXpSummary::where('user_id', $user->id)->lockForUpdate()->first();

        if (! $summary || $summary->available_points < $reward->points_cost) {
            abort(403, 'Insufficient points.');
        }

        if (! $reward->hasStock()) {
            abort(403, 'Reward out of stock.');
        }

        $summary->decrement('available_points', $reward->points_cost);

        if ($reward->stock !== null) {
            $reward->decrement('stock');
        }

        $redemption = RewardRedemption::create([
            'user_id' => $user->id,
            'reward_id' => $reward->id,
            'points_spent' => $reward->points_cost,
            'status' => 'pending',
        ]);

        NotifyCoachOfRedemption::dispatch($redemption->id);
    });

    return back()->with('success', 'Reward redeemed successfully!');
}
```

**Tests should cover:** viewing rewards, successful redemption, insufficient points, out of stock, points balance decremented, redemption record created, job dispatched.

**Run pint and commit after tests pass.**

---

### Task 13: Client Controller — Achievements Page

**Files:**
- Create: `app/Http/Controllers/Client/AchievementController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Client/AchievementTest.php`

**Key functionality:**
- `index()` — show all achievements (global + coach's) with earned status. Pass user's earned achievement IDs, progress data for automatic achievements.

**Route:**
```php
Route::get('achievements', [Client\AchievementController::class, 'index'])->name('achievements');
```

**Run pint and commit after tests pass.**

---

### Task 14: Coach Blade Views — Rewards

**Files:**
- Create: `resources/views/coach/rewards/index.blade.php`
- Create: `resources/views/coach/rewards/create.blade.php`
- Create: `resources/views/coach/rewards/edit.blade.php`

Follow exact patterns from `coach/meals/index.blade.php`, `coach/meals/create.blade.php`, `coach/meals/edit.blade.php`:
- `<x-layouts.coach>` wrapper
- Flash message handling
- Search form on index
- Grid of reward cards: name, points cost, stock indicator, description
- Global rewards shown with "System" badge, not clickable for edit
- Create/edit forms: name, description, points_cost, stock, image upload (using standard file input + Spatie in controller)
- Archive section on edit page

**Run pint and commit.**

---

### Task 15: Coach Blade Views — Achievements + Redemptions

**Files:**
- Create: `resources/views/coach/achievements/index.blade.php`
- Create: `resources/views/coach/achievements/create.blade.php`
- Create: `resources/views/coach/achievements/edit.blade.php`
- Create: `resources/views/coach/redemptions/index.blade.php`

Follow same Blade patterns. Achievements index shows global (read-only "System" badge) + coach's own. Create/edit has conditional fields (condition_type/condition_value visible only when type=automatic) using Alpine.js `x-show`.

Redemptions index: table of redemptions with client name, reward name, points spent, status badge, fulfill/reject buttons as small forms.

**Run pint and commit.**

---

### Task 16: Client Blade Views — Rewards Shop + Achievements

**Files:**
- Create: `resources/views/client/rewards.blade.php`
- Create: `resources/views/client/achievements.blade.php`

**Rewards shop** (`rewards.blade.php`):
- `<x-layouts.client>` wrapper
- Top bar: points balance, current level badge
- Grid of `<x-bladewind::card>` for each reward: image, name, description, points cost
- Redeem button (POST form) — disabled if insufficient points or out of stock

**Achievements** (`achievements.blade.php`):
- `<x-layouts.client>` wrapper
- Grid of achievement cards
- Earned: highlighted with green border/check, shows earned date
- Unearned: greyed out
- For automatic achievements nearing completion: progress bar showing current/required

**Run pint and commit.**

---

### Task 17: Dashboard Additions — Client dashboard + Coach client detail

**Files:**
- Modify: `resources/views/client/dashboard.blade.php`
- Modify: `app/Http/Controllers/Client/DashboardController.php`
- Modify: `resources/views/coach/clients/show.blade.php`
- Modify: `app/Http/Controllers/Coach/ClientController.php`

**Client dashboard additions:**
Add after the "Quick Stats" grid, before the "Daily Check-in Widget":
- XP progress bar toward next level (percentage = `(total_xp - current_level_xp) / (next_level_xp - current_level_xp)`)
- Current level badge with icon (from Spatie media)
- Available points balance
- Last 3 earned achievements as small badge icons

In `DashboardController`, add to the data passed to view:
```php
$xpSummary = $user->xpSummary()->with('currentLevel')->first();
$nextLevel = $xpSummary ? Level::where('xp_required', '>', $xpSummary->total_xp)->orderBy('xp_required')->first() : Level::orderBy('xp_required')->first();
$recentAchievements = $user->achievements()->latest('user_achievements.earned_at')->limit(3)->get();
```

**Coach client detail additions:**
Add a "Loyalty" section showing: level badge, XP progress, points balance, earned achievements, recent redemptions.

In `ClientController::show()`, add:
```php
$xpSummary = $client->xpSummary()->with('currentLevel')->first();
$achievements = $client->achievements()->latest('user_achievements.earned_at')->get();
$redemptions = $client->rewardRedemptions()->with('reward')->latest()->limit(5)->get();
```

**Run pint and commit.**

---

### Task 18: Add manual achievement award UI to coach client detail

**Files:**
- Modify: `resources/views/coach/clients/show.blade.php`

Add to the Loyalty section on the coach client detail page:
- "Award Achievement" button that opens an Alpine.js dropdown/modal
- Lists manual achievements (coach's + global manual) that the client hasn't earned yet
- Each with an "Award" button (POST form to `coach.clients.achievements.award`)

**Run pint and commit.**

---

### Task 19: Final integration test + cleanup

**Files:**
- Create: `tests/Feature/Loyalty/FullFlowTest.php`

**Write an end-to-end test** that:
1. Seeds XP event types and levels
2. Creates a coach and client
3. Client logs a workout → verify XP transaction created, summary updated, level assigned
4. Client logs daily check-ins for 7 days → verify streak achievement awarded
5. Client redeems a reward → verify points deducted, redemption created
6. Coach fulfills redemption → verify status updated

```bash
php artisan test --compact --filter=FullFlowTest
```

**Final steps:**
```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact
```
Expected: All tests pass.

**Final commit:**
```bash
git add -A
git commit -m "feat: complete loyalty system with XP, levels, achievements, and rewards"
```
