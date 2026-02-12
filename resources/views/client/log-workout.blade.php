<x-layouts.client>
    <x-slot:title>{{ $isCustom ? 'Custom Workout' : 'Log: ' . $workout->name }}</x-slot:title>

    <div
        x-data="workoutLogger()"
        class="space-y-6"
    >
        <!-- Header -->
        <div>
            <a href="{{ route('client.log') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            @if($isCustom)
                <h1 class="text-2xl font-bold text-gray-900">Custom Workout</h1>
                <p class="text-sm text-gray-500">Build your workout from scratch</p>
            @else
                <h1 class="text-2xl font-bold text-gray-900">{{ $workout->name }}</h1>
                <p class="text-sm text-gray-500">Day {{ $workout->day_number }} &middot; <span x-text="exercises.length"></span> exercises</p>
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

        <form method="POST" action="{{ route('client.log.store') }}">
            @csrf

            @if(!$isCustom)
                <input type="hidden" name="program_workout_id" value="{{ $workout->id }}">
            @endif

            <div class="space-y-4">
                <!-- Custom Workout Name -->
                @if($isCustom)
                    <x-bladewind::card class="!p-4">
                        <label for="custom_name" class="block text-sm font-medium text-gray-700 mb-1">Workout Name</label>
                        <input
                            type="text"
                            id="custom_name"
                            name="custom_name"
                            value="{{ old('custom_name') }}"
                            placeholder="e.g. Morning Cardio, Extra Arms Day"
                            required
                            class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 @error('custom_name') border-red-300 @enderror"
                        >
                        @error('custom_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </x-bladewind::card>
                @endif

                <!-- Exercises -->
                <template x-for="(exercise, exerciseIndex) in exercises" :key="exerciseIndex">
                    <x-bladewind::card class="!p-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900" x-text="exercise.name"></h3>
                                    <p class="text-xs text-gray-500" x-show="exercise.prescribed_sets">
                                        Prescribed: <span x-text="exercise.prescribed_sets"></span> sets &times; <span x-text="exercise.prescribed_reps"></span> reps
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600" x-text="exercise.muscle_group.replace('_', ' ')"></span>
                                    <button
                                        type="button"
                                        @click="removeExercise(exerciseIndex)"
                                        class="p-1 text-red-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors"
                                        title="Remove exercise"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden fields -->
                            <input type="hidden" :name="`exercises[${exerciseIndex}][workout_exercise_id]`" :value="exercise.workout_exercise_id || ''">
                            <input type="hidden" :name="`exercises[${exerciseIndex}][exercise_id]`" :value="exercise.exercise_id">

                            <!-- Sets Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-xs text-gray-500 border-b border-gray-200">
                                            <th class="pb-2 pr-3 w-12">Set</th>
                                            <th class="pb-2 pr-3">Weight (kg)</th>
                                            <th class="pb-2 pr-2">Reps</th>
                                            <th class="pb-2 w-8"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(set, setIndex) in exercise.sets" :key="setIndex">
                                            <tr>
                                                <td class="py-1.5 pr-3 text-gray-600 font-medium" x-text="setIndex + 1"></td>
                                                <td class="py-1.5 pr-3">
                                                    <input
                                                        type="number"
                                                        step="0.5"
                                                        min="0"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][weight]`"
                                                        x-model="set.weight"
                                                        placeholder="0"
                                                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                                    >
                                                </td>
                                                <td class="py-1.5 pr-2">
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        :name="`exercises[${exerciseIndex}][sets][${setIndex}][reps]`"
                                                        x-model="set.reps"
                                                        :placeholder="exercise.prescribed_reps || '0'"
                                                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                                    >
                                                </td>
                                                <td class="py-1.5">
                                                    <button
                                                        type="button"
                                                        @click="removeSet(exerciseIndex, setIndex)"
                                                        x-show="exercise.sets.length > 1"
                                                        class="p-1 text-gray-400 hover:text-red-500 transition-colors"
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

                <!-- Empty State -->
                <div x-show="exercises.length === 0" class="text-center py-8">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No exercises yet. Add one below.</p>
                </div>

                <!-- Add Exercise -->
                <x-bladewind::card class="!p-4">
                    <div x-show="!showExercisePicker">
                        <button
                            type="button"
                            @click="openExercisePicker()"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:border-blue-400 hover:text-blue-600 transition-colors"
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
                                <label class="block text-sm font-medium text-gray-700">Select Exercise</label>
                                <button type="button" @click="showExercisePicker = false" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <input
                                type="text"
                                x-model="exerciseSearch"
                                placeholder="Search exercises..."
                                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
                                x-ref="exerciseSearchInput"
                            >
                            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md divide-y divide-gray-100">
                                <template x-for="exercise in filteredExercises" :key="exercise.id">
                                    <button
                                        type="button"
                                        @click="addExercise(exercise)"
                                        class="w-full text-left px-3 py-2 hover:bg-blue-50 transition-colors flex items-center justify-between"
                                    >
                                        <div>
                                            <span class="text-sm font-medium text-gray-900" x-text="exercise.name"></span>
                                            <span class="text-xs text-gray-500 ml-2" x-text="exercise.muscle_group.replace('_', ' ')"></span>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </template>
                                <div x-show="filteredExercises.length === 0" class="px-3 py-4 text-center text-sm text-gray-500">
                                    No exercises found
                                </div>
                            </div>
                        </div>
                    </div>
                </x-bladewind::card>

                <!-- Date & Time -->
                <x-bladewind::card class="!p-4">
                    <label for="completed_at" class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                    <input
                        type="datetime-local"
                        id="completed_at"
                        name="completed_at"
                        value="{{ old('completed_at', now()->format('Y-m-d\TH:i')) }}"
                        max="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 @error('completed_at') border-red-300 @enderror"
                    >
                    @error('completed_at')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Defaults to now. Change if logging a past workout.</p>
                </x-bladewind::card>

                <!-- Notes -->
                <x-bladewind::card class="!p-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="2"
                        placeholder="How did the workout feel?"
                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
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
    </div>

    @push('scripts')
    <script>
        function workoutLogger() {
            return {
                exercises: @json($exercisesData),
                availableExercises: [],
                exerciseSearch: '',
                showExercisePicker: false,
                exercisesLoaded: false,

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
                        prescribed_sets: null,
                        prescribed_reps: null,
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
</x-layouts.client>
