# Coach Branding Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Allow each coach to brand the app for their clients with logo/title, colors, description, custom onboarding fields, and welcome email.

**Architecture:** Add branding columns to users table, create onboarding_fields and onboarding_responses tables. New BrandingController with a single-page form using Alpine.js for the dynamic field builder. CSS custom properties for theming. New WelcomeClientMail sent after registration.

**Tech Stack:** Laravel 12, Blade, Alpine.js, Tailwind CSS v3, Pest 4

---

## Codebase Conventions Reference

- **Form Requests:** Array-based rules, `authorize()` checks role (see `app/Http/Requests/StoreMealRequest.php`)
- **Controller auth checks:** `if ($thing->coach_id !== auth()->id()) { abort(403); }` or FormRequest authorize
- **Routes:** Grouped under `coach.` prefix with `role:coach` middleware (see `routes/web.php:19-66`)
- **Views:** `<x-layouts.coach>` wrapper, Alpine.js for interactivity, blue theme throughout
- **Reorder pattern:** `moveUp`/`moveDown` methods swapping `order` column (see `TrackingMetricController`)
- **Logo storage:** `logo` column on users stores file path (varchar); use `Storage::disk('public')`
- **Tests:** Pest 4, mostly feature tests, use `fake()` for faker

---

### Task 1: Migration — Add branding columns to users table

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_add_branding_columns_to_users_table.php`

**Step 1: Create migration**

```bash
php artisan make:migration add_branding_columns_to_users_table --no-interaction
```

**Step 2: Write the migration**

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('secondary_color')->nullable()->after('primary_color');
        $table->text('description')->nullable()->after('bio');
        $table->text('welcome_email_text')->nullable()->after('description');
        $table->text('onboarding_welcome_text')->nullable()->after('welcome_email_text');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['secondary_color', 'description', 'welcome_email_text', 'onboarding_welcome_text']);
    });
}
```

**Step 3: Run migration**

```bash
php artisan migrate
```

**Step 4: Update User model fillable**

Add to `$fillable` in `app/Models/User.php`:
```php
'secondary_color',
'description',
'welcome_email_text',
'onboarding_welcome_text',
```

**Step 5: Commit**

```bash
git add -A && git commit -m "feat: add branding columns to users table"
```

---

### Task 2: Migration — Create onboarding_fields table

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_onboarding_fields_table.php`
- Create: `app/Models/OnboardingField.php`

**Step 1: Create migration + model**

```bash
php artisan make:model OnboardingField -m --no-interaction
```

**Step 2: Write the migration**

```php
public function up(): void
{
    Schema::create('onboarding_fields', function (Blueprint $table) {
        $table->id();
        $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete();
        $table->string('label');
        $table->string('type'); // text, select, textarea
        $table->json('options')->nullable(); // for select type
        $table->boolean('is_required')->default(true);
        $table->integer('order')->default(0);
        $table->timestamps();
    });
}
```

**Step 3: Write the model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingField extends Model
{
    protected $fillable = [
        'coach_id',
        'label',
        'type',
        'options',
        'is_required',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(OnboardingResponse::class);
    }
}
```

**Step 4: Add relationship to User model**

```php
public function onboardingFields(): HasMany
{
    return $this->hasMany(OnboardingField::class, 'coach_id')->orderBy('order');
}
```

**Step 5: Run migration**

```bash
php artisan migrate
```

**Step 6: Commit**

```bash
git add -A && git commit -m "feat: create onboarding_fields table and model"
```

---

### Task 3: Migration — Create onboarding_responses table

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_create_onboarding_responses_table.php`
- Create: `app/Models/OnboardingResponse.php`

**Step 1: Create migration + model**

```bash
php artisan make:model OnboardingResponse -m --no-interaction
```

**Step 2: Write the migration**

```php
public function up(): void
{
    Schema::create('onboarding_responses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('onboarding_field_id')->constrained()->cascadeOnDelete();
        $table->text('value')->nullable();
        $table->timestamps();

        $table->unique(['client_id', 'onboarding_field_id']);
    });
}
```

**Step 3: Write the model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingResponse extends Model
{
    protected $fillable = [
        'client_id',
        'onboarding_field_id',
        'value',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function onboardingField(): BelongsTo
    {
        return $this->belongsTo(OnboardingField::class);
    }
}
```

**Step 4: Add relationship to User model**

```php
public function onboardingResponses(): HasMany
{
    return $this->hasMany(OnboardingResponse::class, 'client_id');
}
```

**Step 5: Run migration**

```bash
php artisan migrate
```

**Step 6: Commit**

```bash
git add -A && git commit -m "feat: create onboarding_responses table and model"
```

---

### Task 4: Seed default onboarding fields for existing coaches

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_seed_default_onboarding_fields.php`

**Step 1: Create migration**

```bash
php artisan make:migration seed_default_onboarding_fields --no-interaction
```

**Step 2: Write the migration**

This migration seeds default onboarding fields for all existing coaches. The default fields match the current hardcoded onboarding form.

```php
use App\Models\OnboardingField;
use App\Models\User;

public function up(): void
{
    $coaches = User::where('role', 'coach')->get();

    foreach ($coaches as $coach) {
        $this->seedDefaultFields($coach->id);
    }
}

private function seedDefaultFields(int $coachId): void
{
    $fields = [
        [
            'label' => 'What is your primary goal?',
            'type' => 'select',
            'options' => ['Fat Loss', 'Build Strength', 'General Fitness'],
            'is_required' => true,
            'order' => 1,
        ],
        [
            'label' => 'What is your experience level?',
            'type' => 'select',
            'options' => ['Beginner', 'Intermediate', 'Advanced'],
            'is_required' => true,
            'order' => 2,
        ],
        [
            'label' => 'Any injuries or limitations?',
            'type' => 'textarea',
            'options' => null,
            'is_required' => false,
            'order' => 3,
        ],
        [
            'label' => 'What equipment do you have access to?',
            'type' => 'textarea',
            'options' => null,
            'is_required' => false,
            'order' => 4,
        ],
    ];

    foreach ($fields as $field) {
        OnboardingField::create(array_merge($field, ['coach_id' => $coachId]));
    }
}
```

**Step 3: Run migration**

```bash
php artisan migrate
```

**Step 4: Commit**

```bash
git add -A && git commit -m "feat: seed default onboarding fields for existing coaches"
```

---

### Task 5: Update UserFactory with coach/client states

**Files:**
- Modify: `database/factories/UserFactory.php`

**Step 1: Add factory states**

Add these methods to `UserFactory`:

```php
public function coach(): static
{
    return $this->state(fn (array $attributes) => [
        'role' => 'coach',
    ]);
}

public function client(): static
{
    return $this->state(fn (array $attributes) => [
        'role' => 'client',
    ]);
}
```

**Step 2: Create OnboardingField factory**

```bash
php artisan make:factory OnboardingFieldFactory --no-interaction
```

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OnboardingFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'coach_id' => User::factory()->coach(),
            'label' => fake()->sentence(4),
            'type' => fake()->randomElement(['text', 'select', 'textarea']),
            'options' => null,
            'is_required' => true,
            'order' => fake()->numberBetween(1, 10),
        ];
    }

    public function select(array $options = ['Option A', 'Option B', 'Option C']): static
    {
        return $this->state(fn () => [
            'type' => 'select',
            'options' => $options,
        ]);
    }

    public function optional(): static
    {
        return $this->state(fn () => [
            'is_required' => false,
        ]);
    }
}
```

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: add coach/client factory states and OnboardingField factory"
```

---

### Task 6: Coach Branding Controller + FormRequest + Routes

**Files:**
- Create: `app/Http/Controllers/Coach/BrandingController.php`
- Create: `app/Http/Requests/UpdateBrandingRequest.php`
- Modify: `routes/web.php`

**Step 1: Write the test**

Create: `tests/Feature/Coach/BrandingTest.php`

```bash
php artisan make:test Coach/BrandingTest --pest --no-interaction
```

```php
<?php

use App\Models\OnboardingField;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->actingAs($this->coach);
});

test('coach can view branding page', function () {
    $this->get(route('coach.branding.edit'))
        ->assertOk()
        ->assertViewIs('coach.branding');
});

test('coach can update branding identity', function () {
    $this->put(route('coach.branding.update'), [
        'gym_name' => 'Iron Forge Gym',
        'description' => 'Best gym in town',
        'primary_color' => '#FF5733',
        'secondary_color' => '#33FF57',
        'onboarding_welcome_text' => 'Welcome to the team!',
        'welcome_email_text' => 'Thanks for joining us!',
        'fields' => [],
    ])->assertRedirect(route('coach.branding.edit'));

    $this->coach->refresh();
    expect($this->coach->gym_name)->toBe('Iron Forge Gym');
    expect($this->coach->description)->toBe('Best gym in town');
    expect($this->coach->primary_color)->toBe('#FF5733');
    expect($this->coach->secondary_color)->toBe('#33FF57');
});

test('coach can upload logo', function () {
    Storage::fake('public');

    $this->put(route('coach.branding.update'), [
        'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        'fields' => [],
    ])->assertRedirect(route('coach.branding.edit'));

    $this->coach->refresh();
    expect($this->coach->logo)->not->toBeNull();
    Storage::disk('public')->assertExists($this->coach->logo);
});

test('coach can remove logo', function () {
    $this->coach->update(['logo' => 'logos/old.png']);

    $this->put(route('coach.branding.update'), [
        'remove_logo' => '1',
        'fields' => [],
    ])->assertRedirect(route('coach.branding.edit'));

    $this->coach->refresh();
    expect($this->coach->logo)->toBeNull();
});

test('coach can save onboarding fields', function () {
    $this->put(route('coach.branding.update'), [
        'fields' => [
            ['label' => 'Your goal?', 'type' => 'select', 'options' => "Lose weight\nBuild muscle", 'is_required' => '1'],
            ['label' => 'Tell us about yourself', 'type' => 'textarea', 'options' => '', 'is_required' => '0'],
        ],
    ])->assertRedirect(route('coach.branding.edit'));

    expect($this->coach->onboardingFields)->toHaveCount(2);
    expect($this->coach->onboardingFields->first()->label)->toBe('Your goal?');
    expect($this->coach->onboardingFields->first()->options)->toBe(['Lose weight', 'Build muscle']);
    expect($this->coach->onboardingFields->last()->is_required)->toBeFalse();
});

test('coach can reorder onboarding fields', function () {
    $this->put(route('coach.branding.update'), [
        'fields' => [
            ['label' => 'Second', 'type' => 'text', 'options' => '', 'is_required' => '1'],
            ['label' => 'First', 'type' => 'text', 'options' => '', 'is_required' => '1'],
        ],
    ])->assertRedirect(route('coach.branding.edit'));

    $fields = $this->coach->onboardingFields()->orderBy('order')->get();
    expect($fields->first()->label)->toBe('Second');
    expect($fields->last()->label)->toBe('First');
});

test('clients cannot access branding page', function () {
    $client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    $this->actingAs($client);

    $this->get(route('coach.branding.edit'))->assertStatus(403);
});

test('branding validates color format', function () {
    $this->put(route('coach.branding.update'), [
        'primary_color' => 'not-a-color',
        'fields' => [],
    ])->assertSessionHasErrors('primary_color');
});
```

**Step 2: Run tests to see them fail**

```bash
php artisan test --compact --filter=BrandingTest
```

**Step 3: Create FormRequest**

```bash
php artisan make:request UpdateBrandingRequest --no-interaction
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isCoach();
    }

    public function rules(): array
    {
        return [
            'gym_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'onboarding_welcome_text' => ['nullable', 'string', 'max:5000'],
            'welcome_email_text' => ['nullable', 'string', 'max:5000'],
            'fields' => ['present', 'array'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.type' => ['required', 'string', 'in:text,select,textarea'],
            'fields.*.options' => ['nullable', 'string'],
            'fields.*.is_required' => ['nullable'],
        ];
    }
}
```

**Step 4: Create BrandingController**

```bash
php artisan make:controller Coach/BrandingController --no-interaction
```

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBrandingRequest;
use App\Models\OnboardingField;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BrandingController extends Controller
{
    public function edit(): View
    {
        $coach = auth()->user();
        $fields = $coach->onboardingFields()->orderBy('order')->get();

        return view('coach.branding', compact('coach', 'fields'));
    }

    public function update(UpdateBrandingRequest $request): RedirectResponse
    {
        $coach = auth()->user();
        $validated = $request->validated();

        // Update branding fields
        $coach->update([
            'gym_name' => $validated['gym_name'] ?? $coach->gym_name,
            'description' => $validated['description'] ?? null,
            'primary_color' => $validated['primary_color'] ?? null,
            'secondary_color' => $validated['secondary_color'] ?? null,
            'onboarding_welcome_text' => $validated['onboarding_welcome_text'] ?? null,
            'welcome_email_text' => $validated['welcome_email_text'] ?? null,
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($coach->logo) {
                Storage::disk('public')->delete($coach->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $coach->update(['logo' => $path]);
        } elseif ($request->boolean('remove_logo') && $coach->logo) {
            Storage::disk('public')->delete($coach->logo);
            $coach->update(['logo' => null]);
        }

        // Sync onboarding fields
        $this->syncOnboardingFields($coach, $validated['fields'] ?? []);

        return redirect()->route('coach.branding.edit')
            ->with('success', 'Branding updated successfully.');
    }

    private function syncOnboardingFields($coach, array $fields): void
    {
        // Delete all existing fields and recreate
        $coach->onboardingFields()->delete();

        foreach ($fields as $index => $fieldData) {
            $options = null;
            if ($fieldData['type'] === 'select' && ! empty($fieldData['options'])) {
                $options = array_values(array_filter(
                    array_map('trim', explode("\n", $fieldData['options']))
                ));
            }

            OnboardingField::create([
                'coach_id' => $coach->id,
                'label' => $fieldData['label'],
                'type' => $fieldData['type'],
                'options' => $options,
                'is_required' => (bool) ($fieldData['is_required'] ?? false),
                'order' => $index + 1,
            ]);
        }
    }
}
```

**Step 5: Add routes** in `routes/web.php` inside the coach group:

```php
// Branding
Route::get('branding', [Coach\BrandingController::class, 'edit'])->name('branding.edit');
Route::put('branding', [Coach\BrandingController::class, 'update'])->name('branding.update');
```

**Step 6: Run tests**

```bash
php artisan test --compact --filter=BrandingTest
```

**Step 7: Commit**

```bash
git add -A && git commit -m "feat: add coach branding controller, form request, and routes"
```

---

### Task 7: Coach Branding View

**Files:**
- Create: `resources/views/coach/branding.blade.php`

**Step 1: Create the branding view**

Follow the pattern from `coach/tracking-metrics/index.blade.php`. The view has 4 card sections in a single `<form>`.

The onboarding fields section uses Alpine.js `x-data` to manage a dynamic fields array. Each field row has: label input, type select, options textarea (shown for select type only), required toggle, move up/down buttons, remove button. "Add Field" button at bottom.

Fields are submitted as `fields[0][label]`, `fields[0][type]`, etc.

Color inputs use `<input type="color">` paired with a text input showing the hex value, synced via Alpine.

Logo section: file input with preview of current logo and a "Remove" checkbox.

**Key UI details:**
- Use `enctype="multipart/form-data"` on the form (for logo upload)
- Use `@method('PUT')` for the update route
- Use existing button/input styling patterns from tracking metrics view

**Step 2: Run tests**

```bash
php artisan test --compact --filter=BrandingTest
```

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: add coach branding view with field builder"
```

---

### Task 8: Add Branding link to coach sidebar

**Files:**
- Modify: `resources/views/components/layouts/coach.blade.php`

**Step 1: Add nav link**

Add a "Branding" link in the sidebar nav (both desktop and mobile sections), after Messages. Use a paint brush or palette SVG icon. Follow existing link pattern with `request()->routeIs('coach.branding.*')` for active state.

**Step 2: Commit**

```bash
git add -A && git commit -m "feat: add branding link to coach sidebar navigation"
```

---

### Task 9: Apply CSS custom properties theming to layouts

**Files:**
- Modify: `resources/views/components/layouts/coach.blade.php`
- Modify: `resources/views/components/layouts/client.blade.php`

**Step 1: Write the test**

Create: `tests/Feature/Coach/BrandingThemeTest.php`

```php
<?php

use App\Models\User;

test('client layout includes coach branding colors as CSS variables', function () {
    $coach = User::factory()->coach()->create([
        'primary_color' => '#FF0000',
        'secondary_color' => '#00FF00',
    ]);
    $client = User::factory()->client()->create(['coach_id' => $coach->id]);

    $this->actingAs($client)
        ->get(route('client.dashboard'))
        ->assertOk()
        ->assertSee('--color-primary: #FF0000')
        ->assertSee('--color-secondary: #00FF00');
});

test('coach layout includes own branding colors as CSS variables', function () {
    $coach = User::factory()->coach()->create([
        'primary_color' => '#FF0000',
        'secondary_color' => '#00FF00',
    ]);

    $this->actingAs($coach)
        ->get(route('coach.dashboard'))
        ->assertOk()
        ->assertSee('--color-primary: #FF0000');
});

test('layouts fall back to default blue when no colors set', function () {
    $coach = User::factory()->coach()->create();
    $client = User::factory()->client()->create(['coach_id' => $coach->id]);

    $this->actingAs($client)
        ->get(route('client.dashboard'))
        ->assertOk()
        ->assertSee('--color-primary: #2563EB');
});
```

**Step 2: Inject CSS variables in coach layout**

In `<head>` of `coach.blade.php`, add:

```blade
<style>
    :root {
        --color-primary: {{ auth()->user()->primary_color ?? '#2563EB' }};
        --color-secondary: {{ auth()->user()->secondary_color ?? '#1E40AF' }};
    }
</style>
```

**Step 3: Inject CSS variables in client layout**

In `<head>` of `client.blade.php`, add:

```blade
@php $brandingCoach = auth()->user()->coach; @endphp
<style>
    :root {
        --color-primary: {{ $brandingCoach?->primary_color ?? '#2563EB' }};
        --color-secondary: {{ $brandingCoach?->secondary_color ?? '#1E40AF' }};
    }
</style>
```

**Step 4: Replace hardcoded blue classes**

In both layouts, replace key accent colors:
- `bg-blue-600` → `style="background-color: var(--color-primary)"`
- `text-blue-600` → `style="color: var(--color-primary)"`
- `bg-blue-50` → use a lighter variant or keep structural colors as-is
- Active nav items: `bg-blue-50 text-blue-700` → use inline styles with CSS vars

Note: For the nav active states, use Tailwind's arbitrary value syntax where possible, or inline styles. The key touchpoints are:
- Coach sidebar: active nav item background/text color, user avatar circle
- Client layout: active bottom tab text color, top header

**Step 5: Update logo/title in both layouts**

Coach layout header (desktop + mobile):
```blade
@if(auth()->user()->logo)
    <img src="{{ Storage::url(auth()->user()->logo) }}" alt="{{ auth()->user()->gym_name }}" class="h-8">
@else
    <span class="text-xl font-bold text-gray-900">{{ auth()->user()->gym_name ?? 'LiftDeck' }}</span>
@endif
```

Client layout header:
```blade
@if($brandingCoach?->logo)
    <img src="{{ Storage::url($brandingCoach->logo) }}" alt="{{ $brandingCoach->gym_name }}" class="h-8">
@else
    <span class="text-lg font-semibold text-gray-900">{{ $brandingCoach?->gym_name ?? 'My Training' }}</span>
@endif
```

**Step 6: Run tests**

```bash
php artisan test --compact --filter=BrandingThemeTest
```

**Step 7: Commit**

```bash
git add -A && git commit -m "feat: apply coach branding colors and logo to layouts"
```

---

### Task 10: Update onboarding flow — dynamic fields

**Files:**
- Modify: `app/Http/Controllers/Client/OnboardingController.php`
- Modify: `resources/views/client/welcome.blade.php`
- Modify: `resources/views/client/onboarding.blade.php`

**Step 1: Write the test**

Create: `tests/Feature/Client/OnboardingBrandingTest.php`

```php
<?php

use App\Models\OnboardingField;
use App\Models\OnboardingResponse;
use App\Models\User;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create([
        'onboarding_welcome_text' => 'Welcome to my gym!',
    ]);
    $this->client = User::factory()->client()->create(['coach_id' => $this->coach->id]);
    $this->actingAs($this->client);
});

test('welcome page shows coach onboarding welcome text', function () {
    $this->get(route('client.welcome'))
        ->assertOk()
        ->assertSee('Welcome to my gym!');
});

test('welcome page shows default text when coach has no custom text', function () {
    $this->coach->update(['onboarding_welcome_text' => null]);

    $this->get(route('client.welcome'))
        ->assertOk()
        ->assertSee('set up your profile');
});

test('onboarding form renders dynamic fields', function () {
    OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'What is your goal?',
        'type' => 'select',
        'options' => ['Lose weight', 'Build muscle'],
        'order' => 1,
    ]);
    OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'Any injuries?',
        'type' => 'textarea',
        'order' => 2,
        'is_required' => false,
    ]);

    $this->get(route('client.onboarding'))
        ->assertOk()
        ->assertSee('What is your goal?')
        ->assertSee('Lose weight')
        ->assertSee('Any injuries?');
});

test('onboarding stores responses to dynamic fields', function () {
    $field1 = OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'Goal',
        'type' => 'select',
        'options' => ['Lose weight', 'Build muscle'],
        'is_required' => true,
        'order' => 1,
    ]);
    $field2 = OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'Notes',
        'type' => 'textarea',
        'is_required' => false,
        'order' => 2,
    ]);

    $this->post(route('client.onboarding.store'), [
        'fields' => [
            $field1->id => 'Lose weight',
            $field2->id => 'Bad knee',
        ],
    ])->assertRedirect(route('client.dashboard'));

    expect(OnboardingResponse::where('client_id', $this->client->id)->count())->toBe(2);
    expect(OnboardingResponse::where('onboarding_field_id', $field1->id)->first()->value)->toBe('Lose weight');
});

test('onboarding validates required fields', function () {
    $field = OnboardingField::factory()->create([
        'coach_id' => $this->coach->id,
        'label' => 'Goal',
        'type' => 'text',
        'is_required' => true,
        'order' => 1,
    ]);

    $this->post(route('client.onboarding.store'), [
        'fields' => [
            $field->id => '',
        ],
    ])->assertSessionHasErrors("fields.{$field->id}");
});
```

**Step 2: Run tests to see them fail**

```bash
php artisan test --compact --filter=OnboardingBrandingTest
```

**Step 3: Update OnboardingController**

```php
public function welcome(): View
{
    $coach = auth()->user()->coach;

    return view('client.welcome', [
        'coach' => $coach,
    ]);
}

public function show(): View
{
    $coach = auth()->user()->coach;
    $fields = $coach->onboardingFields()->orderBy('order')->get();

    return view('client.onboarding', compact('fields'));
}

public function store(Request $request): RedirectResponse
{
    $user = auth()->user();
    $coach = $user->coach;
    $fields = $coach->onboardingFields()->get();

    // Build validation rules dynamically
    $rules = [];
    foreach ($fields as $field) {
        $fieldRules = [];
        if ($field->is_required) {
            $fieldRules[] = 'required';
        } else {
            $fieldRules[] = 'nullable';
        }
        $fieldRules[] = 'string';
        $fieldRules[] = 'max:2000';

        if ($field->type === 'select' && $field->options) {
            $fieldRules[] = 'in:' . implode(',', $field->options);
        }

        $rules["fields.{$field->id}"] = $fieldRules;
    }

    $validated = $request->validate($rules);

    // Save responses
    foreach ($fields as $field) {
        $value = $validated['fields'][$field->id] ?? null;
        if ($value !== null) {
            OnboardingResponse::updateOrCreate(
                ['client_id' => $user->id, 'onboarding_field_id' => $field->id],
                ['value' => $value]
            );
        }
    }

    // Mark onboarding complete
    ClientProfile::updateOrCreate(
        ['user_id' => $user->id],
        ['onboarding_completed_at' => now()]
    );

    return redirect()->route('client.dashboard')
        ->with('success', 'Welcome! Your profile has been set up.');
}
```

**Step 4: Update welcome.blade.php**

Replace the hardcoded paragraph:
```blade
<p class="text-sm text-gray-500 mb-8">
    @if($coach->onboarding_welcome_text)
        {{ $coach->onboarding_welcome_text }}
    @else
        Let's set up your profile so your coach can create the perfect program for you.
    @endif
</p>
```

**Step 5: Rewrite onboarding.blade.php**

Replace the 4 hardcoded fields with a dynamic loop:

```blade
<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tell us about yourself</h1>
        <p class="mt-2 text-sm text-gray-600">This helps your coach create the perfect program for you.</p>
    </div>

    <form method="POST" action="{{ route('client.onboarding.store') }}">
        @csrf

        @foreach($fields as $field)
            <div class="mb-6">
                <x-input-label :for="'field_' . $field->id" :value="$field->label . ($field->is_required ? '' : ' (optional)')" />

                @if($field->type === 'select')
                    <div class="mt-2 space-y-2">
                        @foreach($field->options as $option)
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="fields[{{ $field->id }}]" value="{{ $option }}"
                                    class="focus:ring-blue-500" style="color: var(--color-primary, #2563EB)"
                                    {{ old("fields.{$field->id}") === $option ? 'checked' : '' }}>
                                <span class="ml-3 font-medium text-gray-900">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($field->type === 'textarea')
                    <textarea id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old("fields.{$field->id}") }}</textarea>
                @else
                    <x-text-input :id="'field_' . $field->id" name="fields[{{ $field->id }}]"
                        class="block mt-1 w-full" type="text" :value="old('fields.' . $field->id)" />
                @endif

                <x-input-error :messages="$errors->get('fields.' . $field->id)" class="mt-2" />
            </div>
        @endforeach

        <div class="flex items-center justify-between mt-8">
            <form method="POST" action="{{ route('client.onboarding.skip') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                    Skip for now
                </button>
            </form>

            <x-primary-button>
                {{ __('Complete Setup') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
```

**Step 6: Run tests**

```bash
php artisan test --compact --filter=OnboardingBrandingTest
```

**Step 7: Commit**

```bash
git add -A && git commit -m "feat: dynamic onboarding fields with coach welcome text"
```

---

### Task 11: Welcome email — Mailable + template

**Files:**
- Create: `app/Mail/WelcomeClientMail.php`
- Create: `resources/views/mail/welcome-client.blade.php`
- Modify: `app/Http/Controllers/Auth/ClientRegistrationController.php`

**Step 1: Write the test**

Create: `tests/Feature/Auth/WelcomeEmailTest.php`

```php
<?php

use App\Mail\WelcomeClientMail;
use App\Models\ClientInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('welcome email is sent after client registration', function () {
    Mail::fake();

    $coach = User::factory()->coach()->create([
        'gym_name' => 'Iron Forge',
        'welcome_email_text' => 'Glad to have you!',
    ]);

    $invitation = ClientInvitation::create([
        'coach_id' => $coach->id,
        'token' => 'TESTCODE',
        'expires_at' => now()->addDays(7),
    ]);

    $this->post(route('join.register'), [
        'code' => 'TESTCODE',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('client.welcome'));

    Mail::assertSent(WelcomeClientMail::class, function ($mail) {
        return $mail->hasTo('john@example.com');
    });
});

test('welcome email contains coach branding', function () {
    $coach = User::factory()->coach()->create([
        'gym_name' => 'Iron Forge',
        'welcome_email_text' => 'Glad to have you on board!',
        'primary_color' => '#FF5733',
    ]);

    $client = User::factory()->client()->create(['coach_id' => $coach->id]);

    $mail = new WelcomeClientMail($client, $coach);
    $rendered = $mail->render();

    expect($rendered)->toContain('Iron Forge');
    expect($rendered)->toContain('Glad to have you on board!');
});
```

**Step 2: Run tests to see them fail**

```bash
php artisan test --compact --filter=WelcomeEmailTest
```

**Step 3: Create the Mailable**

```bash
php artisan make:mail WelcomeClientMail --no-interaction
```

```php
<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $client,
        public User $coach,
    ) {}

    public function envelope(): Envelope
    {
        $gymName = $this->coach->gym_name ?? $this->coach->name;

        return new Envelope(
            subject: "Welcome to {$gymName}!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.welcome-client',
        );
    }
}
```

**Step 4: Create email template**

`resources/views/mail/welcome-client.blade.php` — A simple branded HTML email with:
- Coach's logo (if set) or gym name as header
- Coach's `primary_color` for the header bar and button
- Coach's `welcome_email_text` as the body (fallback to generic welcome)
- A "Get Started" button linking to login

**Step 5: Send email after registration**

In `ClientRegistrationController@register`, after `Auth::login($user)`, add:

```php
use App\Mail\WelcomeClientMail;
use Illuminate\Support\Facades\Mail;

Mail::to($user)->send(new WelcomeClientMail($user, $invitation->coach));
```

**Step 6: Run tests**

```bash
php artisan test --compact --filter=WelcomeEmailTest
```

**Step 7: Commit**

```bash
git add -A && git commit -m "feat: send branded welcome email after client registration"
```

---

### Task 12: Update coach client detail to show onboarding responses

**Files:**
- Modify: `app/Http/Controllers/Coach/ClientController.php` (show method)
- Modify: `resources/views/coach/clients/show.blade.php`

**Step 1: Load onboarding responses in ClientController@show**

Add to the `$client->load()` call:
```php
'onboardingResponses.onboardingField'
```

**Step 2: Update the client show view**

Replace or supplement the existing onboarding data section with dynamic responses from the `onboarding_responses` relationship, showing field label → value pairs.

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: show dynamic onboarding responses on coach client detail"
```

---

### Task 13: Run full test suite and fix

**Step 1: Run all tests**

```bash
php artisan test --compact
```

**Step 2: Fix any failures**

**Step 3: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 4: Final commit**

```bash
git add -A && git commit -m "chore: fix formatting and test issues"
```

---

## Dependency Graph

```
Task 1 (users migration) ──┐
Task 2 (onboarding_fields) ─┤
Task 3 (onboarding_responses)┤
                             ├─→ Task 4 (seed defaults) ──→ Task 5 (factories)
                             │
                             ├─→ Task 6 (controller/routes) ──→ Task 7 (view) ──→ Task 8 (sidebar link)
                             │
                             ├─→ Task 9 (CSS theming)
                             │
                             ├─→ Task 10 (onboarding flow)
                             │
                             ├─→ Task 11 (welcome email)
                             │
                             └─→ Task 12 (client detail) ──→ Task 13 (final test suite)
```

Tasks 1-3 must run first (sequentially). After that, Tasks 4-5 can run, followed by Tasks 6-12 which mostly depend on models/migrations being in place. Task 13 runs last.
