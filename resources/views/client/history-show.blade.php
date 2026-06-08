<x-layouts.client>
    <x-slot:title>{{ $workoutLog->displayName() }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <!-- Header -->
        <div class="mb-5">
            <a href="{{ route('client.history') }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#181b22] dark:hover:text-gray-300 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('client.history_show.back') }}
            </a>
            <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ $workoutLog->displayName() }}</h1>
            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ $workoutLog->completed_at->format('D, M j, Y \a\t g:i A') }}</p>
        </div>

        @if($workoutLog->notes)
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                <p class="text-sm text-[#555b66] dark:text-gray-300">{{ $workoutLog->notes }}</p>
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
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ $firstSet->exercise->name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[rgba(198,242,78,0.15)] text-[#5c7a10] dark:bg-[rgba(198,242,78,0.12)] dark:text-[#c6f24e]">
                            {{ ucfirst(str_replace('_', ' ', $firstSet->exercise->muscle_group)) }}
                        </span>
                    </div>

                    @if($firstSet->workoutExercise)
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">
                            {{ __('client.history_show.prescribed', ['sets' => $firstSet->workoutExercise->sets, 'reps' => $firstSet->workoutExercise->reps]) }}
                        </p>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-[#8c93a0] dark:text-[#6b7280] border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                                    <th class="pb-2 pr-3 w-12">{{ __('client.history_show.set') }}</th>
                                    <th class="pb-2 pr-3">{{ __('client.history_show.weight_kg') }}</th>
                                    <th class="pb-2">{{ __('client.history_show.reps') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sets->sortBy('set_number') as $set)
                                    <tr>
                                        <td class="py-1.5 pr-3 text-[#555b66] dark:text-[#a4abb6] font-medium font-mono">{{ $set->set_number }}</td>
                                        <td class="py-1.5 pr-3 font-mono text-[#181b22] dark:text-[#f0f2f5]">{{ $set->weight ? number_format($set->weight, 1) : '-' }}</td>
                                        <td class="py-1.5 font-mono text-[#181b22] dark:text-[#f0f2f5]">{{ $set->reps }}</td>
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
