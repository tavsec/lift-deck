<x-layouts.coach>
    <x-slot:title>Target Weights – {{ $clientProgram->client->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Program
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Target Weights</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Set target weights for <span class="font-medium text-gray-700 dark:text-gray-300">{{ $clientProgram->client->name }}</span> on <span class="font-medium text-gray-700 dark:text-gray-300">{{ $program->name }}</span>. Leave blank to remove a target.
            </p>
        </div>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('coach.programs.assignments.targets.update', [$program, $clientProgram]) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @foreach($program->workouts as $workout)
                @if($workout->exercises->count() > 0)
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $workout->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Day {{ $workout->day_number }} &middot; {{ $workout->exercises->count() }} exercises</p>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($workout->exercises as $workoutExercise)
                                <div class="px-6 py-4 flex flex-col gap-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $workoutExercise->exercise->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                            </p>
                                        </div>
                                        <div class="flex flex-col gap-2 flex-shrink-0">
                                            @if($workoutExercise->sets > 0)
                                                @for ($set = 1; $set <= $workoutExercise->sets; $set++)
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-gray-400 dark:text-gray-500 w-10 text-right">Set {{ $set }}</span>
                                                        <input
                                                            type="number"
                                                            name="targets[{{ $workoutExercise->id }}][{{ $set }}]"
                                                            value="{{ old('targets.' . $workoutExercise->id . '.' . $set, $targetsByExercise->get($workoutExercise->id)?->get($set)?->target_weight) }}"
                                                            min="0"
                                                            max="9999.99"
                                                            step="0.5"
                                                            placeholder="—"
                                                            class="w-28 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-right @error('targets.' . $workoutExercise->id . '.' . $set) border-red-300 @enderror"
                                                        >
                                                        <span class="text-sm text-gray-500 dark:text-gray-400 w-6">kg</span>
                                                    </div>
                                                    @error('targets.' . $workoutExercise->id . '.' . $set)
                                                        <p class="text-xs text-red-600 dark:text-red-400 text-right">{{ $message }}</p>
                                                    @enderror
                                                @endfor
                                            @else
                                                <p class="text-xs text-gray-400 dark:text-gray-500 italic">No sets configured</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($historyByExercise->has($workoutExercise->id) && $historyByExercise->get($workoutExercise->id)->isNotEmpty())
                                        <details class="mt-2">
                                            <summary class="text-xs text-gray-400 dark:text-gray-500 cursor-pointer select-none hover:text-gray-600 dark:hover:text-gray-300">
                                                Target history
                                            </summary>
                                            <div class="mt-2 space-y-1 pl-2 border-l-2 border-gray-200 dark:border-gray-700">
                                                @foreach($historyByExercise->get($workoutExercise->id)->filter(fn ($t) => $t->effective_date !== null)->groupBy(fn ($t) => $t->effective_date->format('Y-m-d'))->sortKeysDesc() as $date => $entries)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
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
                <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save Targets
                </button>
            </div>
        </form>
    </div>
</x-layouts.coach>
