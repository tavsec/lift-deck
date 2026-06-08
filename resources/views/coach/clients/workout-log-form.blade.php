<x-layouts.coach>
    @php
    $isEdit = isset($workoutLog);
    $preloadedExercises = [];
    if ($isEdit) {
        $grouped = $workoutLog->exerciseLogs->groupBy('exercise_id');
        foreach ($grouped as $exerciseId => $logs) {
            $exercise = $logs->first()->exercise;
            $preloadedExercises[] = [
                'workout_exercise_id' => $logs->first()->workout_exercise_id,
                'exercise_id' => $exerciseId,
                'name' => $exercise->name,
                'muscle_group' => $exercise->muscle_group,
                'description' => $exercise->description,
                'embed_url' => $exercise->getYoutubeEmbedUrl(),
                'prescribed_sets' => null,
                'prescribed_reps' => null,
                'previous_sets' => [],
                'sets' => $logs->sortBy('set_number')->map(fn ($l) => ['weight' => $l->weight, 'reps' => $l->reps])->values()->all(),
            ];
        }
    }
    @endphp

    <x-slot:title>{{ $isEdit ? __('coach.clients.workout_log_form.heading_edit', ['name' => $client->name]) : __('coach.clients.workout_log_form.heading_create', ['name' => $client->name]) }}</x-slot:title>

    <div
        x-data="workoutLogger()"
        class="space-y-6"
        @keydown.escape.window="selectedExercise = null"
    >
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.workout_log_form.back', ['name' => $client->name]) }}
            </a>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">
                {{ $isEdit ? __('coach.clients.workout_log_form.heading_edit', ['name' => $client->name]) : __('coach.clients.workout_log_form.heading_create', ['name' => $client->name]) }}
            </h1>
            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">
                {{ $isEdit ? __('coach.clients.workout_log_form.subtitle_edit') : __('coach.clients.workout_log_form.subtitle_create') }}
            </p>
        </div>

        @if($errors->any())
            <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ __('coach.clients.workout_log_form.errors') }}</p>
                <ul class="mt-2 text-sm text-red-700 dark:text-red-400 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $isEdit ? route('coach.clients.workout-logs.update', [$client, $workoutLog]) : route('coach.clients.workout-logs.store', $client) }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="space-y-4">
                <!-- Workout Name -->
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-4">
                    <label for="custom_name" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.workout_log_form.workout_name') }}</label>
                    <input
                        type="text"
                        id="custom_name"
                        name="custom_name"
                        value="{{ old('custom_name', $isEdit ? $workoutLog->custom_name : '') }}"
                        placeholder="{{ __('coach.clients.workout_log_form.workout_name_placeholder') }}"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('custom_name') border-red-300 dark:border-red-700 @enderror"
                    >
                    @error('custom_name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exercises -->
                <div x-ref="exerciseList" x-init="initSortable()" class="space-y-4">
                <template x-for="(exercise, exerciseIndex) in exercises" :key="exercise.exercise_id">
                    <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <!-- Drag Handle -->
                                    <div class="drag-handle cursor-grab active:cursor-grabbing text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] touch-none">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <button type="button" class="text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] text-left hover:underline focus:outline-none" @click="selectedExercise = exercise" x-text="exercise.name"></button>
                                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]" x-show="exercise.prescribed_sets">
                                            Prescribed: <span x-text="exercise.prescribed_sets"></span> sets &times; <span x-text="exercise.prescribed_reps"></span> reps
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-[#45515e] dark:bg-[#11141a] dark:text-[#a4abb6]" x-text="exercise.muscle_group.replace('_', ' ')"></span>
                                    <!-- Move Up -->
                                    <button type="button" @click="moveExerciseUp(exerciseIndex)" :disabled="exerciseIndex === 0"
                                        class="p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-gray-600 dark:hover:text-[#f0f2f5] rounded hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Move up">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </button>
                                    <!-- Move Down -->
                                    <button type="button" @click="moveExerciseDown(exerciseIndex)" :disabled="exerciseIndex === exercises.length - 1"
                                        class="p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-gray-600 dark:hover:text-[#f0f2f5] rounded hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Move down">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <!-- Remove -->
                                    <button type="button" @click="removeExercise(exerciseIndex)"
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
                            <div x-show="exercise.previous_sets && exercise.previous_sets.length > 0" class="text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                <span class="font-medium">{{ __('coach.clients.workout_log_form.last_session') }}</span>
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
                                        <tr class="text-left text-xs text-[#8c93a0] dark:text-[#6b7280] border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                                            <th class="pb-2 pr-3 w-12 font-medium uppercase tracking-wide">{{ __('coach.clients.workout_log_form.set') }}</th>
                                            <th class="pb-2 pr-3 font-medium uppercase tracking-wide">{{ __('coach.clients.workout_log_form.weight_kg') }}</th>
                                            <th class="pb-2 pr-2 font-medium uppercase tracking-wide">{{ __('coach.clients.workout_log_form.reps') }}</th>
                                            <th class="pb-2 w-8"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(set, setIndex) in exercise.sets" :key="setIndex">
                                            <tr>
                                                <td class="py-1.5 pr-3 text-[#8c93a0] dark:text-[#6b7280] font-medium" x-text="setIndex + 1"></td>
                                                <td class="py-1.5 pr-3">
                                                    <input
                                                        type="number"
                                                        step="0.5"
                                                        min="0"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][weight]`"
                                                        x-model="set.weight"
                                                        placeholder="0"
                                                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-2.5 py-1.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                                    >
                                                </td>
                                                <td class="py-1.5 pr-2">
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][reps]`"
                                                        x-model="set.reps"
                                                        :placeholder="exercise.prescribed_reps || '0'"
                                                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-2.5 py-1.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                                    >
                                                </td>
                                                <td class="py-1.5">
                                                    <button
                                                        type="button"
                                                        @click="removeSet(exerciseIndex, setIndex)"
                                                        x-show="exercise.sets.length > 1"
                                                        class="p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-red-500 transition-colors"
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
                                class="inline-flex items-center text-xs font-medium hover:underline focus:outline-none" style="color: var(--color-primary)"
                            >
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ __('coach.clients.workout_log_form.add_set') }}
                            </button>
                        </div>
                    </div>
                </template>
                </div>

                <!-- Empty State -->
                <div x-show="exercises.length === 0" class="text-center py-8">
                    <div class="w-12 h-12 rounded-2xl bg-[#f3f5f7] dark:bg-[#1d2027] flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8c93a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.workout_log_form.no_exercises') }}</p>
                </div>

                <!-- Add Exercise -->
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-4">
                    <div x-show="!showExercisePicker">
                        <button
                            type="button"
                            @click="openExercisePicker()"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 border-2 border-dashed border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-xl text-sm font-medium text-[#8c93a0] dark:text-[#6b7280] hover:border-[#c6f24e] transition-colors" 
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('coach.clients.workout_log_form.add_exercise') }}
                        </button>
                    </div>

                    <div x-show="showExercisePicker" x-cloak>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6]">{{ __('coach.clients.workout_log_form.select_exercise') }}</label>
                                <button type="button" @click="showExercisePicker = false" class="text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <input
                                type="text"
                                x-model="exerciseSearch"
                                placeholder="{{ __('coach.clients.workout_log_form.search_exercises') }}"
                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                x-ref="exerciseSearchInput"
                            >
                            <div class="max-h-48 overflow-y-auto border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] rounded-xl divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                                <template x-for="exercise in filteredExercises" :key="exercise.id">
                                    <button
                                        type="button"
                                        @click="addExercise(exercise)"
                                        class="w-full text-left px-3 py-2 hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors flex items-center justify-between"
                                    >
                                        <div>
                                            <span class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]" x-text="exercise.name"></span>
                                            <span class="text-xs text-[#8c93a0] dark:text-[#6b7280] ml-2" x-text="exercise.muscle_group.replace('_', ' ')"></span>
                                        </div>
                                        <svg class="w-4 h-4 text-[#8c93a0] dark:text-[#6b7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </template>
                                <div x-show="filteredExercises.length === 0" class="px-3 py-4 text-center text-sm text-[#8c93a0] dark:text-[#6b7280]">
                                    {{ __('coach.clients.workout_log_form.no_exercises_found') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-4">
                    <label for="completed_at" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.workout_log_form.date_time') }}</label>
                    <input
                        type="datetime-local"
                        id="completed_at"
                        name="completed_at"
                        value="{{ old('completed_at', $isEdit ? $workoutLog->completed_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                        max="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('completed_at') border-red-300 dark:border-red-700 @enderror"
                    >
                    @error('completed_at')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.workout_log_form.date_time_hint') }}</p>
                </div>

                <!-- Notes -->
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-4">
                    <label for="notes" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.workout_log_form.notes') }}</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="2"
                        placeholder="{{ __('coach.clients.workout_log_form.notes_placeholder') }}"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                    >{{ old('notes', $isEdit ? $workoutLog->notes : '') }}</textarea>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full inline-flex justify-center items-center px-6 py-3 bg-[#181b22] dark:bg-[#c6f24e] border border-transparent rounded-lg font-semibold text-sm text-white dark:text-[#14180a] hover:bg-[#2d3748] dark:hover:bg-[#b4e438] focus:outline-none transition ease-in-out duration-150"
                >
                    {{ $isEdit ? __('coach.clients.workout_log_form.update') : __('coach.clients.workout_log_form.save') }}
                </button>
            </div>
        </form>

        <!-- Exercise Detail Modal -->
        <div x-show="selectedExercise" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="selectedExercise = null"></div>
            <div class="relative w-full max-w-2xl bg-white dark:bg-[#16191f] rounded-2xl border border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)] overflow-y-auto max-h-[85vh]" style="box-shadow: rgba(44,30,116,0.12) 0px 0px 24px;">
                <div class="flex items-start justify-between px-5 pt-5 pb-4">
                    <div>
                        <h2 class="font-display text-lg font-semibold text-[#181b22] dark:text-[#f0f2f5]" x-text="selectedExercise ? selectedExercise.name : ''"></h2>
                        <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#f3f5f7] dark:bg-[#1d2027] text-[#555b66] dark:text-[#a4abb6]" x-text="selectedExercise ? selectedExercise.muscle_group.replace('_', ' ') : ''"></span>
                    </div>
                    <button type="button" @click="selectedExercise = null" class="p-2 -mr-1 text-[#8c93a0] hover:text-[#45515e] dark:hover:text-[#f0f2f5] rounded-lg" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-5 pb-4">
                    <template x-if="selectedExercise && selectedExercise.embed_url">
                        <div class="aspect-video rounded-xl overflow-hidden bg-black">
                            <iframe :src="selectedExercise.embed_url" class="w-full h-full" :title="selectedExercise.name" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </template>
                    <div x-show="!selectedExercise || !selectedExercise.embed_url" class="aspect-video rounded-xl bg-[#f3f5f7] dark:bg-[#1d2027] flex items-center justify-center">
                        <div class="text-center">
                            <svg class="mx-auto h-10 w-10 text-[#8c93a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.workout_log_form.no_video') }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-5 pb-8">
                    <h3 class="text-xs font-medium text-[#8c93a0] dark:text-[#6b7280] uppercase tracking-wide mb-2">{{ __('coach.clients.workout_log_form.description') }}</h3>
                    <p x-show="selectedExercise && selectedExercise.description" class="text-sm text-[#555b66] dark:text-[#a4abb6] whitespace-pre-wrap" x-text="selectedExercise ? selectedExercise.description : ''"></p>
                    <p x-show="!selectedExercise || !selectedExercise.description" class="text-sm text-[#8c93a0] dark:text-[#6b7280] italic">{{ __('coach.clients.workout_log_form.no_description') }}</p>
                </div>
            </div>
        </div>
    </div>

    @php
        $availableExercisesJson = $exercises->map(fn ($e) => [
            'id' => $e->id,
            'name' => $e->name,
            'muscle_group' => $e->muscle_group,
            'description' => $e->description,
            'embed_url' => $e->getYoutubeEmbedUrl(),
            'previous_sets' => [],
        ]);
    @endphp

    @push('scripts')
    <script>
        function workoutLogger() {
            return {
                exercises: @json($preloadedExercises),
                availableExercises: @json($availableExercisesJson),
                exerciseSearch: '',
                showExercisePicker: false,
                exercisesLoaded: true,
                selectedExercise: null,

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

                openExercisePicker() {
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
                        sets: [{ weight: '', reps: '' }],
                    });
                    this.showExercisePicker = false;
                    this.exerciseSearch = '';
                },

                removeExercise(index) {
                    this.exercises.splice(index, 1);
                },

                addSet(exerciseIndex) {
                    this.exercises[exerciseIndex].sets.push({ weight: 0, reps: 0 });
                },

                removeSet(exerciseIndex, setIndex) {
                    this.exercises[exerciseIndex].sets.splice(setIndex, 1);
                },
            }
        }
    </script>
    @endpush
</x-layouts.coach>
