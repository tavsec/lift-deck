# Phase 1: Foundation Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Set up core infrastructure - User roles, middleware, layouts, coach registration, and dashboard shells.

**Architecture:** Single User model with role column (coach/client). Role-based middleware for route protection. Separate Blade layouts for coach and client portals using BladewindUI components.

**Tech Stack:** Laravel 12, BladewindUI 3.3, Livewire, Tailwind CSS v4

---

## Task 1: Add Role and Profile Fields to Users Table

**Files:**
- Create: `database/migrations/xxxx_add_role_and_profile_fields_to_users_table.php`
- Modify: `app/Models/User.php`

**Step 1: Create migration**

```bash
php artisan make:migration add_role_and_profile_fields_to_users_table --table=users
```

**Step 2: Edit migration file**

Replace the migration content with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['coach', 'client'])->default('coach')->after('email');
            $table->foreignId('coach_id')->nullable()->after('role')->constrained('users')->nullOnDelete();
            $table->string('phone')->nullable()->after('coach_id');
            $table->text('bio')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('bio');
            // Coach-specific fields
            $table->string('gym_name')->nullable()->after('avatar');
            $table->string('logo')->nullable()->after('gym_name');
            $table->string('primary_color', 7)->nullable()->after('logo'); // hex color
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['coach_id']);
            $table->dropColumn([
                'role',
                'coach_id',
                'phone',
                'bio',
                'avatar',
                'gym_name',
                'logo',
                'primary_color',
            ]);
        });
    }
};
```

**Step 3: Run migration**

```bash
php artisan migrate
```

**Step 4: Update User model**

Edit `app/Models/User.php` - add to `$fillable` array:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'coach_id',
    'phone',
    'bio',
    'avatar',
    'gym_name',
    'logo',
    'primary_color',
];
```

Add relationships and helper methods after the `casts()` method:

```php
/**
 * Get the coach this client belongs to.
 */
public function coach(): BelongsTo
{
    return $this->belongsTo(User::class, 'coach_id');
}

/**
 * Get all clients for this coach.
 */
public function clients(): HasMany
{
    return $this->hasMany(User::class, 'coach_id');
}

public function isCoach(): bool
{
    return $this->role === 'coach';
}

public function isClient(): bool
{
    return $this->role === 'client';
}
```

Add the imports at the top of the file:

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
```

**Step 5: Commit**

```bash
git add -A && git commit -m "feat: add role and profile fields to users table

- Add role enum (coach/client) with coach default
- Add coach_id foreign key for client-coach relationship
- Add profile fields (phone, bio, avatar)
- Add coach branding fields (gym_name, logo, primary_color)
- Add isCoach/isClient helper methods

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 2: Create Role Middleware

**Files:**
- Create: `app/Http/Middleware/EnsureUserHasRole.php`
- Modify: `bootstrap/app.php`

**Step 1: Create middleware file**

```bash
php artisan make:middleware EnsureUserHasRole
```

**Step 2: Edit middleware**

Replace content of `app/Http/Middleware/EnsureUserHasRole.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            if ($request->user()?->isCoach()) {
                return redirect()->route('coach.dashboard');
            }

            if ($request->user()?->isClient()) {
                return redirect()->route('client.dashboard');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
```

**Step 3: Register middleware alias**

Edit `bootstrap/app.php` - update the `withMiddleware` callback:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    ]);
})
```

**Step 4: Commit**

```bash
git add -A && git commit -m "feat: add role-based middleware

- Create EnsureUserHasRole middleware
- Register 'role' middleware alias
- Redirect to appropriate dashboard based on user role

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 3: Install Laravel Breeze for Authentication

**Files:**
- Multiple auth-related files will be created

**Step 1: Install Breeze**

```bash
composer require laravel/breeze --dev
```

**Step 2: Install Breeze Blade stack**

```bash
php artisan breeze:install blade --no-interaction
```

**Step 3: Install npm dependencies and build**

```bash
npm install && npm run build
```

**Step 4: Run migrations (if any new)**

```bash
php artisan migrate
```

**Step 5: Commit**

```bash
git add -A && git commit -m "feat: install Laravel Breeze authentication

- Add Blade-based auth scaffolding
- Install frontend dependencies

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 4: Create Coach Layout

**Files:**
- Create: `resources/views/layouts/coach.blade.php`
- Create: `resources/views/components/coach/sidebar.blade.php`

**Step 1: Create coach layout**

Create `resources/views/layouts/coach.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Coach Dashboard' }} - {{ config('app.name', 'GymCoach') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-white border-r border-gray-200">
            <div class="flex flex-col flex-grow pt-5 overflow-y-auto">
                <!-- Logo / Brand -->
                <div class="flex items-center flex-shrink-0 px-4 mb-6">
                    <span class="text-xl font-bold text-gray-900">
                        {{ auth()->user()->gym_name ?? 'GymCoach' }}
                    </span>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-2 space-y-1">
                    <a href="{{ route('coach.dashboard') }}"
                       class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('coach.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('coach.clients.index') }}"
                       class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('coach.clients.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Clients
                    </a>

                    <a href="{{ route('coach.programs.index') }}"
                       class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('coach.programs.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Programs
                    </a>

                    <a href="{{ route('coach.exercises.index') }}"
                       class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('coach.exercises.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Exercises
                    </a>

                    <a href="{{ route('coach.messages.index') }}"
                       class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('coach.messages.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Messages
                    </a>
                </nav>

                <!-- User dropdown at bottom -->
                <div class="flex-shrink-0 p-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile header -->
        <div class="md:hidden fixed top-0 left-0 right-0 z-10 bg-white border-b border-gray-200 px-4 py-3">
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900">
                    {{ auth()->user()->gym_name ?? 'GymCoach' }}
                </span>
                <button type="button" onclick="toggleMobileMenu()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu overlay -->
        <div id="mobile-menu" class="hidden fixed inset-0 z-20 md:hidden">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" onclick="toggleMobileMenu()"></div>
            <div class="fixed inset-y-0 left-0 flex flex-col w-64 bg-white">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                    <span class="text-lg font-bold text-gray-900">Menu</span>
                    <button type="button" onclick="toggleMobileMenu()" class="text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-2 py-4 space-y-1">
                    <a href="{{ route('coach.dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">Dashboard</a>
                    <a href="{{ route('coach.clients.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">Clients</a>
                    <a href="{{ route('coach.programs.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">Programs</a>
                    <a href="{{ route('coach.exercises.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">Exercises</a>
                    <a href="{{ route('coach.messages.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-50">Messages</a>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <main class="flex-1 md:ml-64">
            <div class="py-6 px-4 sm:px-6 lg:px-8 mt-14 md:mt-0">
                {{ $slot }}
            </div>
        </main>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
```

**Step 2: Commit**

```bash
git add -A && git commit -m "feat: create coach layout with responsive sidebar

- Add coach.blade.php layout with BladewindUI
- Sidebar navigation for dashboard, clients, programs, exercises, messages
- Mobile-responsive with hamburger menu
- User dropdown with sign out

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 5: Create Client Layout

**Files:**
- Create: `resources/views/layouts/client.blade.php`

**Step 1: Create client layout**

Create `resources/views/layouts/client.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'My Training' }} - {{ config('app.name', 'GymCoach') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top navigation bar -->
    <header class="fixed top-0 left-0 right-0 z-10 bg-white border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-gray-900">
                    {{ auth()->user()->coach?->gym_name ?? 'My Training' }}
                </span>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('client.messages') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="pt-16 pb-20">
        <div class="max-w-4xl mx-auto px-4 py-6">
            {{ $slot }}
        </div>
    </main>

    <!-- Bottom navigation (mobile-first) -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200">
        <div class="max-w-4xl mx-auto px-4">
            <div class="flex items-center justify-around py-2">
                <a href="{{ route('client.dashboard') }}"
                   class="flex flex-col items-center px-3 py-2 text-xs {{ request()->routeIs('client.dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Home
                </a>

                <a href="{{ route('client.program') }}"
                   class="flex flex-col items-center px-3 py-2 text-xs {{ request()->routeIs('client.program*') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Program
                </a>

                <a href="{{ route('client.log') }}"
                   class="flex flex-col items-center px-3 py-2 text-xs {{ request()->routeIs('client.log*') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Log
                </a>

                <a href="{{ route('client.history') }}"
                   class="flex flex-col items-center px-3 py-2 text-xs {{ request()->routeIs('client.history') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    History
                </a>
            </div>
        </div>
    </nav>
</body>
</html>
```

**Step 2: Commit**

```bash
git add -A && git commit -m "feat: create client layout with bottom navigation

- Add client.blade.php layout with BladewindUI
- Top header with coach branding and quick actions
- Bottom tab navigation for mobile-first experience
- Navigation for home, program, log, history

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 6: Create Coach Dashboard Controller and View

**Files:**
- Create: `app/Http/Controllers/Coach/DashboardController.php`
- Create: `resources/views/coach/dashboard.blade.php`

**Step 1: Create controller**

```bash
php artisan make:controller Coach/DashboardController --no-interaction
```

**Step 2: Edit controller**

Replace content of `app/Http/Controllers/Coach/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $stats = [
            'total_clients' => $user->clients()->count(),
            'active_clients' => $user->clients()->count(), // Will add active filter later
        ];

        return view('coach.dashboard', compact('stats'));
    }
}
```

**Step 3: Create view**

Create directory and file `resources/views/coach/dashboard.blade.php`:

```blade
<x-layouts.coach>
    <x-slot:title>Dashboard</x-slot:title>

    <!-- Welcome section -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600 mt-1">Here's what's happening with your clients.</p>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-bladewind::card class="!p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Clients</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_clients'] }}</p>
                </div>
            </div>
        </x-bladewind::card>

        <x-bladewind::card class="!p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Clients</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_clients'] }}</p>
                </div>
            </div>
        </x-bladewind::card>

        <x-bladewind::card class="!p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Unread Messages</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
            </div>
        </x-bladewind::card>

        <x-bladewind::card class="!p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Programs</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
            </div>
        </x-bladewind::card>
    </div>

    <!-- Quick actions -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-3">
            <x-bladewind::button tag="a" href="{{ route('coach.clients.create') }}">
                Add Client
            </x-bladewind::button>
            <x-bladewind::button tag="a" href="{{ route('coach.programs.create') }}" type="secondary">
                Create Program
            </x-bladewind::button>
        </div>
    </div>

    <!-- Recent activity placeholder -->
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
        <x-bladewind::card>
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p>No recent activity yet.</p>
                <p class="text-sm mt-1">Activity from your clients will appear here.</p>
            </div>
        </x-bladewind::card>
    </div>
</x-layouts.coach>
```

**Step 4: Commit**

```bash
git add -A && git commit -m "feat: create coach dashboard controller and view

- Add DashboardController with client stats
- Create dashboard view with stat cards
- Add quick action buttons
- Add recent activity placeholder

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 7: Create Client Dashboard Controller and View

**Files:**
- Create: `app/Http/Controllers/Client/DashboardController.php`
- Create: `resources/views/client/dashboard.blade.php`

**Step 1: Create controller**

```bash
php artisan make:controller Client/DashboardController --no-interaction
```

**Step 2: Edit controller**

Replace content of `app/Http/Controllers/Client/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        return view('client.dashboard', [
            'coach' => $user->coach,
        ]);
    }
}
```

**Step 3: Create view**

Create directory and file `resources/views/client/dashboard.blade.php`:

```blade
<x-layouts.client>
    <x-slot:title>Home</x-slot:title>

    <!-- Welcome section -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Hey, {{ auth()->user()->name }}!</h1>
        @if($coach)
            <p class="text-gray-600 mt-1">Your coach: {{ $coach->name }}</p>
        @endif
    </div>

    <!-- Today's workout card -->
    <x-bladewind::card class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Today's Workout</h2>
        <div class="text-center py-6 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p>No workout assigned for today.</p>
            <p class="text-sm mt-1">Your coach will assign you a program soon.</p>
        </div>
    </x-bladewind::card>

    <!-- Quick stats -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <x-bladewind::card class="!p-4">
            <p class="text-sm text-gray-500">This Week</p>
            <p class="text-xl font-bold text-gray-900">0 / 0</p>
            <p class="text-xs text-gray-500">workouts completed</p>
        </x-bladewind::card>

        <x-bladewind::card class="!p-4">
            <p class="text-sm text-gray-500">Streak</p>
            <p class="text-xl font-bold text-gray-900">0 days</p>
            <p class="text-xs text-gray-500">keep it up!</p>
        </x-bladewind::card>
    </div>

    <!-- Message coach -->
    @if($coach)
        <x-bladewind::card>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                        {{ substr($coach->name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $coach->name }}</p>
                        <p class="text-xs text-gray-500">Your Coach</p>
                    </div>
                </div>
                <x-bladewind::button tag="a" href="{{ route('client.messages') }}" size="small">
                    Message
                </x-bladewind::button>
            </div>
        </x-bladewind::card>
    @endif
</x-layouts.client>
```

**Step 4: Commit**

```bash
git add -A && git commit -m "feat: create client dashboard controller and view

- Add DashboardController showing coach info
- Create dashboard view with today's workout placeholder
- Add quick stats cards
- Add message coach button

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 8: Set Up Routes

**Files:**
- Modify: `routes/web.php`
- Create: Placeholder controllers for routes

**Step 1: Create placeholder controllers**

```bash
php artisan make:controller Coach/ClientController --resource --no-interaction
php artisan make:controller Coach/ProgramController --resource --no-interaction
php artisan make:controller Coach/ExerciseController --resource --no-interaction
php artisan make:controller Coach/MessageController --no-interaction
php artisan make:controller Client/ProgramController --no-interaction
php artisan make:controller Client/LogController --no-interaction
php artisan make:controller Client/HistoryController --no-interaction
php artisan make:controller Client/MessageController --no-interaction
```

**Step 2: Update routes/web.php**

Replace content of `routes/web.php`:

```php
<?php

use App\Http\Controllers\Coach;
use App\Http\Controllers\Client;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Coach routes
Route::middleware(['auth', 'verified', 'role:coach'])
    ->prefix('coach')
    ->name('coach.')
    ->group(function () {
        Route::get('/', Coach\DashboardController::class)->name('dashboard');

        Route::resource('clients', Coach\ClientController::class);
        Route::resource('programs', Coach\ProgramController::class);
        Route::resource('exercises', Coach\ExerciseController::class);

        Route::get('messages', [Coach\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{user}', [Coach\MessageController::class, 'show'])->name('messages.show');
    });

// Client routes
Route::middleware(['auth', 'verified', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/', Client\DashboardController::class)->name('dashboard');
        Route::get('program', [Client\ProgramController::class, 'index'])->name('program');
        Route::get('log', [Client\LogController::class, 'index'])->name('log');
        Route::get('history', [Client\HistoryController::class, 'index'])->name('history');
        Route::get('messages', [Client\MessageController::class, 'index'])->name('messages');
    });

require __DIR__.'/auth.php';
```

**Step 3: Add placeholder index methods to controllers**

Edit `app/Http/Controllers/Coach/MessageController.php`:

```php
<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        return view('coach.messages.index');
    }

    public function show(int $userId): View
    {
        return view('coach.messages.show', ['userId' => $userId]);
    }
}
```

Edit `app/Http/Controllers/Client/ProgramController.php`:

```php
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        return view('client.program');
    }
}
```

Edit `app/Http/Controllers/Client/LogController.php`:

```php
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(): View
    {
        return view('client.log');
    }
}
```

Edit `app/Http/Controllers/Client/HistoryController.php`:

```php
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        return view('client.history');
    }
}
```

Edit `app/Http/Controllers/Client/MessageController.php`:

```php
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        return view('client.messages');
    }
}
```

**Step 4: Create placeholder views**

Create these placeholder view files:

`resources/views/coach/messages/index.blade.php`:
```blade
<x-layouts.coach>
    <x-slot:title>Messages</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Messages</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Messages feature coming soon.</p>
    </x-bladewind::card>
</x-layouts.coach>
```

`resources/views/coach/messages/show.blade.php`:
```blade
<x-layouts.coach>
    <x-slot:title>Conversation</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Conversation</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Message thread coming soon.</p>
    </x-bladewind::card>
</x-layouts.coach>
```

`resources/views/coach/clients/index.blade.php`:
```blade
<x-layouts.coach>
    <x-slot:title>Clients</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Clients</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Client list coming in Phase 2.</p>
    </x-bladewind::card>
</x-layouts.coach>
```

`resources/views/coach/clients/create.blade.php`:
```blade
<x-layouts.coach>
    <x-slot:title>Add Client</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Add Client</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Add client form coming in Phase 2.</p>
    </x-bladewind::card>
</x-layouts.coach>
```

`resources/views/coach/programs/index.blade.php`:
```blade
<x-layouts.coach>
    <x-slot:title>Programs</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Programs</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Program list coming in Phase 4.</p>
    </x-bladewind::card>
</x-layouts.coach>
```

`resources/views/coach/programs/create.blade.php`:
```blade
<x-layouts.coach>
    <x-slot:title>Create Program</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Create Program</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Program builder coming in Phase 4.</p>
    </x-bladewind::card>
</x-layouts.coach>
```

`resources/views/coach/exercises/index.blade.php`:
```blade
<x-layouts.coach>
    <x-slot:title>Exercises</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Exercise Library</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Exercise library coming in Phase 3.</p>
    </x-bladewind::card>
</x-layouts.coach>
```

`resources/views/client/program.blade.php`:
```blade
<x-layouts.client>
    <x-slot:title>My Program</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">My Program</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Your program will appear here once assigned.</p>
    </x-bladewind::card>
</x-layouts.client>
```

`resources/views/client/log.blade.php`:
```blade
<x-layouts.client>
    <x-slot:title>Log Workout</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Log Workout</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Workout logging coming in Phase 5.</p>
    </x-bladewind::card>
</x-layouts.client>
```

`resources/views/client/history.blade.php`:
```blade
<x-layouts.client>
    <x-slot:title>Workout History</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Workout History</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Your workout history will appear here.</p>
    </x-bladewind::card>
</x-layouts.client>
```

`resources/views/client/messages.blade.php`:
```blade
<x-layouts.client>
    <x-slot:title>Messages</x-slot:title>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Messages</h1>
    <x-bladewind::card>
        <p class="text-gray-500 text-center py-8">Messaging coming in Phase 6.</p>
    </x-bladewind::card>
</x-layouts.client>
```

**Step 5: Commit**

```bash
git add -A && git commit -m "feat: set up coach and client routes with placeholders

- Add coach routes (dashboard, clients, programs, exercises, messages)
- Add client routes (dashboard, program, log, history, messages)
- Create placeholder controllers and views
- Apply role middleware to protect routes

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 9: Modify Registration to Default as Coach

**Files:**
- Modify: `app/Http/Controllers/Auth/RegisteredUserController.php`

**Step 1: Edit the store method**

Find the `store` method in `app/Http/Controllers/Auth/RegisteredUserController.php` and update the User creation to include role:

```php
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => 'coach', // Default to coach on registration
]);
```

**Step 2: Update the redirect after registration**

In the same file, update the redirect after registration to go to the coach dashboard:

```php
return redirect(route('coach.dashboard', absolute: false));
```

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: default new registrations to coach role

- Set role to 'coach' on registration
- Redirect to coach dashboard after registration

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 10: Update Login Redirect Based on Role

**Files:**
- Modify: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

**Step 1: Update the store method redirect**

In `app/Http/Controllers/Auth/AuthenticatedSessionController.php`, update the redirect after login:

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    $user = $request->user();

    if ($user->isClient()) {
        return redirect()->intended(route('client.dashboard', absolute: false));
    }

    return redirect()->intended(route('coach.dashboard', absolute: false));
}
```

**Step 2: Add the User import if not present**

Make sure the User model is imported or use `$request->user()` which returns the authenticated user.

**Step 3: Commit**

```bash
git add -A && git commit -m "feat: redirect users to appropriate dashboard on login

- Redirect clients to client dashboard
- Redirect coaches to coach dashboard

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Task 11: Run Pint and Final Verification

**Step 1: Run Laravel Pint**

```bash
vendor/bin/pint
```

**Step 2: Clear caches**

```bash
php artisan optimize:clear
```

**Step 3: Verify the app runs**

```bash
php artisan serve
```

Visit http://localhost:8000 and verify:
- Landing page loads
- Registration creates a coach user
- Login redirects to coach dashboard
- Coach sidebar navigation works
- All placeholder pages load

**Step 4: Final commit**

```bash
git add -A && git commit -m "chore: run pint formatting and verify app

Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
```

---

## Summary

Phase 1 establishes:

1. **User model** with role (coach/client), coach relationship, and profile fields
2. **Role middleware** to protect coach and client routes
3. **Laravel Breeze** authentication
4. **Coach layout** with responsive sidebar
5. **Client layout** with bottom navigation (mobile-first)
6. **Coach dashboard** with stats and quick actions
7. **Client dashboard** with workout placeholder
8. **Route structure** with all placeholders for future phases
9. **Registration/login** flow routing users to correct dashboard

After Phase 1, coaches can register, log in, and see their dashboard. The foundation is ready for Phase 2 (Client Management).
