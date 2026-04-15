<x-layouts.coach>
    <x-slot:title>{{ __('coach.programs.edit.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('coach.programs.edit.back') }}
                </a>
                <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.programs.edit.heading') }}</h1>
                <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-400">{{ __('coach.programs.edit.subtitle') }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Program Details Form -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.programs.edit.program_details') }}</h2>
            <form method="POST" action="{{ route('coach.programs.update', $program) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.edit.name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $program->name) }}" required
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.edit.type') }} <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('type', $program->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="duration_weeks" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.edit.duration') }}</label>
                        <input type="number" name="duration_weeks" id="duration_weeks" value="{{ old('duration_weeks', $program->duration_weeks) }}" min="1" max="52"
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_template" value="1" {{ old('is_template', $program->is_template) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-[#1456f0]/20" style="color: var(--color-primary)">
                            <span class="ml-2 text-sm text-[#45515e] dark:text-gray-300">{{ __('coach.programs.edit.is_template') }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.edit.description') }}</label>
                    <textarea name="description" id="description" rows="2"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">{{ old('description', $program->description) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-[#181e25] dark:bg-gray-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-[#2d3748] dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                        {{ __('coach.programs.edit.save') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Workouts Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.programs.edit.workouts') }}</h2>
            </div>

            <!-- Add Workout Form -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 p-4">
                <form method="POST" action="{{ route('coach.programs.workouts.store', $program) }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="flex-1">
                        <input type="text" name="name" required placeholder="{{ __('coach.programs.edit.workout_name_placeholder') }}"
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                    </div>
                    <div class="w-24">
                        <input type="number" name="day_number" required placeholder="{{ __('coach.programs.edit.day_placeholder') }}" min="1"
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-[#181e25] dark:bg-gray-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-[#2d3748] dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('coach.programs.edit.add_workout') }}
                    </button>
                </form>
            </div>

            <!-- Workout List -->
            @foreach($program->workouts as $workout)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden">
                    <!-- Workout Header -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-[#222222] dark:text-gray-100">{{ $workout->name }}</h3>
                                <p class="text-sm text-[#8e8e93] dark:text-gray-400">Day {{ $workout->day_number }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <!-- Lock toggle -->
                                <form method="POST" action="{{ route('coach.programs.workouts.toggle-lock-removal', [$program, $workout]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="lock_exercise_removal" value="{{ $workout->lock_exercise_removal ? '0' : '1' }}">
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-lg {{ $workout->lock_exercise_removal ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-[#8e8e93] dark:text-gray-400' }} hover:opacity-80 transition-opacity"
                                        title="{{ $workout->lock_exercise_removal ? 'Clients cannot remove exercises — click to unlock' : 'Clients can remove exercises — click to lock' }}"
                                    >
                                        @if($workout->lock_exercise_removal)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            {{ __('coach.programs.edit.locked') }}
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                            </svg>
                                            {{ __('coach.programs.edit.unlocked') }}
                                        @endif
                                    </button>
                                </form>

                                <!-- Delete workout -->
                                <form method="POST" action="{{ route('coach.programs.workouts.destroy', [$program, $workout]) }}" onsubmit="return confirm('{{ __('coach.programs.edit.delete_workout_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">{{ __('coach.programs.edit.delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Exercises in Workout -->
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($workout->exercises as $workoutExercise)
                            <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ $workoutExercise->exercise->name }}</p>
                                    <p class="text-sm text-[#8e8e93] dark:text-gray-400">
                                        {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                        @if($workoutExercise->formatted_rest)
                                            &middot; {{ $workoutExercise->formatted_rest }} rest
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('coach.programs.exercises.move-up', [$program, $workoutExercise]) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 text-[#8e8e93] dark:text-gray-500 hover:text-[#45515e] dark:hover:text-gray-300 rounded" title="Move up">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('coach.programs.exercises.move-down', [$program, $workoutExercise]) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 text-[#8e8e93] dark:text-gray-500 hover:text-[#45515e] dark:hover:text-gray-300 rounded" title="Move down">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('coach.programs.exercises.destroy', [$program, $workoutExercise]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-red-400 hover:text-red-600 rounded" title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        @if($workout->exercises->count() === 0)
                            <div class="px-6 py-4 text-center text-sm text-[#8e8e93] dark:text-gray-400">
                                {{ __('coach.programs.edit.no_exercises') }}
                            </div>
                        @endif
                    </div>

                    <!-- Add Exercise Form -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <form method="POST" action="{{ route('coach.programs.exercises.store', [$program, $workout]) }}" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <div class="flex-1">
                                <select name="exercise_id" required class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                    <option value="">{{ __('coach.programs.edit.select_exercise') }}</option>
                                    @foreach($exercises->groupBy('muscle_group') as $muscleGroup => $groupExercises)
                                        <optgroup label="{{ ucfirst(str_replace('_', ' ', $muscleGroup)) }}">
                                            @foreach($groupExercises as $exercise)
                                                <option value="{{ $exercise->id }}">{{ $exercise->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-20">
                                <input type="number" name="sets" required placeholder="{{ __('coach.programs.edit.sets_placeholder') }}" min="1" max="20" value="3"
                                    class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                            </div>
                            <div class="w-24">
                                <input type="text" name="reps" required placeholder="{{ __('coach.programs.edit.reps_placeholder') }}" value="8-12"
                                    class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                            </div>
                            <div class="w-24">
                                <input type="number" name="rest_seconds" placeholder="{{ __('coach.programs.edit.rest_placeholder') }}" min="0" max="600" value="90"
                                    class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center px-3 py-2 bg-[#181e25] dark:bg-gray-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-[#2d3748] dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if($program->workouts->count() === 0)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card">
                    <div class="text-center py-8 text-sm text-[#8e8e93] dark:text-gray-400">
                        <p>{{ __('coach.programs.edit.no_workouts') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.coach>
