<x-layouts.client>
    <x-slot:title>{{ $isCustom ? 'Custom Workout' : 'Log: ' . $workout->name }}</x-slot:title>

    <div
        x-data="workoutLogger()"
        class="px-4 py-5 space-y-4"
        @keydown.escape.window="selectedExercise = null; rpePicker = null"
        @click.window="rpePicker = null"
    >
        <!-- Header -->
        <div class="mb-5">
            <a href="{{ route('client.log') }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#181b22] dark:hover:text-gray-300 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('client.log_workout.back') }}
            </a>
            @if($isCustom)
                <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('client.log_workout.custom_workout') }}</h1>
                <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.log_workout.custom_workout_description') }}</p>
            @else
                <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ $workout->name }}</h1>
                <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.program.day_n', ['n' => $workout->day_number]) }} &middot; <span x-text="exercises.length"></span> {{ __('client.log_workout.exercises_label') }}</p>
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
            class="rounded-lg bg-[rgba(198,242,78,0.12)] dark:bg-[rgba(198,242,78,0.08)] border border-[rgba(198,242,78,0.3)] p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-[#5c7a10] dark:text-[#c6f24e]">{{ __('client.log_workout.restore_banner_title') }}</p>
                    <p class="text-xs text-[#5c7a10]/70 dark:text-[#c6f24e]/70 mt-0.5">
                        {{ str_replace(':time', '', __('client.log_workout.restore_banner_description')) }}<span x-text="_savedAtFormatted"></span>{{ Str::after(__('client.log_workout.restore_banner_description'), ':time') }}
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button type="button" @click="confirmRestore()"
                        class="text-xs font-semibold px-3 py-1.5 bg-[#c6f24e] text-[#14180a] rounded-lg hover:bg-[#b4e438] transition-colors">
                        {{ __('client.log_workout.restore') }}
                    </button>
                    <button type="button" @click="discardRestore()"
                        class="text-xs font-medium px-3 py-1.5 bg-white dark:bg-gray-800 text-[#555b66] dark:text-[#a4abb6] border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('client.log_workout.start_fresh') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Offline banner -->
        <div x-show="isOffline" x-cloak
            class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-3">
            <p class="text-sm text-amber-700 dark:text-amber-400 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M12 12h.01M8.464 15.536a5 5 0 01-.068-7.004M5.636 5.636a9 9 0 000 12.728"/>
                </svg>
                {{ __('client.log_workout.offline') }}
            </p>
        </div>

        <!-- Offline submission banner -->
        <div x-show="showOfflineSubmitBanner" x-cloak
            class="rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3">
            <p class="text-sm text-green-700 dark:text-green-400 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('client.log_workout.offline_submit') }}
            </p>
        </div>

        <form method="POST" action="{{ route('client.log.store') }}" @submit.prevent="submitWorkout($event)">
            @csrf

            @if(!$isCustom)
                <input type="hidden" name="program_workout_id" value="{{ $workout->id }}">
            @endif

            <div class="space-y-4">
                <!-- Custom Workout Name -->
                @if($isCustom)
                    <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                        <label for="custom_name" class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-1">{{ __('client.log_workout.workout_name') }}</label>
                        <input
                            type="text"
                            id="custom_name"
                            name="custom_name"
                            value="{{ old('custom_name') }}"
                            placeholder="{{ __('client.log_workout.workout_name_placeholder') }}"
                            required
                            class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] @error('custom_name') border-red-300 @enderror"
                        >
                        @error('custom_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Exercises -->
                <div x-ref="exerciseList" x-init="initSortable()" class="space-y-4">
                <template x-for="(exercise, exerciseIndex) in exercises" :key="exercise.exercise_id">
                    <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                        <div class="space-y-3">
                            <!-- Exercise header: drag | thumb | name+prescribed | remove -->
                            <div class="flex items-start gap-2.5">
                                <div class="drag-handle cursor-grab active:cursor-grabbing touch-none flex-shrink-0 mt-1 opacity-40 hover:opacity-70 transition-opacity">
                                    <svg class="w-4 h-4 text-[#555b66] dark:text-[#a4abb6]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                    </svg>
                                </div>
                                <div x-html="exThumbHtml(exercise.muscle_group, 40)" class="flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <button type="button" class="font-display font-semibold text-[15px] text-[#181b22] dark:text-[#f0f2f5] text-left hover:underline focus:outline-none leading-snug" @click="selectedExercise = exercise" x-text="exercise.name"></button>
                                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5" x-show="exercise.prescribed_sets">
                                        {{ Str::before(__('client.log_workout.prescribed'), ':sets') }}<span x-text="exercise.prescribed_sets"></span>{{ Str::between(__('client.log_workout.prescribed'), ':sets', ':reps') }}<span x-text="exercise.prescribed_reps"></span>{{ Str::after(__('client.log_workout.prescribed'), ':reps') }}
                                    </p>
                                </div>
                                <button type="button" @click="removeExercise(exerciseIndex)" x-show="!exercise.lock_removal"
                                    class="flex-shrink-0 mt-0.5 p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-red-500 dark:hover:text-red-400 rounded transition-colors" title="Remove exercise">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Hidden fields -->
                            <input type="hidden" :name="`exercises[${exerciseIndex}][workout_exercise_id]`" :value="exercise.workout_exercise_id || ''">
                            <input type="hidden" :name="`exercises[${exerciseIndex}][exercise_id]`" :value="exercise.exercise_id">

                            <!-- Previous Session Data -->
                            <div x-show="exercise.previous_sets && exercise.previous_sets.length > 0" class="text-xs text-[#8c93a0] dark:text-[#6b7280]">
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
                                        <tr class="text-left text-xs text-[#8c93a0] dark:text-[#6b7280] border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                                            <th class="pb-2 pr-2 w-8">{{ __('client.log_workout.set') }}</th>
                                            <th class="pb-2 pr-2">{{ __('client.log_workout.weight_kg') }}</th>
                                            <th class="pb-2 pr-2">{{ __('client.log_workout.reps') }}</th>
                                            <th class="pb-2 pr-1 w-14 text-center">RPE</th>
                                            <th class="pb-2 w-6"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(set, setIndex) in exercise.sets" :key="setIndex">
                                            <tr>
                                                <td class="py-1.5 pr-3 text-[#555b66] dark:text-[#a4abb6] font-medium font-mono" x-text="setIndex + 1"></td>
                                                <td class="py-1.5 pr-2">
                                                    <input
                                                        type="text"
                                                        inputmode="decimal"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][weight]`"
                                                        x-model="set.weight"
                                                        placeholder="0"
                                                        class="w-full h-[44px] rounded-xl border-[1.5px] border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] font-mono font-semibold text-base text-center outline-none focus:border-[#c6f24e] focus:shadow-[0_0_0_3px_rgba(198,242,78,0.22)] transition-all"
                                                    >
                                                </td>
                                                <td class="py-1.5 pr-2">
                                                    <input
                                                        type="text"
                                                        inputmode="numeric"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][reps]`"
                                                        x-model="set.reps"
                                                        :placeholder="exercise.prescribed_reps || '0'"
                                                        class="w-full h-[44px] rounded-xl border-[1.5px] border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] font-mono font-semibold text-base text-center outline-none focus:border-[#c6f24e] focus:shadow-[0_0_0_3px_rgba(198,242,78,0.22)] transition-all"
                                                    >
                                                </td>
                                                <td class="py-1.5 pr-1 relative">
                                                    <!-- Hidden field for form submission -->
                                                    <input type="hidden" :name="`exercises[${exerciseIndex}][sets][${setIndex}][rpe]`" :value="set.rpe || ''">
                                                    <!-- RPE pill button -->
                                                    <button
                                                        type="button"
                                                        @click.stop="rpePicker = (rpePicker?.ei === exerciseIndex && rpePicker?.si === setIndex) ? null : {ei: exerciseIndex, si: setIndex}"
                                                        class="w-full h-[44px] rounded-xl border-[1.5px] font-mono font-bold text-sm transition-all flex items-center justify-center"
                                                        :style="set.rpe ? `border-color:${rpeColor(set.rpe)};color:${rpeColor(set.rpe)};background:${rpeColor(set.rpe)}18` : 'border-color:rgba(18,22,31,0.14);color:var(--tw-text-opacity,1)'"
                                                        :class="set.rpe ? '' : 'text-[#8c93a0] dark:text-[#6b7280] bg-white dark:bg-[#11141a]'"
                                                    >
                                                        <span x-text="set.rpe || '—'"></span>
                                                    </button>
                                                    <!-- RPE picker dropdown -->
                                                    <div
                                                        x-show="rpePicker?.ei === exerciseIndex && rpePicker?.si === setIndex"
                                                        x-cloak
                                                        @click.stop
                                                        @keydown.escape.window="rpePicker = null"
                                                        class="absolute bottom-full right-0 z-30 mb-2 bg-white dark:bg-[#181b21] border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] rounded-2xl shadow-[0_12px_32px_rgba(18,22,31,0.18)] p-3"
                                                        style="width: 220px"
                                                    >
                                                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#8c93a0] dark:text-[#6b7280] mb-2 text-center">RPE</p>
                                                        <div class="grid grid-cols-5 gap-1.5">
                                                            <template x-for="n in [1,2,3,4,5,6,7,8,9,10]" :key="n">
                                                                <button
                                                                    type="button"
                                                                    @click="set.rpe = n; rpePicker = null"
                                                                    class="h-10 rounded-xl border-[1.5px] flex flex-col items-center justify-center transition-all"
                                                                    :style="`border-color:${set.rpe === n ? rpeColor(n) : 'rgba(18,22,31,0.1)'};background:${set.rpe === n ? rpeColor(n)+'28' : 'transparent'};color:${rpeColor(n)}`"
                                                                >
                                                                    <span class="font-mono font-bold text-sm leading-none" x-text="n"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                        <button type="button" @click="set.rpe = null; rpePicker = null"
                                                            class="w-full mt-2 py-1.5 rounded-lg text-xs font-semibold text-[#8c93a0] dark:text-[#6b7280] hover:bg-[rgba(18,22,31,0.05)] dark:hover:bg-[rgba(255,255,255,0.05)] transition-colors">
                                                            Clear
                                                        </button>
                                                    </div>
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
                                class="inline-flex items-center text-xs text-[#5c7a10] dark:text-[#c6f24e] hover:opacity-80 font-semibold"
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
                    <p class="mt-2 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.log_workout.no_exercises') }}</p>
                </div>

                <!-- Add Exercise -->
                <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                    <div x-show="!showExercisePicker">
                        <button
                            type="button"
                            @click="openExercisePicker()"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3.5 border-2 border-dashed border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.14)] rounded-xl text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:border-[#c6f24e] hover:text-[#5c7a10] dark:hover:border-[#c6f24e] dark:hover:text-[#c6f24e] transition-all"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('client.log_workout.add_exercise') }}
                        </button>
                    </div>

                    <div x-show="showExercisePicker" x-cloak class="space-y-2">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5] flex-1">{{ __('client.log_workout.select_exercise') }}</h3>
                            <button type="button" @click="showExercisePicker = false"
                                class="p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-[#181b22] dark:hover:text-[#f0f2f5] rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-2 px-3 h-11 bg-[#f3f5f7] dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-xl focus-within:border-[#c6f24e] focus-within:shadow-[0_0_0_3px_rgba(198,242,78,0.2)] transition-all">
                            <svg class="w-4 h-4 text-[#8c93a0] dark:text-[#6b7280] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input
                                type="text"
                                x-model="exerciseSearch"
                                placeholder="{{ __('client.log_workout.search_exercises') }}"
                                class="flex-1 border-0 bg-transparent outline-none text-sm text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0]"
                                x-ref="exerciseSearchInput"
                            >
                            <button type="button" x-show="exerciseSearch" @click="exerciseSearch = ''"
                                class="text-[#8c93a0] dark:text-[#6b7280] flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="max-h-64 overflow-y-auto rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] bg-white dark:bg-[#181b21]">
                            <template x-for="exercise in filteredExercises" :key="exercise.id">
                                <button
                                    type="button"
                                    @click="addExercise(exercise)"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-[rgba(198,242,78,0.08)] dark:hover:bg-[rgba(198,242,78,0.05)] transition-colors border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.05)] last:border-0 text-left"
                                >
                                    <div x-html="exThumbHtml(exercise.muscle_group, 36)" class="flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5] truncate" x-text="exercise.name"></div>
                                        <div class="text-xs text-[#8c93a0] dark:text-[#6b7280]" x-text="exercise.muscle_group.replace(/_/g, ' ')"></div>
                                    </div>
                                    <svg class="w-4 h-4 text-[#c6f24e] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </template>
                            <div x-show="filteredExercises.length === 0 && exercisesLoaded"
                                class="px-3 py-6 text-center text-sm text-[#8c93a0] dark:text-[#6b7280]">
                                {{ __('client.log_workout.no_exercises_found') }}
                            </div>
                            <div x-show="!exercisesLoaded"
                                class="px-3 py-6 text-center text-sm text-[#8c93a0] dark:text-[#6b7280]">
                                Loading…
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                    <label for="completed_at" class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-1">{{ __('client.log_workout.date_time') }}</label>
                    <input
                        type="datetime-local"
                        id="completed_at"
                        name="completed_at"
                        value="{{ old('completed_at', now()->format('Y-m-d\TH:i')) }}"
                        max="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] @error('completed_at') border-red-300 @enderror"
                    >
                    @error('completed_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.log_workout.date_time_hint') }}</p>
                </div>

                <!-- Notes -->
                <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                    <label for="notes" class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-1">{{ __('client.log_workout.notes_optional') }}</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="2"
                        placeholder="{{ __('client.log_workout.notes_placeholder') }}"
                        x-model="notes"
                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#c6f24e] focus:ring-[#c6f24e]"
                    >{{ old('notes') }}</textarea>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    x-bind:disabled="exercises.length === 0"
                    class="w-full inline-flex justify-center items-center px-6 py-3 bg-[#c6f24e] text-[#14180a] text-sm font-semibold rounded-xl hover:bg-[#b4e438] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ __('client.log_workout.complete_workout') }}
                </button>
            </div>
        </form>

        <!-- Exercise Detail Modal -->
        <div x-show="selectedExercise" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="selectedExercise = null"></div>
            <div class="relative w-full max-w-2xl bg-white dark:bg-[#181b21] rounded-2xl shadow-xl overflow-y-auto max-h-[85vh]">
                <div class="flex items-start justify-between px-5 pt-5 pb-4">
                    <div>
                        <h2 class="font-display text-lg font-semibold text-[#181b22] dark:text-[#f0f2f5]" x-text="selectedExercise ? selectedExercise.name : ''"></h2>
                        <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[rgba(198,242,78,0.15)] text-[#5c7a10] dark:bg-[rgba(198,242,78,0.12)] dark:text-[#c6f24e]" x-text="selectedExercise ? selectedExercise.muscle_group.replace('_', ' ') : ''"></span>
                    </div>
                    <button type="button" @click="selectedExercise = null" class="p-2 -mr-1 text-[#8c93a0] hover:text-[#181b22] dark:hover:text-gray-300 rounded-lg" aria-label="Close">
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
                            <p class="mt-2 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.log_workout.no_video') }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-5 pb-8">
                    <h3 class="text-sm font-medium text-[#8c93a0] dark:text-[#6b7280] mb-2">{{ __('client.log_workout.description') }}</h3>
                    <p x-show="selectedExercise && selectedExercise.description" class="text-sm text-[#555b66] dark:text-gray-300 whitespace-pre-wrap" x-text="selectedExercise ? selectedExercise.description : ''"></p>
                    <p x-show="!selectedExercise || !selectedExercise.description" class="text-sm text-[#8c93a0] dark:text-[#6b7280] italic">{{ __('client.log_workout.no_description') }}</p>
                </div>

                <!-- Progress Section -->
                <div class="px-5 pb-8 border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)] pt-5">
                    <h3 class="text-sm font-medium text-[#8c93a0] dark:text-[#6b7280] mb-3">{{ __('client.exercise_progress.heading') }}</h3>

                    <!-- Range selector -->
                    <div class="flex gap-1 mb-4">
                        <template x-for="r in [30, 90, 365, 0]" :key="r">
                            <button
                                type="button"
                                @click="progressRange = r; selectedExercise && loadProgress(selectedExercise.exercise_id, r)"
                                :class="progressRange === r ? 'bg-[#c6f24e] text-[#14180a]' : 'bg-gray-100 dark:bg-gray-800 text-[#555b66] dark:text-gray-300'"
                                class="px-2.5 py-1 rounded text-xs font-medium transition-colors"
                                x-text="r === 30 ? '30d' : r === 90 ? '90d' : r === 365 ? '1yr' : '{{ __('client.exercise_progress.all_time') }}'"
                            ></button>
                        </template>
                    </div>

                    <!-- Loading spinner -->
                    <div x-show="progressLoading" class="flex items-center justify-center py-8">
                        <svg class="animate-spin h-6 w-6 text-[#5c7a10] dark:text-[#c6f24e]" fill="none" viewBox="0 0 24 24">
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
                                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.exercise_progress.max_weight') }}</p>
                                    <p class="text-lg font-semibold font-mono text-[#181b22] dark:text-[#f0f2f5] mt-1" x-text="progressData.maxWeight !== null ? progressData.maxWeight + ' kg' : '—'"></p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 text-center">
                                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.exercise_progress.est_1rm') }}</p>
                                    <p class="text-lg font-semibold font-mono text-[#181b22] dark:text-[#f0f2f5] mt-1" x-text="progressData.estimated1rm !== null ? progressData.estimated1rm + ' kg' : '—'"></p>
                                </div>
                            </div>

                            <!-- No chart data -->
                            <p x-show="progressData.weightChart.length === 0" class="text-sm text-[#8c93a0] dark:text-[#6b7280] italic text-center py-4">{{ __('client.exercise_progress.no_data') }}</p>

                            <!-- Charts -->
                            <template x-if="progressData.weightChart.length > 0">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mb-1">{{ __('client.exercise_progress.weight_chart') }}</p>
                                        <canvas id="logProgressWeightChart" height="120"></canvas>
                                    </div>
                                    <div>
                                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mb-1">{{ __('client.exercise_progress.volume_chart') }}</p>
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
                isOffline: false,
                showOfflineSubmitBanner: false,
                notes: '',
                rpePicker: null,
                _pendingRestore: null,
                _savedAtFormatted: '',
                _saveTimer: null,

                init() {
                    this.isOffline = !navigator.onLine;
                    window.addEventListener('online', () => { this.isOffline = false; });
                    window.addEventListener('offline', () => { this.isOffline = true; });

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

                    if (!navigator.onLine) {
                        await this.queueWorkout(payload);
                        this.showOfflineSubmitBanner = true;
                        return;
                    }

                    await this.postWorkout(payload);
                },

                async postWorkout(payload) {
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
                        } else {
                            // Validation error — fall back to native form submit so errors display
                            const form = document.querySelector('form[action="{{ route("client.log.store") }}"]');
                            if (form) {
                                form.removeEventListener('submit', () => {});
                                form.submit();
                            }
                        }
                    } catch {
                        await this.queueWorkout(payload);
                        this.showOfflineSubmitBanner = true;
                    }
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

                async queueWorkout(payload) {
                    const db = await this.openDb();
                    await new Promise((resolve, reject) => {
                        const tx = db.transaction('pending_workouts', 'readwrite');
                        tx.objectStore('pending_workouts').add({ payload, queuedAt: new Date().toISOString() });
                        tx.oncomplete = resolve;
                        tx.onerror = reject;
                    });
                    db.close();

                    if ('serviceWorker' in navigator && 'SyncManager' in window) {
                        const reg = await navigator.serviceWorker.ready;
                        await reg.sync.register('sync-workout-logs');
                    } else {
                        window.addEventListener('online', async () => {
                            await this.flushQueuedWorkouts();
                        }, { once: true });
                    }
                },

                async flushQueuedWorkouts() {
                    const db = await this.openDb();
                    const all = await new Promise((resolve, reject) => {
                        const tx = db.transaction('pending_workouts', 'readonly');
                        const req = tx.objectStore('pending_workouts').getAll();
                        req.onsuccess = () => resolve(req.result);
                        req.onerror = reject;
                    });
                    db.close();

                    for (const entry of all) {
                        try {
                            const token = this.getCsrfToken();
                            const response = await fetch('{{ route("client.log.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-XSRF-TOKEN': token,
                                },
                                body: JSON.stringify(entry.payload),
                                credentials: 'include',
                            });
                            if (response.ok) {
                                const db2 = await this.openDb();
                                await new Promise((resolve, reject) => {
                                    const tx2 = db2.transaction('pending_workouts', 'readwrite');
                                    tx2.objectStore('pending_workouts').delete(entry.id);
                                    tx2.oncomplete = resolve;
                                    tx2.onerror = reject;
                                });
                                db2.close();
                                const data = await response.json();
                                window.location.href = data.redirect;
                            }
                        } catch {}
                    }
                },

                openDb() {
                    return new Promise((resolve, reject) => {
                        const req = indexedDB.open('liftdeck', 1);
                        req.onupgradeneeded = (e) => {
                            e.target.result.createObjectStore('pending_workouts', { keyPath: 'id', autoIncrement: true });
                        };
                        req.onsuccess = () => resolve(req.result);
                        req.onerror = reject;
                    });
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
                        sets: [{ weight: '', reps: '', rpe: '' }],
                    });
                    this.showExercisePicker = false;
                    this.exerciseSearch = '';
                },

                removeExercise(index) {
                    this.exercises.splice(index, 1);
                },

                addSet(exerciseIndex) {
                    this.exercises[exerciseIndex].sets.push({ weight: '', reps: '', rpe: '' });
                },

                removeSet(exerciseIndex, setIndex) {
                    this.exercises[exerciseIndex].sets.splice(setIndex, 1);
                },

                rpeColor(rpe) {
                    var n = parseInt(rpe);
                    if (!n) return '';
                    if (n <= 3) return 'oklch(0.78 0.15 145)';
                    if (n <= 6) return 'oklch(0.82 0.15 90)';
                    if (n <= 8) return 'oklch(0.74 0.17 55)';
                    return 'oklch(0.66 0.2 28)';
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
                                    borderColor: '#5c7a10',
                                    backgroundColor: 'rgba(198, 242, 78, 0.15)',
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

        window.exThumbHtml = function(muscle, size) {
            size = size || 40;
            var themes = {
                back:       { from: '#3b82f6', to: '#1e40af', ic: 'back' },
                chest:      { from: '#f0653e', to: '#b8311a', ic: 'chest' },
                shoulders:  { from: '#a06bff', to: '#6d28d9', ic: 'shoulder' },
                shoulder:   { from: '#a06bff', to: '#6d28d9', ic: 'shoulder' },
                core:       { from: '#2dd4bf', to: '#0d9488', ic: 'core' },
                quadriceps: { from: '#34d27b', to: '#15803d', ic: 'legs' },
                legs:       { from: '#34d27b', to: '#15803d', ic: 'legs' },
                glutes:     { from: '#f472b6', to: '#be185d', ic: 'legs' },
                biceps:     { from: '#f5b53d', to: '#c2790a', ic: 'arm' },
                triceps:    { from: '#f59e3d', to: '#c2620a', ic: 'arm' },
                arms:       { from: '#f5b53d', to: '#c2790a', ic: 'arm' },
                hamstrings: { from: '#34d27b', to: '#15803d', ic: 'legs' },
                calves:     { from: '#34d27b', to: '#15803d', ic: 'legs' },
            };
            var glyphs = {
                back:     '<path d="M12 3v18"/><path d="M12 6c-2.5 0-5 1.5-5 4M12 6c2.5 0 5 1.5 5 4"/><path d="M7 10c0 3 2 5 5 5s5-2 5-5"/>',
                chest:    '<path d="M4 8c2-1.5 5-2 8-2s6 .5 8 2"/><path d="M4 8v4c0 3 3.5 5 8 5s8-2 8-5V8"/><path d="M12 6v11"/>',
                shoulder: '<circle cx="12" cy="8" r="3.2"/><path d="M5 20c.5-4 3-6 7-6s6.5 2 7 6"/>',
                core:     '<rect x="7" y="4" width="10" height="16" rx="3"/><path d="M7 9h10M7 13h10M12 4v16"/>',
                legs:     '<path d="M9 3v7l-2 11M15 3v7l2 11"/><path d="M9 10h6"/>',
                arm:      '<path d="M6 6v5a4 4 0 0 0 4 4h2"/><path d="M12 15a3 3 0 0 0 6 0v-2"/><circle cx="6" cy="5" r="1.5" fill="white" stroke="none"/>',
                dumbbell: '<path d="M6.5 6.5l11 11"/><path d="M3 10l-1-1a2 2 0 0 1 3-3l1 1M14 21l1 1a2 2 0 0 0 3-3l-1-1"/>',
            };
            var key = (muscle || '').toLowerCase().replace(/[\s-]+/g, '_');
            var t = themes[key] || { from: '#94a3b8', to: '#475569', ic: 'dumbbell' };
            var g = glyphs[t.ic] || glyphs.dumbbell;
            var br = Math.round(size * 0.25);
            var ic = Math.round(size * 0.56);
            return '<div style="width:'+size+'px;height:'+size+'px;border-radius:'+br+'px;background:linear-gradient(150deg,'+t.from+','+t.to+');flex-shrink:0;position:relative;overflow:hidden;display:grid;place-items:center;box-shadow:inset 0 0 0 1px rgba(255,255,255,.12),inset 0 -10px 18px rgba(0,0,0,.22)"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:'+ic+'px;height:'+ic+'px;position:relative;z-index:1;filter:drop-shadow(0 1px 2px rgba(0,0,0,.35))">'+g+'</svg><div style="position:absolute;inset:0;background:radial-gradient(120% 80% at 25% 15%,rgba(255,255,255,.28),transparent 55%);pointer-events:none"></div></div>';
        };
    </script>
    @endpush
</x-layouts.client>
