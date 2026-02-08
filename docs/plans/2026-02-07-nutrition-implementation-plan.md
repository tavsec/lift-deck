# Nutritional Planning & Meal Logging - Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add nutritional macro goal setting (coach), meal library (coach), and meal logging (client) to LiftDeck.

**Architecture:** Three new models (MacroGoal, Meal, MealLog) following existing patterns. Coach controllers for goal/meal management, client controller for meal logging. Blade + Alpine.js views matching existing UI conventions.

**Tech Stack:** Laravel 12, Blade, Alpine.js, Tailwind CSS, Pest 4

---

### Task 1: Database Migrations

**Files:**
- Create: `database/migrations/xxxx_create_macro_goals_table.php`
- Create: `database/migrations/xxxx_create_meals_table.php`
- Create: `database/migrations/xxxx_create_meal_logs_table.php`

**Step 1: Create migrations**

Run these artisan commands:

```bash
php artisan make:migration create_macro_goals_table --no-interaction
php artisan make:migration create_meals_table --no-interaction
php artisan make:migration create_meal_logs_table --no-interaction
```

**Step 2: Define macro_goals schema**

```php
Schema::create('macro_goals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete();
    $table->integer('calories');
    $table->decimal('protein', 6, 1);
    $table->decimal('carbs', 6, 1);
    $table->decimal('fat', 6, 1);
    $table->date('effective_date');
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index(['client_id', 'effective_date']);
});
```

**Step 3: Define meals schema**

```php
Schema::create('meals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->integer('calories');
    $table->decimal('protein', 6, 1);
    $table->decimal('carbs', 6, 1);
    $table->decimal('fat', 6, 1);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('coach_id');
});
```

**Step 4: Define meal_logs schema**

```php
Schema::create('meal_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('meal_id')->nullable()->constrained('meals')->nullOnDelete();
    $table->date('date');
    $table->string('meal_type');
    $table->string('name');
    $table->integer('calories');
    $table->decimal('protein', 6, 1);
    $table->decimal('carbs', 6, 1);
    $table->decimal('fat', 6, 1);
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index(['client_id', 'date']);
});
```

**Step 5: Run migrations**

```bash
php artisan migrate --no-interaction
```

**Step 6: Commit**

```bash
git add database/migrations/
git commit -m "feat: add macro_goals, meals, and meal_logs migrations"
```

---

### Task 2: Models & Factories

**Files:**
- Create: `app/Models/MacroGoal.php`
- Create: `app/Models/Meal.php`
- Create: `app/Models/MealLog.php`
- Create: `database/factories/MacroGoalFactory.php`
- Create: `database/factories/MealFactory.php`
- Create: `database/factories/MealLogFactory.php`
- Modify: `app/Models/User.php` (add relationships)

**Step 1: Create models with factories**

```bash
php artisan make:model MacroGoal --factory --no-interaction
php artisan make:model Meal --factory --no-interaction
php artisan make:model MealLog --factory --no-interaction
```

**Step 2: Define MacroGoal model**

`app/Models/MacroGoal.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MacroGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'coach_id',
        'calories',
        'protein',
        'carbs',
        'fat',
        'effective_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'protein' => 'decimal:1',
            'carbs' => 'decimal:1',
            'fat' => 'decimal:1',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get the active macro goal for a client on a given date.
     */
    public static function activeForClient(int $clientId, string $date): ?self
    {
        return static::where('client_id', $clientId)
            ->where('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->first();
    }
}
```

**Step 3: Define Meal model**

`app/Models/Meal.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'calories',
        'protein',
        'carbs',
        'fat',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'protein' => 'decimal:1',
            'carbs' => 'decimal:1',
            'fat' => 'decimal:1',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

**Step 4: Define MealLog model**

`app/Models/MealLog.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'meal_id',
        'date',
        'meal_type',
        'name',
        'calories',
        'protein',
        'carbs',
        'fat',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'protein' => 'decimal:1',
            'carbs' => 'decimal:1',
            'fat' => 'decimal:1',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
```

**Step 5: Define factories**

`database/factories/MacroGoalFactory.php`:
```php
public function definition(): array
{
    return [
        'client_id' => User::factory()->state(['role' => 'client']),
        'coach_id' => User::factory()->state(['role' => 'coach']),
        'calories' => fake()->numberBetween(1500, 3000),
        'protein' => fake()->numberBetween(100, 250),
        'carbs' => fake()->numberBetween(150, 400),
        'fat' => fake()->numberBetween(40, 120),
        'effective_date' => fake()->date(),
        'notes' => null,
    ];
}
```

`database/factories/MealFactory.php`:
```php
public function definition(): array
{
    return [
        'coach_id' => User::factory()->state(['role' => 'coach']),
        'name' => fake()->words(3, true),
        'description' => null,
        'calories' => fake()->numberBetween(200, 800),
        'protein' => fake()->numberBetween(10, 50),
        'carbs' => fake()->numberBetween(20, 80),
        'fat' => fake()->numberBetween(5, 30),
        'is_active' => true,
    ];
}

public function inactive(): static
{
    return $this->state(fn () => ['is_active' => false]);
}
```

`database/factories/MealLogFactory.php`:
```php
public function definition(): array
{
    return [
        'client_id' => User::factory()->state(['role' => 'client']),
        'meal_id' => null,
        'date' => now()->format('Y-m-d'),
        'meal_type' => fake()->randomElement(['Breakfast', 'Lunch', 'Dinner', 'Snack']),
        'name' => fake()->words(3, true),
        'calories' => fake()->numberBetween(200, 800),
        'protein' => fake()->numberBetween(10, 50),
        'carbs' => fake()->numberBetween(20, 80),
        'fat' => fake()->numberBetween(5, 30),
        'notes' => null,
    ];
}
```

**Step 6: Add relationships to User model**

Add to `app/Models/User.php`:
```php
public function macroGoals(): HasMany
{
    return $this->hasMany(MacroGoal::class, 'client_id');
}

public function meals(): HasMany
{
    return $this->hasMany(Meal::class, 'coach_id');
}

public function mealLogs(): HasMany
{
    return $this->hasMany(MealLog::class, 'client_id');
}
```

**Step 7: Run pint & commit**

```bash
vendor/bin/pint --dirty
git add app/Models/ database/factories/
git commit -m "feat: add MacroGoal, Meal, MealLog models with factories and User relationships"
```

---

### Task 3: Coach Meal Library (Controller + Views)

**Files:**
- Create: `app/Http/Controllers/Coach/MealController.php`
- Create: `app/Http/Requests/StoreMealRequest.php`
- Create: `app/Http/Requests/UpdateMealRequest.php`
- Create: `resources/views/coach/meals/index.blade.php`
- Create: `resources/views/coach/meals/create.blade.php`
- Create: `resources/views/coach/meals/edit.blade.php`
- Modify: `routes/web.php`
- Modify: `resources/views/components/layouts/coach.blade.php`

**Step 1: Create controller and form requests**

```bash
php artisan make:controller Coach/MealController --no-interaction
php artisan make:request StoreMealRequest --no-interaction
php artisan make:request UpdateMealRequest --no-interaction
```

**Step 2: Define StoreMealRequest**

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
        'calories' => ['required', 'integer', 'min:0'],
        'protein' => ['required', 'numeric', 'min:0'],
        'carbs' => ['required', 'numeric', 'min:0'],
        'fat' => ['required', 'numeric', 'min:0'],
    ];
}
```

**Step 3: Define UpdateMealRequest** (same rules as Store)

**Step 4: Implement MealController**

Follow the ExerciseController pattern exactly:
- `index()` — list coach's meals with search, paginate 20. Show active meals by default.
- `create()` — return create form view
- `store(StoreMealRequest)` — create meal with `coach_id = auth()->id()`, redirect to index
- `edit(Meal)` — authorize ownership, return edit form view
- `update(UpdateMealRequest, Meal)` — authorize ownership, update, redirect to index
- `destroy(Meal)` — authorize ownership, toggle `is_active` to false (soft archive), redirect to index

Authorization pattern: private `authorizeMeal(Meal)` method checking `$meal->coach_id !== auth()->id()`.

**Step 5: Create views**

`resources/views/coach/meals/index.blade.php` — follow `coach/exercises/index.blade.php` pattern:
- Header with title "Meal Library" and "Add Meal" button
- Search form
- Grid of meal cards showing: name, calories, protein/carbs/fat in small badges
- Empty state

`resources/views/coach/meals/create.blade.php` — follow `coach/exercises/create.blade.php` pattern:
- Back link, title "Add Meal"
- Form fields: name (required), description (textarea), calories (number), protein (number step=0.1), carbs (number step=0.1), fat (number step=0.1)
- Cancel + Create buttons

`resources/views/coach/meals/edit.blade.php` — same as create but pre-filled, with archive/delete option.

**Step 6: Add routes to `routes/web.php`**

Inside the coach group, add:
```php
Route::resource('meals', Coach\MealController::class)->except(['show']);
```

**Step 7: Add "Meals" nav item to coach layout**

In `resources/views/components/layouts/coach.blade.php`, add a "Meals" navigation link after "Exercises" in both desktop sidebar and mobile menu. Use a plate/utensils SVG icon. Active state: `request()->routeIs('coach.meals.*')`.

**Step 8: Run pint & commit**

```bash
vendor/bin/pint --dirty
git add app/Http/Controllers/Coach/MealController.php app/Http/Requests/ resources/views/coach/meals/ routes/web.php resources/views/components/layouts/coach.blade.php
git commit -m "feat: add coach meal library with CRUD"
```

---

### Task 4: Coach Macro Goals & Client Nutrition Page

**Files:**
- Create: `app/Http/Controllers/Coach/MacroGoalController.php`
- Create: `app/Http/Controllers/Coach/NutritionController.php`
- Create: `app/Http/Requests/StoreMacroGoalRequest.php`
- Create: `resources/views/coach/clients/nutrition.blade.php`
- Modify: `resources/views/coach/clients/show.blade.php`
- Modify: `routes/web.php`

**Step 1: Create controllers and request**

```bash
php artisan make:controller Coach/MacroGoalController --no-interaction
php artisan make:controller Coach/NutritionController --no-interaction
php artisan make:request StoreMacroGoalRequest --no-interaction
```

**Step 2: Define StoreMacroGoalRequest**

```php
public function authorize(): bool
{
    return $this->user()->isCoach();
}

public function rules(): array
{
    return [
        'calories' => ['required', 'integer', 'min:0'],
        'protein' => ['required', 'numeric', 'min:0'],
        'carbs' => ['required', 'numeric', 'min:0'],
        'fat' => ['required', 'numeric', 'min:0'],
        'effective_date' => ['required', 'date'],
        'notes' => ['nullable', 'string', 'max:1000'],
    ];
}
```

**Step 3: Implement MacroGoalController**

- `store(StoreMacroGoalRequest, User $client)` — verify `$client->coach_id === auth()->id()`, create MacroGoal with coach_id/client_id, redirect back to nutrition page with success
- `destroy(MacroGoal $macroGoal)` — verify `$macroGoal->coach_id === auth()->id()`, delete, redirect back

**Step 4: Implement NutritionController**

- `show(User $client)` — verify `$client->coach_id === auth()->id()`. Load:
  - All macro goals for this client, ordered by `effective_date DESC`
  - Current active goal (for today)
  - Last 7 days of meal logs grouped by date, with daily totals computed
  - Return `coach.clients.nutrition` view

**Step 5: Create nutrition view**

`resources/views/coach/clients/nutrition.blade.php`:
- Back link to client show page
- Header: "{Client Name} — Nutrition"
- **Set Macro Goals card**: Form with calories, protein, carbs, fat, effective_date (default today), notes. POST to `coach.clients.macro-goals.store`.
- **Current Goal card**: Shows the active goal if one exists, or "No goals set" message.
- **Goal History card**: Table listing all goals with effective_date, calories, P/C/F, notes, delete button.
- **Recent Meal Logs (Last 7 Days)**: For each day, show a summary row (date, total cals, total P/C/F, vs goal). Each day expandable to show individual meals.

**Step 6: Add nutrition summary to client show page**

In `resources/views/coach/clients/show.blade.php`, add a "Nutrition" card in the main content area (after Recent Workouts), showing:
- Today's macro goal (if set) with current totals
- Link to full nutrition page: `route('coach.clients.nutrition', $client)`

Update `Coach\ClientController@show` to also load:
```php
$currentMacroGoal = MacroGoal::activeForClient($client->id, now()->format('Y-m-d'));
$todayMealTotals = $client->mealLogs()
    ->whereDate('date', now())
    ->selectRaw('SUM(calories) as calories, SUM(protein) as protein, SUM(carbs) as carbs, SUM(fat) as fat')
    ->first();
```

**Step 7: Add routes**

Inside coach group:
```php
Route::get('clients/{client}/nutrition', [Coach\NutritionController::class, 'show'])->name('clients.nutrition');
Route::post('clients/{client}/macro-goals', [Coach\MacroGoalController::class, 'store'])->name('clients.macro-goals.store');
Route::delete('macro-goals/{macroGoal}', [Coach\MacroGoalController::class, 'destroy'])->name('macro-goals.destroy');
```

**Step 8: Run pint & commit**

```bash
vendor/bin/pint --dirty
git add app/Http/Controllers/Coach/MacroGoalController.php app/Http/Controllers/Coach/NutritionController.php app/Http/Controllers/Coach/ClientController.php app/Http/Requests/StoreMacroGoalRequest.php resources/views/coach/clients/ routes/web.php
git commit -m "feat: add coach macro goals and client nutrition overview"
```

---

### Task 5: Client Nutrition Page (Controller + View)

**Files:**
- Create: `app/Http/Controllers/Client/NutritionController.php`
- Create: `app/Http/Requests/StoreMealLogRequest.php`
- Create: `resources/views/client/nutrition.blade.php`
- Modify: `resources/views/components/layouts/client.blade.php`
- Modify: `routes/web.php`

**Step 1: Create controller and request**

```bash
php artisan make:controller Client/NutritionController --no-interaction
php artisan make:request StoreMealLogRequest --no-interaction
```

**Step 2: Define StoreMealLogRequest**

```php
public function authorize(): bool
{
    return $this->user()->isClient();
}

public function rules(): array
{
    return [
        'meal_id' => ['nullable', 'exists:meals,id'],
        'date' => ['required', 'date', 'before_or_equal:today'],
        'meal_type' => ['required', 'string', 'max:50'],
        'name' => ['required', 'string', 'max:255'],
        'calories' => ['required', 'integer', 'min:0'],
        'protein' => ['required', 'numeric', 'min:0'],
        'carbs' => ['required', 'numeric', 'min:0'],
        'fat' => ['required', 'numeric', 'min:0'],
        'notes' => ['nullable', 'string', 'max:1000'],
    ];
}
```

**Step 3: Implement Client\NutritionController**

- `index(Request)` — Get date from query (default today). Load:
  - Active macro goal for user via `MacroGoal::activeForClient($user->id, $date)`
  - Meal logs for this date, ordered by created_at
  - Daily totals (sum of logged meals)
  - Return view

- `store(StoreMealLogRequest)` — Create MealLog with `client_id = auth()->id()`. If `meal_id` is provided, verify the meal belongs to the client's coach (`$meal->coach_id === $user->coach_id`). Redirect back.

- `destroy(MealLog $mealLog)` — Verify `$mealLog->client_id === auth()->id()`, delete, redirect back.

- `meals(Request)` — JSON endpoint. Return the client's coach's active meals, optionally filtered by `search` query param. Used by Alpine.js for meal library search:
  ```php
  $meals = Meal::where('coach_id', $user->coach_id)
      ->active()
      ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
      ->orderBy('name')
      ->get(['id', 'name', 'calories', 'protein', 'carbs', 'fat']);
  return response()->json($meals);
  ```

**Step 4: Create client nutrition view**

`resources/views/client/nutrition.blade.php` — follow `client/check-in.blade.php` pattern:

Uses `x-data="nutritionLogger()"` Alpine component.

Layout:
1. **Header with date navigation** — same pattern as check-in (prev/next arrows, date picker, Today button)
2. **Macro progress section** — 4 progress bars (one per macro):
   - Each shows: label, current/target, percentage bar
   - Calories: blue bar. Protein: green. Carbs: yellow. Fat: red/orange.
   - If no goal set: show totals only, no bar percentages
3. **Logged meals list** — grouped by meal_type. Each entry shows:
   - Name, calories, P/C/F in small text
   - Delete button (form with DELETE)
4. **Add meal section** — Alpine.js component with two tabs: "Library" and "Custom"
   - **Library tab**: Search input that fetches from `/client/nutrition/meals?search=`. Results listed as tappable cards. Selecting a meal pre-fills the form. User picks meal_type then submits.
   - **Custom tab**: Manual form with name, meal_type (quick-select buttons: Breakfast/Lunch/Dinner/Snack + custom text input), calories, protein, carbs, fat, notes.
   - Both submit via POST to `/client/nutrition`.

**Step 5: Add nutrition tab to client layout**

In `resources/views/components/layouts/client.blade.php`:
- Change `grid-cols-5` to `grid-cols-6` in bottom nav
- Add Nutrition tab after Check-in tab, before History. Use a plate/utensils SVG icon. Active state: `request()->routeIs('client.nutrition*')`.

**Step 6: Add routes**

Inside client group in `routes/web.php`:
```php
Route::get('nutrition', [Client\NutritionController::class, 'index'])->name('nutrition');
Route::post('nutrition', [Client\NutritionController::class, 'store'])->name('nutrition.store');
Route::delete('nutrition/{mealLog}', [Client\NutritionController::class, 'destroy'])->name('nutrition.destroy');
Route::get('nutrition/meals', [Client\NutritionController::class, 'meals'])->name('nutrition.meals');
```

**Step 7: Run pint & commit**

```bash
vendor/bin/pint --dirty
git add app/Http/Controllers/Client/NutritionController.php app/Http/Requests/StoreMealLogRequest.php resources/views/client/nutrition.blade.php resources/views/components/layouts/client.blade.php routes/web.php
git commit -m "feat: add client nutrition logging page with library and custom meals"
```

---

### Task 6: Tests

**Files:**
- Create: `tests/Feature/Coach/MealTest.php`
- Create: `tests/Feature/Coach/MacroGoalTest.php`
- Create: `tests/Feature/Coach/NutritionTest.php`
- Create: `tests/Feature/Client/NutritionTest.php`

**Step 1: Create test files**

```bash
php artisan make:test --pest Coach/MealTest --no-interaction
php artisan make:test --pest Coach/MacroGoalTest --no-interaction
php artisan make:test --pest Coach/NutritionTest --no-interaction
php artisan make:test --pest Client/NutritionTest --no-interaction
```

**Step 2: Coach Meal Tests** (`tests/Feature/Coach/MealTest.php`)

Test cases:
- Coach can view meal library index
- Coach can create a meal
- Coach can update a meal
- Coach can archive (delete) a meal
- Coach cannot edit another coach's meal
- Validation rejects missing required fields

**Step 3: Coach Macro Goal Tests** (`tests/Feature/Coach/MacroGoalTest.php`)

Test cases:
- Coach can create a macro goal for their client
- Coach cannot create macro goal for another coach's client
- Coach can delete a macro goal
- Coach cannot delete another coach's macro goal
- Validation rejects missing required fields

**Step 4: Coach Nutrition Tests** (`tests/Feature/Coach/NutritionTest.php`)

Test cases:
- Coach can view client nutrition page
- Coach cannot view another coach's client nutrition page

**Step 5: Client Nutrition Tests** (`tests/Feature/Client/NutritionTest.php`)

Test cases:
- Client can view nutrition page
- Client can log a custom meal
- Client can log a meal from library
- Client cannot log a meal from another coach's library
- Client can delete their own meal log
- Client cannot delete another client's meal log
- Client can fetch meals JSON from their coach's library
- Validation rejects missing required fields

**Step 6: Run all tests**

```bash
php artisan test --compact
```

**Step 7: Commit**

```bash
vendor/bin/pint --dirty
git add tests/Feature/
git commit -m "test: add nutrition feature tests for coach and client"
```
