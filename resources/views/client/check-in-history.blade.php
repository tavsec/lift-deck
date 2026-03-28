<x-layouts.client>
    <x-slot:title>{{ __('client.check_in_history.heading') }}</x-slot:title>

    <div class="py-6 space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('client.check-in') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('client.check_in_history.back') }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('client.check_in_history.heading') }}</h1>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
            <form method="GET" action="{{ route('client.check-in.history') }}">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('client.check_in_history.time_period') }}</label>
                    <select name="range" onchange="this.form.submit()"
                        class="block rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="7" @selected($range == '7')>{{ __('client.check_in_history.last_7_days') }}</option>
                        <option value="14" @selected($range == '14')>{{ __('client.check_in_history.last_14_days') }}</option>
                        <option value="30" @selected($range == '30')>{{ __('client.check_in_history.last_30_days') }}</option>
                        <option value="90" @selected($range == '90')>{{ __('client.check_in_history.last_90_days') }}</option>
                    </select>
                </div>
            </form>
        </div>

        @if(count($checkInCharts) === 0 && $tableMetrics->count() === 0 && $imageMetrics->isEmpty())
            <!-- No metrics assigned -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('client.check_in_history.no_metrics') }}</p>
                </div>
            </div>
        @else
            <!-- Check-in Charts & Table -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('client.check_in.heading') }}</h2>
                </div>

                <div class="px-4 pb-4 pt-4 space-y-6">
                    @if(count($checkInCharts) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($checkInCharts as $chart)
                                <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-3">
                                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $chart['name'] }} @if($chart['unit'])({{ $chart['unit'] }})@endif</h3>
                                    <div x-data="clientCheckInChart({{ json_encode($chart) }})" x-init="init()">
                                        <canvas x-ref="canvas" height="200"></canvas>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($tableMetrics->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('client.check_in_history.date') }}</th>
                                        @foreach($tableMetrics as $metric)
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $metric->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
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
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($row['date'])->format('M j') }}</td>
                                                @foreach($tableMetrics as $metric)
                                                    <td class="px-3 py-2">
                                                        @if($row['metric_' . $metric->id] === null)
                                                            <span class="text-gray-300 dark:text-gray-600">—</span>
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
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">{{ __('client.check_in_history.no_data') }}</p>
                    @endif
                </div>
            </div>

            @if($imageMetrics->isNotEmpty())
                <!-- Progress Photos -->
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('client.check_in_history.progress_photos') }}</h2>
                    </div>

                    <div class="px-4 pb-4 pt-4 space-y-6">
                        @php $hasAnyPhotos = collect($imageMetricData)->contains(fn ($m) => count($m['photos']) > 0); @endphp

                        @if($hasAnyPhotos)
                            @foreach($imageMetricData as $metricData)
                                @if(count($metricData['photos']) > 0)
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $metricData['name'] }}</h3>
                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                                            @foreach($metricData['photos'] as $photo)
                                                <div x-data="{ showLightbox: false }" class="relative">
                                                    <button @click="showLightbox = true" class="block w-full aspect-square rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800 hover:border-blue-400 transition-colors">
                                                        <img src="{{ $photo['thumbUrl'] }}" alt="{{ $metricData['name'] }} - {{ $photo['date'] }}" class="w-full h-full object-cover">
                                                    </button>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">{{ \Carbon\Carbon::parse($photo['date'])->format('M j') }}</p>

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
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">{{ __('client.check_in_history.no_photos') }}</p>
                        @endif
                    </div>
                </div>
            @endif
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

        function clientCheckInChart(chartData) {
            return {
                init() {
                    const existing = Chart.getChart(this.$refs.canvas);
                    if (existing) existing.destroy();
                    const ctx = this.$refs.canvas.getContext('2d');
                    const theme = chartTheme();
                    const labels = chartData.data.map(d => {
                        const date = new Date(d.date + 'T00:00:00');
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    });
                    const values = chartData.data.map(d => d.value);

                    const yScale = { grid: { color: theme.gridColor }, ticks: { color: theme.tickColor } };
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
                                    ticks: { maxTicksLimit: 10, color: theme.tickColor },
                                    grid: { color: theme.gridColor },
                                },
                                y: yScale,
                            },
                        }
                    });
                }
            };
        }
    </script>
    @endpush
</x-layouts.client>
