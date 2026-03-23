# PWA Offline Support Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Make the client-facing app installable as a PWA and fully functional offline, with Background Sync for workout log submission.

**Architecture:** `vite-plugin-pwa` (Workbox) handles service worker generation and manifest. Three caching strategies: cache-first for static assets, network-first-with-cache for HTML/JSON. Workout form submission converted from HTML POST to `fetch()` so it works with Background Sync. Stale pages show a cached-data banner via `postMessage` from the service worker.

**Tech Stack:** vite-plugin-pwa, Workbox, IndexedDB (via vanilla JS), Background Sync API, Laravel 12, Alpine.js v3, Tailwind CSS v3

---

### Task 1: Install vite-plugin-pwa and generate PWA icons

**Files:**
- Modify: `package.json`
- Modify: `vite.config.js`
- Create: `public/images/pwa-192.png`
- Create: `public/images/pwa-512.png`

**Step 1: Install vite-plugin-pwa**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && npm install -D vite-plugin-pwa
```

**Step 2: Generate PWA icons from existing logo**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && sips -z 192 192 public/images/logo/logo.png --out public/images/pwa-192.png && sips -z 512 512 public/images/logo/logo.png --out public/images/pwa-512.png
```

**Step 3: Add VitePWA plugin to vite.config.js**

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'script',
            strategies: 'injectManifest',
            srcDir: 'resources/js',
            filename: 'sw.js',
            manifest: {
                name: 'LiftDeck',
                short_name: 'LiftDeck',
                description: 'Your personal training companion',
                theme_color: '#2563EB',
                background_color: '#f9fafb',
                display: 'standalone',
                start_url: '/dashboard',
                scope: '/',
                icons: [
                    {
                        src: '/images/pwa-192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                    {
                        src: '/images/pwa-512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },
            devOptions: {
                enabled: true,
                type: 'module',
            },
        }),
    ],
});
```

**Step 4: Run build to verify no errors**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && npm run build 2>&1 | tail -20
```

Expected: Build succeeds, `public/build/sw.js` (or similar) is generated.

**Step 5: Commit**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && git add package.json package-lock.json vite.config.js public/images/pwa-192.png public/images/pwa-512.png && git commit -m "feat: install vite-plugin-pwa and generate PWA icons"
```

---

### Task 2: Write the service worker (sw.js)

**Files:**
- Create: `resources/js/sw.js`

**Step 1: Create the service worker**

Create `resources/js/sw.js` with the following content:

```js
import { precacheAndRoute, cleanupOutdatedCaches } from 'workbox-precaching';
import { registerRoute, NavigationRoute } from 'workbox-routing';
import { CacheFirst, NetworkFirst } from 'workbox-strategies';
import { ExpirationPlugin } from 'workbox-expiration';
import { BackgroundSyncPlugin } from 'workbox-background-sync';

// Injected by vite-plugin-pwa at build time
precacheAndRoute(self.__WB_MANIFEST);
cleanupOutdatedCaches();

// --- Static assets: cache-first (Vite content-hashes filenames) ---
registerRoute(
    ({ url }) => url.pathname.startsWith('/build/') || url.pathname.startsWith('/vendor/bladewind/'),
    new CacheFirst({
        cacheName: 'static-assets',
        plugins: [
            new ExpirationPlugin({ maxEntries: 100, maxAgeSeconds: 60 * 60 * 24 * 365 }),
        ],
    })
);

// --- Google Fonts / external fonts: cache-first ---
registerRoute(
    ({ url }) => url.origin === 'https://fonts.bunny.net' || url.origin === 'https://fonts.gstatic.com',
    new CacheFirst({
        cacheName: 'fonts',
        plugins: [
            new ExpirationPlugin({ maxEntries: 20, maxAgeSeconds: 60 * 60 * 24 * 365 }),
        ],
    })
);

// --- Exercises JSON API: network-first, 1-day cache ---
registerRoute(
    ({ url }) => url.pathname === '/log/exercises',
    new NetworkFirst({
        cacheName: 'api-exercises',
        plugins: [
            new ExpirationPlugin({ maxEntries: 10, maxAgeSeconds: 60 * 60 * 24 }),
        ],
    })
);

// --- Client HTML pages: network-first, 7-day cache ---
const clientRoutes = [
    '/dashboard',
    '/log',
    '/program',
    '/history',
    '/check-in',
    '/nutrition',
    '/achievements',
    '/loyalty',
    '/rewards',
];

registerRoute(
    ({ url, request }) =>
        request.mode === 'navigate' &&
        (clientRoutes.some(r => url.pathname === r || url.pathname.startsWith(r + '/')) ||
         url.pathname.startsWith('/log/')),
    new NetworkFirst({
        cacheName: 'client-pages',
        plugins: [
            new ExpirationPlugin({ maxEntries: 30, maxAgeSeconds: 60 * 60 * 24 * 7 }),
        ],
        fetchOptions: { credentials: 'include' },
    })
);

// --- Offline fallback for uncached navigation requests ---
const OFFLINE_URL = '/offline';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('offline-fallback').then(cache => cache.add(OFFLINE_URL))
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(async () => {
                const cache = await caches.open('client-pages');
                const cached = await cache.match(event.request);
                if (cached) {
                    // Tell the page it was served from cache
                    const response = cached.clone();
                    const client = await self.clients.get(event.clientId);
                    if (client) {
                        client.postMessage({ type: 'SERVED_FROM_CACHE', url: event.request.url });
                    }
                    return cached;
                }
                return caches.match(OFFLINE_URL);
            })
        );
    }
});

// --- Background Sync: workout log submissions ---
const workoutSyncPlugin = new BackgroundSyncPlugin('sync-workout-logs', {
    maxRetentionTime: 60 * 24 * 7, // retry for up to 7 days
});

registerRoute(
    ({ url, request }) => url.pathname === '/log' && request.method === 'POST',
    new NetworkFirst({
        cacheName: 'workout-submissions',
        plugins: [workoutSyncPlugin],
        fetchOptions: { credentials: 'include' },
    }),
    'POST'
);

// Listen for messages from the page
self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
```

**Step 2: Install Workbox packages**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && npm install -D workbox-precaching workbox-routing workbox-strategies workbox-expiration workbox-background-sync
```

**Step 3: Build and verify**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && npm run build 2>&1 | tail -20
```

Expected: Build succeeds with no errors.

**Step 4: Commit**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && git add resources/js/sw.js package.json package-lock.json && git commit -m "feat: add service worker with caching strategies and background sync"
```

---

### Task 3: Add PWA manifest link and service worker registration to client layout

**Files:**
- Modify: `resources/views/components/layouts/client.blade.php`

**Step 1: Add manifest link and register SW**

In `resources/views/components/layouts/client.blade.php`, add to the `<head>` section after the `<meta name="csrf-token">` line:

```blade
<!-- PWA -->
<link rel="manifest" href="/build/manifest.webmanifest">
<meta name="theme-color" content="#2563EB">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="LiftDeck">
<link rel="apple-touch-icon" href="/images/pwa-192.png">
```

**Step 2: Add stale cache listener before `@stack('scripts')`**

At the bottom of the layout, before `@stack('scripts')`:

```blade
<script>
    // Listen for service worker cache notification
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('message', (event) => {
            if (event.data?.type === 'SERVED_FROM_CACHE') {
                window.__servedFromCache = true;
                document.dispatchEvent(new CustomEvent('sw:served-from-cache'));
            }
        });
    }
</script>
```

**Step 3: Add stale data banner inside `<main>` after the existing unfinished workout banner `@endunless`**

```blade
<!-- Stale cache banner -->
<div
    x-data="{ show: false, cachedAt: '' }"
    x-init="
        if (window.__servedFromCache) { show = true; }
        document.addEventListener('sw:served-from-cache', () => { show = true; });
    "
    x-show="show"
    x-cloak
    class="mb-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-3"
>
    <p class="text-sm text-yellow-700 dark:text-yellow-400 flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        You're viewing cached content. Some data may be outdated.
    </p>
</div>
```

**Step 4: Run existing tests**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && php artisan test --compact tests/Feature/Client/WorkoutLogTest.php
```

Expected: All pass (layout change doesn't affect server-side tests).

**Step 5: Commit**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && git add resources/views/components/layouts/client.blade.php && git commit -m "feat: add PWA manifest link, SW registration, and stale cache banner to client layout"
```

---

### Task 4: Create the offline fallback page

**Files:**
- Create: `routes/web.php` (add offline route)
- Create: `resources/views/offline.blade.php`

**Step 1: Add offline route**

In `routes/web.php`, near the top with other public routes (before middleware groups), add:

```php
Route::get('/offline', function () {
    return view('offline');
})->name('offline');
```

**Step 2: Create the offline view**

Create `resources/views/offline.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>You're Offline — LiftDeck</title>
    <meta name="theme-color" content="#2563EB">
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-gray-50 flex items-center justify-center px-4">
    <div class="text-center max-w-sm">
        <div class="mx-auto mb-6 w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M12 12h.01M8.464 15.536a5 5 0 01-.068-7.004M5.636 5.636a9 9 0 000 12.728"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">You're offline</h1>
        <p class="text-sm text-gray-500 mb-6">
            This page isn't available offline. Your workout progress is still being saved — head back to the log when you're ready.
        </p>
        <button
            onclick="window.history.back()"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700"
        >
            Go Back
        </button>
    </div>
</body>
</html>
```

**Step 3: Commit**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && git add routes/web.php resources/views/offline.blade.php && git commit -m "feat: add offline fallback page"
```

---

### Task 5: Convert workout log submission to fetch() + IndexedDB queue

**Files:**
- Modify: `resources/views/client/log-workout.blade.php`
- Modify: `app/Http/Controllers/Client/LogController.php`

**Step 1: Update LogController::store() to support JSON responses**

In `app/Http/Controllers/Client/LogController.php`, change the `store()` method's return statements to support both HTML redirects and JSON:

At the top of `store()`, after validation, when returning errors, add JSON support. And change the success redirect at the bottom:

```php
// Replace the final return in store():

// Before:
return redirect()->route('client.history')
    ->with('success', 'Workout logged!');

// After:
if ($request->expectsJson()) {
    return response()->json(['redirect' => route('client.history')]);
}

return redirect()->route('client.history')
    ->with('success', 'Workout logged!');
```

Also handle validation errors for JSON requests by letting Laravel's default JSON validation error response handle it (it already does this automatically for `expectsJson()`).

**Step 2: Write a failing test**

Add to `tests/Feature/Client/WorkoutLogTest.php`:

```php
it('accepts json submission and returns redirect url', function () {
    $response = $this->actingAs($this->client)
        ->postJson(route('client.log.store'), [
            'program_workout_id' => $this->workout->id,
            'completed_at' => now()->format('Y-m-d\TH:i'),
            'exercises' => [
                [
                    'workout_exercise_id' => $this->workoutExercise->id,
                    'exercise_id' => $this->exercise->id,
                    'sets' => [
                        ['weight' => '100', 'reps' => '10'],
                    ],
                ],
            ],
        ]);

    $response->assertOk()
        ->assertJsonStructure(['redirect']);
});
```

**Step 3: Run test to verify it fails**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && php artisan test --compact --filter="accepts json submission"
```

Expected: FAIL

**Step 4: Implement the JSON response**

In `app/Http/Controllers/Client/LogController.php`, update the end of `store()`:

```php
ProcessXpEvent::dispatch(auth()->id(), 'workout_logged', ['workout_log_id' => $workoutLog->id]);

if ($request->expectsJson()) {
    return response()->json(['redirect' => route('client.history')]);
}

return redirect()->route('client.history')
    ->with('success', 'Workout logged!');
```

**Step 5: Run test to verify it passes**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && php artisan test --compact --filter="accepts json submission"
```

Expected: PASS

**Step 6: Convert the workout log form submission to fetch() in the view**

In `resources/views/client/log-workout.blade.php`:

1. Remove `@submit="clearSavedState()"` from the form tag and add `@submit.prevent="submitWorkout($event)"` instead:

```blade
<form method="POST" action="{{ route('client.log.store') }}" @submit.prevent="submitWorkout($event)">
```

2. Add the `submitWorkout()` method and IndexedDB helpers to the Alpine component:

```js
async submitWorkout(event) {
    const form = event.target;
    const formData = new FormData(form);
    const payload = {};
    for (const [key, value] of formData.entries()) {
        // Convert FormData to nested object for JSON
        this.setNestedValue(payload, key, value);
    }

    this.clearSavedState();

    if (!navigator.onLine) {
        await this.queueWorkout(payload);
        this.showOfflineSubmitBanner = true;
        return;
    }

    await this.postWorkout(payload);
},

async postWorkout(payload) {
    const token = this.getCsrfToken();
    try {
        const response = await fetch('{{ route("client.log.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': token,
            },
            body: JSON.stringify(payload),
            credentials: 'include',
        });

        if (response.ok) {
            const data = await response.json();
            window.location.href = data.redirect;
        } else {
            // Server-side validation error — fall back to regular form submit
            form.submit();
        }
    } catch {
        await this.queueWorkout(payload);
        this.showOfflineSubmitBanner = true;
    }
},

getCsrfToken() {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
},

setNestedValue(obj, key, value) {
    // Convert "exercises[0][sets][0][weight]" style keys into nested object
    const keys = key.replace(/\]/g, '').split('[');
    let current = obj;
    for (let i = 0; i < keys.length - 1; i++) {
        const k = keys[i];
        if (current[k] === undefined) {
            current[k] = isNaN(keys[i + 1]) ? {} : [];
        }
        current = current[k];
    }
    current[keys[keys.length - 1]] = value;
},

async queueWorkout(payload) {
    const db = await this.openDb();
    const tx = db.transaction('pending_workouts', 'readwrite');
    tx.objectStore('pending_workouts').add({ payload, queuedAt: new Date().toISOString() });
    await new Promise((resolve, reject) => { tx.oncomplete = resolve; tx.onerror = reject; });
    db.close();

    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        const reg = await navigator.serviceWorker.ready;
        await reg.sync.register('sync-workout-logs');
    } else {
        // Fallback: retry when online
        window.addEventListener('online', async () => {
            await this.flushQueuedWorkouts();
        }, { once: true });
    }
},

async flushQueuedWorkouts() {
    const db = await this.openDb();
    const tx = db.transaction('pending_workouts', 'readonly');
    const all = await new Promise((resolve, reject) => {
        const req = tx.objectStore('pending_workouts').getAll();
        req.onsuccess = () => resolve(req.result);
        req.onerror = reject;
    });
    db.close();

    for (const entry of all) {
        const token = this.getCsrfToken();
        try {
            const response = await fetch('{{ route("client.log.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-XSRF-TOKEN': token,
                },
                body: JSON.stringify(entry.payload),
                credentials: 'include',
            });
            if (response.ok) {
                const db2 = await this.openDb();
                const tx2 = db2.transaction('pending_workouts', 'readwrite');
                tx2.objectStore('pending_workouts').delete(entry.id);
                db2.close();
                const data = await response.json();
                window.location.href = data.redirect;
            }
        } catch {}
    }
},

openDb() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('liftdeck', 1);
        req.onupgradeneeded = (e) => {
            e.target.result.createObjectStore('pending_workouts', { keyPath: 'id', autoIncrement: true });
        };
        req.onsuccess = () => resolve(req.result);
        req.onerror = reject;
    });
},
```

3. Add `showOfflineSubmitBanner: false` to the component state.

4. Add the offline submit banner after the existing offline banner in the HTML:

```blade
<!-- Offline submission banner -->
<div x-show="showOfflineSubmitBanner" x-cloak
    class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3">
    <p class="text-sm text-green-700 dark:text-green-400 flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        You're offline. Your workout is saved and will submit automatically when you reconnect.
    </p>
</div>
```

**Step 7: Run all workout log tests**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && php artisan test --compact tests/Feature/Client/WorkoutLogTest.php
```

Expected: All pass.

**Step 8: Run pint**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && vendor/bin/pint --dirty --format agent
```

**Step 9: Commit**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && git add app/Http/Controllers/Client/LogController.php resources/views/client/log-workout.blade.php tests/Feature/Client/WorkoutLogTest.php && git commit -m "feat: convert workout log to fetch() with IndexedDB queue and background sync fallback"
```

---

### Task 6: Final build verification

**Step 1: Run full test suite for affected files**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && php artisan test --compact tests/Feature/Client/WorkoutLogTest.php tests/Feature/Coach/WorkoutLockRemovalTest.php
```

Expected: All pass.

**Step 2: Build production assets**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && npm run build 2>&1 | tail -30
```

Expected: Build succeeds, `public/build/` contains `sw.js` and `manifest.webmanifest`.

**Step 3: Verify manifest and SW are in build output**

```bash
ls /Users/timotejavsec/Documents/Projects/lift-deck/public/build/ | grep -E "sw|manifest"
```

Expected: `sw.js` and `manifest.webmanifest` present.

**Step 4: Run pint final pass**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && vendor/bin/pint --dirty --format agent
```

**Step 5: Commit**

```bash
cd /Users/timotejavsec/Documents/Projects/lift-deck && git add public/build/ && git commit -m "chore: production PWA build artifacts"
```
