<x-layouts.coach>
    <x-slot:title>{{ __('coach.programs.targets.heading', ['name' => $clientProgram->client->name]) }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.programs.targets.back') }}
            </a>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.programs.targets.title') }}</h1>
            <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">
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
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ $workout->name }}</h3>
                            <p class="text-xs text-[#8e8e93] dark:text-gray-400 mt-0.5">Day {{ $workout->day_number }} &middot; {{ $workout->exercises->count() }} exercises</p>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($workout->exercises as $workoutExercise)
                                <div class="px-6 py-4 flex flex-col gap-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-[#222222] dark:text-gray-100 truncate">{{ $workoutExercise->exercise->name }}</p>
                                            <p class="text-xs text-[#8e8e93] dark:text-gray-400">
                                                {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                            </p>
                                        </div>
                                        <div class="flex flex-col gap-2 flex-shrink-0">
                                            @if($workoutExercise->sets > 0)
                                                @for ($set = 1; $set <= $workoutExercise->sets; $set++)
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-[#8e8e93] dark:text-gray-500 w-10 text-right">{{ __('coach.programs.targets.set_n', ['n' => $set]) }}</span>
                                                        <input
                                                            type="number"
                                                            name="targets[{{ $workoutExercise->id }}][{{ $set }}]"
                                                            value="{{ old('targets.' . $workoutExercise->id . '.' . $set, $targetsByExercise->get($workoutExercise->id)?->get($set)?->target_weight) }}"
                                                            min="0"
                                                            max="9999.99"
                                                            step="0.5"
                                                            placeholder="—"
                                                            class="w-28 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm text-right focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('targets.' . $workoutExercise->id . '.' . $set) border-red-300 dark:border-red-700 @enderror"
                                                        >
                                                        <span class="text-sm text-[#8e8e93] dark:text-gray-400 w-6">{{ __('coach.programs.targets.kg') }}</span>
                                                    </div>
                                                    @error('targets.' . $workoutExercise->id . '.' . $set)
                                                        <p class="text-xs text-red-600 dark:text-red-400 text-right">{{ $message }}</p>
                                                    @enderror
                                                @endfor
                                            @else
                                                <p class="text-xs text-[#8e8e93] dark:text-gray-500 italic">{{ __('coach.programs.targets.no_sets') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($historyByExercise->has($workoutExercise->id) && $historyByExercise->get($workoutExercise->id)->isNotEmpty())
                                        <details class="mt-2">
                                            <summary class="text-xs text-[#8e8e93] dark:text-gray-500 cursor-pointer select-none hover:text-[#45515e] dark:hover:text-gray-300">
                                                {{ __('coach.programs.targets.history') }}
                                            </summary>
                                            <div class="mt-2 space-y-1 pl-2 border-l-2 border-gray-200 dark:border-gray-700">
                                                @foreach($historyByExercise->get($workoutExercise->id)->filter(fn ($t) => $t->effective_date !== null)->groupBy(fn ($t) => $t->effective_date->format('Y-m-d'))->sortKeysDesc() as $date => $entries)
                                                    <div class="text-xs text-[#8e8e93] dark:text-gray-400">
                                                        <span class="font-medium text-[#45515e] dark:text-gray-300">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
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
                <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('coach.programs.targets.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    {{ __('coach.programs.targets.save') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.coach>
