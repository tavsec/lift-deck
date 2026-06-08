<x-layouts.coach>
    <x-slot:title>{{ $client->name }} - {{ $workoutLog->displayName() }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.workout_log.back', ['name' => $client->name]) }}
            </a>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ $workoutLog->displayName() }}</h1>
            @if($workoutLog->custom_name)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 mt-1">{{ __('coach.clients.workout_log.custom_workout') }}</span>
            @endif
            <div class="flex items-center gap-3 mt-1">
                <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ $workoutLog->completed_at->format('D, M j, Y \a\t g:i A') }}</p>
                <span class="text-sm text-[#8c93a0] dark:text-[#6b7280]">&middot;</span>
                <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ $client->name }}</p>
            </div>
        </div>

        @if($workoutLog->notes)
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
                <h2 class="text-xs font-medium text-[#8c93a0] dark:text-[#6b7280] uppercase tracking-wide mb-1">{{ __('coach.clients.workout_log.notes') }}</h2>
                <p class="text-sm text-[#181b22] dark:text-[#f0f2f5]">{{ $workoutLog->notes }}</p>
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
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] overflow-hidden">
                <div class="px-6 py-4 bg-[#f3f5f7] dark:bg-[#1d2027] border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ $firstSet->exercise->name }}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#f3f5f7] dark:bg-[#1d2027] text-[#555b66] dark:text-[#a4abb6]">
                            {{ ucfirst(str_replace('_', ' ', $firstSet->exercise->muscle_group)) }}
                        </span>
                    </div>
                    @if($firstSet->workoutExercise)
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-1">
                            {{ __('coach.clients.workout_log.prescribed', ['sets' => $firstSet->workoutExercise->sets, 'reps' => $firstSet->workoutExercise->reps]) }}
                        </p>
                    @endif
                </div>
                <div class="overflow-x-auto">
                <div class="px-6 py-3">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-[#8c93a0] dark:text-[#6b7280] border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                                <th class="pb-2 pr-3 w-16 font-medium uppercase tracking-wide">{{ __('coach.clients.workout_log.set') }}</th>
                                <th class="pb-2 pr-3 font-medium uppercase tracking-wide">{{ __('coach.clients.workout_log.weight_kg') }}</th>
                                <th class="pb-2 font-medium uppercase tracking-wide">{{ __('coach.clients.workout_log.reps') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                            @foreach($sets->sortBy('set_number') as $set)
                                <tr class="hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                                    <td class="py-2 pr-3 text-[#8c93a0] dark:text-[#6b7280] font-medium">{{ $set->set_number }}</td>
                                    <td class="py-2 pr-3 text-[#181b22] dark:text-[#f0f2f5]">{{ $set->weight ? number_format($set->weight, 1) : '-' }}</td>
                                    <td class="py-2 text-[#181b22] dark:text-[#f0f2f5]">{{ $set->reps }}</td>
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
