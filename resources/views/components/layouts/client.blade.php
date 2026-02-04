<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'My Training' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- BladewindUI CSS -->
        <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <!-- Top Header (Fixed) -->
        <div class="fixed top-0 left-0 right-0 bg-white shadow-sm z-40">
            <div class="flex items-center justify-between px-4 h-16">
                <span class="text-lg font-semibold text-gray-900">
                    {{ auth()->user()->coach?->gym_name ?? 'My Training' }}
                </span>
                @php $unreadNotificationCount = auth()->user()->unreadNotifications()->count(); @endphp
                <div class="flex items-center space-x-3">
                    <a href="{{ route('client.messages') }}" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="pt-16 pb-20 max-w-4xl mx-auto px-4">
            {{ $slot }}
        </main>

        <!-- Bottom Navigation (Fixed) -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40">
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-4 gap-1">
                    <!-- Home Tab -->
                    <a href="{{ route('client.dashboard') }}" class="flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="text-xs mt-1 font-medium">Home</span>
                    </a>

                    <!-- Program Tab -->
                    <a href="{{ route('client.program') }}" class="flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.program*') ? 'text-blue-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-xs mt-1 font-medium">Program</span>
                    </a>

                    <!-- Log Tab -->
                    <a href="{{ route('client.log') }}" class="flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.log*') ? 'text-blue-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="text-xs mt-1 font-medium">Log</span>
                    </a>

                    <!-- History Tab -->
                    <a href="{{ route('client.history') }}" class="relative flex flex-col items-center justify-center py-3 {{ request()->routeIs('client.history*') ? 'text-blue-600' : 'text-gray-500' }}">
                        @if($unreadNotificationCount > 0)
                            <span class="absolute top-2 right-1/4 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
                        @endif
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs mt-1 font-medium">History</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- BladewindUI JS -->
        <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>

        @stack('scripts')
    </body>
</html>
