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
