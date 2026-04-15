<x-layouts.client>
    <x-slot:title>{{ $workoutLog->displayName() }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <!-- Header -->
        <div class="mb-5">
            <a href="{{ route('client.history') }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-500 hover:text-[#222222] dark:hover:text-gray-300 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('client.history_show.back') }}
            </a>
            <h1 class="font-display text-xl font-semibold text-[#222222] dark:text-gray-100">{{ $workoutLog->displayName() }}</h1>
            <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ $workoutLog->completed_at->format('D, M j, Y \a\t g:i A') }}</p>
        </div>

        @if($workoutLog->notes)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <p class="text-sm text-[#45515e] dark:text-gray-300">{{ $workoutLog->notes }}</p>
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
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ $firstSet->exercise->name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-[#1456f0] dark:bg-blue-900/30 dark:text-blue-400">
                            {{ ucfirst(str_replace('_', ' ', $firstSet->exercise->muscle_group)) }}
                        </span>
                    </div>

                    @if($firstSet->workoutExercise)
                        <p class="text-xs text-[#8e8e93] dark:text-gray-500">
                            {{ __('client.history_show.prescribed', ['sets' => $firstSet->workoutExercise->sets, 'reps' => $firstSet->workoutExercise->reps]) }}
                        </p>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-[#8e8e93] dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                                    <th class="pb-2 pr-3 w-12">{{ __('client.history_show.set') }}</th>
                                    <th class="pb-2 pr-3">{{ __('client.history_show.weight_kg') }}</th>
                                    <th class="pb-2">{{ __('client.history_show.reps') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sets->sortBy('set_number') as $set)
                                    <tr>
                                        <td class="py-1.5 pr-3 text-[#45515e] dark:text-gray-400 font-medium">{{ $set->set_number }}</td>
                                        <td class="py-1.5 pr-3 text-[#222222] dark:text-gray-100">{{ $set->weight ? number_format($set->weight, 1) : '-' }}</td>
                                        <td class="py-1.5 text-[#222222] dark:text-gray-100">{{ $set->reps }}</td>
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
            :comment-route="route('client.history.comment', $workoutLog)"
        />
    </div>
</x-layouts.client>
