<x-layouts.coach>
    <x-slot:title>{{ $client->name }} - {{ $workoutLog->displayName() }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to {{ $client->name }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $workoutLog->displayName() }}</h1>
            @if($workoutLog->custom_name)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">Custom Workout</span>
            @endif
            <div class="flex items-center gap-3 mt-1">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $workoutLog->completed_at->format('D, M j, Y \a\t g:i A') }}</p>
                <span class="text-sm text-gray-400 dark:text-gray-500">&middot;</span>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $client->name }}</p>
            </div>
        </div>

        @if($workoutLog->notes)
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</h2>
                <p class="text-sm text-gray-900 dark:text-gray-100">{{ $workoutLog->notes }}</p>
            </div>
        @endif

        <!-- Exercise Logs grouped by exercise -->
        @php
            $grouped = $workoutLog->exerciseLogs->groupBy('exercise_id');
        @endphp

        @foreach($grouped as $exerciseId => $sets)
            @php
                $firstSet = $sets->first();
            @endphp
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $firstSet->exercise->name }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                            {{ ucfirst(str_replace('_', ' ', $firstSet->exercise->muscle_group)) }}
                        </span>
                    </div>
                    @if($firstSet->workoutExercise)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Prescribed: {{ $firstSet->workoutExercise->sets }} sets &times; {{ $firstSet->workoutExercise->reps }} reps
                        </p>
                    @endif
                </div>
                <div class="overflow-x-auto">
                <div class="px-6 py-3">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                                <th class="pb-2 pr-3 w-16">Set</th>
                                <th class="pb-2 pr-3">Weight (kg)</th>
                                <th class="pb-2">Reps</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sets->sortBy('set_number') as $set)
                                <tr>
                                    <td class="py-2 pr-3 text-gray-600 dark:text-gray-400 font-medium">{{ $set->set_number }}</td>
                                    <td class="py-2 pr-3 text-gray-900 dark:text-gray-100">{{ $set->weight ? number_format($set->weight, 1) : '-' }}</td>
                                    <td class="py-2 text-gray-900 dark:text-gray-100">{{ $set->reps }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        @endforeach

        <!-- Comments -->
        <x-workout-log-comments
            :workout-log="$workoutLog"
            :comment-route="route('coach.clients.workout-log.comment', [$client, $workoutLog])"
        />
    </div>
</x-layouts.coach>
