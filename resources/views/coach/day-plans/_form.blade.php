@php
    $existingItems = isset($dayPlan)
        ? $dayPlan->items->map(fn ($item) => [
            'meal_id' => $item->meal_id,
            'meal_type' => $item->meal_type,
            'sort_order' => $item->sort_order,
            'meal' => [
                'id' => $item->meal->id,
                'name' => $item->meal->name,
                'calories' => (int) $item->meal->calories,
                'protein' => (float) $item->meal->protein,
                'carbs' => (float) $item->meal->carbs,
                'fat' => (float) $item->meal->fat,
            ],
        ])->values()
        : collect();
@endphp

<div x-data="dayPlanBuilder({{ Js::from($availableMeals) }}, {{ Js::from($existingItems) }})">
    <form method="POST" action="{{ $action }}" class="space-y-6" @submit="syncItemsBeforeSubmit($event)">
        @csrf
        @if(($method ?? 'POST') === 'PUT')
            @method('PUT')
        @endif

        <input type="hidden" name="items_json" :value="JSON.stringify(items)">

        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.day_plans.form.name') }} <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $dayPlan->name ?? '') }}" required
                    class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('name') border-red-300 dark:border-red-700 @enderror"
                    placeholder="{{ __('coach.day_plans.form.name_placeholder') }}">
                @error('name')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.day_plans.form.description') }}</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150"
                    placeholder="{{ __('coach.day_plans.form.description_placeholder') }}">{{ old('description', $dayPlan->description ?? '') }}</textarea>
            </div>

            <!-- Running totals -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-gray-50 dark:bg-gray-950 rounded-xl border border-gray-100 dark:border-gray-800">
                <div>
                    <p class="text-xs text-[#8e8e93] dark:text-gray-500 uppercase">{{ __('coach.day_plans.form.total_calories') }}</p>
                    <p class="text-lg font-semibold text-[#222222] dark:text-gray-100" x-text="totals.calories + ' kcal'"></p>
                </div>
                <div>
                    <p class="text-xs text-[#8e8e93] dark:text-gray-500 uppercase">{{ __('coach.day_plans.form.total_protein') }}</p>
                    <p class="text-lg font-semibold text-[#222222] dark:text-gray-100" x-text="totals.protein.toFixed(1) + 'g'"></p>
                </div>
                <div>
                    <p class="text-xs text-[#8e8e93] dark:text-gray-500 uppercase">{{ __('coach.day_plans.form.total_carbs') }}</p>
                    <p class="text-lg font-semibold text-[#222222] dark:text-gray-100" x-text="totals.carbs.toFixed(1) + 'g'"></p>
                </div>
                <div>
                    <p class="text-xs text-[#8e8e93] dark:text-gray-500 uppercase">{{ __('coach.day_plans.form.total_fat') }}</p>
                    <p class="text-lg font-semibold text-[#222222] dark:text-gray-100" x-text="totals.fat.toFixed(1) + 'g'"></p>
                </div>
            </div>
        </div>

        <!-- Meal sections -->
        <template x-for="section in mealSections" :key="section.type">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100" x-text="section.label"></h3>
                    <button type="button" @click="openPicker(section.type)"
                        class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('coach.day_plans.form.add_meal') }}
                    </button>
                </div>

                <div x-show="itemsForType(section.type).length === 0" class="text-center py-6 border border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.day_plans.form.empty_section') }}</p>
                </div>

                <div class="space-y-2" x-show="itemsForType(section.type).length > 0">
                    <template x-for="(item, idx) in itemsForType(section.type)" :key="item._key">
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-950">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#222222] dark:text-gray-100" x-text="item.meal.name"></p>
                                <p class="text-xs text-[#8e8e93] dark:text-gray-500 mt-0.5">
                                    <span x-text="item.meal.calories"></span> kcal &middot;
                                    P <span x-text="item.meal.protein"></span>g &middot;
                                    C <span x-text="item.meal.carbs"></span>g &middot;
                                    F <span x-text="item.meal.fat"></span>g
                                </p>
                            </div>
                            <button type="button" @click="removeItem(item._key)"
                                class="ml-3 p-1.5 text-[#8e8e93] dark:text-gray-500 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Picker modal -->
        <div x-show="picker.open" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 px-4"
            @keydown.escape.window="closePicker()">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-xl max-w-lg w-full max-h-[85vh] flex flex-col" @click.outside="closePicker()">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">
                        {{ __('coach.day_plans.form.picker.heading') }}
                        <span class="text-[#8e8e93] dark:text-gray-500" x-text="' — ' + (picker.label ?? '')"></span>
                    </h3>
                    <button type="button" @click="closePicker()" class="p-1 text-[#8e8e93] dark:text-gray-500 hover:text-[#45515e] dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800">
                    <input type="text" x-model="picker.search"
                        placeholder="{{ __('coach.day_plans.form.picker.search_placeholder') }}"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                </div>
                <div class="flex-1 overflow-y-auto p-3">
                    <template x-if="filteredMeals().length === 0">
                        <p class="text-center text-sm text-[#8e8e93] dark:text-gray-500 py-6">{{ __('coach.day_plans.form.picker.no_meals') }}</p>
                    </template>
                    <template x-for="meal in filteredMeals()" :key="meal.id">
                        <button type="button" @click="addItem(meal)"
                            class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-800 hover:border-[#1456f0] hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors mb-2">
                            <p class="text-sm font-medium text-[#222222] dark:text-gray-100" x-text="meal.name"></p>
                            <p class="text-xs text-[#8e8e93] dark:text-gray-500 mt-0.5">
                                <span x-text="meal.calories"></span> kcal &middot;
                                P <span x-text="meal.protein"></span>g &middot;
                                C <span x-text="meal.carbs"></span>g &middot;
                                F <span x-text="meal.fat"></span>g
                            </p>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('coach.day-plans.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                {{ __('coach.day_plans.form.cancel') }}
            </a>
            @if(isset($dayPlan))
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    {{ __('coach.day_plans.form.update') }}
                </button>
            @else
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    {{ __('coach.day_plans.form.create') }}
                </button>
            @endif
        </div>
    </form>

    @if(isset($dayPlan))
        <form method="POST" action="{{ route('coach.day-plans.destroy', $dayPlan) }}"
            onsubmit="return confirm('{{ __('coach.day_plans.form.archive_confirm') }}');"
            class="mt-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            @csrf
            @method('DELETE')
            <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.day_plans.form.archive_heading') }}</h3>
            <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.day_plans.form.archive_description') }}</p>
            <button type="submit" class="mt-4 inline-flex items-center px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-400 text-sm font-semibold rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                {{ __('coach.day_plans.form.archive') }}
            </button>
        </form>
    @endif
</div>

@push('scripts')
<script>
    function dayPlanBuilder(availableMeals, existingItems) {
        return {
            availableMeals,
            mealSections: [
                { type: 'Breakfast', label: '{{ __('coach.day_plans.meal_types.breakfast') }}' },
                { type: 'Lunch', label: '{{ __('coach.day_plans.meal_types.lunch') }}' },
                { type: 'Dinner', label: '{{ __('coach.day_plans.meal_types.dinner') }}' },
                { type: 'Snack', label: '{{ __('coach.day_plans.meal_types.snack') }}' },
            ],
            items: existingItems.map((item, i) => ({ ...item, _key: 'i' + i + '_' + Math.random().toString(36).slice(2) })),
            picker: { open: false, type: null, label: null, search: '' },
            nextKey: 0,

            get totals() {
                return this.items.reduce((acc, it) => {
                    acc.calories += Number(it.meal.calories) || 0;
                    acc.protein += Number(it.meal.protein) || 0;
                    acc.carbs += Number(it.meal.carbs) || 0;
                    acc.fat += Number(it.meal.fat) || 0;
                    return acc;
                }, { calories: 0, protein: 0, carbs: 0, fat: 0 });
            },

            itemsForType(type) {
                return this.items.filter(i => i.meal_type === type);
            },

            openPicker(type) {
                const section = this.mealSections.find(s => s.type === type);
                this.picker = { open: true, type, label: section?.label ?? type, search: '' };
            },

            closePicker() {
                this.picker = { open: false, type: null, label: null, search: '' };
            },

            filteredMeals() {
                const q = (this.picker.search || '').toLowerCase().trim();
                if (!q) return this.availableMeals;
                return this.availableMeals.filter(m => m.name.toLowerCase().includes(q));
            },

            addItem(meal) {
                const sortOrder = this.itemsForType(this.picker.type).length;
                this.items.push({
                    meal_id: meal.id,
                    meal_type: this.picker.type,
                    sort_order: sortOrder,
                    meal: { ...meal },
                    _key: 'k' + (this.nextKey++) + '_' + Math.random().toString(36).slice(2),
                });
                this.closePicker();
            },

            removeItem(key) {
                this.items = this.items.filter(i => i._key !== key);
            },

            syncItemsBeforeSubmit(event) {
                // Build hidden inputs reflecting items array so PHP receives `items[i][meal_id]`, etc.
                const form = event.target;
                form.querySelectorAll('input[data-day-plan-item]').forEach(el => el.remove());
                this.items.forEach((it, idx) => {
                    [
                        ['meal_id', it.meal_id],
                        ['meal_type', it.meal_type],
                        ['sort_order', idx],
                    ].forEach(([k, v]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `items[${idx}][${k}]`;
                        input.value = v;
                        input.setAttribute('data-day-plan-item', '1');
                        form.appendChild(input);
                    });
                });
            },
        };
    }
</script>
@endpush
