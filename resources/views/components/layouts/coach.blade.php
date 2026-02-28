<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->user()->dark_mode ? 'dark' : '' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Coach Dashboard' }}</title>

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
                --color-primary: {{ auth()->user()->primary_color ?? '#2563EB' }};
                --color-secondary: {{ auth()->user()->secondary_color ?? '#1E40AF' }};
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <!-- Mobile Header -->
        <div class="md:hidden fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 shadow-sm z-40">
            <div class="flex items-center justify-between px-4 h-14">
                <button onclick="toggleMobileMenu()" class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                @if(auth()->user()->logo)
                    <img src="{{ auth()->user()->logo }}" alt="{{ auth()->user()->gym_name ?? 'LiftDeck' }}" class="h-6">
                @else
                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ auth()->user()->gym_name ?? 'LiftDeck' }}</span>
                @endif
                <form method="POST" action="{{ route('user.dark-mode.toggle') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        aria-label="{{ auth()->user()->dark_mode ? 'Switch to light mode' : 'Switch to dark mode' }}"
                        class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700">
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

        <!-- Desktop Sidebar -->
        <aside class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:left-0 md:w-64 md:bg-white md:border-r md:border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-col flex-1 min-h-0">
                <!-- Brand -->
                <div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-700">
                    @if(auth()->user()->logo)
                        <img src="{{ auth()->user()->logo }}" alt="{{ auth()->user()->gym_name ?? 'LiftDeck' }}" class="h-8">
                    @else
                        <span class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ auth()->user()->gym_name ?? 'LiftDeck' }}</span>
                    @endif
                </div>

                @php $unreadNotificationCount = auth()->user()->unreadNotifications()->count(); @endphp

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('coach.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.dashboard') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.dashboard') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.dashboard') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('coach.clients.index') }}" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.clients.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.clients.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.clients.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Clients
                        </span>
                        @if($unreadNotificationCount > 0)
                            <span class="inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1 rounded-full bg-red-500 text-xs font-bold text-white">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('coach.programs.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.programs.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.programs.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.programs.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Programs
                    </a>

                    <a href="{{ route('coach.exercises.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.exercises.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.exercises.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.exercises.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Exercises
                    </a>

                    <a href="{{ route('coach.meals.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.meals.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.meals.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.meals.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Meals
                    </a>

                    <a href="{{ route('coach.tracking-metrics.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.tracking-metrics.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.tracking-metrics.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.tracking-metrics.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Tracking
                    </a>

                    <a href="{{ route('coach.messages.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.messages.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.messages.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.messages.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        Messages
                    </a>

                    <a href="{{ route('coach.branding.edit') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.branding.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.branding.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.branding.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Branding
                    </a>
                </nav>

                <!-- User Info -->
                <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center px-4 py-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold" style="background-color: var(--color-primary)">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                    Sign out
                                </button>
                            </form>
                        </div>
                        <form method="POST" action="{{ route('user.dark-mode.toggle') }}" class="ml-auto flex-shrink-0">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                aria-label="{{ auth()->user()->dark_mode ? 'Switch to light mode' : 'Switch to dark mode' }}"
                                class="p-1.5 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
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
                </div>
            </div>
        </aside>

        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu" class="md:hidden fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" onclick="toggleMobileMenu()"></div>
            <div class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-xl">
                <div class="flex flex-col h-full">
                    <!-- Mobile Menu Header -->
                    <div class="flex items-center justify-between h-14 px-4 border-b border-gray-200 dark:border-gray-700">
                        @if(auth()->user()->logo)
                            <img src="{{ auth()->user()->logo }}" alt="{{ auth()->user()->gym_name ?? 'LiftDeck' }}" class="h-6">
                        @else
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ auth()->user()->gym_name ?? 'LiftDeck' }}</span>
                        @endif
                        <button onclick="toggleMobileMenu()" class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Navigation -->
                    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                        <a href="{{ route('coach.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.dashboard') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.dashboard') ? 'style="color: var(--color-primary)"' : '' !!}>
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.dashboard') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Dashboard
                        </a>

                        <a href="{{ route('coach.clients.index') }}" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.clients.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.clients.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.clients.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Clients
                            </span>
                            @if($unreadNotificationCount > 0)
                                <span class="inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1 rounded-full bg-red-500 text-xs font-bold text-white">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('coach.programs.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.programs.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.programs.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.programs.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Programs
                        </a>

                        <a href="{{ route('coach.exercises.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.exercises.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.exercises.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.exercises.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Exercises
                        </a>

                        <a href="{{ route('coach.meals.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.meals.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.meals.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.meals.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Meals
                        </a>

                        <a href="{{ route('coach.tracking-metrics.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.tracking-metrics.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.tracking-metrics.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.tracking-metrics.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Tracking
                        </a>

                        <a href="{{ route('coach.messages.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.messages.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.messages.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.messages.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            Messages
                        </a>

                        <a href="{{ route('coach.branding.edit') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('coach.branding.*') ? 'bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" {!! request()->routeIs('coach.branding.*') ? 'style="color: var(--color-primary)"' : '' !!}>
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('coach.branding.*') ? '' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                            Branding
                        </a>
                    </nav>

                    <!-- Mobile User Info -->
                    <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center px-4 py-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold" style="background-color: var(--color-primary)">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ auth()->user()->name }}
                                </p>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="mt-14 md:mt-0 md:ml-64 min-h-screen dark:bg-gray-900">
            <div class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </div>
        </main>

        <!-- BladewindUI JS -->
        <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>

        <!-- Mobile Menu Toggle Script -->
        <script>
            function toggleMobileMenu() {
                const menu = document.getElementById('mobile-menu');
                menu.classList.toggle('hidden');
            }
        </script>

        @stack('scripts')
    </body>
</html>
