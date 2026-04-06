# User Settings Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a Settings page for both coaches and clients where they can update name, email, phone, bio, avatar, and password.

**Architecture:** Single `SettingsController` (top-level namespace, alongside `ProfileController`) handles both roles — `edit()` returns the role-appropriate view, `update()` saves profile fields + avatar, `updatePassword()` changes password. Routes added to both the coach and client route groups in `web.php`. Avatar uses the same `Storage` pattern as the existing `logo` field.

**Tech Stack:** Laravel 12, Livewire-free (plain Blade + Alpine.js), Tailwind CSS v3, Pest 4

---

### Task 1: Add `avatar` Attribute accessor to User model

**Files:**
- Modify: `app/Models/User.php`

**Step 1: Add the accessor method**

In `app/Models/User.php`, after the `logo(): Attribute` method (around line 74), add:

```php
public function avatar(): Attribute
{
    return Attribute::make(
        get: fn (?string $value) => $value ? Storage::temporaryUrl($value, now()->addDay()) : null
    );
}
```

**Step 2: Run pint**

```bash
vendor/bin/pint app/Models/User.php --format agent
```

**Step 3: Commit**

```bash
git add app/Models/User.php
git commit -m "feat: add avatar attribute accessor to User model"
```

---

### Task 2: Create `UpdateSettingsRequest`

**Files:**
- Create: `app/Http/Requests/UpdateSettingsRequest.php`

**Step 1: Generate the file**

```bash
php artisan make:request UpdateSettingsRequest --no-interaction
```

**Step 2: Write the rules**

Replace the contents of `app/Http/Requests/UpdateSettingsRequest.php`:

```php
<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
```

**Step 3: Run pint**

```bash
vendor/bin/pint app/Http/Requests/UpdateSettingsRequest.php --format agent
```

**Step 4: Commit**

```bash
git add app/Http/Requests/UpdateSettingsRequest.php
git commit -m "feat: add UpdateSettingsRequest"
```

---

### Task 3: Write failing tests for SettingsController

**Files:**
- Create: `tests/Feature/SettingsTest.php`

**Step 1: Generate test file**

```bash
php artisan make:test SettingsTest --pest --no-interaction
```

**Step 2: Write the tests**

Replace the contents of `tests/Feature/SettingsTest.php`:

```php
<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->coach = User::factory()->coach()->create();
    $this->client = User::factory()->create([
        'role' => 'client',
        'coach_id' => $this->coach->id,
    ]);
});

// --- Coach ---

test('coach can view settings page', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.settings.edit'))
        ->assertOk()
        ->assertViewIs('coach.settings.edit');
});

test('coach can update profile', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.settings.update'), [
            'name' => 'Jane Coach',
            'email' => 'jane@example.com',
            'phone' => '+1 555-0100',
            'bio' => 'Certified personal trainer.',
        ])
        ->assertRedirect(route('coach.settings.edit'));

    $this->coach->refresh();
    expect($this->coach->name)->toBe('Jane Coach');
    expect($this->coach->email)->toBe('jane@example.com');
    expect($this->coach->phone)->toBe('+1 555-0100');
    expect($this->coach->bio)->toBe('Certified personal trainer.');
});

test('coach can upload avatar', function () {
    Storage::fake();

    $this->actingAs($this->coach)
        ->put(route('coach.settings.update'), [
            'name' => $this->coach->name,
            'email' => $this->coach->email,
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 200, 200),
        ])
        ->assertRedirect(route('coach.settings.edit'));

    $this->coach->refresh();
    expect($this->coach->getRawOriginal('avatar'))->not->toBeNull();
});

test('coach can remove avatar', function () {
    Storage::fake();
    $this->coach->update(['avatar' => 'avatars/old.jpg']);

    $this->actingAs($this->coach)
        ->put(route('coach.settings.update'), [
            'name' => $this->coach->name,
            'email' => $this->coach->email,
            'remove_avatar' => '1',
        ])
        ->assertRedirect(route('coach.settings.edit'));

    $this->coach->refresh();
    expect($this->coach->getRawOriginal('avatar'))->toBeNull();
});

test('coach can update password', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.settings.password'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertRedirect(route('coach.settings.edit'));

    expect(Hash::check('new-password-123', $this->coach->fresh()->password))->toBeTrue();
});

test('coach cannot update password with wrong current password', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.settings.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasErrors('current_password');
});

test('coach settings update requires valid email', function () {
    $this->actingAs($this->coach)
        ->put(route('coach.settings.update'), [
            'name' => 'Jane',
            'email' => 'not-an-email',
        ])
        ->assertSessionHasErrors('email');
});

// --- Client ---

test('client can view settings page', function () {
    $this->actingAs($this->client)
        ->get(route('client.settings.edit'))
        ->assertOk()
        ->assertViewIs('client.settings.edit');
});

test('client can update profile', function () {
    $this->actingAs($this->client)
        ->put(route('client.settings.update'), [
            'name' => 'John Client',
            'email' => 'john@example.com',
            'phone' => '+1 555-0200',
            'bio' => 'Fitness enthusiast.',
        ])
        ->assertRedirect(route('client.settings.edit'));

    $this->client->refresh();
    expect($this->client->name)->toBe('John Client');
    expect($this->client->email)->toBe('john@example.com');
    expect($this->client->phone)->toBe('+1 555-0200');
    expect($this->client->bio)->toBe('Fitness enthusiast.');
});

test('client can update password', function () {
    $this->actingAs($this->client)
        ->put(route('client.settings.password'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertRedirect(route('client.settings.edit'));

    expect(Hash::check('new-password-123', $this->client->fresh()->password))->toBeTrue();
});

test('guest cannot access coach settings', function () {
    $this->get(route('coach.settings.edit'))->assertRedirect(route('login'));
});

test('guest cannot access client settings', function () {
    $this->get(route('client.settings.edit'))->assertRedirect(route('login'));
});
```

**Step 3: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=SettingsTest
```

Expected: FAIL — routes not found.

**Step 4: Commit**

```bash
git add tests/Feature/SettingsTest.php
git commit -m "test: add failing tests for user settings"
```

---

### Task 4: Create `SettingsController` and routes

**Files:**
- Create: `app/Http/Controllers/SettingsController.php`
- Modify: `routes/web.php`

**Step 1: Generate the controller**

```bash
php artisan make:controller SettingsController --no-interaction
```

**Step 2: Implement the controller**

Replace the contents of `app/Http/Controllers/SettingsController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function editCoach(): View
    {
        return view('coach.settings.edit', ['user' => auth()->user()]);
    }

    public function editClient(): View
    {
        return view('client.settings.edit', ['user' => auth()->user()]);
    }

    public function update(UpdateSettingsRequest $request, string $role): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($request->hasFile('avatar')) {
            if ($user->getRawOriginal('avatar')) {
                Storage::delete($user->getRawOriginal('avatar'));
            }
            $path = $request->file('avatar')->store('avatars');
            $user->update(['avatar' => $path]);
        } elseif ($request->boolean('remove_avatar') && $user->getRawOriginal('avatar')) {
            Storage::delete($user->getRawOriginal('avatar'));
            $user->update(['avatar' => null]);
        }

        $redirectRoute = $role === 'coach' ? 'coach.settings.edit' : 'client.settings.edit';

        return redirect()->route($redirectRoute)->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request, string $role): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $redirectRoute = $role === 'coach' ? 'coach.settings.edit' : 'client.settings.edit';

        return redirect()->route($redirectRoute)->with('status', 'password-updated');
    }
}
```

**Step 3: Add routes to `routes/web.php`**

In the coach route group (after the branding routes, around line 102), add:

```php
Route::get('settings', [App\Http\Controllers\SettingsController::class, 'editCoach'])->name('settings.edit');
Route::put('settings', fn (App\Http\Requests\UpdateSettingsRequest $request) => app(App\Http\Controllers\SettingsController::class)->update($request, 'coach'))->name('settings.update');
Route::put('settings/password', fn (Illuminate\Http\Request $request) => app(App\Http\Controllers\SettingsController::class)->updatePassword($request, 'coach'))->name('settings.password');
```

In the client route group (after existing routes, around line 138), add:

```php
Route::get('settings', [App\Http\Controllers\SettingsController::class, 'editClient'])->name('settings.edit');
Route::put('settings', fn (App\Http\Requests\UpdateSettingsRequest $request) => app(App\Http\Controllers\SettingsController::class)->update($request, 'client'))->name('settings.update');
Route::put('settings/password', fn (Illuminate\Http\Request $request) => app(App\Http\Controllers\SettingsController::class)->updatePassword($request, 'client'))->name('settings.password');
```

**Step 4: Run pint**

```bash
vendor/bin/pint app/Http/Controllers/SettingsController.php routes/web.php --format agent
```

**Step 5: Run tests (should still fail — views missing)**

```bash
php artisan test --compact --filter=SettingsTest
```

Expected: FAIL — view not found.

**Step 6: Commit**

```bash
git add app/Http/Controllers/SettingsController.php routes/web.php
git commit -m "feat: add SettingsController and settings routes"
```

---

### Task 5: Create coach settings view

**Files:**
- Create: `resources/views/coach/settings/edit.blade.php`

**Step 1: Create the view**

```bash
mkdir -p resources/views/coach/settings
```

Create `resources/views/coach/settings/edit.blade.php`:

```blade
<x-layouts.coach>
    <x-slot:title>Settings</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Settings</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your account information and password.</p>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">Profile updated successfully.</p>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">Password updated successfully.</p>
            </div>
        @endif

        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Profile</h2>

            <form method="POST" action="{{ route('coach.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xl font-semibold text-gray-500 dark:text-gray-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            <input type="file" name="avatar" accept="image/*" class="text-sm text-gray-600 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-100 dark:file:bg-gray-800 file:text-gray-700 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-700">
                            @if($user->avatar)
                                <label class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 cursor-pointer">
                                    <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    Remove photo
                                </label>
                            @endif
                        </div>
                    </div>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bio <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea id="bio" name="bio" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Save
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Card -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Change Password</h2>

            <form method="POST" action="{{ route('coach.settings.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.coach>
```

**Step 2: Run tests**

```bash
php artisan test --compact --filter=SettingsTest
```

Expected: Coach tests pass, client tests still fail (view missing).

**Step 3: Commit**

```bash
git add resources/views/coach/settings/edit.blade.php
git commit -m "feat: add coach settings view"
```

---

### Task 6: Create client settings view

**Files:**
- Create: `resources/views/client/settings/edit.blade.php`

**Step 1: Create the view**

```bash
mkdir -p resources/views/client/settings
```

Create `resources/views/client/settings/edit.blade.php` — same structure as coach but using `<x-layouts.client>`, client route names, and with a "Sign out" button at the bottom:

```blade
<x-layouts.client>
    <x-slot:title>Settings</x-slot:title>

    <div class="space-y-6 py-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Settings</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your account information and password.</p>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">Profile updated successfully.</p>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">Password updated successfully.</p>
            </div>
        @endif

        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Profile</h2>

            <form method="POST" action="{{ route('client.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xl font-semibold text-gray-500 dark:text-gray-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            <input type="file" name="avatar" accept="image/*" class="text-sm text-gray-600 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-100 dark:file:bg-gray-800 file:text-gray-700 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-700">
                            @if($user->avatar)
                                <label class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 cursor-pointer">
                                    <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    Remove photo
                                </label>
                            @endif
                        </div>
                    </div>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bio <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea id="bio" name="bio" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Save
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Card -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Change Password</h2>

            <form method="POST" action="{{ route('client.settings.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Sign Out -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Sign Out</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Sign out of your account on this device.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</x-layouts.client>
```

**Step 2: Run all settings tests**

```bash
php artisan test --compact --filter=SettingsTest
```

Expected: All pass.

**Step 3: Commit**

```bash
git add resources/views/client/settings/edit.blade.php
git commit -m "feat: add client settings view"
```

---

### Task 7: Update coach layout — add Settings nav item

**Files:**
- Modify: `resources/views/components/layouts/coach.blade.php`

**Step 1: Add Settings link after Branding in the sidebar nav**

In the desktop sidebar nav (around line 136 after the Branding `<a>` tag), add a Settings nav item:

```blade
<a href="{{ route('coach.settings.edit') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.settings.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.settings.*') ? 'style="color: var(--color-primary)"' : '' !!}>
    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.settings.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    Settings
</a>
```

The same link also needs to appear in the **mobile menu** section of the coach layout. Find the mobile menu nav (search for `toggleMobileMenu`) and add the same Settings link there.

**Step 2: Run pint**

```bash
vendor/bin/pint resources/views/components/layouts/coach.blade.php --format agent
```

**Step 3: Commit**

```bash
git add resources/views/components/layouts/coach.blade.php
git commit -m "feat: add Settings nav item to coach sidebar"
```

---

### Task 8: Update client layout — add avatar/settings link in header

**Files:**
- Modify: `resources/views/components/layouts/client.blade.php`

**Step 1: Replace the logout button in the top header with an avatar link**

In the client layout top header (around line 72), replace the logout `<form>` button with a settings avatar link:

```blade
<a href="{{ route('client.settings.edit') }}"
    class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0"
    style="background-color: var(--color-primary)"
    aria-label="Settings">
    @if(auth()->user()->avatar)
        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover">
    @else
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    @endif
</a>
```

The logout button is removed from the header (it now lives on the settings page).

**Step 2: Run pint**

```bash
vendor/bin/pint resources/views/components/layouts/client.blade.php --format agent
```

**Step 3: Run full settings test suite**

```bash
php artisan test --compact --filter=SettingsTest
```

Expected: All pass.

**Step 4: Commit**

```bash
git add resources/views/components/layouts/client.blade.php
git commit -m "feat: add avatar settings link to client top header"
```

---

### Task 9: Final verification

**Step 1: Run all tests**

```bash
php artisan test --compact
```

Expected: All existing tests pass, no regressions.

**Step 2: Run pint on all modified files**

```bash
vendor/bin/pint --dirty --format agent
```

**Step 3: Commit any pint fixes if needed**

```bash
git add -p
git commit -m "style: apply pint formatting"
```
