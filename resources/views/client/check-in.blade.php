<x-layouts.client>
    <x-slot:title>Daily Check-in</x-slot:title>

    <div class="py-6 space-y-6" x-data="checkIn()">
        <!-- Header with Date Navigation -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daily Check-in</h1>
            <div class="mt-3 flex items-center justify-between">
                <a :href="prevUrl" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div class="flex items-center space-x-2">
                    <input type="date" x-model="currentDate" @change="navigateToDate()" max="{{ now()->format('Y-m-d') }}"
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <button @click="goToToday()" x-show="currentDate !== today"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        Today
                    </button>
                </div>

                <template x-if="currentDate < today">
                    <a :href="nextUrl" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </template>
                <template x-if="currentDate >= today">
                    <div class="w-9"></div>
                </template>
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

        @if($assignedMetrics->count() > 0)
            <form method="POST" action="{{ route('client.check-in.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                @foreach($assignedMetrics as $metric)
                    <div class="bg-white rounded-lg shadow p-4">
                        <label class="block text-sm font-medium text-gray-900 mb-1">
                            {{ $metric->name }}
                            @if($metric->unit)
                                <span class="text-gray-400 font-normal">({{ $metric->unit }})</span>
                            @endif
                        </label>
                        @if($metric->description)
                            <p class="text-xs text-gray-500 mb-2">{{ $metric->description }}</p>
                        @endif

                        @if($metric->type === 'number')
                            <input type="number" step="any" name="metrics[{{ $metric->id }}]"
                                value="{{ $existingLogs->get($metric->id)?->value }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Enter value...">

                        @elseif($metric->type === 'scale')
                            @php $currentVal = $existingLogs->get($metric->id)?->value; @endphp
                            <div x-data="{ value: '{{ $currentVal ?? '' }}' }" class="space-y-2">
                                <input type="hidden" name="metrics[{{ $metric->id }}]" :value="value">
                                <div class="flex items-center justify-between gap-1">
                                    @for($i = $metric->scale_min; $i <= $metric->scale_max; $i++)
                                        <button type="button" @click="value = value === '{{ $i }}' ? '' : '{{ $i }}'"
                                            :class="value === '{{ $i }}' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                            class="flex-1 py-2 text-sm font-medium border rounded-md transition-colors">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <div class="flex justify-between text-xs text-gray-400">
                                    <span>Low</span>
                                    <span>High</span>
                                </div>
                            </div>

                        @elseif($metric->type === 'boolean')
                            @php $currentVal = $existingLogs->get($metric->id)?->value; @endphp
                            <div x-data="{ value: '{{ $currentVal ?? '' }}' }" class="flex gap-3">
                                <input type="hidden" name="metrics[{{ $metric->id }}]" :value="value">
                                <button type="button" @click="value = value === '1' ? '' : '1'"
                                    :class="value === '1' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                    class="flex-1 py-2 text-sm font-medium border rounded-md transition-colors">
                                    Yes
                                </button>
                                <button type="button" @click="value = value === '0' ? '' : '0'"
                                    :class="value === '0' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                    class="flex-1 py-2 text-sm font-medium border rounded-md transition-colors">
                                    No
                                </button>
                            </div>

                        @elseif($metric->type === 'text')
                            <textarea name="metrics[{{ $metric->id }}]" rows="2"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Write notes...">{{ $existingLogs->get($metric->id)?->value }}</textarea>
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save Check-in
                </button>
            </form>
        @else
            <div class="bg-white rounded-lg shadow">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No metrics assigned</h3>
                    <p class="mt-1 text-sm text-gray-500">Your coach hasn't assigned any tracking metrics yet.</p>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function checkIn() {
            const currentDate = '{{ $date }}';
            const today = '{{ now()->format("Y-m-d") }}';
            const baseUrl = '{{ route("client.check-in") }}';

            function shiftDate(dateStr, days) {
                const d = new Date(dateStr + 'T00:00:00');
                d.setDate(d.getDate() + days);
                return d.toISOString().split('T')[0];
            }

            return {
                currentDate: currentDate,
                today: today,
                get prevUrl() {
                    return baseUrl + '?date=' + shiftDate(this.currentDate, -1);
                },
                get nextUrl() {
                    const next = shiftDate(this.currentDate, 1);
                    return next <= today ? baseUrl + '?date=' + next : '#';
                },
                navigateToDate() {
                    if (this.currentDate && this.currentDate <= today) {
                        window.location.href = baseUrl + '?date=' + this.currentDate;
                    }
                },
                goToToday() {
                    window.location.href = baseUrl;
                }
            };
        }
    </script>
    @endpush
</x-layouts.client>
