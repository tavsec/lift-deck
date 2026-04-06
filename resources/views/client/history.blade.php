<x-layouts.client>
    <x-slot:title>{{ __('client.history.heading') }}</x-slot:title>

    <div class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('client.history.heading') }}</h1>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(count($exerciseProgressionData) > 0)
            <x-bladewind::card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('client.history.exercise_progress') }}</h2>

                <div x-data="clientExerciseProgression({{ json_encode($exerciseProgressionData) }}, {{ json_encode($exercisesByMuscleGroup) }}, {{ json_encode($exerciseTargetHistory) }})" x-init="init()">
                    <div class="mb-4">
                        <select x-model="selectedExercise" @change="updateChart()" class="block w-full sm:w-64 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <template x-for="(exercises, group) in exerciseGroups" :key="group">
                                <optgroup :label="group">
                                    <template x-for="ex in exercises" :key="ex.id">
                                        <option :value="ex.id" x-text="ex.name"></option>
                                    </template>
                                </optgroup>
                            </template>
                        </select>
                    </div>

                    <div class="h-48 mb-4">
                        <canvas x-ref="canvas"></canvas>
                    </div>

                    <div x-show="summary" class="grid grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-950 rounded-lg">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.history.start_end') }}</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="summary?.startWeight + 'kg → ' + summary?.endWeight + 'kg'"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.history.change') }}</p>
                            <p class="text-sm font-bold" :class="summary?.change >= 0 ? 'text-green-600' : 'text-red-600'"
                               x-text="(summary?.change >= 0 ? '+' : '') + summary?.change + 'kg (' + (summary?.change >= 0 ? '+' : '') + summary?.changePercent + '%)'"></p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('client.history.sessions') }}</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="summary?.sessions"></p>
                        </div>
                    </div>
                </div>
            </x-bladewind::card>
        @endif

        @if($workoutLogs->count() > 0)
            <div class="space-y-3">
                @foreach($workoutLogs as $log)
                    <a href="{{ route('client.history.show', $log) }}" class="block">
                        <x-bladewind::card class="!p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $log->displayName() }}</h3>
                                        @if($unreadWorkoutLogIds->contains($log->id))
                                            <span class="flex h-2 w-2 rounded-full bg-blue-500" title="{{ __('client.history.unread_comments') }}"></span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $log->completed_at->format('D, M j, Y \a\t g:i A') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($log->comments_count > 0)
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            {{ $log->comments_count }}
                                        </span>
                                    @endif
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </x-bladewind::card>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $workoutLogs->links() }}
            </div>
        @else
            <x-bladewind::card class="!p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">{{ __('client.history.no_workouts') }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('client.history.no_workouts_description') }}</p>
                </div>
            </x-bladewind::card>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            function chartTheme() {
                const dark = document.documentElement.classList.contains('dark');
                return {
                    tickColor:  dark ? '#9ca3af' : '#6b7280',
                    gridColor:  dark ? 'rgba(75, 85, 99, 0.25)' : 'rgba(229, 231, 235, 1)',
                    legendColor: dark ? '#d1d5db' : '#374151',
                };
            }

            function clientExerciseProgression(allData, exerciseGroups, targetHistory = {}) {
                return {
                    selectedExercise: '',
                    exerciseGroups,
                    summary: null,

                    init() {
                        const firstGroup = Object.values(exerciseGroups)[0];
                        if (firstGroup && firstGroup.length > 0) {
                            this.selectedExercise = String(firstGroup[0].id);
                        }
                        this.$nextTick(() => {
                            if (this.selectedExercise) this.updateChart();
                        });
                    },

                    updateChart() {
                        const data = allData[this.selectedExercise] || [];
                        const existing = Chart.getChart(this.$refs.canvas);
                        if (existing) existing.destroy();

                        if (data.length === 0) {
                            this.summary = null;
                            return;
                        }

                        const startW = data[0].weight;
                        const endW = data[data.length - 1].weight;
                        const change = Math.round((endW - startW) * 100) / 100;
                        const changePercent = startW > 0 ? Math.round((change / startW) * 1000) / 10 : 0;

                        this.summary = {
                            startWeight: startW,
                            endWeight: endW,
                            change,
                            changePercent,
                            sessions: data.length,
                        };

                        const ctx = this.$refs.canvas.getContext('2d');
                        const theme = chartTheme();

                        const targets = (targetHistory[this.selectedExercise] || [])
                            .slice()
                            .sort((a, b) => a.date.localeCompare(b.date));

                        const hasTargets = targets.length > 0;

                        function activeTarget(dateStr) {
                            let result = null;
                            for (const t of targets) {
                                if (t.date <= dateStr) { result = t.target; } else { break; }
                            }
                            return result;
                        }

                        const logDateSet = new Set(data.map(d => d.date));
                        const allDates = [...logDateSet];
                        if (hasTargets) {
                            for (const t of targets) {
                                if (!logDateSet.has(t.date)) { allDates.push(t.date); }
                            }
                        }
                        allDates.sort();

                        const logByDate = {};
                        for (const d of data) { logByDate[d.date] = d; }

                        const labels = allDates.map(dateStr => {
                            const date = new Date(dateStr + 'T00:00:00');
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        });

                        const datasets = [{
                            label: 'Top Set Weight (kg)',
                            data: allDates.map(dateStr => logByDate[dateStr]?.weight ?? null),
                            borderColor: '#8B5CF6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: allDates.map(dateStr => logByDate[dateStr] ? 4 : 0),
                            spanGaps: true,
                        }];

                        if (hasTargets) {
                            datasets.push({
                                label: 'Target (kg)',
                                data: allDates.map(dateStr => activeTarget(dateStr)),
                                borderColor: '#f59e0b',
                                backgroundColor: 'transparent',
                                borderDash: [5, 5],
                                pointRadius: 0,
                                tension: 0.3,
                            });
                        }

                        new Chart(ctx, {
                            type: 'line',
                            data: { labels, datasets },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: hasTargets, labels: { color: theme.tickColor, boxWidth: 12, padding: 12 } },
                                    tooltip: {
                                        filter: (item) => item.parsed.y !== null,
                                        callbacks: {
                                            label: function(ctx) {
                                                if (ctx.datasetIndex === 0) {
                                                    const d = logByDate[allDates[ctx.dataIndex]];
                                                    return d ? d.weight + 'kg x ' + d.reps + ' reps' : null;
                                                }
                                                return ctx.parsed.y !== null ? 'Target: ' + ctx.parsed.y + 'kg' : null;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: { ticks: { maxTicksLimit: 10, color: theme.tickColor }, grid: { color: theme.gridColor } },
                                    y: { beginAtZero: false, ticks: { color: theme.tickColor }, grid: { color: theme.gridColor } }
                                }
                            }
                        });
                    }
                };
            }
        </script>
    @endpush
</x-layouts.client>
