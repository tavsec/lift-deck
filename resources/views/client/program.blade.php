<x-layouts.client>
    <x-slot:title>{{ __('client.program.heading') }}</x-slot:title>

    <div
    class="space-y-6"
    x-data="exerciseInfoModal()"
    @keydown.escape.window="selectedExercise = null"
>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('client.program.heading') }}</h1>

        @if($activeProgram)
            <!-- Program Info -->
            <x-bladewind::card class="!p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $activeProgram->program->name }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ __('client.program.active') }}</span>
                    </div>
                    @if($activeProgram->program->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $activeProgram->program->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 text-sm text-gray-500 dark:text-gray-400">
                        @if($activeProgram->program->type)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ ucfirst(str_replace('_', ' ', $activeProgram->program->type)) }}</span>
                        @endif
                        @if($activeProgram->program->duration_weeks)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ __('client.program.weeks', ['n' => $activeProgram->program->duration_weeks]) }}</span>
                        @endif
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ __('client.program.workouts', ['n' => $activeProgram->program->workouts->count()]) }}</span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('client.program.started', ['date' => $activeProgram->started_at->format('M d, Y')]) }}</p>
                </div>
            </x-bladewind::card>

            <!-- Workouts -->
            @if($activeProgram->program->workouts->count() > 0)
                @foreach($activeProgram->program->workouts as $workout)
                    <x-bladewind::card class="!p-0 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $workout->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('client.program.day_n', ['n' => $workout->day_number]) }} &middot; {{ __('client.program.n_exercises', ['n' => $workout->exercises->count()]) }}</p>
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
                                                    exerciseId: {{ $workoutExercise->exercise->id }},
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
                                                    &middot; {{ $workoutExercise->formatted_rest }} {{ __('client.program.rest') }}
                                                @endif
                                            </p>
                                            @if($workoutExercise->notes)
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $workoutExercise->notes }}</p>
                                            @endif
                                            @if($currentTargets->has($workoutExercise->id))
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @foreach($currentTargets->get($workoutExercise->id)->sortKeys() as $setNum => $target)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300">
                                                            {{ __('client.program.set_weight', ['n' => $setNum, 'weight' => $target->target_weight]) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($targetHistory->has($workoutExercise->id) && $targetHistory->get($workoutExercise->id)->count() > 0)
                                                <details class="mt-1">
                                                    <summary class="text-xs text-gray-400 dark:text-gray-500 cursor-pointer select-none">{{ __('client.program.target_history') }}</summary>
                                                    <div class="mt-1 space-y-0.5 pl-2 border-l-2 border-gray-200 dark:border-gray-700">
                                                        @foreach($targetHistory->get($workoutExercise->id)->filter(fn ($t) => $t->effective_date !== null)->groupBy(fn ($t) => $t->effective_date->format('Y-m-d'))->sortKeysDesc() as $date => $entries)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                <span class="font-medium">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                                                                @foreach($entries->sortBy('set_number') as $entry)
                                                                    &middot; {{ __('client.program.set_weight', ['n' => $entry->set_number, 'weight' => $entry->target_weight]) }}
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
                                {{ __('client.program.no_exercises_added') }}
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
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">{{ __('client.program.no_program_assigned') }}</p>
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
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('client.program.no_video') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="px-5 pb-8">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('client.program.description') }}</h3>
                    <p x-show="selectedExercise && selectedExercise.description" class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="selectedExercise ? selectedExercise.description : ''"></p>
                    <p x-show="!selectedExercise || !selectedExercise.description" class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('client.program.no_description') }}</p>
                </div>

                <!-- Progress Section -->
                <div class="px-5 pb-8 border-t border-gray-100 dark:border-gray-800 pt-5">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('client.exercise_progress.heading') }}</h3>

                    <!-- Range selector -->
                    <div class="flex gap-1 mb-4">
                        <template x-for="r in [30, 90, 365, 0]" :key="r">
                            <button
                                type="button"
                                @click="progressRange = r; selectedExercise && loadProgress(selectedExercise.exerciseId, r)"
                                :class="progressRange === r ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300'"
                                class="px-2.5 py-1 rounded text-xs font-medium transition-colors"
                                x-text="r === 30 ? '30d' : r === 90 ? '90d' : r === 365 ? '1yr' : '{{ __('client.exercise_progress.all_time') }}'"
                            ></button>
                        </template>
                    </div>

                    <!-- Loading spinner -->
                    <div x-show="progressLoading" class="flex items-center justify-center py-8">
                        <svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>

                    <!-- Data -->
                    <template x-if="!progressLoading && progressData">
                        <div class="space-y-4">
                            <!-- PR stats -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('client.exercise_progress.max_weight') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="progressData.maxWeight !== null ? progressData.maxWeight + ' kg' : '—'"></p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('client.exercise_progress.est_1rm') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 mt-1" x-text="progressData.estimated1rm !== null ? progressData.estimated1rm + ' kg' : '—'"></p>
                                </div>
                            </div>

                            <!-- No chart data -->
                            <p x-show="progressData.weightChart.length === 0" class="text-sm text-gray-400 dark:text-gray-500 italic text-center py-4">{{ __('client.exercise_progress.no_data') }}</p>

                            <!-- Charts -->
                            <template x-if="progressData.weightChart.length > 0">
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.exercise_progress.weight_chart') }}</p>
                                        <canvas id="progressWeightChart" height="120"></canvas>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('client.exercise_progress.volume_chart') }}</p>
                                        <canvas id="progressVolumeChart" height="120"></canvas>
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
        function exerciseInfoModal() {
            return {
                selectedExercise: null,
                progressData: null,
                progressRange: 90,
                progressLoading: false,
                _progressCharts: [],

                init() {
                    this.$watch('selectedExercise', (val) => {
                        if (val && val.exerciseId) {
                            this.progressRange = 90;
                            this.loadProgress(val.exerciseId, 90);
                        } else {
                            this.progressData = null;
                            this._destroyCharts();
                        }
                    });
                },

                loadProgress(exerciseId, range) {
                    this.progressLoading = true;
                    this.progressData = null;
                    this._destroyCharts();
                    fetch(`/client/exercises/${exerciseId}/progress?range=${range}`)
                        .then(r => r.json())
                        .then(data => {
                            this.progressData = data;
                            this.progressLoading = false;
                            this.$nextTick(() => this._renderCharts(data));
                        });
                },

                _destroyCharts() {
                    this._progressCharts.forEach(c => c.destroy());
                    this._progressCharts = [];
                },

                _renderCharts(data) {
                    if (data.weightChart.length === 0) {
                        return;
                    }

                    const labels = data.weightChart.map(p => p.date);
                    const commonOptions = {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { x: { ticks: { maxTicksLimit: 8 } } },
                    };

                    const wCtx = document.getElementById('progressWeightChart');
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
                            options: commonOptions,
                        }));
                    }

                    const vCtx = document.getElementById('progressVolumeChart');
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
                            options: commonOptions,
                        }));
                    }
                },
            };
        }
    </script>
    @endpush
</x-layouts.client>
