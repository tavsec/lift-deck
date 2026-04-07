<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'LiftDeck') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 min-h-screen">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
            <a href="/" class="mb-8">
                <x-application-logo class="h-9 w-auto" />
            </a>
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8"
                 style="box-shadow: rgba(44, 30, 116, 0.12) 0px 0px 24px;">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
