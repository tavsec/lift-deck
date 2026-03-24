<x-layouts.client>
    <x-slot:title>My Program</x-slot:title>

    <div
    class="space-y-6"
    x-data="{ selectedExercise: null }"
    @keydown.escape.window="selectedExercise = null"
>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">My Program</h1>

        @if($activeProgram)
            <!-- Program Info -->
            <x-bladewind::card class="!p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $activeProgram->program->name }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Active</span>
                    </div>
                    @if($activeProgram->program->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $activeProgram->program->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 text-sm text-gray-500 dark:text-gray-400">
                        @if($activeProgram->program->type)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ ucfirst(str_replace('_', ' ', $activeProgram->program->type)) }}</span>
                        @endif
                        @if($activeProgram->program->duration_weeks)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ $activeProgram->program->duration_weeks }} weeks</span>
                        @endif
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ $activeProgram->program->workouts->count() }} workouts</span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Started {{ $activeProgram->started_at->format('M d, Y') }}</p>
                </div>
            </x-bladewind::card>

            <!-- Workouts -->
            @if($activeProgram->program->workouts->count() > 0)
                @foreach($activeProgram->program->workouts as $workout)
                    <x-bladewind::card class="!p-0 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $workout->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Day {{ $workout->day_number }} &middot; {{ $workout->exercises->count() }} exercises</p>
                            @if($workout->notes)
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $workout->notes }}</p>
                            @endif
                        </div>

                        @if($workout->exercises->count() > 0)
                            <div class="divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($workout->exercises as $workoutExercise)
                                    <div class="px-6 py-4 flex items-center justify-between">
                                        <div class="flex-1">
                                            <button
                                                type="button"
                                                class="text-sm font-medium text-gray-900 dark:text-gray-100 text-left hover:underline focus:outline-none"
                                                @click="selectedExercise = {
                                                    name: @js($workoutExercise->exercise->name),
                                                    muscleGroup: @js(ucfirst(str_replace('_', ' ', $workoutExercise->exercise->muscle_group))),
                                                    description: @js($workoutExercise->exercise->description),
                                                    {{-- Uses {{ }} not @js() — single quotes safe in double-quoted HTML attribute; outputs unescaped slashes for test assertions --}}
                                                    embedUrl: '{{ $workoutExercise->exercise->getYoutubeEmbedUrl() ?? '' }}',
                                                }"
                                            >
                                                {{ $workoutExercise->exercise->name }}
                                            </button>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                                @if($workoutExercise->formatted_rest)
                                                    &middot; {{ $workoutExercise->formatted_rest }} rest
                                                @endif
                                            </p>
                                            @if($workoutExercise->notes)
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $workoutExercise->notes }}</p>
                                            @endif
                                            @if($currentTargets->has($workoutExercise->id))
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach($currentTargets->get($workoutExercise->id)->sortKeys() as $setNum => $target)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300">
                                                            Set {{ $setNum }}: {{ $target->target_weight }} kg
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($targetHistory->has($workoutExercise->id) && $targetHistory->get($workoutExercise->id)->count() > 0)
                                                <details class="mt-1">
                                                    <summary class="text-xs text-gray-400 dark:text-gray-500 cursor-pointer select-none">Target history</summary>
                                                    <div class="mt-1 space-y-0.5 pl-2 border-l-2 border-gray-200 dark:border-gray-700">
                                                        @foreach($targetHistory->get($workoutExercise->id)->filter(fn ($t) => $t->effective_date !== null)->groupBy(fn ($t) => $t->effective_date->format('Y-m-d'))->sortKeysDesc() as $date => $entries)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                <span class="font-medium">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                                                                @foreach($entries->sortBy('set_number') as $entry)
                                                                    &middot; Set {{ $entry->set_number }}: {{ $entry->target_weight }} kg
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </details>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                            {{ ucfirst(str_replace('_', ' ', $workoutExercise->exercise->muscle_group)) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No exercises added yet
                            </div>
                        @endif
                    </x-bladewind::card>
                @endforeach
            @endif
        @else
            <x-bladewind::card class="!p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Your program will appear here once assigned</p>
                </div>
            </x-bladewind::card>
        @endif
        <!-- Exercise Detail Modal -->
        <div x-show="selectedExercise" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div
                class="absolute inset-0 bg-black/50"
                @click="selectedExercise = null"
            ></div>

            <!-- Modal -->
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-y-auto max-h-[85vh]">
                <!-- Header -->
                <div class="flex items-start justify-between px-5 pt-5 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100" x-text="selectedExercise ? selectedExercise.name : ''"></h2>
                        <span
                            class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                            x-text="selectedExercise ? selectedExercise.muscleGroup : ''"
                        ></span>
                    </div>
                    <button
                        type="button"
                        @click="selectedExercise = null"
                        class="p-2 -mr-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md"
                        aria-label="Close"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Video -->
                <div class="px-5 pb-4">
                    <template x-if="selectedExercise && selectedExercise.embedUrl">
                        <div class="aspect-video rounded-lg overflow-hidden bg-black">
                            <iframe
                                :src="selectedExercise.embedUrl"
                                class="w-full h-full"
                                :title="selectedExercise.name"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </template>
                    <div x-show="!selectedExercise || !selectedExercise.embedUrl" class="aspect-video rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No video available</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="px-5 pb-8">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</h3>
                    <p x-show="selectedExercise && selectedExercise.description" class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="selectedExercise ? selectedExercise.description : ''"></p>
                    <p x-show="!selectedExercise || !selectedExercise.description" class="text-sm text-gray-400 dark:text-gray-500 italic">No description provided</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.client>
