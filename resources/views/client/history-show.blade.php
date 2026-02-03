<x-layouts.client>
    <x-slot:title>{{ $workoutLog->programWorkout->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('client.history') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to History
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $workoutLog->programWorkout->name }}</h1>
            <p class="text-sm text-gray-500">{{ $workoutLog->completed_at->format('D, M j, Y \a\t g:i A') }}</p>
        </div>

        @if($workoutLog->notes)
            <x-bladewind::card class="!p-4">
                <p class="text-sm text-gray-700">{{ $workoutLog->notes }}</p>
            </x-bladewind::card>
        @endif

        <!-- Exercise Logs grouped by exercise -->
        @php
            $grouped = $workoutLog->exerciseLogs->groupBy('workout_exercise_id');
        @endphp

        @foreach($grouped as $workoutExerciseId => $sets)
            @php
                $firstSet = $sets->first();
            @endphp
            <x-bladewind::card class="!p-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">{{ $firstSet->exercise->name }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                            {{ ucfirst(str_replace('_', ' ', $firstSet->exercise->muscle_group)) }}
                        </span>
                    </div>

                    @if($firstSet->workoutExercise)
                        <p class="text-xs text-gray-500">
                            Prescribed: {{ $firstSet->workoutExercise->sets }} sets &times; {{ $firstSet->workoutExercise->reps }} reps
                        </p>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-500 border-b border-gray-200">
                                    <th class="pb-2 pr-3 w-12">Set</th>
                                    <th class="pb-2 pr-3">Weight (kg)</th>
                                    <th class="pb-2">Reps</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sets->sortBy('set_number') as $set)
                                    <tr>
                                        <td class="py-1.5 pr-3 text-gray-600 font-medium">{{ $set->set_number }}</td>
                                        <td class="py-1.5 pr-3 text-gray-900">{{ $set->weight ? number_format($set->weight, 1) : '-' }}</td>
                                        <td class="py-1.5 text-gray-900">{{ $set->reps }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-bladewind::card>
        @endforeach
    </div>
</x-layouts.client>
