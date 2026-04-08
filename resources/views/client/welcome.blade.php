<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-[#e8ffea] dark:bg-green-900/30 mb-6">
            <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100 mb-2">{{ __('client.welcome.heading') }}</h1>

        <p class="text-base text-[#45515e] dark:text-gray-400 mb-8">
            {{ __('client.welcome.connected_with') }}<br>
            <span class="font-semibold text-[#222222] dark:text-gray-100">{{ $coach->gym_name ?? $coach->name }}</span>
        </p>

        <div class="mx-auto h-24 w-24 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center overflow-hidden mb-8">
            @if($coach->avatar)
                <img src="{{ $coach->avatar }}" alt="{{ $coach->name }}" class="w-full h-full object-cover">
            @else
                <span class="text-3xl font-bold text-[#1456f0] dark:text-blue-400">{{ strtoupper(substr($coach->name, 0, 1)) }}</span>
            @endif
        </div>

        <p class="text-sm text-[#8e8e93] dark:text-gray-500 mb-8">
            @if($coach->onboarding_welcome_text)
                {{ $coach->onboarding_welcome_text }}
            @else
                {{ __('client.welcome.default_message') }}
            @endif
        </p>

        <a href="{{ route('client.onboarding') }}" class="inline-flex items-center px-6 py-3 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
            {{ __('client.welcome.continue') }}
        </a>
    </div>
</x-guest-layout>
