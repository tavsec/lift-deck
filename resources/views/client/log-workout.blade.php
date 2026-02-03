<x-layouts.client>
    <x-slot:title>Log: {{ $workout->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('client.log') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-3">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $workout->name }}</h1>
            <p class="text-sm text-gray-500">Day {{ $workout->day_number }} &middot; {{ $workout->exercises->count() }} exercises</p>
        </div>

        @if($errors->any())
            <div class="rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800">Please fix the errors below.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('client.log.store') }}">
            @csrf
            <input type="hidden" name="program_workout_id" value="{{ $workout->id }}">

            <div class="space-y-4">
                @foreach($workout->exercises as $exerciseIndex => $workoutExercise)
                    <x-bladewind::card class="!p-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ $workoutExercise->exercise->name }}</h3>
                                    <p class="text-xs text-gray-500">
                                        Prescribed: {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                        @if($workoutExercise->formatted_rest) &middot; {{ $workoutExercise->formatted_rest }} rest @endif
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                    {{ ucfirst(str_replace('_', ' ', $workoutExercise->exercise->muscle_group)) }}
                                </span>
                            </div>

                            @if($workoutExercise->notes)
                                <p class="text-xs text-gray-500 italic">{{ $workoutExercise->notes }}</p>
                            @endif

                            <input type="hidden" name="exercises[{{ $exerciseIndex }}][workout_exercise_id]" value="{{ $workoutExercise->id }}">
                            <input type="hidden" name="exercises[{{ $exerciseIndex }}][exercise_id]" value="{{ $workoutExercise->exercise_id }}">

                            <!-- Sets Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-xs text-gray-500 border-b border-gray-200">
                                            <th class="pb-2 pr-3 w-12">Set</th>
                                            <th class="pb-2 pr-3">Weight (kg)</th>
                                            <th class="pb-2">Reps</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($setIndex = 0; $setIndex < $workoutExercise->sets; $setIndex++)
                                            <tr>
                                                <td class="py-1.5 pr-3 text-gray-600 font-medium">{{ $setIndex + 1 }}</td>
                                                <td class="py-1.5 pr-3">
                                                    <input
                                                        type="number"
                                                        step="0.5"
                                                        min="0"
                                                        name="exercises[{{ $exerciseIndex }}][sets][{{ $setIndex }}][weight]"
                                                        value="{{ old("exercises.{$exerciseIndex}.sets.{$setIndex}.weight") }}"
                                                        placeholder="0"
                                                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 @error("exercises.{$exerciseIndex}.sets.{$setIndex}.weight") border-red-300 @enderror"
                                                    >
                                                </td>
                                                <td class="py-1.5">
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        name="exercises[{{ $exerciseIndex }}][sets][{{ $setIndex }}][reps]"
                                                        value="{{ old("exercises.{$exerciseIndex}.sets.{$setIndex}.reps") }}"
                                                        placeholder="{{ $workoutExercise->reps }}"
                                                        class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 @error("exercises.{$exerciseIndex}.sets.{$setIndex}.reps") border-red-300 @enderror"
                                                    >
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </x-bladewind::card>
                @endforeach

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
                    class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Complete Workout
                </button>
            </div>
        </form>
    </div>
</x-layouts.client>
