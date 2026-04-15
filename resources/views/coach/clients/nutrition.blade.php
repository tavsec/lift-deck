<x-layouts.coach>
    <x-slot:title>{{ __('coach.clients.nutrition.heading', ['name' => $client->name]) }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.nutrition.back', ['name' => $client->name]) }}
            </a>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.clients.nutrition.heading', ['name' => $client->name]) }}</h1>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Set Goals -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Set Macro Goal Form -->
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                    <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.clients.nutrition.set_macro_goal') }}</h2>
                    <form method="POST" action="{{ route('coach.clients.macro-goals.store', $client) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="calories" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.calories') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="calories" id="calories" value="{{ old('calories', $currentGoal?->calories) }}" required min="0"
                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('calories') border-red-500 @enderror">
                            @error('calories')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label for="protein" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.protein') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="protein" id="protein" value="{{ old('protein', $currentGoal?->protein) }}" required min="0" step="0.1"
                                    class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('protein') border-red-500 @enderror">
                                @error('protein')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="carbs" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.carbs') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="carbs" id="carbs" value="{{ old('carbs', $currentGoal?->carbs) }}" required min="0" step="0.1"
                                    class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('carbs') border-red-500 @enderror">
                                @error('carbs')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="fat" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.fat') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="fat" id="fat" value="{{ old('fat', $currentGoal?->fat) }}" required min="0" step="0.1"
                                    class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('fat') border-red-500 @enderror">
                                @error('fat')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="effective_date" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.effective_date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date', now()->format('Y-m-d')) }}" required
                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('effective_date') border-red-500 @enderror">
                            @error('effective_date')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.notes') }}</label>
                            <textarea name="notes" id="notes" rows="2"
                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150"
                                placeholder="{{ __('coach.clients.nutrition.notes_placeholder') }}">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                            {{ __('coach.clients.nutrition.set_goal') }}
                        </button>
                    </form>
                </div>

                <!-- Current Goal -->
                @if($currentGoal)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                    <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.clients.nutrition.current_goal') }}</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-[#8e8e93] dark:text-gray-400">{{ __('coach.clients.nutrition.calories') }}</span>
                            <span class="font-medium text-[#222222] dark:text-gray-100">{{ number_format($currentGoal->calories) }} kcal</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#8e8e93] dark:text-gray-400">{{ __('coach.clients.nutrition.protein') }}</span>
                            <span class="font-medium text-[#222222] dark:text-gray-100">{{ $currentGoal->protein }}g</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#8e8e93] dark:text-gray-400">{{ __('coach.clients.nutrition.carbs') }}</span>
                            <span class="font-medium text-[#222222] dark:text-gray-100">{{ $currentGoal->carbs }}g</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#8e8e93] dark:text-gray-400">{{ __('coach.clients.nutrition.fat') }}</span>
                            <span class="font-medium text-[#222222] dark:text-gray-100">{{ $currentGoal->fat }}g</span>
                        </div>
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                            <span class="text-xs text-[#8e8e93] dark:text-gray-500">{{ __('coach.clients.nutrition.since', ['date' => $currentGoal->effective_date->format('M d, Y')]) }}</span>
                            @if($currentGoal->notes)
                                <p class="mt-1 text-xs text-[#8e8e93] dark:text-gray-400 italic">{{ $currentGoal->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Goal History -->
                @if($macroGoals->count() > 0)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                    <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.clients.nutrition.goal_history') }}</h2>
                    <div class="space-y-3">
                        @foreach($macroGoals as $goal)
                            <div class="flex items-start justify-between p-3 rounded-xl {{ $currentGoal && $goal->id === $currentGoal->id ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-gray-50 dark:bg-gray-950' }}">
                                <div>
                                    <p class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ number_format($goal->calories) }} kcal</p>
                                    <p class="text-xs text-[#8e8e93] dark:text-gray-400">P {{ $goal->protein }}g / C {{ $goal->carbs }}g / F {{ $goal->fat }}g</p>
                                    <p class="text-xs text-[#8e8e93] dark:text-gray-500 mt-1">{{ $goal->effective_date->format('M d, Y') }}</p>
                                    @if($goal->notes)
                                        <p class="text-xs text-[#8e8e93] dark:text-gray-400 italic mt-1">{{ $goal->notes }}</p>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('coach.macro-goals.destroy', $goal) }}" onsubmit="return confirm('Remove this goal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[#8e8e93] dark:text-gray-500 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
                <!-- Log Meal for Client -->
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6" x-data="{ open: false }">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.clients.nutrition.log_meal') }}</h3>
                        <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <span x-text="open ? '{{ __('coach.clients.nutrition.cancel') }}' : '{{ __('coach.clients.nutrition.add_log') }}'"></span>
                        </button>
                    </div>
                    <div x-show="open" x-cloak class="mt-4">
                        <form method="POST" action="{{ route('coach.clients.meal-logs.store', $client) }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="date" value="{{ $from }}">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.meal_type') }}</label>
                                    <select name="meal_type" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                        <option value="breakfast">{{ __('coach.clients.nutrition.breakfast') }}</option>
                                        <option value="lunch">{{ __('coach.clients.nutrition.lunch') }}</option>
                                        <option value="dinner">{{ __('coach.clients.nutrition.dinner') }}</option>
                                        <option value="snack">{{ __('coach.clients.nutrition.snack') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.meal_name') }}</label>
                                    <input type="text" name="name" required placeholder="{{ __('coach.clients.nutrition.meal_name_placeholder') }}" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.calories') }}</label>
                                    <input type="number" name="calories" min="0" required value="0" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.protein') }}</label>
                                    <input type="number" name="protein" min="0" step="0.1" required value="0" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.carbs') }}</label>
                                    <input type="number" name="carbs" min="0" step="0.1" required value="0" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.nutrition.fat') }}</label>
                                    <input type="number" name="fat" min="0" step="0.1" required value="0" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                                    {{ __('coach.clients.nutrition.log_meal_button') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                        <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.clients.nutrition.meal_logs') }}</h2>

                        <form method="GET" action="{{ route('coach.clients.nutrition', $client) }}" x-data="{ range: '{{ $range }}' }" class="flex flex-wrap items-end gap-2">
                            <div>
                                <select name="range" x-model="range" @change="if (range !== 'custom') $el.closest('form').submit()"
                                    class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                    <option value="7">{{ __('coach.clients.nutrition.last_7_days') }}</option>
                                    <option value="14">{{ __('coach.clients.nutrition.last_14_days') }}</option>
                                    <option value="30">{{ __('coach.clients.nutrition.last_30_days') }}</option>
                                    <option value="custom">{{ __('coach.clients.nutrition.custom_range') }}</option>
                                </select>
                            </div>

                            <template x-if="range === 'custom'">
                                <div class="flex items-end gap-2">
                                    <div>
                                        <label class="block text-xs text-[#8e8e93] dark:text-gray-400">{{ __('coach.clients.nutrition.from') }}</label>
                                        <input type="date" name="from" value="{{ $from }}" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-[#8e8e93] dark:text-gray-400">{{ __('coach.clients.nutrition.to') }}</label>
                                        <input type="date" name="to" value="{{ $to }}" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                    </div>
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                                        {{ __('coach.clients.nutrition.apply') }}
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
                            <div x-data="{ open: false }" class="border border-gray-200 dark:border-gray-800 rounded-xl">
                                <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-800/40 rounded-xl transition-colors">
                                    <div>
                                        <p class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ \Carbon\Carbon::parse($date)->format('l, M j') }}</p>
                                        <p class="text-xs text-[#8e8e93] dark:text-gray-400 mt-1">
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
                                        <span class="text-xs text-[#8e8e93] dark:text-gray-500">{{ $dayMeals->count() }} {{ $dayMeals->count() === 1 ? __('coach.clients.nutrition.meal_singular') : __('coach.clients.nutrition.meal_plural') }}</span>
                                        <svg class="w-4 h-4 text-[#8e8e93] dark:text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="open" x-collapse>
                                    @if($dayMeals->count() > 0)
                                        <div class="border-t border-gray-100 dark:border-gray-800 divide-y divide-gray-100 dark:divide-gray-800">
                                            @foreach($dayMeals as $log)
                                                <div class="px-4 py-3">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-[#45515e] dark:text-gray-300">{{ $log->meal_type }}</span>
                                                            <span class="ml-2 text-sm font-medium text-[#222222] dark:text-gray-100">{{ $log->name }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <span class="text-sm text-[#8e8e93] dark:text-gray-400">{{ $log->calories }} kcal</span>
                                                            <form method="POST" action="{{ route('coach.clients.meal-logs.destroy', [$client, $log]) }}" onsubmit="return confirm('{{ __('coach.clients.nutrition.remove_confirm') }}')" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-xs font-medium text-red-500 dark:text-red-400 hover:underline">{{ __('coach.clients.nutrition.remove') }}</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <p class="mt-1 text-xs text-[#8e8e93] dark:text-gray-500">
                                                        P {{ $log->protein }}g / C {{ $log->carbs }}g / F {{ $log->fat }}g
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="border-t border-gray-100 dark:border-gray-800 p-4 text-center">
                                            <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.clients.nutrition.no_meals') }}</p>
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
