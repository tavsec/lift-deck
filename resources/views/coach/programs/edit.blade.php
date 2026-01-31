<x-layouts.coach>
    <x-slot:title>Edit {{ $program->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Program
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Edit Program</h1>
                <p class="mt-1 text-sm text-gray-500">Update program details and manage workouts</p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Program Details Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Program Details</h2>
            <form method="POST" action="{{ route('coach.programs.update', $program) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $program->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('type', $program->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="duration_weeks" class="block text-sm font-medium text-gray-700">Duration (weeks)</label>
                        <input type="number" name="duration_weeks" id="duration_weeks" value="{{ old('duration_weeks', $program->duration_weeks) }}" min="1" max="52"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_template" value="1" {{ old('is_template', $program->is_template) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Save as template</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description', $program->description) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Workouts Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Workouts</h2>
            </div>

            <!-- Add Workout Form -->
            <div class="bg-gray-50 rounded-lg p-4 border border-dashed border-gray-300">
                <form method="POST" action="{{ route('coach.programs.workouts.store', $program) }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="flex-1">
                        <input type="text" name="name" required placeholder="Workout name (e.g., Push Day)"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div class="w-24">
                        <input type="number" name="day_number" required placeholder="Day #" min="1"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Workout
                    </button>
                </form>
            </div>

            <!-- Workout List -->
            @foreach($program->workouts as $workout)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Workout Header -->
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-md font-medium text-gray-900">{{ $workout->name }}</h3>
                                <p class="text-sm text-gray-500">Day {{ $workout->day_number }}</p>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('coach.programs.workouts.destroy', [$program, $workout]) }}" onsubmit="return confirm('Delete this workout and all its exercises?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Exercises in Workout -->
                    <div class="divide-y divide-gray-200">
                        @foreach($workout->exercises as $workoutExercise)
                            <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $workoutExercise->exercise->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                        @if($workoutExercise->formatted_rest)
                                            &middot; {{ $workoutExercise->formatted_rest }} rest
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('coach.programs.exercises.move-up', [$program, $workoutExercise]) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 text-gray-400 hover:text-gray-600" title="Move up">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('coach.programs.exercises.move-down', [$program, $workoutExercise]) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1 text-gray-400 hover:text-gray-600" title="Move down">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('coach.programs.exercises.destroy', [$program, $workoutExercise]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-red-400 hover:text-red-600" title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        @if($workout->exercises->count() === 0)
                            <div class="px-6 py-4 text-center text-sm text-gray-500">
                                No exercises added yet
                            </div>
                        @endif
                    </div>

                    <!-- Add Exercise Form -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <form method="POST" action="{{ route('coach.programs.exercises.store', [$program, $workout]) }}" class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <div class="flex-1">
                                <select name="exercise_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">Select exercise...</option>
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
                                <input type="number" name="sets" required placeholder="Sets" min="1" max="20" value="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="w-24">
                                <input type="text" name="reps" required placeholder="Reps" value="8-12"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="w-24">
                                <input type="number" name="rest_seconds" placeholder="Rest (s)" min="0" max="600" value="90"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if($program->workouts->count() === 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="text-center py-8 text-sm text-gray-500">
                        <p>No workouts yet. Add your first workout above.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.coach>
