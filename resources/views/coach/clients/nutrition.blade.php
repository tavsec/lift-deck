<x-layouts.coach>
    <x-slot:title>{{ __('coach.clients.nutrition.heading', ['name' => $client->name]) }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.nutrition.back', ['name' => $client->name]) }}
            </a>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.clients.nutrition.heading', ['name' => $client->name]) }}</h1>
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
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6"
                    x-data="macroCalculator()">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.clients.nutrition.set_macro_goal') }}</h2>
                        <button @click="open = !open" type="button" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-xs font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-6 4h6M5 5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5z"/>
                            </svg>
                            <span x-text="open ? '{{ __('coach.clients.nutrition.calculator.hide') }}' : '{{ __('coach.clients.nutrition.calculator.button') }}'"></span>
                        </button>
                    </div>

                    <div x-show="open" x-cloak x-transition class="mb-4 border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] rounded-xl p-4 bg-gray-50 dark:bg-[#0b0d10]">
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mb-3">{{ __('coach.clients.nutrition.calculator.description') }}</p>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.clients.nutrition.calculator.weight') }}</label>
                                <input type="number" x-model.number="weight" step="0.1" min="30" max="250"
                                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.clients.nutrition.calculator.height') }}</label>
                                <input type="number" x-model.number="height" step="1" min="100" max="230"
                                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.clients.nutrition.calculator.age') }}</label>
                                <input type="number" x-model.number="age" step="1" min="14" max="100"
                                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.calculator.sex') }}</label>
                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center text-sm text-[#555b66] dark:text-[#a4abb6]">
                                    <input type="radio" x-model="sex" value="male" class="mr-1.5 text-[#5c7a10] focus:ring-[#c6f24e]/20">
                                    {{ __('coach.clients.nutrition.calculator.male') }}
                                </label>
                                <label class="inline-flex items-center text-sm text-[#555b66] dark:text-[#a4abb6]">
                                    <input type="radio" x-model="sex" value="female" class="mr-1.5 text-[#5c7a10] focus:ring-[#c6f24e]/20">
                                    {{ __('coach.clients.nutrition.calculator.female') }}
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.clients.nutrition.calculator.activity') }}</label>
                                <select x-model.number="activity" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                    <option value="1.2">{{ __('coach.clients.nutrition.calculator.activity_sedentary') }}</option>
                                    <option value="1.375">{{ __('coach.clients.nutrition.calculator.activity_light') }}</option>
                                    <option value="1.55">{{ __('coach.clients.nutrition.calculator.activity_moderate') }}</option>
                                    <option value="1.725">{{ __('coach.clients.nutrition.calculator.activity_active') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.clients.nutrition.calculator.goal') }}</label>
                                <select x-model.number="goal" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2.5 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                    <option value="-500">{{ __('coach.clients.nutrition.calculator.goal_lose_05') }}</option>
                                    <option value="-250">{{ __('coach.clients.nutrition.calculator.goal_lose_025') }}</option>
                                    <option value="0">{{ __('coach.clients.nutrition.calculator.goal_maintain') }}</option>
                                    <option value="250">{{ __('coach.clients.nutrition.calculator.goal_gain_025') }}</option>
                                    <option value="500">{{ __('coach.clients.nutrition.calculator.goal_gain_05') }}</option>
                                </select>
                            </div>
                        </div>

                        <template x-if="!isValid()">
                            <p class="mt-3 text-xs text-amber-600 dark:text-amber-400">{{ __('coach.clients.nutrition.calculator.fill_all') }}</p>
                        </template>

                        <div class="mt-4 flex items-center justify-end gap-2">
                            <button type="button" @click="open = false" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-xs font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                                {{ __('coach.clients.nutrition.calculator.cancel') }}
                            </button>
                            <button type="button" @click="apply()" :disabled="!isValid()" :class="isValid() ? 'bg-[#181b22] dark:bg-[#c6f24e] hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438]' : 'bg-[#f3f5f7] dark:bg-[#1d2027] cursor-not-allowed opacity-60'" class="inline-flex items-center px-3 py-1.5 text-white text-xs font-semibold rounded-lg transition-colors">
                                {{ __('coach.clients.nutrition.calculator.apply') }}
                            </button>
                        </div>
                    </div>

                    <template x-if="summary">
                        <p class="mb-3 text-xs text-[#555b66] dark:text-[#a4abb6] bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900 rounded-lg px-3 py-2" x-text="summary"></p>
                    </template>

                    <form method="POST" action="{{ route('coach.clients.macro-goals.store', $client) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="calories" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.calories') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="calories" id="calories" value="{{ old('calories', $currentGoal?->calories) }}" required min="0"
                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('calories') border-red-500 @enderror">
                            @error('calories')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label for="protein" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.protein') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="protein" id="protein" value="{{ old('protein', $currentGoal?->protein) }}" required min="0" step="0.1"
                                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('protein') border-red-500 @enderror">
                                @error('protein')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="carbs" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.carbs') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="carbs" id="carbs" value="{{ old('carbs', $currentGoal?->carbs) }}" required min="0" step="0.1"
                                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('carbs') border-red-500 @enderror">
                                @error('carbs')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="fat" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.fat') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="fat" id="fat" value="{{ old('fat', $currentGoal?->fat) }}" required min="0" step="0.1"
                                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('fat') border-red-500 @enderror">
                                @error('fat')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="effective_date" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.effective_date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date', now()->format('Y-m-d')) }}" required
                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('effective_date') border-red-500 @enderror">
                            @error('effective_date')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.notes') }}</label>
                            <textarea name="notes" id="notes" rows="2"
                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                placeholder="{{ __('coach.clients.nutrition.notes_placeholder') }}">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                            {{ __('coach.clients.nutrition.set_goal') }}
                        </button>
                    </form>
                </div>

                <!-- Current Goal -->
                @if($currentGoal)
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
                    <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] mb-4">{{ __('coach.clients.nutrition.current_goal') }}</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.nutrition.calories') }}</span>
                            <span class="font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ number_format($currentGoal->calories) }} kcal</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.nutrition.protein') }}</span>
                            <span class="font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $currentGoal->protein }}g</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.nutrition.carbs') }}</span>
                            <span class="font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $currentGoal->carbs }}g</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.nutrition.fat') }}</span>
                            <span class="font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $currentGoal->fat }}g</span>
                        </div>
                        <div class="pt-2 border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                            <span class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.nutrition.since', ['date' => $currentGoal->effective_date->format('M d, Y')]) }}</span>
                            @if($currentGoal->notes)
                                <p class="mt-1 text-xs text-[#8c93a0] dark:text-[#6b7280] italic">{{ $currentGoal->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Plans for this client -->
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.clients.nutrition.plans.heading') }}</h2>
                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('coach.clients.nutrition.plans.subtitle') }}</p>
                        </div>
                        <a href="{{ route('coach.clients.day-plans.create', $client) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-[#181b22] dark:bg-[#c6f24e] dark:text-[#14180a] text-xs font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('coach.clients.nutrition.plans.add_plan') }}
                        </a>
                    </div>

                    @if($clientDayPlans->isEmpty())
                        <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.nutrition.plans.no_plans') }}</p>
                    @else
                        <div class="space-y-2">
                            @foreach($clientDayPlans as $plan)
                                @php
                                    $kcal = (int) $plan->items->sum('calories');
                                @endphp
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-[#0b0d10] border border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] truncate">{{ $plan->name }}</p>
                                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
                                            {{ trans_choice('coach.clients.nutrition.plans.item_count', $plan->items_count, ['count' => $plan->items_count]) }}
                                            &middot; {{ $kcal }} kcal
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0 ml-3">
                                        <a href="{{ route('coach.clients.day-plans.edit', [$client, $plan]) }}"
                                            class="p-1.5 text-[#8c93a0] dark:text-[#6b7280] hover:text-[#5c7a10] dark:text-[#c6f24e] transition-colors" aria-label="{{ __('coach.clients.nutrition.plans.edit') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('coach.clients.day-plans.destroy', [$client, $plan]) }}"
                                            onsubmit="return confirm('{{ __('coach.clients.nutrition.plans.archive_confirm') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-[#8c93a0] dark:text-[#6b7280] hover:text-red-500 transition-colors" aria-label="{{ __('coach.clients.nutrition.plans.archive') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Day Plan Assignment -->
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
                    <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] mb-4">{{ __('coach.day_plans.assignments.heading') }}</h2>

                    @if($availableDayPlans->isEmpty())
                        <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">
                            {{ __('coach.day_plans.assignments.no_plans_yet') }}
                            <a href="{{ route('coach.clients.day-plans.create', $client) }}" class="text-[#5c7a10] dark:text-[#c6f24e] font-semibold hover:underline">{{ __('coach.day_plans.assignments.create_link') }}</a>
                        </p>
                    @else
                        <form method="POST" action="{{ route('coach.clients.day-assignments.store', $client) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="day_plan_id" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.day_plans.assignments.day_plan_label') }} <span class="text-red-500">*</span></label>
                                <select name="day_plan_id" id="day_plan_id" required
                                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('day_plan_id') border-red-500 @enderror">
                                    @foreach($availableDayPlans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                                @error('day_plan_id')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="assignment_date" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.day_plans.assignments.date_label') }} <span class="text-red-500">*</span></label>
                                <input type="date" name="date" id="assignment_date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('date') border-red-500 @enderror">
                                @error('date')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                                {{ __('coach.day_plans.assignments.submit') }}
                            </button>
                        </form>

                        @if($upcomingAssignments->isNotEmpty())
                            <div class="mt-5 pt-5 border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-[#8c93a0] dark:text-[#6b7280] mb-3">{{ __('coach.day_plans.assignments.upcoming_heading') }}</h3>
                                <div class="space-y-2">
                                    @foreach($upcomingAssignments as $assignment)
                                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-[#0b0d10] border border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] truncate">{{ $assignment->dayPlan?->name }}</p>
                                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ $assignment->date->format('M d, Y') }}</p>
                                            </div>
                                            <form method="POST" action="{{ route('coach.clients.day-assignments.destroy', [$client, $assignment]) }}"
                                                onsubmit="return confirm('{{ __('coach.day_plans.assignments.remove_confirm') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-red-500 transition-colors">
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
                    @endif
                </div>

                <!-- Goal History -->
                @if($macroGoals->count() > 0)
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
                    <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] mb-4">{{ __('coach.clients.nutrition.goal_history') }}</h2>
                    <div class="space-y-3">
                        @foreach($macroGoals as $goal)
                            <div class="flex items-start justify-between p-3 rounded-xl {{ $currentGoal && $goal->id === $currentGoal->id ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-gray-50 dark:bg-[#0b0d10]' }}">
                                <div>
                                    <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ number_format($goal->calories) }} kcal</p>
                                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">P {{ $goal->protein }}g / C {{ $goal->carbs }}g / F {{ $goal->fat }}g</p>
                                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-1">{{ $goal->effective_date->format('M d, Y') }}</p>
                                    @if($goal->notes)
                                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] italic mt-1">{{ $goal->notes }}</p>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('coach.macro-goals.destroy', $goal) }}" onsubmit="return confirm('Remove this goal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[#8c93a0] dark:text-[#6b7280] hover:text-red-500 transition-colors">
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
                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6" x-data="{ open: false }">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.clients.nutrition.log_meal') }}</h3>
                        <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                            <span x-text="open ? '{{ __('coach.clients.nutrition.cancel') }}' : '{{ __('coach.clients.nutrition.add_log') }}'"></span>
                        </button>
                    </div>
                    <div x-show="open" x-cloak class="mt-4">
                        <form method="POST" action="{{ route('coach.clients.meal-logs.store', $client) }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="date" value="{{ $from }}">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.meal_type') }}</label>
                                    <select name="meal_type" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                        <option value="breakfast">{{ __('coach.clients.nutrition.breakfast') }}</option>
                                        <option value="lunch">{{ __('coach.clients.nutrition.lunch') }}</option>
                                        <option value="dinner">{{ __('coach.clients.nutrition.dinner') }}</option>
                                        <option value="snack">{{ __('coach.clients.nutrition.snack') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.meal_name') }}</label>
                                    <input type="text" name="name" required placeholder="{{ __('coach.clients.nutrition.meal_name_placeholder') }}" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.calories') }}</label>
                                    <input type="number" name="calories" min="0" required value="0" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.protein') }}</label>
                                    <input type="number" name="protein" min="0" step="0.1" required value="0" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.carbs') }}</label>
                                    <input type="number" name="carbs" min="0" step="0.1" required value="0" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.nutrition.fat') }}</label>
                                    <input type="number" name="fat" min="0" step="0.1" required value="0" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                                    {{ __('coach.clients.nutrition.log_meal_button') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                        <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.clients.nutrition.meal_logs') }}</h2>

                        <form method="GET" action="{{ route('coach.clients.nutrition', $client) }}" x-data="{ range: '{{ $range }}' }" class="flex flex-wrap items-end gap-2">
                            <div>
                                <select name="range" x-model="range" @change="if (range !== 'custom') $el.closest('form').submit()"
                                    class="border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                    <option value="7">{{ __('coach.clients.nutrition.last_7_days') }}</option>
                                    <option value="14">{{ __('coach.clients.nutrition.last_14_days') }}</option>
                                    <option value="30">{{ __('coach.clients.nutrition.last_30_days') }}</option>
                                    <option value="custom">{{ __('coach.clients.nutrition.custom_range') }}</option>
                                </select>
                            </div>

                            <template x-if="range === 'custom'">
                                <div class="flex items-end gap-2">
                                    <div>
                                        <label class="block text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.nutrition.from') }}</label>
                                        <input type="date" name="from" value="{{ $from }}" class="border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.nutrition.to') }}</label>
                                        <input type="date" name="to" value="{{ $to }}" class="border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                    </div>
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
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
                            <div x-data="{ open: false }" class="border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] rounded-xl">
                                <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] rounded-xl transition-colors">
                                    <div>
                                        <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ \Carbon\Carbon::parse($date)->format('l, M j') }}</p>
                                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-1">
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
                                        <span class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ $dayMeals->count() }} {{ $dayMeals->count() === 1 ? __('coach.clients.nutrition.meal_singular') : __('coach.clients.nutrition.meal_plural') }}</span>
                                        <svg class="w-4 h-4 text-[#8c93a0] dark:text-[#6b7280] transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="open" x-collapse>
                                    @if($dayMeals->count() > 0)
                                        <div class="border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)] divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                                            @foreach($dayMeals as $log)
                                                <div class="px-4 py-3">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[#f3f5f7] dark:bg-[#1d2027] text-[#555b66] dark:text-[#a4abb6]">{{ $log->meal_type }}</span>
                                                            <span class="ml-2 text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $log->name }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <span class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ $log->calories }} kcal</span>
                                                            <form method="POST" action="{{ route('coach.clients.meal-logs.destroy', [$client, $log]) }}" onsubmit="return confirm('{{ __('coach.clients.nutrition.remove_confirm') }}')" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-xs font-medium text-red-500 dark:text-red-400 hover:underline">{{ __('coach.clients.nutrition.remove') }}</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <p class="mt-1 text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                                        P {{ $log->protein }}g / C {{ $log->carbs }}g / F {{ $log->fat }}g
                                                    </p>

                                                    {{-- Comments thread --}}
                                                    <div class="mt-3 ml-3 pl-3 border-l-2 border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]" x-data="{ commenting: false }">
                                                        @if($log->comments->isNotEmpty())
                                                            <ul class="space-y-2 mb-2">
                                                                @foreach($log->comments as $comment)
                                                                    <li class="flex items-start gap-2">
                                                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#1456f0]/10 text-[#5c7a10] dark:text-[#c6f24e] text-[11px] font-semibold flex items-center justify-center">
                                                                            {{ mb_strtoupper(mb_substr($comment->author?->name ?? '?', 0, 1)) }}
                                                                        </div>
                                                                        <div class="flex-1 min-w-0">
                                                                            <p class="text-xs text-[#555b66] dark:text-[#a4abb6] whitespace-pre-line">{{ $comment->body }}</p>
                                                                            <p class="text-[11px] text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
                                                                                {{ $comment->author?->name }} · {{ $comment->created_at->diffForHumans() }}
                                                                            </p>
                                                                        </div>
                                                                        @if($comment->author_id === auth()->id())
                                                                            <form method="POST" action="{{ route('coach.meal-log-comments.destroy', $comment) }}"
                                                                                onsubmit="return confirm('{{ __('coach.clients.nutrition.comments.delete_confirm') }}');">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="text-[#8c93a0] dark:text-[#6b7280] hover:text-red-500 transition-colors" aria-label="{{ __('coach.clients.nutrition.comments.delete') }}">
                                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                                    </svg>
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif

                                                        <div x-show="!commenting">
                                                            <button type="button" @click="commenting = true"
                                                                class="text-xs font-medium text-[#5c7a10] dark:text-[#c6f24e] font-semibold hover:underline">
                                                                {{ __('coach.clients.nutrition.comments.add') }}
                                                            </button>
                                                        </div>

                                                        <form x-show="commenting" x-cloak method="POST"
                                                            action="{{ route('coach.clients.meal-logs.comments.store', [$client, $log]) }}"
                                                            class="space-y-2">
                                                            @csrf
                                                            <textarea name="body" rows="2" required maxlength="1000"
                                                                placeholder="{{ __('coach.clients.nutrition.comments.placeholder') }}"
                                                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"></textarea>
                                                            <div class="flex items-center gap-2">
                                                                <button type="submit"
                                                                    class="inline-flex items-center px-3 py-1.5 bg-[#181b22] dark:bg-[#c6f24e] dark:text-[#14180a] text-xs font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                                                                    {{ __('coach.clients.nutrition.comments.submit') }}
                                                                </button>
                                                                <button type="button" @click="commenting = false"
                                                                    class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] text-[#555b66] dark:text-[#a4abb6] text-xs font-semibold rounded-lg hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                                                                    {{ __('coach.clients.nutrition.comments.cancel') }}
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)] p-4 text-center">
                                            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.nutrition.no_meals') }}</p>
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

    @push('scripts')
        <script>
            function macroCalculator() {
                return {
                    open: false,
                    weight: null,
                    height: null,
                    age: null,
                    sex: 'male',
                    activity: 1.55,
                    goal: 0,
                    summary: '',
                    isValid() {
                        return Number.isFinite(this.weight) && this.weight >= 30 && this.weight <= 250
                            && Number.isFinite(this.height) && this.height >= 100 && this.height <= 230
                            && Number.isFinite(this.age) && this.age >= 14 && this.age <= 100
                            && (this.sex === 'male' || this.sex === 'female')
                            && Number.isFinite(this.activity) && Number.isFinite(this.goal);
                    },
                    apply() {
                        if (!this.isValid()) {
                            return;
                        }
                        const bmr = this.sex === 'male'
                            ? 10 * this.weight + 6.25 * this.height - 5 * this.age + 5
                            : 10 * this.weight + 6.25 * this.height - 5 * this.age - 161;
                        const tdee = bmr * this.activity;
                        const calories = Math.round((tdee + this.goal) / 50) * 50;
                        const protein = Math.round(this.weight * 1.8);
                        const fat = Math.round(this.weight * 0.8);
                        const carbs = Math.round(Math.max(0, (calories - protein * 4 - fat * 9) / 4));

                        document.querySelector('input[name="calories"]#calories').value = calories;
                        document.querySelector('input[name="protein"]#protein').value = protein;
                        document.querySelector('input[name="carbs"]#carbs').value = carbs;
                        document.querySelector('input[name="fat"]#fat').value = fat;

                        this.summary = `{{ __('coach.clients.nutrition.calculator.summary_prefix') }} ${calories} kcal • ${protein} P • ${carbs} C • ${fat} F (Mifflin-St Jeor)`;
                        this.open = false;
                    },
                };
            }
        </script>
    @endpush
</x-layouts.coach>
