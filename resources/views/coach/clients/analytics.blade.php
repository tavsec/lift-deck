<x-layouts.coach>
    <x-slot:title>{{ $client->name }} — Analytics</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to {{ $client->name }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }} — Analytics</h1>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow p-4 flex justify-between align-middle">
            <form method="GET" action="{{ route('coach.clients.analytics', $client) }}" x-data="{ range: '{{ $range }}' }" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Time Period</label>
                    <select name="range" x-model="range" @change="if (range !== 'custom') $el.closest('form').submit()"
                        class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="7">Last 7 days</option>
                        <option value="14">Last 14 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="custom">Custom range</option>
                    </select>
                </div>

                <template x-if="range === 'custom'">
                    <div class="flex items-end gap-2">
                        <div>
                            <label class="block text-xs text-gray-500">From</label>
                            <input type="date" name="from" value="{{ $from }}" class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500">To</label>
                            <input type="date" name="to" value="{{ $to }}" class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Apply
                        </button>
                    </div>
                </template>
            </form>
            <a href="{{ route('coach.clients.analytics.export', $client) }}" class="cursor-pointer inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Export all data</a>
        </div>

        <!-- Daily Check-ins Section -->
        <div x-data="{ open: true }" class="bg-white rounded-lg shadow">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-left">
                <h2 class="text-lg font-semibold text-gray-900">Daily Check-ins</h2>
                <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="px-4 pb-4 space-y-6">
                @if(count($checkInCharts) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($checkInCharts as $chart)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <h3 class="text-sm font-medium text-gray-700 mb-2">{{ $chart['name'] }} @if($chart['unit'])({{ $chart['unit'] }})@endif</h3>
                                <div x-data="checkInChart({{ json_encode($chart) }})" x-init="init()">
                                    <canvas x-ref="canvas" height="200"></canvas>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($tableMetrics->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    @foreach($tableMetrics as $metric)
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ $metric->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($checkInTableData as $row)
                                    @php
                                        $hasValue = false;
                                        foreach ($tableMetrics as $metric) {
                                            if ($row['metric_' . $metric->id] !== null) {
                                                $hasValue = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if($hasValue)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($row['date'])->format('M j') }}</td>
                                            @foreach($tableMetrics as $metric)
                                                <td class="px-3 py-2">
                                                    @if($row['metric_' . $metric->id] === null)
                                                        <span class="text-gray-300">—</span>
                                                    @elseif($metric->type === 'boolean')
                                                        @if($row['metric_' . $metric->id] === '1')
                                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        @endif
                                                    @else
                                                        <span title="{{ $row['metric_' . $metric->id] }}">{{ Str::limit($row['metric_' . $metric->id], 30) }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if(count($checkInCharts) === 0 && $tableMetrics->count() === 0)
                    <p class="text-sm text-gray-500">No check-in data for this period.</p>
                @endif
            </div>
        </div>

        <!-- Nutrition -->
        <div x-data="{ open: true }" class="bg-white rounded-lg shadow">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-left">
                <h2 class="text-lg font-semibold text-gray-900">Nutrition</h2>
                <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="px-4 pb-4">
                @if($nutritionStats['daysLogged'] > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Calories</h3>
                            <div class="h-56">
                                <canvas
                                    x-data="caloriesChart({{ json_encode($nutritionData) }})"
                                    x-ref="canvas"
                                    x-init="renderChart()"
                                ></canvas>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Macros (g)</h3>
                            <div class="h-56">
                                <canvas
                                    x-data="macrosChart({{ json_encode($nutritionData) }})"
                                    x-ref="canvas"
                                    x-init="renderChart()"
                                ></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase">Avg. Daily Calories</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($nutritionStats['avgCalories']) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase">Avg. Protein</p>
                            <p class="text-lg font-bold text-gray-900">{{ $nutritionStats['avgProtein'] }}g</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase">Avg. Carbs</p>
                            <p class="text-lg font-bold text-gray-900">{{ $nutritionStats['avgCarbs'] }}g</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase">Avg. Fat</p>
                            <p class="text-lg font-bold text-gray-900">{{ $nutritionStats['avgFat'] }}g</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase">Adherence</p>
                            @if($nutritionStats['adherenceRate'] !== null)
                                <p class="text-lg font-bold {{ $nutritionStats['adherenceRate'] >= 80 ? 'text-green-600' : ($nutritionStats['adherenceRate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $nutritionStats['adherenceRate'] }}%</p>
                            @else
                                <p class="text-lg font-bold text-gray-400">—</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">No nutrition data for this period.</p>
                @endif
            </div>
        </div>
        @if($imageMetrics->isNotEmpty())
        <!-- Progress Photos -->
            <div x-data="{ open: true }" class="bg-white rounded-lg shadow">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-left">
                    <h2 class="text-lg font-semibold text-gray-900">Progress Photos</h2>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-collapse class="px-4 pb-4 space-y-6">
                    @php $hasAnyPhotos = collect($imageMetricData)->contains(fn ($m) => count($m['photos']) > 0); @endphp

                    @if($hasAnyPhotos)
                        @foreach($imageMetricData as $metricData)
                            @if(count($metricData['photos']) > 0)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 mb-2">{{ $metricData['name'] }}</h3>
                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                                        @foreach($metricData['photos'] as $photo)
                                            <div x-data="{ showLightbox: false }" class="relative">
                                                <button @click="showLightbox = true" class="block w-full aspect-square rounded-lg overflow-hidden border border-gray-200 hover:border-blue-400 transition-colors">
                                                    <img src="{{ $photo['thumbUrl'] }}" alt="{{ $metricData['name'] }} - {{ $photo['date'] }}" class="w-full h-full object-cover">
                                                </button>
                                                <p class="text-xs text-gray-500 text-center mt-1">{{ \Carbon\Carbon::parse($photo['date'])->format('M j') }}</p>

                                                {{-- Lightbox --}}
                                                <div x-show="showLightbox" x-cloak @click.self="showLightbox = false" @keydown.escape.window="showLightbox = false"
                                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4">
                                                    <div class="relative max-w-4xl max-h-full">
                                                        <img src="{{ $photo['fullUrl'] }}" alt="{{ $metricData['name'] }} - {{ $photo['date'] }}" class="max-w-full max-h-[90vh] rounded-lg">
                                                        <button @click="showLightbox = false" class="absolute top-2 right-2 bg-black/50 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-black/70">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                        <p class="text-white text-center mt-2 text-sm">{{ \Carbon\Carbon::parse($photo['date'])->format('M j, Y') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 text-center py-8">No progress photos for this period.</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Exercise Progression -->
        <div x-data="{ open: true }" class="bg-white rounded-lg shadow">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-left">
                <h2 class="text-lg font-semibold text-gray-900">Exercise Progression</h2>
                <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-collapse class="px-4 pb-4">
                @if(count($exerciseProgressionData) > 0)
                    <div x-data="exerciseProgression({{ json_encode($exerciseProgressionData) }}, {{ json_encode($exercisesByMuscleGroup) }})" x-init="init()">
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Exercise</label>
                            <select x-model="selectedExercise" @change="updateChart()" class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <template x-for="(exercises, group) in exerciseGroups" :key="group">
                                    <optgroup :label="group">
                                        <template x-for="ex in exercises" :key="ex.id">
                                            <option :value="ex.id" x-text="ex.name"></option>
                                        </template>
                                    </optgroup>
                                </template>
                            </select>
                        </div>

                        <div class="h-56 mb-4">
                            <canvas x-ref="canvas"></canvas>
                        </div>

                        <div x-show="summary" class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase">Start &rarr; End</p>
                                <p class="text-sm font-bold text-gray-900" x-text="summary?.startWeight + 'kg &rarr; ' + summary?.endWeight + 'kg'"></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase">Change</p>
                                <p class="text-sm font-bold" :class="summary?.change >= 0 ? 'text-green-600' : 'text-red-600'"
                                   x-text="(summary?.change >= 0 ? '+' : '') + summary?.change + 'kg (' + (summary?.change >= 0 ? '+' : '') + summary?.changePercent + '%)'"></p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase">Sessions</p>
                                <p class="text-sm font-bold text-gray-900" x-text="summary?.sessions"></p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">No workouts logged for this period.</p>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            function checkInChart(chartData) {
                return {
                    init() {
                        const existing = Chart.getChart(this.$refs.canvas);
                        if (existing) existing.destroy();
                        const ctx = this.$refs.canvas.getContext('2d');
                        const labels = chartData.data.map(d => {
                            const date = new Date(d.date + 'T00:00:00');
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        });
                        const values = chartData.data.map(d => d.value);

                        const yScale = {};
                        if (chartData.type === 'scale') {
                            yScale.min = chartData.scaleMin;
                            yScale.max = chartData.scaleMax;
                        } else {
                            yScale.beginAtZero = false;
                        }

                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    data: values,
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    fill: true,
                                    tension: 0.3,
                                    spanGaps: false,
                                    pointRadius: 3,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false },
                                },
                                scales: {
                                    x: {
                                        type: 'category',
                                        ticks: { maxTicksLimit: 10 },
                                    },
                                    y: yScale,
                                },
                            }
                        });
                    }
                };
            }
            function caloriesChart(nutritionData) {
                return {
                    renderChart() {
                        const existing = Chart.getChart(this.$refs.canvas);
                        if (existing) existing.destroy();
                        const ctx = this.$refs.canvas.getContext('2d');
                        const labels = nutritionData.map(d => {
                            const date = new Date(d.date + 'T00:00:00');
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        });
                        const calories = nutritionData.map(d => d.calories);
                        const goals = nutritionData.map(d => d.goalCalories);

                        const bgColors = nutritionData.map(d => {
                            if (!d.goalCalories || d.calories === 0) return 'rgba(209, 213, 219, 0.5)';
                            const dev = Math.abs(d.calories - d.goalCalories) / d.goalCalories;
                            if (dev <= 0.10) return 'rgba(34, 197, 94, 0.7)';
                            if (dev <= 0.25) return 'rgba(234, 179, 8, 0.7)';
                            return 'rgba(239, 68, 68, 0.7)';
                        });

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels,
                                datasets: [
                                    { label: 'Calories', data: calories, backgroundColor: bgColors, borderRadius: 3 },
                                    { label: 'Goal', data: goals, type: 'line', borderColor: 'rgba(107, 114, 128, 0.5)', borderDash: [5, 5], pointRadius: 0, fill: false, borderWidth: 2 }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12 } } },
                                scales: {
                                    x: { ticks: { maxTicksLimit: 10 } },
                                    y: { beginAtZero: true }
                                }
                            }
                        });
                    }
                };
            }

            function macrosChart(nutritionData) {
                return {
                    renderChart() {
                        const existing = Chart.getChart(this.$refs.canvas);
                        if (existing) existing.destroy();
                        const ctx = this.$refs.canvas.getContext('2d');
                        const labels = nutritionData.map(d => {
                            const date = new Date(d.date + 'T00:00:00');
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        });

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels,
                                datasets: [
                                    { label: 'Protein', data: nutritionData.map(d => d.protein), backgroundColor: 'rgba(59, 130, 246, 0.7)', borderRadius: 2 },
                                    { label: 'Carbs', data: nutritionData.map(d => d.carbs), backgroundColor: 'rgba(234, 179, 8, 0.7)', borderRadius: 2 },
                                    { label: 'Fat', data: nutritionData.map(d => d.fat), backgroundColor: 'rgba(239, 68, 68, 0.7)', borderRadius: 2 },
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12 } } },
                                scales: {
                                    x: { stacked: true, ticks: { maxTicksLimit: 10 } },
                                    y: { stacked: true, beginAtZero: true }
                                }
                            }
                        });
                    }
                };
            }

            function exerciseProgression(allData, exerciseGroups) {
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
                        const labels = data.map(d => {
                            const date = new Date(d.date + 'T00:00:00');
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        });

                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'Top Set Weight (kg)',
                                    data: data.map(d => d.weight),
                                    borderColor: '#8B5CF6',
                                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                    fill: true,
                                    tension: 0.3,
                                    pointRadius: 4,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function(ctx) {
                                                const d = data[ctx.dataIndex];
                                                return d.weight + 'kg x ' + d.reps + ' reps';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: { ticks: { maxTicksLimit: 10 } },
                                    y: { beginAtZero: false }
                                }
                            }
                        });
                    }
                };
            }
        </script>
    @endpush
</x-layouts.coach>
