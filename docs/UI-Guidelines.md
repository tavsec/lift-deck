# GYM Coach App UI Guidelines v1.0 (MVP Phase 1)

**For Claude Code Agent & Laravel Devs**  
BladewindUI + Tailwind CSS | Laravel Blade | Functional MVP  
Created: Jan 30, 2026 | Based on GYM-Coach-V1-Roadmap.docx

---

## Goal
Build professional, consistent coach dashboard that saves time. Green gym theme, mobile-responsive, 90% Bladewind components. No custom CSS/JS.

## Tech Stack
- Laravel 11 + Blade views
- BladewindUI (composer require mkocansey/bladewind)
- Tailwind CSS (via Vite)
- No Livewire/JS (MVP web-only)

---

## 1. Design System - tailwind.config.js

Add to your Tailwind configuration:

    module.exports = {
      theme: {
        extend: {
          colors: {
            'gym-primary': '#10B981',    // Main green (buttons)
            'gym-dark': '#059669',       // Dark green (hover)
            'gym-accent': '#F59E0B',     // Orange (progress)
            'gym-bg': '#F8FAFC',         // Light background
            'gym-card': '#FFFFFF',       // Card background
            'gym-border': '#E2E8F0'      // Border color
          }
        }
      }
    }

**Usage in templates:** bg-gym-primary text-white hover:bg-gym-dark

---

## 2. Spacing & Typography Standards

Spacing Scale:
- Padding: p-4 | p-6 | p-8
- Margin: m-2 | m-4 | m-6
- Gap/Space: gap-4 | gap-6 | space-y-4 | space-y-6

Shadows:
- Tables: shadow-md
- Cards: shadow-lg

Border Radius:
- Default: rounded-lg (Bladewind handles this)

Typography:
- Font: Inter/Sans (Tailwind default)
- Headings: Use page-header component
- Body: Default Tailwind

---

## 3. EVERY PAGE STRUCTURE (MANDATORY)

All views must follow this pattern:

    @extends('layouts.app')

    @section('content')
      <x-bladewind::page-header 
        title="Page Title" 
        subtitle="Brief description"
      />

      <div class="mb-8 flex flex-wrap gap-4">
        <x-bladewind::button 
          label="Primary Action" 
          variant="primary" 
          href="{{ route('action') }}" 
          size="lg"
        />
        <x-bladewind::button 
          label="Secondary" 
          variant="secondary"
        />
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-bladewind::card title="Section Title">
          Content here (table, form, stats)
        </x-bladewind::card>
      </div>
    @endsection

**Always include:**
1. page-header at top
2. Quick actions (buttons)
3. Grid layout with cards

---

## 4. CORE BLADEWIND COMPONENTS (90% of all UI)

### Layout Components

**page-header**
Purpose: Every page header
Usage: <x-bladewind::page-header title="Clients" subtitle="Manage roster" />

**card**
Purpose: Wrap tables, forms, content
Usage: <x-bladewind::card title="Active Clients" variant="glass">Content</x-bladewind::card>

### Action Components

**button**
Variants: primary (green), secondary, outline
Sizes: lg, md, sm
Icons: Plus, Edit, Delete, etc.
Usage: variant="primary" size="lg"

**button-group**
Purpose: Group 2-4 buttons together
Usage: Wrap multiple buttons in flex container

### Data Display Components

**table**
Features: searchable, paginated, row-clickable
Headers: :headers="['Name', 'Goal', 'Progress']"
Rows: :rows="$clients"

**stats**
Purpose: Dashboard metrics
Usage: number="15" label="Clients" color="green"

**stats-grid**
Purpose: Multiple stats in grid
Usage: cols="3" for 3-column layout

**progress**
Purpose: Progress bars/percentages
Usage: :value="85" color="blue" label="85%"

### Form Components

**form-group**
Purpose: Text inputs
Usage: label="Name" name="name" type="text|email|number" required

**select**
Purpose: Dropdowns
Usage: :data="$options" label_key="name" value_key="id"

**textarea**
Purpose: Multi-line text
Usage: label="Notes" name="notes" rows="4"

### Media Components

**avatar**
Purpose: User/client photos
Usage: :src="$photo" initials="JD" size="md"

**badge**
Purpose: Status, goals, tags
Usage: variant="green|red|blue">Fat Loss</x-bladewind::badge>

### UX Components

**dropdown**
Purpose: Action menus
Usage: <x-bladewind::dropdown-item label="Edit" />

**modal**
Purpose: Confirmations, edits
Usage: Trigger with button modal-id="confirm"

**alert**
Purpose: Messages and notifications
Variants: info, success, warning, danger

---

## 5. MVP PAGE PATTERNS (Phase 1)

### Dashboard Page
1. Stats grid (3 metrics: Active Clients, Today's Sessions, Adherence %)
2. Quick buttons (Add Client, New Session)
3. Clients table (name, goal, progress, actions)
4. Sessions table (client, time, type, status)

### Clients List Page
- Searchable table with columns: Photo, Name, Goal, Progress, Last Checkin
- Add Client button (primary green)
- Click row to view profile

### Client Onboarding
- 2-column form grid
- Fields: Name, Email, Age, Goal, Weight, Experience, Injuries, Equipment
- Submit button

### Client Profile Page
- Tabs: Overview, Training Plans, Meal Plans, Checkins, Logs
- Each tab contains relevant cards and tables

### Workout Builder
- Exercise library dropdown/search
- Form for sets, reps, rest period
- Save as template button
- Assign to client button

### Meal Planner
- Food search input
- Daily meal structure (breakfast, lunch, dinner, snacks)
- Macro summary card
- Assign to client button

---

## 6. BASE LAYOUT FILE - layouts/app.blade.php

    <!DOCTYPE html>
    <html class="scroll-smooth">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>@yield('title', 'Gym Coach')</title>
      @vite(['resources/css/app.css', 'resources/js/app.js'])
      <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>
    </head>
    <body class="bg-gym-bg antialiased">
      <nav class="bg-gym-card shadow-lg">
        <div class="container mx-auto px-6 py-4">
          <div class="flex justify-between items-center">
            <div class="text-2xl font-bold text-gym-primary">
              Gym Coach
            </div>
            <x-bladewind::dropdown>
              <x-slot:trigger>
                <x-bladewind::avatar 
                  initials="{{ substr(auth()->user()->name, 0, 2) }}" 
                />
              </x-slot:trigger>
              <x-bladewind::dropdown-item 
                href="{{ route('dashboard') }}"
              >
                Dashboard
              </x-bladewind::dropdown-item>
              <x-bladewind::dropdown-item 
                href="{{ route('profile.show') }}"
              >
                Profile
              </x-bladewind::dropdown-item>
              <x-bladewind::dropdown-divider />
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2">
                  Logout
                </button>
              </form>
            </x-bladewind::dropdown>
          </div>
        </div>
      </nav>

      <main class="container mx-auto px-4 py-8 max-w-7xl">
        @if (session('success'))
          <x-bladewind::alert variant="success">
            {{ session('success') }}
          </x-bladewind::alert>
        @endif
        
        @if ($errors->any())
          <x-bladewind::alert variant="danger">
            Please fix the errors below
          </x-bladewind::alert>
        @endif

        @yield('content')
      </main>
    </body>
    </html>

---

## 7. Responsive Design

Mobile (< 768px):
- grid-cols-1
- Stack all components vertically

Tablet (768px - 1024px):
- grid-cols-2
- 2-column layouts

Desktop (> 1024px):
- grid-cols-3 or grid-cols-4
- Full width layouts

Example: grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6

---

## 8. CODE REVIEW CHECKLIST

When generating UI code, verify:

- [ ] View extends layouts/app.blade.php
- [ ] page-header component present on every page
- [ ] At least 90% Bladewind components used
- [ ] No raw HTML tables (use x-bladewind::table)
- [ ] Controller passes data as $variable names
- [ ] Forms include @csrf token
- [ ] Forms include @error() validation display
- [ ] Mobile responsive: grid-cols-1 md:grid-cols-2 lg:grid-cols-3
- [ ] Primary buttons use gym-primary green color
- [ ] Tables have searchable and paginated props
- [ ] Cards use consistent padding (p-6)
- [ ] Spacing uses Tailwind scale (gap-4, gap-6)

---

## 9. STRICT DON'TS (These break consistency)

DO NOT:
- Use raw HTML <table> tags (always use x-bladewind::table)
- Write custom CSS classes
- Import external JS libraries
- Hardcode mock data (use @foreach with $variable)
- Nest components more than 3 levels deep
- Use non-green colors for primary actions
- Add extra styling beyond Tailwind utilities
- Skip page-header component
- Mix Bladewind with custom HTML forms

---

## 10. CONTROLLER PATTERN

When generating controllers, follow this pattern:

    <?php
    namespace App\Http\Controllers;
    
    use App\Models\Client;
    use Illuminate\Http\Request;
    
    class ClientController extends Controller
    {
      public function index()
      {
        return view('clients.index', [
          'clients' => Client::with('latestCheckin')
            ->paginate(15)
        ]);
      }
      
      public function create()
      {
        return view('clients.create');
      }
      
      public function store(Request $request)
      {
        $client = Client::create($request->validated());
        return redirect()->route('clients.show', $client)
          ->with('success', 'Client created!');
      }
    }

**Always pass data as clear variable names:** $clients, $stats, $sessions, etc.

---

## 11. ROUTING PATTERN

Add to routes/web.php:

    Route::middleware(['auth'])->group(function () {
      Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
      Route::resource('clients', ClientController::class);
      Route::resource('workouts', WorkoutController::class);
      Route::resource('meals', MealPlanController::class);
    });

---

## 12. PROMPT TEMPLATE FOR CLAUDE CODE AGENT

Use this exact format when requesting UI generation:

    Follow GYM Coach UI Guidelines v1.0 EXACTLY.
    
    Generate: [Screen Name]
    Example: Client Profile page
    
    Controller Data:
    - $client: Client model with programs, meals relationships
    - $programs: Collection of assigned programs
    - $checkins: Collection of recent checkins
    
    Requirements:
    - Mobile responsive (grid-cols-1 md:grid-cols-2)
    - Searchable if showing lists
    - Page-header with title and subtitle
    - Primary button is green gym-primary
    
    Output:
    1. Controller method stub (e.g., show())
    2. FULL Blade view file
    3. Route entry
    4. Any database migrations needed

---

## 13. COLOR REFERENCE

Primary Actions: gym-primary (#10B981 - Green)
Hover: gym-dark (#059669 - Darker green)
Success: green-500
Warning: orange-500 (gym-accent #F59E0B)
Error: red-500
Info: blue-500
Background: gym-bg (#F8FAFC - Light slate)
Cards: gym-card (#FFFFFF - White)
Borders: gym-border (#E2E8F0 - Light gray)

---

## 14. INSTALLATION CHECKLIST

Before building UI:

- [ ] Laravel 11 installed
- [ ] Tailwind CSS configured via Vite
- [ ] BladewindUI installed: composer require mkocansey/bladewind
- [ ] Assets published: php artisan vendor:publish --tag=bladewind-public
- [ ] tailwind.config.js updated with gym colors
- [ ] npm run dev (Tailwind watching)
- [ ] php artisan serve running
- [ ] Database migrations created

---

## 15. PERFORMANCE TIPS

- Use eager loading in controllers (.with('relationship'))
- Paginate tables if >10 items
- Cache frequently used data
- Use indexes on frequently searched columns
- Compress images before upload
- Defer non-critical components

---

## Reference

BladewindUI Docs: https://bladewindui.com
Laravel Docs: https://laravel.com
Tailwind Docs: https://tailwindcss.com

---

**Version:** 1.0  
**Status:** Production Ready  
**Last Updated:** Jan 30, 2026

**Save as:** UI_GUIDELINES.md in docs/ folder

Use this document for all UI generation. Consistency across all screens = professional app.
