<x-layouts.coach>
    <x-slot:title>{{ __('coach.programs.targets.heading', ['name' => $clientProgram->client->name]) }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.programs.targets.back') }}
            </a>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.programs.targets.title') }}</h1>
            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">
                {{ __('coach.programs.targets.subtitle', ['client' => $clientProgram->client->name, 'program' => $program->name]) }}
            </p>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('coach.programs.assignments.targets.update', [$program, $clientProgram]) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @foreach($program->workouts as $workout)
                @if($workout->exercises->count() > 0)
                    <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] overflow-hidden">
                        <div class="px-6 py-4 bg-[#f3f5f7] dark:bg-[#1d2027] border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                            <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ $workout->name }}</h3>
                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">Day {{ $workout->day_number }} &middot; {{ $workout->exercises->count() }} exercises</p>
                        </div>
                        <div class="divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                            @foreach($workout->exercises as $workoutExercise)
                                <div class="px-6 py-4 flex flex-col gap-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] truncate">{{ $workoutExercise->exercise->name }}</p>
                                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                                {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                            </p>
                                        </div>
                                        <div class="flex flex-col gap-2 flex-shrink-0">
                                            @if($workoutExercise->sets > 0)
                                                @for ($set = 1; $set <= $workoutExercise->sets; $set++)
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-[#8c93a0] dark:text-[#6b7280] w-10 text-right">{{ __('coach.programs.targets.set_n', ['n' => $set]) }}</span>
                                                        <input
                                                            type="number"
                                                            name="targets[{{ $workoutExercise->id }}][{{ $set }}]"
                                                            value="{{ old('targets.' . $workoutExercise->id . '.' . $set, $targetsByExercise->get($workoutExercise->id)?->get($set)?->target_weight) }}"
                                                            min="0"
                                                            max="9999.99"
                                                            step="0.5"
                                                            placeholder="—"
                                                            class="w-28 border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2 text-sm text-right focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('targets.' . $workoutExercise->id . '.' . $set) border-red-300 dark:border-red-700 @enderror"
                                                        >
                                                        <span class="text-sm text-[#8c93a0] dark:text-[#6b7280] w-6">{{ __('coach.programs.targets.kg') }}</span>
                                                    </div>
                                                    @error('targets.' . $workoutExercise->id . '.' . $set)
                                                        <p class="text-xs text-red-600 dark:text-red-400 text-right">{{ $message }}</p>
                                                    @enderror
                                                @endfor
                                            @else
                                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] italic">{{ __('coach.programs.targets.no_sets') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($historyByExercise->has($workoutExercise->id) && $historyByExercise->get($workoutExercise->id)->isNotEmpty())
                                        <details class="mt-2">
                                            <summary class="text-xs text-[#8c93a0] dark:text-[#6b7280] cursor-pointer select-none hover:text-[#45515e] dark:hover:text-[#f0f2f5]">
                                                {{ __('coach.programs.targets.history') }}
                                            </summary>
                                            <div class="mt-2 space-y-1 pl-2 border-l-2 border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)]">
                                                @foreach($historyByExercise->get($workoutExercise->id)->filter(fn ($t) => $t->effective_date !== null)->groupBy(fn ($t) => $t->effective_date->format('Y-m-d'))->sortKeysDesc() as $date => $entries)
                                                    <div class="text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                                        <span class="font-medium text-[#555b66] dark:text-[#a4abb6]">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                                                        @foreach($entries->sortBy('set_number') as $entry)
                                                            &middot; Set {{ $entry->set_number }}: {{ $entry->target_weight }} kg
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                    {{ __('coach.programs.targets.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                    {{ __('coach.programs.targets.save') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.coach>
