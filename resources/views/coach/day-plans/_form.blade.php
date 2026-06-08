@php
    $existingItems = isset($dayPlan)
        ? $dayPlan->items->map(fn ($item) => [
            'source' => $item->meal_id ? 'library' : ($item->off_code ? 'off' : 'custom'),
            'meal_id' => $item->meal_id,
            'off_code' => $item->off_code,
            'meal_type' => $item->meal_type,
            'name' => $item->name,
            'calories' => (int) $item->calories,
            'protein' => (float) $item->protein,
            'carbs' => (float) $item->carbs,
            'fat' => (float) $item->fat,
            'portion_grams' => $item->portion_grams,
        ])->values()
        : collect();

    $existingSections = isset($dayPlan)
        ? $dayPlan->items->pluck('meal_type')->unique()->values()->all()
        : [];

    $initialSections = isset($dayPlan)
        ? (count($existingSections) > 0 ? $existingSections : $defaultSections)
        : $defaultSections;

    $foodSearchUrl = route('coach.day-plans.food-search');
    $customMacrosDefault = __('coach.day_plans.form.custom_macros_default');
@endphp

<div x-data="dayPlanBuilder({{ Js::from($availableMeals) }}, {{ Js::from($existingItems) }}, {{ Js::from($initialSections) }}, {{ Js::from($foodSearchUrl) }}, {{ Js::from($customMacrosDefault) }})">
    <form method="POST" action="{{ $action }}" class="space-y-6" @submit="syncItemsBeforeSubmit($event)">
        @csrf
        @if(($method ?? 'POST') === 'PUT')
            @method('PUT')
        @endif

        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.day_plans.form.name') }} <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $dayPlan->name ?? '') }}" required
                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('name') border-red-300 dark:border-red-700 @enderror"
                    placeholder="{{ __('coach.day_plans.form.name_placeholder') }}">
                @error('name')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.day_plans.form.description') }}</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                    placeholder="{{ __('coach.day_plans.form.description_placeholder') }}">{{ old('description', $dayPlan->description ?? '') }}</textarea>
            </div>

            <!-- Running totals -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-gray-50 dark:bg-[#0b0d10] rounded-xl border border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                <div>
                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] uppercase">{{ __('coach.day_plans.form.total_calories') }}</p>
                    <p class="text-lg font-semibold text-[#181b22] dark:text-[#f0f2f5]" x-text="totals.calories + ' kcal'"></p>
                </div>
                <div>
                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] uppercase">{{ __('coach.day_plans.form.total_protein') }}</p>
                    <p class="text-lg font-semibold text-[#181b22] dark:text-[#f0f2f5]" x-text="totals.protein.toFixed(1) + 'g'"></p>
                </div>
                <div>
                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] uppercase">{{ __('coach.day_plans.form.total_carbs') }}</p>
                    <p class="text-lg font-semibold text-[#181b22] dark:text-[#f0f2f5]" x-text="totals.carbs.toFixed(1) + 'g'"></p>
                </div>
                <div>
                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] uppercase">{{ __('coach.day_plans.form.total_fat') }}</p>
                    <p class="text-lg font-semibold text-[#181b22] dark:text-[#f0f2f5]" x-text="totals.fat.toFixed(1) + 'g'"></p>
                </div>
            </div>
        </div>

        <!-- Meal sections -->
        <template x-for="(section, sIdx) in sections" :key="section._key">
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
                <div class="flex items-center justify-between mb-4 gap-2">
                    <div class="flex-1 min-w-0">
                        <template x-if="!section.editing">
                            <div class="flex items-center gap-2">
                                <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] truncate" x-text="section.label"></h3>
                                <button type="button" @click="startRenameSection(sIdx)" class="p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] transition-colors" :aria-label="'{{ __('coach.day_plans.form.rename_section') }}'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="section.editing">
                            <input type="text" x-model="section.label" @keydown.enter.prevent="finishRenameSection(sIdx)" @blur="finishRenameSection(sIdx)" x-init="$nextTick(() => $el.focus())"
                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                        </template>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button type="button" @click="openPicker(section._key)"
                            class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-xs font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('coach.day_plans.form.add_item') }}
                        </button>
                        <button type="button" x-show="itemsForSection(section._key).length === 0" @click="removeSection(sIdx)"
                            class="p-1.5 text-[#8c93a0] dark:text-[#6b7280] hover:text-red-500 transition-colors" :aria-label="'{{ __('coach.day_plans.form.remove_section') }}'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div x-show="itemsForSection(section._key).length === 0" class="text-center py-6 border border-dashed border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg">
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.day_plans.form.empty_section') }}</p>
                </div>

                <div class="space-y-2" x-show="itemsForSection(section._key).length > 0">
                    <template x-for="item in itemsForSection(section._key)" :key="item._key">
                        <div class="flex items-center justify-between p-3 rounded-lg border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] bg-gray-50 dark:bg-[#0b0d10]">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]" x-text="item.name"></p>
                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
                                    <span x-text="item.calories"></span> kcal &middot;
                                    P <span x-text="Number(item.protein).toFixed(1)"></span>g &middot;
                                    C <span x-text="Number(item.carbs).toFixed(1)"></span>g &middot;
                                    F <span x-text="Number(item.fat).toFixed(1)"></span>g
                                    <template x-if="item.portion_grams"><span class="ml-1 text-[#8c93a0] dark:text-[#6b7280]">(<span x-text="item.portion_grams"></span> g)</span></template>
                                </p>
                            </div>
                            <button type="button" @click="removeItem(item._key)"
                                class="ml-3 p-1.5 text-[#8c93a0] dark:text-[#6b7280] hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Add section -->
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-dashed border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] p-4">
            <template x-if="!addingSection">
                <button type="button" @click="addingSection = true; $nextTick(() => $refs.newSection?.focus())"
                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('coach.day_plans.form.add_section') }}
                </button>
            </template>
            <template x-if="addingSection">
                <div class="flex gap-2">
                    <input type="text" x-ref="newSection" x-model="newSectionLabel" @keydown.enter.prevent="confirmAddSection()" @keydown.escape="cancelAddSection()"
                        placeholder="{{ __('coach.day_plans.form.section_name_placeholder') }}"
                        class="flex-1 border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                    <button type="button" @click="confirmAddSection()" class="inline-flex items-center px-3 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                        {{ __('coach.day_plans.form.add') }}
                    </button>
                    <button type="button" @click="cancelAddSection()" class="inline-flex items-center px-3 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] text-[#555b66] dark:text-[#a4abb6] text-sm font-semibold rounded-lg hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                        {{ __('coach.day_plans.form.cancel') }}
                    </button>
                </div>
            </template>
        </div>

        <!-- Picker modal -->
        <div x-show="picker.open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 px-4"
            @keydown.escape.window="closePicker()">
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-xl max-w-lg w-full max-h-[90vh] flex flex-col" @click.outside="closePicker()">
                <div class="px-5 py-4 border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)] flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5]">
                        {{ __('coach.day_plans.form.picker.heading') }}
                        <span class="text-[#8c93a0] dark:text-[#6b7280]" x-text="' — ' + (picker.label ?? '')"></span>
                    </h3>
                    <button type="button" @click="closePicker()" class="p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Tabs -->
                <div class="border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                    <nav class="flex">
                        <button type="button" @click="picker.tab = 'library'" :class="picker.tab === 'library' ? 'border-[#c6f24e] text-[#5c7a10] dark:text-[#c6f24e]' : 'border-transparent text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5]'" class="flex-1 py-2.5 px-3 text-center text-xs font-medium border-b-2 transition-colors">{{ __('coach.day_plans.form.sources.library') }}</button>
                        <button type="button" @click="picker.tab = 'custom'" :class="picker.tab === 'custom' ? 'border-[#c6f24e] text-[#5c7a10] dark:text-[#c6f24e]' : 'border-transparent text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5]'" class="flex-1 py-2.5 px-3 text-center text-xs font-medium border-b-2 transition-colors">{{ __('coach.day_plans.form.sources.custom') }}</button>
                        <button type="button" @click="picker.tab = 'off'" :class="picker.tab === 'off' ? 'border-[#c6f24e] text-[#5c7a10] dark:text-[#c6f24e]' : 'border-transparent text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5]'" class="flex-1 py-2.5 px-3 text-center text-xs font-medium border-b-2 transition-colors">{{ __('coach.day_plans.form.sources.off') }}</button>
                        <button type="button" @click="picker.tab = 'macros'" :class="picker.tab === 'macros' ? 'border-[#c6f24e] text-[#5c7a10] dark:text-[#c6f24e]' : 'border-transparent text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5]'" class="flex-1 py-2.5 px-3 text-center text-xs font-medium border-b-2 transition-colors">{{ __('coach.day_plans.form.sources.macros') }}</button>
                    </nav>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <!-- Library tab -->
                    <div x-show="picker.tab === 'library'" class="space-y-3">
                        <input type="text" x-model="picker.search"
                            placeholder="{{ __('coach.day_plans.form.picker.search_placeholder') }}"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">

                        <template x-if="filteredMeals().length === 0">
                            <p class="text-center text-sm text-[#8c93a0] dark:text-[#6b7280] py-4">{{ __('coach.day_plans.form.picker.no_meals') }}</p>
                        </template>

                        <div x-show="!libraryDraft" class="max-h-72 overflow-y-auto space-y-2">
                            <template x-for="meal in filteredMeals()" :key="meal.id">
                                <button type="button" @click="selectLibraryMeal(meal)"
                                    class="w-full text-left p-3 rounded-lg border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] hover:border-[#c6f24e] hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]" x-text="meal.name"></p>
                                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
                                        <span x-text="meal.calories"></span> kcal &middot;
                                        P <span x-text="meal.protein"></span>g &middot;
                                        C <span x-text="meal.carbs"></span>g &middot;
                                        F <span x-text="meal.fat"></span>g
                                    </p>
                                </button>
                            </template>
                        </div>

                        <div x-show="libraryDraft" x-cloak class="space-y-3 pt-3 border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                            <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]" x-text="libraryDraft?.name"></p>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.day_plans.form.portion') }}</label>
                                <div class="flex gap-2 flex-wrap">
                                    <template x-for="p in [0.5, 1, 1.5, 2]" :key="p">
                                        <button type="button" @click="libraryPortion = p"
                                            :class="libraryPortion === p ? 'text-white border-[#1456f0]' : 'bg-white dark:bg-[#11141a] text-[#555b66] dark:text-[#a4abb6] border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027]'"
                                            :style="libraryPortion === p ? 'background-color: var(--color-primary)' : ''"
                                            class="px-3 py-1.5 text-sm font-medium border rounded-lg transition-colors">
                                            <span x-text="p + '×'"></span>
                                        </button>
                                    </template>
                                    <input type="number" step="0.1" min="0.1" max="10" x-model.number="libraryPortion"
                                        class="w-24 border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-[#1456f0]">
                                </div>
                            </div>
                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                <span x-text="Math.round((libraryDraft?.calories || 0) * libraryPortion)"></span> kcal &middot;
                                P <span x-text="((libraryDraft?.protein || 0) * libraryPortion).toFixed(1)"></span>g &middot;
                                C <span x-text="((libraryDraft?.carbs || 0) * libraryPortion).toFixed(1)"></span>g &middot;
                                F <span x-text="((libraryDraft?.fat || 0) * libraryPortion).toFixed(1)"></span>g
                            </p>
                            <div class="flex gap-2">
                                <button type="button" @click="libraryDraft = null" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] text-[#555b66] dark:text-[#a4abb6] text-sm font-semibold rounded-lg hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">{{ __('coach.day_plans.form.cancel') }}</button>
                                <button type="button" @click="confirmLibrary()" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">{{ __('coach.day_plans.form.add_to_section') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Custom tab -->
                    <div x-show="picker.tab === 'custom'" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.day_plans.form.item_name') }} <span class="text-red-500">*</span></label>
                            <input type="text" x-model="customDraft.name" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)]" placeholder="{{ __('coach.day_plans.form.item_name_placeholder') }}">
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.day_plans.form.kcal') }}</label>
                                <input type="number" min="0" x-model.number="customDraft.calories" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#1456f0]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">P</label>
                                <input type="number" step="0.1" min="0" x-model.number="customDraft.protein" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#1456f0]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">C</label>
                                <input type="number" step="0.1" min="0" x-model.number="customDraft.carbs" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#1456f0]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">F</label>
                                <input type="number" step="0.1" min="0" x-model.number="customDraft.fat" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#1456f0]">
                            </div>
                        </div>
                        <button type="button" @click="confirmCustom()" :disabled="!customDraft.name?.trim()" class="w-full inline-flex items-center justify-center px-3 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">{{ __('coach.day_plans.form.add_to_section') }}</button>
                    </div>

                    <!-- OFF Search tab -->
                    <div x-show="picker.tab === 'off'" class="space-y-3">
                        <input type="text" x-model="offQuery" @input.debounce.300ms="searchOff()"
                            placeholder="{{ __('coach.day_plans.form.off.search_placeholder') }}"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)]">
                        <template x-if="offSearching">
                            <p class="text-center text-sm text-[#8c93a0] dark:text-[#6b7280] py-3">{{ __('coach.day_plans.form.off.loading') }}</p>
                        </template>
                        <template x-if="!offSearching && offSearched && offResults.length === 0">
                            <p class="text-center text-sm text-[#8c93a0] dark:text-[#6b7280] py-3">{{ __('coach.day_plans.form.off.no_results') }}</p>
                        </template>

                        <div x-show="!offDraft && offResults.length > 0" class="max-h-60 overflow-y-auto space-y-2">
                            <template x-for="r in offResults" :key="r.code">
                                <button type="button" @click="selectOffResult(r)"
                                    class="w-full text-left p-3 rounded-lg border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] hover:border-[#c6f24e] hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                    <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]" x-text="r.name"></p>
                                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]" x-show="r.brand" x-text="r.brand"></p>
                                    <p class="text-xs text-[#555b66] dark:text-[#a4abb6] mt-0.5">
                                        <span x-text="Math.round(r.kcal_per_100g)"></span> kcal &middot;
                                        P<span x-text="r.protein_per_100g"></span>/C<span x-text="r.carbs_per_100g"></span>/F<span x-text="r.fat_per_100g"></span>
                                        <span class="text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.day_plans.form.off.per_100g') }}</span>
                                    </p>
                                </button>
                            </template>
                        </div>

                        <div x-show="offDraft" x-cloak class="space-y-3 pt-3 border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                            <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]" x-text="offDraft?.name"></p>
                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]" x-show="offDraft?.brand" x-text="offDraft?.brand"></p>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.day_plans.form.off.portion_grams') }}</label>
                                <input type="number" min="1" max="2000" step="1" x-model.number="offGrams"
                                    class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0]">
                            </div>
                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                <span x-text="offScaled.calories"></span> kcal &middot;
                                P <span x-text="offScaled.protein"></span>g &middot;
                                C <span x-text="offScaled.carbs"></span>g &middot;
                                F <span x-text="offScaled.fat"></span>g
                            </p>
                            <div class="flex gap-2">
                                <button type="button" @click="offDraft = null" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] text-[#555b66] dark:text-[#a4abb6] text-sm font-semibold rounded-lg hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">{{ __('coach.day_plans.form.cancel') }}</button>
                                <button type="button" @click="confirmOff()" :disabled="!offFormReady()" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">{{ __('coach.day_plans.form.add_to_section') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- Macros only tab -->
                    <div x-show="picker.tab === 'macros'" class="space-y-3">
                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.day_plans.form.kcal') }}</label>
                                <input type="number" min="0" x-model.number="macrosDraft.calories" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#1456f0]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">P</label>
                                <input type="number" step="0.1" min="0" x-model.number="macrosDraft.protein" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#1456f0]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">C</label>
                                <input type="number" step="0.1" min="0" x-model.number="macrosDraft.carbs" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#1456f0]">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">F</label>
                                <input type="number" step="0.1" min="0" x-model.number="macrosDraft.fat" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-[#1456f0]">
                            </div>
                        </div>
                        <button type="button" @click="confirmMacros()" class="w-full inline-flex items-center justify-center px-3 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">{{ __('coach.day_plans.form.add_to_section') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('coach.clients.nutrition', $client) }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                {{ __('coach.day_plans.form.cancel') }}
            </a>
            @if(isset($dayPlan))
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                    {{ __('coach.day_plans.form.update') }}
                </button>
            @else
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                    {{ __('coach.day_plans.form.create') }}
                </button>
            @endif
        </div>
    </form>

    @if(isset($dayPlan))
        <form method="POST" action="{{ route('coach.clients.day-plans.destroy', [$client, $dayPlan]) }}"
            onsubmit="return confirm('{{ __('coach.day_plans.form.archive_confirm') }}');"
            class="mt-6 bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
            @csrf
            @method('DELETE')
            <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.day_plans.form.archive_heading') }}</h3>
            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.day_plans.form.archive_description') }}</p>
            <button type="submit" class="mt-4 inline-flex items-center px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-400 text-sm font-semibold rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                {{ __('coach.day_plans.form.archive') }}
            </button>
        </form>
    @endif
</div>

@push('scripts')
<script>
    function dayPlanBuilder(availableMeals, existingItems, initialSections, foodSearchUrl, customMacrosDefault) {
        const makeKey = () => 'k' + Math.random().toString(36).slice(2) + Date.now().toString(36);
        return {
            availableMeals,
            foodSearchUrl,
            customMacrosDefault,
            sections: initialSections.map(label => ({ _key: makeKey(), label, editing: false })),
            items: existingItems.map(it => {
                // Map each existing item to its initial section by matching meal_type label.
                const sectionKey = null; // resolved in init()
                return { ...it, _key: makeKey(), _sectionKey: sectionKey };
            }),
            addingSection: false,
            newSectionLabel: '',
            picker: { open: false, sectionKey: null, label: null, search: '', tab: 'library' },
            // Library draft state
            libraryDraft: null,
            libraryPortion: 1,
            // Custom draft state
            customDraft: { name: '', calories: 0, protein: 0, carbs: 0, fat: 0 },
            // OFF state
            offQuery: '',
            offResults: [],
            offSearching: false,
            offSearched: false,
            offDraft: null,
            offGrams: 100,
            // Macros state
            macrosDraft: { calories: 0, protein: 0, carbs: 0, fat: 0 },

            init() {
                // Resolve each existing item's section by label match.
                this.items.forEach(item => {
                    const match = this.sections.find(s => s.label === item.meal_type);
                    if (match) {
                        item._sectionKey = match._key;
                    } else {
                        // unknown label — append a new section so it isn't lost
                        const s = { _key: makeKey(), label: item.meal_type, editing: false };
                        this.sections.push(s);
                        item._sectionKey = s._key;
                    }
                });
            },

            get totals() {
                return this.items.reduce((acc, it) => {
                    acc.calories += Number(it.calories) || 0;
                    acc.protein += Number(it.protein) || 0;
                    acc.carbs += Number(it.carbs) || 0;
                    acc.fat += Number(it.fat) || 0;
                    return acc;
                }, { calories: 0, protein: 0, carbs: 0, fat: 0 });
            },

            itemsForSection(sectionKey) {
                return this.items.filter(i => i._sectionKey === sectionKey);
            },

            confirmAddSection() {
                const label = (this.newSectionLabel || '').trim();
                if (!label) {
                    this.cancelAddSection();
                    return;
                }
                this.sections.push({ _key: makeKey(), label, editing: false });
                this.newSectionLabel = '';
                this.addingSection = false;
            },
            cancelAddSection() {
                this.newSectionLabel = '';
                this.addingSection = false;
            },
            startRenameSection(idx) {
                this.sections.forEach(s => s.editing = false);
                this.sections[idx].editing = true;
            },
            finishRenameSection(idx) {
                const label = (this.sections[idx].label || '').trim();
                if (!label) {
                    this.sections[idx].label = '{{ __('coach.day_plans.form.untitled_section') }}';
                }
                this.sections[idx].editing = false;
            },
            removeSection(idx) {
                if (this.itemsForSection(this.sections[idx]._key).length > 0) return;
                this.sections.splice(idx, 1);
            },

            openPicker(sectionKey) {
                const section = this.sections.find(s => s._key === sectionKey);
                this.picker = { open: true, sectionKey, label: section?.label ?? '', search: '', tab: 'library' };
                this.libraryDraft = null;
                this.libraryPortion = 1;
                this.customDraft = { name: '', calories: 0, protein: 0, carbs: 0, fat: 0 };
                this.offQuery = '';
                this.offResults = [];
                this.offSearched = false;
                this.offDraft = null;
                this.offGrams = 100;
                this.macrosDraft = { calories: 0, protein: 0, carbs: 0, fat: 0 };
            },
            closePicker() {
                this.picker.open = false;
            },

            filteredMeals() {
                const q = (this.picker.search || '').toLowerCase().trim();
                if (!q) return this.availableMeals;
                return this.availableMeals.filter(m => m.name.toLowerCase().includes(q));
            },

            selectLibraryMeal(meal) {
                this.libraryDraft = meal;
                this.libraryPortion = 1;
            },
            confirmLibrary() {
                if (!this.libraryDraft) return;
                const p = Number(this.libraryPortion) || 1;
                const round1 = (n) => Math.round(n * 10) / 10;
                const sectionLabel = this.sections.find(s => s._key === this.picker.sectionKey)?.label ?? '';
                const suffix = p === 1 ? '' : ' (×' + (Math.round(p * 100) / 100) + ')';
                this.items.push({
                    _key: makeKey(),
                    _sectionKey: this.picker.sectionKey,
                    source: 'library',
                    meal_id: this.libraryDraft.id,
                    off_code: null,
                    meal_type: sectionLabel,
                    name: this.libraryDraft.name + suffix,
                    calories: Math.round(Number(this.libraryDraft.calories) * p),
                    protein: round1(Number(this.libraryDraft.protein) * p),
                    carbs: round1(Number(this.libraryDraft.carbs) * p),
                    fat: round1(Number(this.libraryDraft.fat) * p),
                    portion_grams: null,
                });
                this.closePicker();
            },

            confirmCustom() {
                const name = (this.customDraft.name || '').trim();
                if (!name) return;
                const sectionLabel = this.sections.find(s => s._key === this.picker.sectionKey)?.label ?? '';
                this.items.push({
                    _key: makeKey(),
                    _sectionKey: this.picker.sectionKey,
                    source: 'custom',
                    meal_id: null,
                    off_code: null,
                    meal_type: sectionLabel,
                    name,
                    calories: Math.max(0, Math.round(Number(this.customDraft.calories) || 0)),
                    protein: Math.max(0, Number(this.customDraft.protein) || 0),
                    carbs: Math.max(0, Number(this.customDraft.carbs) || 0),
                    fat: Math.max(0, Number(this.customDraft.fat) || 0),
                    portion_grams: null,
                });
                this.closePicker();
            },

            async searchOff() {
                const q = (this.offQuery || '').trim();
                if (q.length < 2) {
                    this.offResults = [];
                    this.offSearched = false;
                    return;
                }
                this.offSearching = true;
                this.offSearched = false;
                try {
                    const res = await fetch(this.foodSearchUrl + '?q=' + encodeURIComponent(q), {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (!res.ok) {
                        this.offResults = [];
                        return;
                    }
                    const data = await res.json();
                    this.offResults = Array.isArray(data.results) ? data.results : [];
                } catch (_) {
                    this.offResults = [];
                } finally {
                    this.offSearching = false;
                    this.offSearched = true;
                }
            },
            selectOffResult(r) {
                this.offDraft = r;
                this.offGrams = 100;
            },
            get offScaled() {
                const r = this.offDraft;
                const grams = Number(this.offGrams);
                if (!r || isNaN(grams) || grams < 1) {
                    return { calories: 0, protein: 0, carbs: 0, fat: 0 };
                }
                const scale = grams / 100;
                const round1 = (n) => Math.round(n * 10) / 10;
                return {
                    calories: Math.round(Number(r.kcal_per_100g) * scale),
                    protein: round1(Number(r.protein_per_100g) * scale),
                    carbs: round1(Number(r.carbs_per_100g) * scale),
                    fat: round1(Number(r.fat_per_100g) * scale),
                };
            },
            offFormReady() {
                if (!this.offDraft) return false;
                const grams = Number(this.offGrams);
                if (isNaN(grams) || grams < 1 || grams > 2000) return false;
                return true;
            },
            confirmOff() {
                if (!this.offFormReady()) return;
                const sectionLabel = this.sections.find(s => s._key === this.picker.sectionKey)?.label ?? '';
                const r = this.offDraft;
                const scaled = this.offScaled;
                const display = r.brand ? r.name + ' (' + r.brand + ')' : r.name;
                this.items.push({
                    _key: makeKey(),
                    _sectionKey: this.picker.sectionKey,
                    source: 'off',
                    meal_id: null,
                    off_code: r.code,
                    meal_type: sectionLabel,
                    name: display,
                    calories: scaled.calories,
                    protein: scaled.protein,
                    carbs: scaled.carbs,
                    fat: scaled.fat,
                    portion_grams: Number(this.offGrams),
                });
                this.closePicker();
            },

            confirmMacros() {
                const sectionLabel = this.sections.find(s => s._key === this.picker.sectionKey)?.label ?? '';
                this.items.push({
                    _key: makeKey(),
                    _sectionKey: this.picker.sectionKey,
                    source: 'macros',
                    meal_id: null,
                    off_code: null,
                    meal_type: sectionLabel,
                    name: this.customMacrosDefault,
                    calories: Math.max(0, Math.round(Number(this.macrosDraft.calories) || 0)),
                    protein: Math.max(0, Number(this.macrosDraft.protein) || 0),
                    carbs: Math.max(0, Number(this.macrosDraft.carbs) || 0),
                    fat: Math.max(0, Number(this.macrosDraft.fat) || 0),
                    portion_grams: null,
                });
                this.closePicker();
            },

            removeItem(key) {
                this.items = this.items.filter(i => i._key !== key);
            },

            syncItemsBeforeSubmit(event) {
                const form = event.target;
                form.querySelectorAll('input[data-day-plan-item]').forEach(el => el.remove());

                // Re-sync meal_type from current section label (in case it was renamed)
                const sectionByKey = {};
                this.sections.forEach(s => { sectionByKey[s._key] = s.label; });

                // Build a payload skipping items whose section was deleted.
                let idx = 0;
                this.items.forEach((it) => {
                    const label = sectionByKey[it._sectionKey];
                    if (!label) return; // orphaned (section removed) — skip silently
                    const sortInSection = this.items.filter(i2 => i2._sectionKey === it._sectionKey).indexOf(it);
                    [
                        ['source', it.source],
                        ['meal_id', it.meal_id ?? ''],
                        ['off_code', it.off_code ?? ''],
                        ['meal_type', label],
                        ['name', it.name],
                        ['calories', it.calories ?? 0],
                        ['protein', it.protein ?? 0],
                        ['carbs', it.carbs ?? 0],
                        ['fat', it.fat ?? 0],
                        ['portion_grams', it.portion_grams ?? ''],
                        ['sort_order', sortInSection],
                    ].forEach(([k, v]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `items[${idx}][${k}]`;
                        input.value = v;
                        input.setAttribute('data-day-plan-item', '1');
                        form.appendChild(input);
                    });
                    idx++;
                });
            },
        };
    }
</script>
@endpush
