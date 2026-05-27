<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->check() && auth()->user()->dark_mode ? 'dark' : '' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Favicons -->
        <x-favicons />

        <title>{{ config('app.name', 'LiftDeck') }}</title>

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

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 min-h-screen">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
            <a href="/" class="mb-8">
                <x-application-logo />
            </a>
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8"
                 style="box-shadow: rgba(44, 30, 116, 0.12) 0px 0px 24px;">
                {{ $slot }}
            </div>
        </div>
        @include('partials.ga-events')
        <x-cookie-banner />
        @stack('scripts')
    </body>
</html>
