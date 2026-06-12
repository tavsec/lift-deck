<x-layouts.client>
    <x-slot:title>{{ $isCustom ? 'Custom Workout' : 'Log: ' . $workout->name }}</x-slot:title>

    <div
        x-data="workoutLogger()"
        class="px-4 py-5 space-y-4"
        @keydown.escape.window="selectedExercise = null"
    >
        <!-- Header -->
        <div class="mb-5">
            <a href="{{ route('client.log') }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-500 hover:text-[#222222] dark:hover:text-gray-300 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('client.log_workout.back') }}
            </a>
            @if($isCustom)
                <h1 class="font-display text-xl font-semibold text-[#222222] dark:text-gray-100">{{ __('client.log_workout.custom_workout') }}</h1>
                <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('client.log_workout.custom_workout_description') }}</p>
            @else
                <h1 class="font-display text-xl font-semibold text-[#222222] dark:text-gray-100">{{ $workout->name }}</h1>
                <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('client.program.day_n', ['n' => $workout->day_number]) }} &middot; <span x-text="exercises.length"></span> {{ __('client.log_workout.exercises_label') }}</p>
            @endif
        </div>

        @if($errors->any())
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 mb-4">
                <p class="text-sm font-semibold text-red-800 dark:text-red-200">{{ __('client.log_workout.errors') }}</p>
                <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Restore banner -->
        <div x-show="restoreBanner" x-cloak
            class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-blue-800 dark:text-blue-300">{{ __('client.log_workout.restore_banner_title') }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">
                        {{ str_replace(':time', '', __('client.log_workout.restore_banner_description')) }}<span x-text="_savedAtFormatted"></span>{{ Str::after(__('client.log_workout.restore_banner_description'), ':time') }}
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button type="button" @click="confirmRestore()"
                        class="text-xs font-semibold px-3 py-1.5 bg-[#1456f0] text-white rounded-lg hover:bg-[#2563eb] transition-colors">
                        {{ __('client.log_workout.restore') }}
                    </button>
                    <button type="button" @click="discardRestore()"
                        class="text-xs font-medium px-3 py-1.5 bg-white dark:bg-gray-800 text-[#45515e] dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('client.log_workout.start_fresh') }}
                    </button>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('client.log.store') }}" @submit.prevent="submitWorkout($event)">
            @csrf

            @if(!$isCustom)
                <input type="hidden" name="program_workout_id" value="{{ $workout->id }}">
            @endif

            <div class="space-y-4">
                <!-- Custom Workout Name -->
                @if($isCustom)
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                        <label for="custom_name" class="block text-sm font-medium text-[#222222] dark:text-gray-100 mb-1">{{ __('client.log_workout.workout_name') }}</label>
                        <input
                            type="text"
                            id="custom_name"
                            name="custom_name"
                            value="{{ old('custom_name') }}"
                            placeholder="{{ __('client.log_workout.workout_name_placeholder') }}"
                            required
                            class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#1456f0] focus:ring-[#1456f0] @error('custom_name') border-red-300 @enderror"
                        >
                        @error('custom_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Exercises -->
                <div x-ref="exerciseList" x-init="initSortable()" class="space-y-4">
                <template x-for="(exercise, exerciseIndex) in exercises" :key="exercise.exercise_id">
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <!-- Drag Handle -->
                                    <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 touch-none">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <button type="button" class="text-sm font-semibold text-[#222222] dark:text-gray-100 text-left hover:underline focus:outline-none" @click="selectedExercise = exercise" x-text="exercise.name"></button>
                                        <p class="text-xs text-[#8e8e93] dark:text-gray-500" x-show="exercise.prescribed_sets">
                                            {{ Str::before(__('client.log_workout.prescribed'), ':sets') }}<span x-text="exercise.prescribed_sets"></span>{{ Str::between(__('client.log_workout.prescribed'), ':sets', ':reps') }}<span x-text="exercise.prescribed_reps"></span>{{ Str::after(__('client.log_workout.prescribed'), ':reps') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-[#1456f0] dark:bg-blue-900/30 dark:text-blue-400" x-text="exercise.muscle_group.replace('_', ' ')"></span>
                                    <!-- Move Up -->
                                    <button type="button" @click="moveExerciseUp(exerciseIndex)" :disabled="exerciseIndex === 0"
                                        class="p-1 text-[#8e8e93] dark:text-gray-500 hover:text-[#222222] dark:hover:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Move up">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </button>
                                    <!-- Move Down -->
                                    <button type="button" @click="moveExerciseDown(exerciseIndex)" :disabled="exerciseIndex === exercises.length - 1"
                                        class="p-1 text-[#8e8e93] dark:text-gray-500 hover:text-[#222222] dark:hover:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Move down">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <!-- Remove -->
                                    <button type="button" @click="removeExercise(exerciseIndex)" x-show="!exercise.lock_removal"
                                        class="p-1 text-red-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Remove exercise">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden fields -->
                            <input type="hidden" :name="`exercises[${exerciseIndex}][workout_exercise_id]`" :value="exercise.workout_exercise_id || ''">
                            <input type="hidden" :name="`exercises[${exerciseIndex}][exercise_id]`" :value="exercise.exercise_id">

                            <!-- Previous Session Data -->
                            <div x-show="exercise.previous_sets && exercise.previous_sets.length > 0" class="text-xs text-[#8e8e93] dark:text-gray-500">
                                <span class="font-medium">{{ __('client.log_workout.last_session') }}</span>
                                <template x-for="(prev, prevIndex) in (exercise.previous_sets || [])" :key="prevIndex">
                                    <span>
                                        <span x-text="`${prev.weight}kg × ${prev.reps}`"></span><span x-show="prevIndex < exercise.previous_sets.length - 1">, </span>
                                    </span>
                                </template>
                            </div>

                            <!-- Sets Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-xs text-[#8e8e93] dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                                            <th class="pb-2 pr-3 w-12">{{ __('client.log_workout.set') }}</th>
                                            <th class="pb-2 pr-3">{{ __('client.log_workout.weight_kg') }}</th>
                                            <th class="pb-2 pr-2">{{ __('client.log_workout.reps') }}</th>
                                            <th class="pb-2 w-8"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(set, setIndex) in exercise.sets" :key="setIndex">
                                            <tr>
                                                <td class="py-1.5 pr-3 text-[#45515e] dark:text-gray-400 font-medium" x-text="setIndex + 1"></td>
                                                <td class="py-1.5 pr-3">
                                                    <input
                                                        type="number"
                                                        step="0.5"
                                                        min="0"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][weight]`"
                                                        x-model="set.weight"
                                                        placeholder="0"
                                                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#1456f0] focus:ring-[#1456f0]"
                                                    >
                                                </td>
                                                <td class="py-1.5 pr-2">
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][reps]`"
                                                        x-model="set.reps"
                                                        :placeholder="exercise.prescribed_reps || '0'"
                                                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#1456f0] focus:ring-[#1456f0]"
                                                    >
                                                </td>
                                                <td class="py-1.5">
                                                    <button
                                                        type="button"
                                                        @click="removeSet(exerciseIndex, setIndex)"
                                                        x-show="exercise.sets.length > 1"
                                                        class="p-1 text-[#8e8e93] dark:text-gray-500 hover:text-red-500 transition-colors"
                                                        title="Remove set"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Add Set Button -->
                            <button
                                type="button"
                                @click="addSet(exerciseIndex)"
                                class="inline-flex items-center text-xs text-[#1456f0] hover:opacity-80 font-medium"
                            >
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ __('client.log_workout.add_set') }}
                            </button>
                        </div>
                    </div>
                </template>
                </div>

                <!-- Empty State -->
                <div x-show="exercises.length === 0" class="text-center py-8">
                    <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="mt-2 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.log_workout.no_exercises') }}</p>
                </div>

                <!-- Add Exercise -->
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                    <div x-show="!showExercisePicker">
                        <button
                            type="button"
                            @click="openExercisePicker()"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium text-[#45515e] dark:text-gray-400 hover:border-[#1456f0] hover:text-[#1456f0] transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('client.log_workout.add_exercise') }}
                        </button>
                    </div>

                    <div x-show="showExercisePicker" x-cloak>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('client.log_workout.select_exercise') }}</label>
                                <button type="button" @click="showExercisePicker = false" class="text-[#8e8e93] dark:text-gray-500 hover:text-[#222222] dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <input
                                type="text"
                                x-model="exerciseSearch"
                                placeholder="{{ __('client.log_workout.search_exercises') }}"
                                class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#1456f0] focus:ring-[#1456f0]"
                                x-ref="exerciseSearchInput"
                            >
                            <div class="max-h-48 overflow-y-auto border border-gray-100 dark:border-gray-800 rounded-xl divide-y divide-gray-100 dark:divide-gray-800">
                                <template x-for="exercise in filteredExercises" :key="exercise.id">
                                    <button
                                        type="button"
                                        @click="addExercise(exercise)"
                                        class="w-full text-left px-3 py-2 hover:bg-blue-50 dark:hover:bg-gray-800 transition-colors flex items-center justify-between"
                                    >
                                        <div>
                                            <span class="text-sm font-medium text-[#222222] dark:text-gray-100" x-text="exercise.name"></span>
                                            <span class="text-xs text-[#8e8e93] dark:text-gray-500 ml-2" x-text="exercise.muscle_group.replace('_', ' ')"></span>
                                        </div>
                                        <svg class="w-4 h-4 text-[#8e8e93] dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </template>
                                <div x-show="filteredExercises.length === 0" class="px-3 py-4 text-center text-sm text-[#8e8e93] dark:text-gray-500">
                                    {{ __('client.log_workout.no_exercises_found') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                    <label for="completed_at" class="block text-sm font-medium text-[#222222] dark:text-gray-100 mb-1">{{ __('client.log_workout.date_time') }}</label>
                    <input
                        type="datetime-local"
                        id="completed_at"
                        name="completed_at"
                        value="{{ old('completed_at', now()->format('Y-m-d\TH:i')) }}"
                        max="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#1456f0] focus:ring-[#1456f0] @error('completed_at') border-red-300 @enderror"
                    >
                    @error('completed_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-[#8e8e93] dark:text-gray-500">{{ __('client.log_workout.date_time_hint') }}</p>
                </div>

                <!-- Notes -->
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                    <label for="notes" class="block text-sm font-medium text-[#222222] dark:text-gray-100 mb-1">{{ __('client.log_workout.notes_optional') }}</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="2"
                        placeholder="{{ __('client.log_workout.notes_placeholder') }}"
                        x-model="notes"
                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#1456f0] focus:ring-[#1456f0]"
                    >{{ old('notes') }}</textarea>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    x-bind:disabled="exercises.length === 0"
                    class="w-full inline-flex justify-center items-center px-6 py-3 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ __('client.log_workout.complete_workout') }}
                </button>
            </div>
        </form>

        <!-- Exercise Detail Modal -->
        <div x-show="selectedExercise" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="selectedExercise = null"></div>
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-y-auto max-h-[85vh]">
                <div class="flex items-start justify-between px-5 pt-5 pb-4">
                    <div>
                        <h2 class="font-display text-lg font-semibold text-[#222222] dark:text-gray-100" x-text="selectedExercise ? selectedExercise.name : ''"></h2>
                        <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-[#1456f0] dark:bg-blue-900/30 dark:text-blue-400" x-text="selectedExercise ? selectedExercise.muscle_group.replace('_', ' ') : ''"></span>
                    </div>
                    <button type="button" @click="selectedExercise = null" class="p-2 -mr-1 text-[#8e8e93] hover:text-[#222222] dark:hover:text-gray-300 rounded-lg" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-5 pb-4">
                    <template x-if="selectedExercise && selectedExercise.embed_url">
                        <div class="aspect-video rounded-lg overflow-hidden bg-black">
                            <iframe :src="selectedExercise.embed_url" class="w-full h-full" :title="selectedExercise.name" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </template>
                    <div x-show="!selectedExercise || !selectedExercise.embed_url" class="aspect-video rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.log_workout.no_video') }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-5 pb-8">
                    <h3 class="text-sm font-medium text-[#8e8e93] dark:text-gray-500 mb-2">{{ __('client.log_workout.description') }}</h3>
                    <p x-show="selectedExercise && selectedExercise.description" class="text-sm text-[#45515e] dark:text-gray-300 whitespace-pre-wrap" x-text="selectedExercise ? selectedExercise.description : ''"></p>
                    <p x-show="!selectedExercise || !selectedExercise.description" class="text-sm text-[#8e8e93] dark:text-gray-500 italic">{{ __('client.log_workout.no_description') }}</p>
                </div>

                <!-- Progress Section -->
                <div class="px-5 pb-8 border-t border-gray-100 dark:border-gray-800 pt-5">
                    <h3 class="text-sm font-medium text-[#8e8e93] dark:text-gray-500 mb-3">{{ __('client.exercise_progress.heading') }}</h3>

                    <!-- Range selector -->
                    <div class="flex gap-1 mb-4">
                        <template x-for="r in [30, 90, 365, 0]" :key="r">
                            <button
                                type="button"
                                @click="progressRange = r; selectedExercise && loadProgress(selectedExercise.exercise_id, r)"
                                :class="progressRange === r ? 'text-white' : 'bg-gray-100 dark:bg-gray-800 text-[#45515e] dark:text-gray-300'"
                                :style="progressRange === r ? 'background-color: var(--color-primary)' : ''"
                                class="px-2.5 py-1 rounded text-xs font-medium transition-colors"
                                x-text="r === 30 ? '30d' : r === 90 ? '90d' : r === 365 ? '1yr' : '{{ __('client.exercise_progress.all_time') }}'"
                            ></button>
                        </template>
                    </div>

                    <!-- Loading spinner -->
                    <div x-show="progressLoading" class="flex items-center justify-center py-8">
                        <svg class="animate-spin h-6 w-6 text-[#1456f0]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>

                    <!-- Data -->
                    <template x-if="!progressLoading && progressData">
                        <div class="space-y-4">
                            <!-- PR stats -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                                    <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ __('client.exercise_progress.max_weight') }}</p>
                                    <p class="text-lg font-semibold text-[#222222] dark:text-gray-100 mt-1" x-text="progressData.maxWeight !== null ? progressData.maxWeight + ' kg' : '—'"></p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                                    <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ __('client.exercise_progress.est_1rm') }}</p>
                                    <p class="text-lg font-semibold text-[#222222] dark:text-gray-100 mt-1" x-text="progressData.estimated1rm !== null ? progressData.estimated1rm + ' kg' : '—'"></p>
                                </div>
                            </div>

                            <!-- No chart data -->
                            <p x-show="progressData.weightChart.length === 0" class="text-sm text-[#8e8e93] dark:text-gray-500 italic text-center py-4">{{ __('client.exercise_progress.no_data') }}</p>

                            <!-- Charts -->
                            <template x-if="progressData.weightChart.length > 0">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs text-[#8e8e93] dark:text-gray-500 mb-1">{{ __('client.exercise_progress.weight_chart') }}</p>
                                        <canvas id="logProgressWeightChart" height="120"></canvas>
                                    </div>
                                    <div>
                                        <p class="text-xs text-[#8e8e93] dark:text-gray-500 mb-1">{{ __('client.exercise_progress.volume_chart') }}</p>
                                        <canvas id="logProgressVolumeChart" height="120"></canvas>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        function workoutLogger() {
            const storageKey = '{{ $isCustom ? "workout_logger_custom" : "workout_logger_" . ($workout->id ?? "custom") }}';
            const resumeUrl = '{{ url()->current() }}';
            const workoutName = '{{ $isCustom ? "Custom Workout" : $workout->name }}';

            return {
                exercises: @json($exercisesData),
                availableExercises: [],
                exerciseSearch: '',
                showExercisePicker: false,
                exercisesLoaded: false,
                selectedExercise: null,
                progressData: null,
                progressRange: 90,
                progressLoading: false,
                _progressCharts: [],
                restoreBanner: false,
                notes: '',
                _pendingRestore: null,
                _savedAtFormatted: '',
                _saveTimer: null,

                init() {
                    const saved = localStorage.getItem(storageKey);
                    if (saved) {
                        try {
                            const parsed = JSON.parse(saved);
                            // Always refresh workout metadata so the global banner shows the correct name
                            const refreshed = { ...parsed, workoutName, resumeUrl };
                            localStorage.setItem(storageKey, JSON.stringify(refreshed));
                            const savedAt = new Date(parsed.savedAt);
                            const isToday = savedAt.toDateString() === new Date().toDateString();
                            this._pendingRestore = refreshed;
                            this.restoreBanner = true;
                            this._savedAtFormatted = isToday
                                ? savedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                                : savedAt.toLocaleDateString([], { month: 'short', day: 'numeric' }) + ' at ' + savedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        } catch {
                            localStorage.removeItem(storageKey);
                        }
                    }

                    this.$watch('exercises', () => { this.debouncedSave(); }, { deep: true });
                    this.$watch('notes', () => { this.debouncedSave(); });

                    this.$watch('selectedExercise', (val) => {
                        if (val && val.exercise_id) {
                            this.progressRange = 90;
                            this.loadProgress(val.exercise_id, 90);
                        } else {
                            this.progressData = null;
                            this._destroyProgressCharts();
                        }
                    });
                },

                debouncedSave() {
                    clearTimeout(this._saveTimer);
                    this._saveTimer = setTimeout(() => { this.saveState(); }, 800);
                },

                saveState() {
                    const state = {
                        exercises: this.exercises,
                        notes: this.notes,
                        savedAt: new Date().toISOString(),
                        resumeUrl: resumeUrl,
                        workoutName: workoutName,
                    };
                    localStorage.setItem(storageKey, JSON.stringify(state));
                },

                clearSavedState() {
                    localStorage.removeItem(storageKey);
                },

                confirmRestore() {
                    if (this._pendingRestore) {
                        this.exercises = this._pendingRestore.exercises;
                        this.notes = this._pendingRestore.notes ?? '';
                    }
                    this._pendingRestore = null;
                    this.restoreBanner = false;
                },

                discardRestore() {
                    this._pendingRestore = null;
                    this.restoreBanner = false;
                    this.clearSavedState();
                },

                async submitWorkout(event) {
                    const form = event.target;
                    const formData = new FormData(form);
                    const payload = this.formDataToObject(formData);

                    this.clearSavedState();

                    const token = this.getCsrfToken();
                    try {
                        const response = await fetch('{{ route("client.log.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-XSRF-TOKEN': token,
                            },
                            body: JSON.stringify(payload),
                            credentials: 'include',
                        });

                        if (response.ok) {
                            const data = await response.json();
                            window.location.href = data.redirect;
                            return;
                        }
                    } catch {}

                    // Validation error or network failure — native submit shows server errors
                    form.submit();
                },

                getCsrfToken() {
                    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
                    return match ? decodeURIComponent(match[1]) : '';
                },

                formDataToObject(formData) {
                    const obj = {};
                    for (const [key, value] of formData.entries()) {
                        const keys = key.replace(/\]/g, '').split('[');
                        let current = obj;
                        for (let i = 0; i < keys.length - 1; i++) {
                            const k = keys[i];
                            const nextKey = keys[i + 1];
                            if (current[k] === undefined) {
                                current[k] = isNaN(nextKey) ? {} : [];
                            }
                            current = current[k];
                        }
                        current[keys[keys.length - 1]] = value;
                    }
                    return obj;
                },

                initSortable() {
                    this.$nextTick(() => {
                        const container = this.$refs.exerciseList;
                        if (!container) return;
                        Sortable.create(container, {
                            handle: '.drag-handle',
                            animation: 150,
                            onEnd: (evt) => {
                                const item = this.exercises.splice(evt.oldIndex, 1)[0];
                                this.exercises.splice(evt.newIndex, 0, item);
                            },
                        });
                    });
                },

                moveExerciseUp(index) {
                    if (index <= 0) return;
                    const temp = this.exercises[index];
                    this.exercises.splice(index, 1);
                    this.exercises.splice(index - 1, 0, temp);
                },

                moveExerciseDown(index) {
                    if (index >= this.exercises.length - 1) return;
                    const temp = this.exercises[index];
                    this.exercises.splice(index, 1);
                    this.exercises.splice(index + 1, 0, temp);
                },

                get filteredExercises() {
                    const search = this.exerciseSearch.toLowerCase();
                    const usedIds = this.exercises.map(e => e.exercise_id);

                    return this.availableExercises
                        .filter(e => !usedIds.includes(e.id))
                        .filter(e =>
                            !search ||
                            e.name.toLowerCase().includes(search) ||
                            e.muscle_group.toLowerCase().includes(search)
                        );
                },

                async openExercisePicker() {
                    if (!this.exercisesLoaded) {
                        try {
                            const response = await fetch('{{ route("client.log.exercises") }}');
                            this.availableExercises = await response.json();
                            this.exercisesLoaded = true;
                        } catch (error) {
                            console.error('Failed to load exercises:', error);
                        }
                    }
                    this.exerciseSearch = '';
                    this.showExercisePicker = true;
                    this.$nextTick(() => this.$refs.exerciseSearchInput?.focus());
                },

                addExercise(exercise) {
                    this.exercises.push({
                        workout_exercise_id: null,
                        exercise_id: exercise.id,
                        name: exercise.name,
                        muscle_group: exercise.muscle_group,
                        description: exercise.description || null,
                        embed_url: exercise.embed_url || null,
                        prescribed_sets: null,
                        prescribed_reps: null,
                        previous_sets: exercise.previous_sets || [],
                        lock_removal: false,
                        sets: [{ weight: '', reps: '' }],
                    });
                    this.showExercisePicker = false;
                    this.exerciseSearch = '';
                },

                removeExercise(index) {
                    this.exercises.splice(index, 1);
                },

                addSet(exerciseIndex) {
                    this.exercises[exerciseIndex].sets.push({ weight: '', reps: '' });
                },

                removeSet(exerciseIndex, setIndex) {
                    this.exercises[exerciseIndex].sets.splice(setIndex, 1);
                },

                loadProgress(exerciseId, range) {
                    this.progressLoading = true;
                    this.progressData = null;
                    this._destroyProgressCharts();
                    fetch(`/client/exercises/${exerciseId}/progress?range=${range}`)
                        .then(r => {
                            if (!r.ok) {
                                throw new Error(r.status);
                            }
                            return r.json();
                        })
                        .then(data => {
                            this.progressData = data;
                            this.progressLoading = false;
                            this.$nextTick(() => this._renderProgressCharts(data));
                        })
                        .catch(() => {
                            this.progressLoading = false;
                        });
                },

                _destroyProgressCharts() {
                    this._progressCharts.forEach(c => c.destroy());
                    this._progressCharts = [];
                },

                _renderProgressCharts(data) {
                    if (data.weightChart.length === 0) {
                        return;
                    }

                    const labels = data.weightChart.map(p => p.date);
                    const commonOptions = {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { x: { ticks: { maxTicksLimit: 8 } } },
                    };

                    const wCtx = document.getElementById('logProgressWeightChart');
                    if (wCtx) {
                        this._progressCharts.push(new Chart(wCtx, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    data: data.weightChart.map(p => p.weight),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    pointRadius: 3,
                                }],
                            },
                            options: { ...commonOptions },
                        }));
                    }

                    const vCtx = document.getElementById('logProgressVolumeChart');
                    if (vCtx) {
                        this._progressCharts.push(new Chart(vCtx, {
                            type: 'line',
                            data: {
                                labels: data.volumeChart.map(p => p.date),
                                datasets: [{
                                    data: data.volumeChart.map(p => p.volume),
                                    borderColor: 'rgb(16, 185, 129)',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    pointRadius: 3,
                                }],
                            },
                            options: { ...commonOptions },
                        }));
                    }
                },
            }
        }
    </script>
    @endpush
</x-layouts.client>
