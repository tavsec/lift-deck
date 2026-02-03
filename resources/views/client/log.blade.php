<x-layouts.client>
    <x-slot:title>Log Workout</x-slot:title>

    <div class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">Log Workout</h1>

        @if(session('error'))
            <div class="rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if($activeProgram)
            <p class="text-sm text-gray-600">Select a workout from <span class="font-medium">{{ $activeProgram->program->name }}</span> to log.</p>

            <div class="space-y-3">
                @foreach($activeProgram->program->workouts as $workout)
                    <a href="{{ route('client.log.create', $workout) }}" class="block">
                        <x-bladewind::card class="!p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ $workout->name }}</h3>
                                    <p class="text-sm text-gray-500">Day {{ $workout->day_number }} &middot; {{ $workout->exercises->count() }} exercises</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </x-bladewind::card>
                    </a>
                @endforeach
            </div>
        @else
            <x-bladewind::card class="!p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <p class="mt-4 text-lg text-gray-600">No active program to log workouts for</p>
                    <p class="mt-1 text-sm text-gray-500">Ask your coach to assign you a program.</p>
                </div>
            </x-bladewind::card>
        @endif
    </div>
</x-layouts.client>
