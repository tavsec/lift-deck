<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()->dark_mode ? 'dark' : '' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Favicons -->
        <x-favicons />

        <title>{{ $title ?? __('coach.layout.title') }}</title>

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-4NVX4MTRKN"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('consent', 'default', {
                analytics_storage: 'denied',
                ad_storage: 'denied',
                ad_user_data: 'denied',
                ad_personalization: 'denied',
                wait_for_update: 500,
            });
            gtag('js', new Date());
            gtag('config', 'G-4NVX4MTRKN');
        </script>

        <!-- BladewindUI CSS -->
        <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Branding -->
        <style>
            :root {
                --color-primary: {{ auth()->user()->primary_color ?? '#2563EB' }};
                --color-secondary: {{ auth()->user()->secondary_color ?? '#1E40AF' }};
            }
        </style>
    </head>
    <body x-data="{ trialBanner: {{ auth()->user()?->onTrial() ? 'true' : 'false' }}, graceBanner: {{ session('subscription_grace_days') !== null ? 'true' : 'false' }} }" class="font-sans antialiased bg-[#eceef2] dark:bg-[#0b0d10]">
        {{-- Trial Banner --}}
        @if(auth()->user()?->onTrial())
            @php
                $trialEndsAt = auth()->user()->trial_ends_at ?? auth()->user()->subscription('default')?->trial_ends_at;
                $trialDaysRemaining = $trialEndsAt ? max(0, (int) now()->diffInDays($trialEndsAt, false)) : null;
            @endphp
            <div
                x-show="trialBanner"
                x-transition
                class="fixed top-14 md:top-0 inset-x-0 z-50 bg-[rgba(198,242,78,0.9)] text-[#14180a] px-4 py-3 flex items-center justify-between text-sm"
            >
                <span>
                    You're on a free trial.
                    @if($trialDaysRemaining !== null)
                        <strong>{{ $trialDaysRemaining }} {{ $trialDaysRemaining === 1 ? 'day' : 'days' }}</strong> remaining.
                    @endif
                    @if(auth()->user()->subscribed('default'))
                        <a href="{{ route('coach.subscription.portal') }}" class="underline ml-1 font-medium">Manage subscription →</a>
                    @else
                        <a href="{{ route('coach.plan') }}" class="underline ml-1 font-medium">Choose a plan →</a>
                    @endif
                </span>
                <button @click="trialBanner = false" class="ml-4 text-[#14180a] hover:text-[#14180a]/70 flex-shrink-0" aria-label="Dismiss">✕</button>
            </div>
        @endif

        {{-- Grace Period Alert --}}
        @if(session('subscription_grace_days') !== null)
            <div
                x-show="graceBanner"
                x-transition
                class="fixed top-14 md:top-0 inset-x-0 z-50 bg-amber-500 text-white px-4 py-3 flex items-center justify-between text-sm"
            >
                <span>
                    Your subscription has ended. You have
                    <strong>{{ session('subscription_grace_days') }} day(s)</strong> remaining.
                    <a href="{{ route('coach.subscription.portal') }}" class="underline ml-1 font-medium">Manage subscription →</a>
                </span>
                <button @click="graceBanner = false" class="ml-4 text-white hover:text-amber-100 flex-shrink-0" aria-label="Dismiss">✕</button>
            </div>
        @endif
        <!-- Mobile Header -->
        <div class="md:hidden fixed top-0 left-0 right-0 bg-white dark:bg-[#16191f] z-40">
            <div class="flex items-center justify-between px-4 h-14 border-b border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
                <button onclick="toggleMobileMenu()" class="p-2 rounded-md text-[#555b66] dark:text-[#a4abb6] hover:text-[#181b22] dark:hover:text-[#f0f2f5] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                @if(auth()->user()->logo)
                    <img src="{{ auth()->user()->logo }}" alt="{{ auth()->user()->gym_name ?? 'LiftDeck' }}" class="h-6">
                @else
                    <div class="flex items-center gap-2">
                        <img src="/images/logo/liftdeck-monogram-dark.png" alt="LiftDeck" class="h-6 dark:hidden">
                        <img src="/images/logo/liftdeck-monogram-light.png" alt="LiftDeck" class="h-6 hidden dark:block">
                        @if(auth()->user()->gym_name)
                            <span class="font-display font-bold text-base tracking-tight text-[#181b22] dark:text-[#f0f2f5]">{{ auth()->user()->gym_name }}</span>
                        @endif
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <x-locale-switcher />
                    <form method="POST" action="{{ route('user.dark-mode.toggle') }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            aria-label="{{ auth()->user()->dark_mode ? __('coach.layout.nav.switch_to_light') : __('coach.layout.nav.switch_to_dark') }}"
                            class="p-2 rounded-md text-[#555b66] dark:text-[#a4abb6] hover:text-[#181b22] dark:hover:text-[#f0f2f5] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027]">
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
                </div>
            </div>
        </div>

        <!-- Desktop Sidebar -->
        <aside
            class="hidden md:flex md:flex-col md:fixed md:bottom-0 md:left-0 md:w-56 md:bg-white md:dark:bg-[#16191f] md:border-r md:border-[rgba(18,22,31,0.09)] md:dark:border-[rgba(255,255,255,0.08)] {{ (auth()->user()?->onTrial() || session('subscription_grace_days') !== null) ? 'md:top-11' : 'md:top-0' }}"
            :class="{ 'md:!top-0': !trialBanner && !graceBanner }"
        >
            <div class="flex flex-col flex-1 min-h-0">
                <!-- Brand -->
                <div class="flex items-center h-16 px-5 border-b border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
                    @if(auth()->user()->logo)
                        <img src="{{ auth()->user()->logo }}" alt="{{ auth()->user()->gym_name ?? 'LiftDeck' }}" class="h-8">
                    @else
                        <div class="flex items-center gap-2.5">
                            <img src="/images/logo/liftdeck-monogram-dark.png" alt="LiftDeck" class="h-8 dark:hidden">
                            <img src="/images/logo/liftdeck-monogram-light.png" alt="LiftDeck" class="h-8 hidden dark:block">
                            @if(auth()->user()->gym_name)
                                <span class="font-display font-bold text-lg tracking-tight text-[#181b22] dark:text-[#f0f2f5]">{{ auth()->user()->gym_name }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                @php $unreadNotificationCount = auth()->user()->unreadNotifications()->count(); @endphp

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                    <a href="{{ route('coach.dashboard') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.dashboard') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.dashboard'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.dashboard') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        {{ __('coach.layout.nav.dashboard') }}
                    </a>

                    <a href="{{ route('coach.clients.index') }}" class="relative flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.clients.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.clients.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.clients.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ __('coach.layout.nav.clients') }}
                        </span>
                        @if($unreadNotificationCount > 0)
                            <span class="inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1 rounded-full bg-red-500 text-xs font-bold text-white">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('coach.programs.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.programs.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.programs.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.programs.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('coach.layout.nav.programs') }}
                    </a>

                    <a href="{{ route('coach.exercises.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.exercises.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.exercises.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.exercises.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        {{ __('coach.layout.nav.exercises') }}
                    </a>

                    <a href="{{ route('coach.meals.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.meals.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.meals.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.meals.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        {{ __('coach.layout.nav.meals') }}
                    </a>

                    <a href="{{ route('coach.tracking-metrics.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.tracking-metrics.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.tracking-metrics.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.tracking-metrics.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        {{ __('coach.layout.nav.tracking') }}
                    </a>

                    <a href="{{ route('coach.messages.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.messages.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.messages.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.messages.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        {{ __('coach.layout.nav.messages') }}
                    </a>

                    <a href="{{ route('coach.branding.edit') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.branding.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.branding.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.branding.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        {{ __('coach.layout.nav.branding') }}
                    </a>

                    <a href="{{ route('coach.settings.edit') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.settings.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.settings.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.settings.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ __('coach.layout.nav.settings') }}
                    </a>

                    @feature(\App\Features\Loyalty::class)
                    <div class="pt-3 pb-1">
                        <p class="px-3 text-xs font-semibold text-[#8c93a0] dark:text-[#6b7280] uppercase tracking-wider">{{ __('coach.layout.nav.loyalty') }}</p>
                    </div>

                    <a href="{{ route('coach.rewards.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.rewards.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.rewards.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.rewards.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                        {{ __('coach.layout.nav.rewards') }}
                    </a>

                    <a href="{{ route('coach.achievements.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.achievements.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.achievements.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.achievements.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        {{ __('coach.layout.nav.achievements') }}
                    </a>

                    <a href="{{ route('coach.redemptions.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.redemptions.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                        @if(request()->routeIs('coach.redemptions.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                        @endif
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.redemptions.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        {{ __('coach.layout.nav.redemptions') }}
                    </a>
                    @endfeature
                </nav>

                <!-- User Info -->
                <div class="flex-shrink-0 border-t border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
                    <div class="flex items-center px-4 py-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold overflow-hidden bg-gradient-to-br from-[#7c5cff] to-[#c6f24e]">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#a4abb6] transition-colors">
                                    {{ __('coach.layout.nav.sign_out') }}
                                </button>
                            </form>
                        </div>
                        <form method="POST" action="{{ route('user.dark-mode.toggle') }}" class="ml-auto flex-shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                aria-label="{{ auth()->user()->dark_mode ? __('coach.layout.nav.switch_to_light') : __('coach.layout.nav.switch_to_dark') }}"
                                class="p-1.5 rounded-md text-[#8c93a0] dark:text-[#6b7280] hover:text-[#181b22] dark:hover:text-[#f0f2f5] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027]">
                                @if(auth()->user()->dark_mode)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                    </svg>
                                @endif
                            </button>
                        </form>
                    </div>
                    <div class="px-4 pb-3">
                        <x-locale-switcher direction="top" />
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu" class="md:hidden fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" onclick="toggleMobileMenu()"></div>
            <div class="fixed inset-y-0 left-0 w-56 bg-white dark:bg-[#16191f] shadow-xl border-r border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
                <div class="flex flex-col h-full">
                    <!-- Mobile Menu Header -->
                    <div class="flex items-center justify-between h-14 px-4 border-b border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
                        @if(auth()->user()->logo)
                            <img src="{{ auth()->user()->logo }}" alt="{{ auth()->user()->gym_name ?? 'LiftDeck' }}" class="h-6">
                        @else
                            <div class="flex items-center gap-2">
                                <img src="/images/logo/liftdeck-monogram-dark.png" alt="LiftDeck" class="h-6 dark:hidden">
                                <img src="/images/logo/liftdeck-monogram-light.png" alt="LiftDeck" class="h-6 hidden dark:block">
                                @if(auth()->user()->gym_name)
                                    <span class="font-display font-bold text-base tracking-tight text-[#181b22] dark:text-[#f0f2f5]">{{ auth()->user()->gym_name }}</span>
                                @endif
                            </div>
                        @endif
                        <button onclick="toggleMobileMenu()" class="p-2 rounded-md text-[#555b66] dark:text-[#a4abb6] hover:text-[#181b22] dark:hover:text-[#f0f2f5] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Navigation -->
                    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                        <a href="{{ route('coach.dashboard') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.dashboard') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.dashboard'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.dashboard') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            {{ __('coach.layout.nav.dashboard') }}
                        </a>

                        <a href="{{ route('coach.clients.index') }}" class="relative flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.clients.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.clients.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.clients.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ __('coach.layout.nav.clients') }}
                            </span>
                            @if($unreadNotificationCount > 0)
                                <span class="inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1 rounded-full bg-red-500 text-xs font-bold text-white">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('coach.programs.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.programs.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.programs.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.programs.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('coach.layout.nav.programs') }}
                        </a>

                        <a href="{{ route('coach.exercises.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.exercises.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.exercises.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.exercises.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            {{ __('coach.layout.nav.exercises') }}
                        </a>

                        <a href="{{ route('coach.meals.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.meals.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.meals.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.meals.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            {{ __('coach.layout.nav.meals') }}
                        </a>

                        <a href="{{ route('coach.tracking-metrics.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.tracking-metrics.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.tracking-metrics.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.tracking-metrics.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            {{ __('coach.layout.nav.tracking') }}
                        </a>

                        <a href="{{ route('coach.messages.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.messages.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.messages.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.messages.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            {{ __('coach.layout.nav.messages') }}
                        </a>

                        <a href="{{ route('coach.branding.edit') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.branding.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.branding.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.branding.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                            {{ __('coach.layout.nav.branding') }}
                        </a>

                        <a href="{{ route('coach.settings.edit') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.settings.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.settings.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.settings.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ __('coach.layout.nav.settings') }}
                        </a>

                        @feature(\App\Features\Loyalty::class)
                        <div class="pt-3 pb-1">
                            <p class="px-3 text-xs font-semibold text-[#8c93a0] dark:text-[#6b7280] uppercase tracking-wider">{{ __('coach.layout.nav.loyalty') }}</p>
                        </div>

                        <a href="{{ route('coach.rewards.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.rewards.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.rewards.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.rewards.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                            {{ __('coach.layout.nav.rewards') }}
                        </a>

                        <a href="{{ route('coach.achievements.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.achievements.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.achievements.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.achievements.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            {{ __('coach.layout.nav.achievements') }}
                        </a>

                        <a href="{{ route('coach.redemptions.index') }}" class="relative flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.redemptions.*') ? 'bg-[rgba(198,242,78,0.16)] dark:bg-[rgba(198,242,78,0.12)] text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#45515e] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] hover:text-[#181b22] dark:hover:text-[#f0f2f5]' }}">
                            @if(request()->routeIs('coach.redemptions.*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r bg-[#c6f24e]"></span>
                            @endif
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.redemptions.*') ? 'text-[#5c7a10] dark:text-[#c6f24e]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            {{ __('coach.layout.nav.redemptions') }}
                        </a>
                        @endfeature
                    </nav>

                    <!-- Mobile User Info -->
                    <div class="flex-shrink-0 border-t border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
                        <div class="flex items-center px-4 py-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold overflow-hidden bg-gradient-to-br from-[#7c5cff] to-[#c6f24e]">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    @endif
                                </div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] truncate">
                                    {{ auth()->user()->name }}
                                </p>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#a4abb6] transition-colors">
                                        {{ __('coach.layout.nav.sign_out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div
            class="mt-14 md:mt-0 md:pl-56 flex flex-col flex-1 {{ (auth()->user()?->onTrial() || session('subscription_grace_days') !== null) ? 'pt-11' : '' }}"
            :class="{ '!pt-0': !trialBanner && !graceBanner }"
        >
            <main class="flex-1 min-h-screen bg-[#eceef2] dark:bg-[#0b0d10] p-6">
                {{ $slot }}
            </main>
        </div>

        <!-- BladewindUI JS -->
        <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>

        <!-- Mobile Menu Toggle Script -->
        <script>
            function toggleMobileMenu() {
                const menu = document.getElementById('mobile-menu');
                menu.classList.toggle('hidden');
            }
        </script>

        @include('partials.ga-events')
        <x-cookie-banner />
        @stack('scripts')
    </body>
</html>
