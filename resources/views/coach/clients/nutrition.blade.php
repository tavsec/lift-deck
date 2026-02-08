<x-layouts.coach>
    <x-slot:title>{{ $client->name }} — Nutrition</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to {{ $client->name }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }} — Nutrition</h1>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Set Goals -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Set Macro Goal Form -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Set Macro Goal</h2>
                    <form method="POST" action="{{ route('coach.clients.macro-goals.store', $client) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="calories" class="block text-sm font-medium text-gray-700">Calories (kcal) <span class="text-red-500">*</span></label>
                            <input type="number" name="calories" id="calories" value="{{ old('calories', $currentGoal?->calories) }}" required min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('calories') border-red-300 @enderror">
                            @error('calories')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label for="protein" class="block text-sm font-medium text-gray-700">Protein (g) <span class="text-red-500">*</span></label>
                                <input type="number" name="protein" id="protein" value="{{ old('protein', $currentGoal?->protein) }}" required min="0" step="0.1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('protein') border-red-300 @enderror">
                                @error('protein')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="carbs" class="block text-sm font-medium text-gray-700">Carbs (g) <span class="text-red-500">*</span></label>
                                <input type="number" name="carbs" id="carbs" value="{{ old('carbs', $currentGoal?->carbs) }}" required min="0" step="0.1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('carbs') border-red-300 @enderror">
                                @error('carbs')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="fat" class="block text-sm font-medium text-gray-700">Fat (g) <span class="text-red-500">*</span></label>
                                <input type="number" name="fat" id="fat" value="{{ old('fat', $currentGoal?->fat) }}" required min="0" step="0.1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('fat') border-red-300 @enderror">
                                @error('fat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="effective_date" class="block text-sm font-medium text-gray-700">Effective Date <span class="text-red-500">*</span></label>
                            <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date', now()->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('effective_date') border-red-300 @enderror">
                            @error('effective_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Why the change?">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Set Goal
                        </button>
                    </form>
                </div>

                <!-- Current Goal -->
                @if($currentGoal)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Current Goal</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Calories</span>
                            <span class="font-medium text-gray-900">{{ number_format($currentGoal->calories) }} kcal</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Protein</span>
                            <span class="font-medium text-gray-900">{{ $currentGoal->protein }}g</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Carbs</span>
                            <span class="font-medium text-gray-900">{{ $currentGoal->carbs }}g</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Fat</span>
                            <span class="font-medium text-gray-900">{{ $currentGoal->fat }}g</span>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <span class="text-xs text-gray-400">Since {{ $currentGoal->effective_date->format('M d, Y') }}</span>
                            @if($currentGoal->notes)
                                <p class="mt-1 text-xs text-gray-500 italic">{{ $currentGoal->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Goal History -->
                @if($macroGoals->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Goal History</h2>
                    <div class="space-y-3">
                        @foreach($macroGoals as $goal)
                            <div class="flex items-start justify-between p-3 rounded-md {{ $currentGoal && $goal->id === $currentGoal->id ? 'bg-blue-50' : 'bg-gray-50' }}">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ number_format($goal->calories) }} kcal</p>
                                    <p class="text-xs text-gray-500">P {{ $goal->protein }}g / C {{ $goal->carbs }}g / F {{ $goal->fat }}g</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $goal->effective_date->format('M d, Y') }}</p>
                                    @if($goal->notes)
                                        <p class="text-xs text-gray-500 italic mt-1">{{ $goal->notes }}</p>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('coach.macro-goals.destroy', $goal) }}" onsubmit="return confirm('Remove this goal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right: Meal Logs -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Meal Logs</h2>

                        <form method="GET" action="{{ route('coach.clients.nutrition', $client) }}" x-data="{ range: '{{ $range }}' }" class="flex flex-wrap items-end gap-2">
                            <div>
                                <select name="range" x-model="range" @change="if (range !== 'custom') $el.closest('form').submit()"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="7">Last 7 days</option>
                                    <option value="14">Last 14 days</option>
                                    <option value="30">Last 30 days</option>
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

                    <div class="space-y-4">
                        @foreach($dates->reverse() as $date)
                            @php
                                $totals = $dailyTotals[$date];
                                $dayMeals = $totals['meals'];
                                $hasGoal = $currentGoal !== null;
                            @endphp
                            <div x-data="{ open: false }" class="border border-gray-200 rounded-lg">
                                <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($date)->format('l, M j') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $totals['calories'] }} kcal
                                            @if($hasGoal)
                                                / {{ number_format($currentGoal->calories) }}
                                            @endif
                                            &middot;
                                            P {{ number_format($totals['protein'], 1) }}g &middot;
                                            C {{ number_format($totals['carbs'], 1) }}g &middot;
                                            F {{ number_format($totals['fat'], 1) }}g
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400">{{ $dayMeals->count() }} {{ Str::plural('meal', $dayMeals->count()) }}</span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="open" x-collapse>
                                    @if($dayMeals->count() > 0)
                                        <div class="border-t border-gray-200 divide-y divide-gray-100">
                                            @foreach($dayMeals as $log)
                                                <div class="px-4 py-3">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $log->meal_type }}</span>
                                                            <span class="ml-2 text-sm font-medium text-gray-900">{{ $log->name }}</span>
                                                        </div>
                                                        <span class="text-sm text-gray-500">{{ $log->calories }} kcal</span>
                                                    </div>
                                                    <p class="mt-1 text-xs text-gray-400">
                                                        P {{ $log->protein }}g / C {{ $log->carbs }}g / F {{ $log->fat }}g
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="border-t border-gray-200 p-4 text-center">
                                            <p class="text-sm text-gray-400">No meals logged</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
