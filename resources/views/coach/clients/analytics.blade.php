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
        <div class="bg-white rounded-lg shadow p-4">
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
        </div>

        <!-- Daily Check-ins Section (Task 2) -->
        <!-- Nutrition Section (Task 3) -->
        <!-- Exercise Progression Section (Task 4) -->
    </div>
</x-layouts.coach>
