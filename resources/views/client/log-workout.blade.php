<x-layouts.client>
    <x-slot:title>{{ $isCustom ? 'Custom Workout' : 'Log: ' . $workout->name }}</x-slot:title>

    <div
        x-data="workoutLogger()"
        class="space-y-6"
        @keydown.escape.window="selectedExercise = null"
    >
        <!-- Header -->
        <div>
            <a href="{{ route('client.log') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            @if($isCustom)
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Custom Workout</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Build your workout from scratch</p>
            @else
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $workout->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Day {{ $workout->day_number }} &middot; <span x-text="exercises.length"></span> exercises</p>
            @endif
        </div>

        @if($errors->any())
            <div class="rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800">Please fix the errors below.</p>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
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
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Unfinished workout found</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">
                        We saved your progress from <span x-text="_savedAtFormatted"></span>. Continue where you left off?
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button type="button" @click="confirmRestore()"
                        class="text-xs font-semibold px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Restore
                    </button>
                    <button type="button" @click="discardRestore()"
                        class="text-xs font-medium px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50">
                        Start Fresh
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
                You're offline — your progress is being saved locally.
            </p>
        </div>

        <form method="POST" action="{{ route('client.log.store') }}" @submit="clearSavedState()">
            @csrf

            @if(!$isCustom)
                <input type="hidden" name="program_workout_id" value="{{ $workout->id }}">
            @endif

            <div class="space-y-4">
                <!-- Custom Workout Name -->
                @if($isCustom)
                    <x-bladewind::card class="!p-4">
                        <label for="custom_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Workout Name</label>
                        <input
                            type="text"
                            id="custom_name"
                            name="custom_name"
                            value="{{ old('custom_name') }}"
                            placeholder="e.g. Morning Cardio, Extra Arms Day"
                            required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 @error('custom_name') border-red-300 @enderror"
                        >
                        @error('custom_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </x-bladewind::card>
                @endif

                <!-- Exercises -->
                <div x-ref="exerciseList" x-init="initSortable()" class="space-y-4">
                <template x-for="(exercise, exerciseIndex) in exercises" :key="exercise.exercise_id">
                    <x-bladewind::card class="!p-4">
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
                                        <button type="button" class="text-base font-semibold text-gray-900 dark:text-gray-100 text-left hover:underline focus:outline-none" @click="selectedExercise = exercise" x-text="exercise.name"></button>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="exercise.prescribed_sets">
                                            Prescribed: <span x-text="exercise.prescribed_sets"></span> sets &times; <span x-text="exercise.prescribed_reps"></span> reps
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300" x-text="exercise.muscle_group.replace('_', ' ')"></span>
                                    <!-- Move Up -->
                                    <button type="button" @click="moveExerciseUp(exerciseIndex)" :disabled="exerciseIndex === 0"
                                        class="p-1 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Move up">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </button>
                                    <!-- Move Down -->
                                    <button type="button" @click="moveExerciseDown(exerciseIndex)" :disabled="exerciseIndex === exercises.length - 1"
                                        class="p-1 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors disabled:opacity-30 disabled:cursor-not-allowed" title="Move down">
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
                            <div x-show="exercise.previous_sets && exercise.previous_sets.length > 0" class="text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-medium">Last session:</span>
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
                                        <tr class="text-left text-xs text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                                            <th class="pb-2 pr-3 w-12">Set</th>
                                            <th class="pb-2 pr-3">Weight (kg)</th>
                                            <th class="pb-2 pr-2">Reps</th>
                                            <th class="pb-2 w-8"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(set, setIndex) in exercise.sets" :key="setIndex">
                                            <tr>
                                                <td class="py-1.5 pr-3 text-gray-600 dark:text-gray-400 font-medium" x-text="setIndex + 1"></td>
                                                <td class="py-1.5 pr-3">
                                                    <input
                                                        type="number"
                                                        step="0.5"
                                                        min="0"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][weight]`"
                                                        x-model="set.weight"
                                                        placeholder="0"
                                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                                    >
                                                </td>
                                                <td class="py-1.5 pr-2">
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][reps]`"
                                                        x-model="set.reps"
                                                        :placeholder="exercise.prescribed_reps || '0'"
                                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                                    >
                                                </td>
                                                <td class="py-1.5">
                                                    <button
                                                        type="button"
                                                        @click="removeSet(exerciseIndex, setIndex)"
                                                        x-show="exercise.sets.length > 1"
                                                        class="p-1 text-gray-400 dark:text-gray-500 hover:text-red-500 transition-colors"
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
                                class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 font-medium"
                            >
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Set
                            </button>
                        </div>
                    </x-bladewind::card>
                </template>
                </div>

                <!-- Empty State -->
                <div x-show="exercises.length === 0" class="text-center py-8">
                    <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No exercises yet. Add one below.</p>
                </div>

                <!-- Add Exercise -->
                <x-bladewind::card class="!p-4">
                    <div x-show="!showExercisePicker">
                        <button
                            type="button"
                            @click="openExercisePicker()"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:border-blue-400 hover:text-blue-600 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Exercise
                        </button>
                    </div>

                    <div x-show="showExercisePicker" x-cloak>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Exercise</label>
                                <button type="button" @click="showExercisePicker = false" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <input
                                type="text"
                                x-model="exerciseSearch"
                                placeholder="Search exercises..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                x-ref="exerciseSearchInput"
                            >
                            <div class="max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-800 rounded-md divide-y divide-gray-100 dark:divide-gray-800">
                                <template x-for="exercise in filteredExercises" :key="exercise.id">
                                    <button
                                        type="button"
                                        @click="addExercise(exercise)"
                                        class="w-full text-left px-3 py-2 hover:bg-blue-50 dark:hover:bg-gray-800 transition-colors flex items-center justify-between"
                                    >
                                        <div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="exercise.name"></span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2" x-text="exercise.muscle_group.replace('_', ' ')"></span>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </template>
                                <div x-show="filteredExercises.length === 0" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No exercises found
                                </div>
                            </div>
                        </div>
                    </div>
                </x-bladewind::card>

                <!-- Date & Time -->
                <x-bladewind::card class="!p-4">
                    <label for="completed_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date & Time</label>
                    <input
                        type="datetime-local"
                        id="completed_at"
                        name="completed_at"
                        value="{{ old('completed_at', now()->format('Y-m-d\TH:i')) }}"
                        max="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 @error('completed_at') border-red-300 @enderror"
                    >
                    @error('completed_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Defaults to now. Change if logging a past workout.</p>
                </x-bladewind::card>

                <!-- Notes -->
                <x-bladewind::card class="!p-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (optional)</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="2"
                        placeholder="How did the workout feel?"
                        x-model="notes"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('notes') }}</textarea>
                </x-bladewind::card>

                <!-- Submit -->
                <button
                    type="submit"
                    x-bind:disabled="exercises.length === 0"
                    class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Complete Workout
                </button>
            </div>
        </form>

        <!-- Exercise Detail Modal -->
        <div x-show="selectedExercise" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="selectedExercise = null"></div>
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-y-auto max-h-[85vh]">
                <div class="flex items-start justify-between px-5 pt-5 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="selectedExercise ? selectedExercise.name : ''"></h2>
                        <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300" x-text="selectedExercise ? selectedExercise.muscle_group.replace('_', ' ') : ''"></span>
                    </div>
                    <button type="button" @click="selectedExercise = null" class="p-2 -mr-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md" aria-label="Close">
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
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No video available</p>
                        </div>
                    </div>
                </div>
                <div class="px-5 pb-8">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                    <p x-show="selectedExercise && selectedExercise.description" class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="selectedExercise ? selectedExercise.description : ''"></p>
                    <p x-show="!selectedExercise || !selectedExercise.description" class="text-sm text-gray-400 dark:text-gray-500 italic">No description provided</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
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
                restoreBanner: false,
                isOffline: false,
                notes: '',
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
            }
        }
    </script>
    @endpush
</x-layouts.client>
