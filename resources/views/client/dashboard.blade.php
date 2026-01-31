<x-layouts.client>
    <x-slot:title>Home</x-slot:title>

    <div class="py-6 space-y-6">
        <!-- Welcome Greeting -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Hey, {{ auth()->user()->name }}!</h1>
            @if ($coach)
                <p class="mt-1 text-sm text-gray-600">Your coach: {{ $coach->name }}</p>
            @endif
        </div>

        <!-- Today's Workout -->
        <x-bladewind::card>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Today's Workout</h2>
                    <span class="text-xs text-gray-500">{{ now()->format('D, M j') }}</span>
                </div>
                <div class="py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-sm">No workout assigned yet</p>
                </div>
            </div>
        </x-bladewind::card>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-4">
            <!-- This Week -->
            <x-bladewind::card>
                <div class="space-y-2">
                    <h3 class="text-sm font-medium text-gray-600">This Week</h3>
                    <p class="text-2xl font-bold text-gray-900">0 / 0</p>
                    <p class="text-xs text-gray-500">workouts completed</p>
                </div>
            </x-bladewind::card>

            <!-- Streak -->
            <x-bladewind::card>
                <div class="space-y-2">
                    <h3 class="text-sm font-medium text-gray-600">Streak</h3>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                    <p class="text-xs text-gray-500">days</p>
                </div>
            </x-bladewind::card>
        </div>

        <!-- Coach Card -->
        @if ($coach)
            <x-bladewind::card>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 text-blue-600 rounded-full font-semibold text-lg">
                            {{ strtoupper(substr($coach->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $coach->name }}</p>
                            <p class="text-xs text-gray-500">Your Coach</p>
                        </div>
                    </div>
                    <x-bladewind::button
                        tag="a"
                        href="{{ route('client.messages') }}"
                        size="small"
                        color="blue"
                    >
                        Message
                    </x-bladewind::button>
                </div>
            </x-bladewind::card>
        @endif
    </div>
</x-layouts.client>
