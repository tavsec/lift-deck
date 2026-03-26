@php $brandingCoach = auth()->user()->coach; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()->dark_mode ? 'dark' : '' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- PWA -->
        <link rel="manifest" href="/build/manifest.webmanifest">
        <meta name="theme-color" content="#2563EB">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="LiftDeck">
        <link rel="apple-touch-icon" href="/images/pwa-192.png">

        <title>{{ $title ?? __('client.layout.title') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- BladewindUI CSS -->
        <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Branding -->
        <style>
            :root {
                --color-primary: {{ $brandingCoach?->primary_color ?? '#2563EB' }};
                --color-secondary: {{ $brandingCoach?->secondary_color ?? '#1E40AF' }};
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950">
        <!-- Top Header (Fixed) -->
        <div class="fixed top-0 left-0 right-0 bg-white dark:bg-gray-900 shadow-sm z-40">
            <div class="flex items-center justify-between px-4 h-16">
                @if($brandingCoach?->logo)
                    <img src="{{ $brandingCoach->logo }}" alt="{{ $brandingCoach->gym_name ?? 'My Training' }}" class="h-8">
                @else
                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $brandingCoach?->gym_name ?? __('client.layout.title') }}</span>
                @endif
                @php $unreadNotificationCount = auth()->user()->unreadNotifications()->count(); @endphp
                <div class="flex items-center space-x-3">
                    <x-locale-switcher />
                    <form method="POST" action="{{ route('user.dark-mode.toggle') }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            aria-label="{{ auth()->user()->dark_mode ? __('client.layout.switch_to_light') : __('client.layout.switch_to_dark') }}"
                            class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800">
                            @if(auth()->user()->dark_mode)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                            @endif
                        </button>
                    </form>
                    <a href="{{ route('client.messages') }}" class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </a>
                    <a href="{{ route('client.settings.edit') }}"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 overflow-hidden"
                        style="background-color: var(--color-primary)"
                        aria-label="Settings">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="pt-16 pb-20 max-w-4xl mx-auto px-4">
            @unless(request()->routeIs('client.log.create*') || request()->routeIs('client.log.custom'))
            <div
                x-data="{
                    pendingWorkouts: [],
                    init() {
                        const pending = [];
                        for (let i = 0; i < localStorage.length; i++) {
                            const key = localStorage.key(i);
                            if (!key || !key.startsWith('workout_logger_')) continue;
                            try {
                                const parsed = JSON.parse(localStorage.getItem(key));
                                if (parsed && parsed.savedAt && parsed.exercises && parsed.exercises.length > 0) {
                                    const savedAt = new Date(parsed.savedAt);
                                    const isToday = savedAt.toDateString() === new Date().toDateString();
                                    pending.push({
                                        key: key,
                                        workoutName: parsed.workoutName ?? 'Workout',
                                        resumeUrl: parsed.resumeUrl ?? '{{ route('client.log') }}',
                                        savedAtFormatted: isToday
                                            ? savedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                                            : savedAt.toLocaleDateString([], { month: 'short', day: 'numeric' }) + ' at ' + savedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                                    });
                                }
                            } catch {}
                        }
                        this.pendingWorkouts = pending;
                    }
                }"
            >
                <template x-for="workout in pendingWorkouts" :key="workout.key">
                    <div class="mb-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-300"
                                    x-text="`{{ str_replace(':name', '', __('client.layout.unfinished_workout')) }}` + workout.workoutName">
                                </p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5"
                                    x-text="`{{ str_replace(':time', '', __('client.layout.last_saved')) }}` + workout.savedAtFormatted">
                                </p>
                            </div>
                            <a :href="workout.resumeUrl"
                                class="shrink-0 text-xs font-semibold px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                {{ __('client.layout.continue') }}
                            </a>
                        </div>
                    </div>
                </template>
            </div>
            @endunless

            <!-- Stale cache banner -->
            <div
                x-data="{ show: false }"
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
                    {{ __('client.layout.cached_content') }}
                </p>
            </div>

            {{ $slot }}
        </main>

        <!-- Bottom Navigation (Fixed) -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 z-40">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-6 gap-1">
                    <!-- Home Tab -->
                    <a href="{{ route('client.dashboard') }}" class="flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.dashboard') ? '' : 'text-gray-500 dark:text-gray-400' }}" {!! request()->routeIs('client.dashboard') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="text-xs mt-1 font-medium">{{ __('client.layout.nav.home') }}</span>
                    </a>

                    <!-- Program Tab -->
                    <a href="{{ route('client.program') }}" class="flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.program*') ? '' : 'text-gray-500 dark:text-gray-400' }}" {!! request()->routeIs('client.program*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-xs mt-1 font-medium">{{ __('client.layout.nav.program') }}</span>
                    </a>

                    <!-- Log Tab -->
                    <a href="{{ route('client.log') }}" class="flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.log*') ? '' : 'text-gray-500 dark:text-gray-400' }}" {!! request()->routeIs('client.log*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="text-xs mt-1 font-medium">{{ __('client.layout.nav.log') }}</span>
                    </a>

                    <!-- Check-in Tab -->
                    <a href="{{ route('client.check-in') }}" class="flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.check-in*') ? '' : 'text-gray-500 dark:text-gray-400' }}" {!! request()->routeIs('client.check-in*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-xs mt-1 font-medium">{{ __('client.layout.nav.check_in') }}</span>
                    </a>

                    <!-- Nutrition Tab -->
                    <a href="{{ route('client.nutrition') }}" class="flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.nutrition*') ? '' : 'text-gray-500 dark:text-gray-400' }}" {!! request()->routeIs('client.nutrition*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="text-xs mt-1 font-medium">{{ __('client.layout.nav.nutrition') }}</span>
                    </a>

                    <!-- History Tab -->
                    <a href="{{ route('client.history') }}" class="relative flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.history*') ? '' : 'text-gray-500 dark:text-gray-400' }}" {!! request()->routeIs('client.history*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        @if($unreadNotificationCount > 0)
                            <span class="absolute top-2 right-1/4 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
                        @endif
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs mt-1 font-medium">{{ __('client.layout.nav.history') }}</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- BladewindUI JS -->
        <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>

        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data?.type === 'SERVED_FROM_CACHE') {
                        window.__servedFromCache = true;
                        document.dispatchEvent(new CustomEvent('sw:served-from-cache'));
                    }
                });
            }
        </script>

        @stack('scripts')
    </body>
</html>
