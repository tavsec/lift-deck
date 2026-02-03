<x-layouts.client>
    <x-slot:title>My Program</x-slot:title>

    <div class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">My Program</h1>

        @if($activeProgram)
            <!-- Program Info -->
            <x-bladewind::card class="!p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $activeProgram->program->name }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                    </div>
                    @if($activeProgram->program->description)
                        <p class="text-sm text-gray-600">{{ $activeProgram->program->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 text-sm text-gray-500">
                        @if($activeProgram->program->type)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst(str_replace('_', ' ', $activeProgram->program->type)) }}</span>
                        @endif
                        @if($activeProgram->program->duration_weeks)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $activeProgram->program->duration_weeks }} weeks</span>
                        @endif
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $activeProgram->program->workouts->count() }} workouts</span>
                    </div>
                    <p class="text-xs text-gray-400">Started {{ $activeProgram->started_at->format('M d, Y') }}</p>
                </div>
            </x-bladewind::card>

            <!-- Workouts -->
            @if($activeProgram->program->workouts->count() > 0)
                @foreach($activeProgram->program->workouts as $workout)
                    <x-bladewind::card class="!p-0 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-base font-medium text-gray-900">{{ $workout->name }}</h3>
                            <p class="text-sm text-gray-500">Day {{ $workout->day_number }} &middot; {{ $workout->exercises->count() }} exercises</p>
                            @if($workout->notes)
                                <p class="mt-2 text-sm text-gray-600">{{ $workout->notes }}</p>
                            @endif
                        </div>

                        @if($workout->exercises->count() > 0)
                            <div class="divide-y divide-gray-200">
                                @foreach($workout->exercises as $workoutExercise)
                                    <div class="px-6 py-4 flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $workoutExercise->exercise->name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                                @if($workoutExercise->formatted_rest)
                                                    &middot; {{ $workoutExercise->formatted_rest }} rest
                                                @endif
                                            </p>
                                            @if($workoutExercise->notes)
                                                <p class="text-xs text-gray-400 mt-1">{{ $workoutExercise->notes }}</p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                            {{ ucfirst(str_replace('_', ' ', $workoutExercise->exercise->muscle_group)) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-8 text-center text-sm text-gray-500">
                                No exercises added yet
                            </div>
                        @endif
                    </x-bladewind::card>
                @endforeach
            @endif
        @else
            <x-bladewind::card class="!p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <p class="mt-4 text-lg text-gray-600">Your program will appear here once assigned</p>
                </div>
            </x-bladewind::card>
        @endif
    </div>
</x-layouts.client>
