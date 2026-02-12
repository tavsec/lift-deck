<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
            <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome!</h1>

        <p class="text-lg text-gray-600 mb-8">
            You're now connected with<br>
            <span class="font-semibold text-gray-900">{{ $coach->gym_name ?? $coach->name }}</span>
        </p>

        @if($coach->avatar)
            <img src="{{ $coach->avatar }}" alt="{{ $coach->name }}" class="mx-auto h-24 w-24 rounded-full object-cover mb-8">
        @endif

        <p class="text-sm text-gray-500 mb-8">
            @if($coach->onboarding_welcome_text)
                {{ $coach->onboarding_welcome_text }}
            @else
                Let's set up your profile so your coach can create the perfect program for you.
            @endif
        </p>

        <a href="{{ route('client.onboarding') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Continue to Setup
        </a>
    </div>
</x-guest-layout>
