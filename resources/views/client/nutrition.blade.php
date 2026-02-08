<x-layouts.client>
    <x-slot:title>Nutrition</x-slot:title>

    <div class="py-6 space-y-6" x-data="nutritionLogger()">
        <!-- Header with Date Navigation -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nutrition</h1>
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

        <!-- Macro Progress Bars -->
        <div class="bg-white rounded-lg shadow p-4 space-y-4">
            @if($macroGoal)
                @php
                    $macros = [
                        ['label' => 'Calories', 'current' => $totals['calories'], 'target' => $macroGoal->calories, 'unit' => 'kcal', 'color' => 'bg-blue-500'],
                        ['label' => 'Protein', 'current' => $totals['protein'], 'target' => $macroGoal->protein, 'unit' => 'g', 'color' => 'bg-green-500'],
                        ['label' => 'Carbs', 'current' => $totals['carbs'], 'target' => $macroGoal->carbs, 'unit' => 'g', 'color' => 'bg-yellow-500'],
                        ['label' => 'Fat', 'current' => $totals['fat'], 'target' => $macroGoal->fat, 'unit' => 'g', 'color' => 'bg-red-500'],
                    ];
                @endphp
                @foreach($macros as $macro)
                    @php
                        $pct = $macro['target'] > 0 ? min(100, ($macro['current'] / $macro['target']) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">{{ $macro['label'] }}</span>
                            <span class="text-gray-500">{{ number_format($macro['current'], $macro['unit'] === 'kcal' ? 0 : 1) }} / {{ number_format($macro['target'], $macro['unit'] === 'kcal' ? 0 : 1) }}{{ $macro['unit'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $macro['color'] }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-2">
                    <p class="text-sm text-gray-500">No macro goals set by your coach yet.</p>
                    @if($totals['calories'] > 0)
                        <p class="text-sm text-gray-700 mt-2">
                            Today: {{ $totals['calories'] }} kcal &middot;
                            P {{ number_format($totals['protein'], 1) }}g &middot;
                            C {{ number_format($totals['carbs'], 1) }}g &middot;
                            F {{ number_format($totals['fat'], 1) }}g
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Logged Meals -->
        @if($mealLogs->count() > 0)
            <div class="space-y-3">
                @foreach($mealLogs->groupBy('meal_type') as $type => $logs)
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-700">{{ $type }}</h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($logs as $log)
                                <div class="px-4 py-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $log->name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $log->calories }} kcal &middot;
                                            P {{ $log->protein }}g &middot;
                                            C {{ $log->carbs }}g &middot;
                                            F {{ $log->fat }}g
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('client.nutrition.destroy', $log) }}" onsubmit="return confirm('Remove this meal?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Add Meal Section -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <button @click="mode = 'library'" :class="mode === 'library' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 py-3 px-4 text-center text-sm font-medium border-b-2 transition-colors" type="button">
                        Library
                    </button>
                    <button @click="mode = 'custom'" :class="mode === 'custom' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-1 py-3 px-4 text-center text-sm font-medium border-b-2 transition-colors" type="button">
                        Custom
                    </button>
                </nav>
            </div>

            <div class="p-4">
                <!-- Library Mode -->
                <div x-show="mode === 'library'" class="space-y-4">
                    <div>
                        <input type="text" x-model="mealSearch" @input.debounce.300ms="searchMeals()" placeholder="Search meals..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div x-show="libraryMeals.length > 0" class="max-h-60 overflow-y-auto space-y-2">
                        <template x-for="meal in libraryMeals" :key="meal.id">
                            <button @click="selectLibraryMeal(meal)" type="button"
                                :class="selectedMeal && selectedMeal.id === meal.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50'"
                                class="w-full text-left p-3 rounded-md border transition-colors">
                                <p class="text-sm font-medium text-gray-900" x-text="meal.name"></p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <span x-text="meal.calories"></span> kcal &middot;
                                    P <span x-text="meal.protein"></span>g &middot;
                                    C <span x-text="meal.carbs"></span>g &middot;
                                    F <span x-text="meal.fat"></span>g
                                </p>
                            </button>
                        </template>
                    </div>

                    <div x-show="mealSearch && libraryMeals.length === 0 && !searching" class="text-center py-4">
                        <p class="text-sm text-gray-500">No meals found. Try a different search or add a custom meal.</p>
                    </div>

                    <div x-show="selectedMeal" class="pt-2 border-t border-gray-200">
                        <form method="POST" action="{{ route('client.nutrition.store') }}">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="meal_id" :value="selectedMeal?.id">
                            <input type="hidden" name="name" :value="selectedMeal?.name">
                            <input type="hidden" name="calories" :value="selectedMeal?.calories">
                            <input type="hidden" name="protein" :value="selectedMeal?.protein">
                            <input type="hidden" name="carbs" :value="selectedMeal?.carbs">
                            <input type="hidden" name="fat" :value="selectedMeal?.fat">

                            <!-- Meal Type Selector -->
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meal Type</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="type in mealTypes" :key="type">
                                        <button type="button" @click="mealType = type; customMealType = ''"
                                            :class="mealType === type && !customMealType ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                            class="px-3 py-1.5 text-sm font-medium border rounded-md transition-colors" x-text="type">
                                        </button>
                                    </template>
                                </div>
                                <input type="text" x-model="customMealType" @input="if(customMealType) mealType = ''" placeholder="Or type custom..."
                                    class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <input type="hidden" name="meal_type" :value="customMealType || mealType">
                            </div>

                            <button type="submit" :disabled="!(mealType || customMealType)"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                Log Meal
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Custom Mode -->
                <div x-show="mode === 'custom'">
                    <form method="POST" action="{{ route('client.nutrition.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="e.g., Grilled chicken with rice">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meal Type Selector -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meal Type <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="type in mealTypes" :key="type">
                                    <button type="button" @click="customFormMealType = type; customFormCustomType = ''"
                                        :class="customFormMealType === type && !customFormCustomType ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                        class="px-3 py-1.5 text-sm font-medium border rounded-md transition-colors" x-text="type">
                                    </button>
                                </template>
                            </div>
                            <input type="text" x-model="customFormCustomType" @input="if(customFormCustomType) customFormMealType = ''" placeholder="Or type custom..."
                                class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <input type="hidden" name="meal_type" :value="customFormCustomType || customFormMealType">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Calories <span class="text-red-500">*</span></label>
                            <input type="number" name="calories" required min="0" value="{{ old('calories') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="0">
                            @error('calories')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Protein (g) <span class="text-red-500">*</span></label>
                                <input type="number" name="protein" required min="0" step="0.1" value="{{ old('protein') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Carbs (g) <span class="text-red-500">*</span></label>
                                <input type="number" name="carbs" required min="0" step="0.1" value="{{ old('carbs') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fat (g) <span class="text-red-500">*</span></label>
                                <input type="number" name="fat" required min="0" step="0.1" value="{{ old('fat') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    placeholder="0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Optional notes...">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" :disabled="!(customFormMealType || customFormCustomType)"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                            Log Meal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function nutritionLogger() {
            const currentDate = '{{ $date }}';
            const today = '{{ now()->format("Y-m-d") }}';
            const baseUrl = '{{ route("client.nutrition") }}';
            const mealsUrl = '{{ route("client.nutrition.meals") }}';

            function shiftDate(dateStr, days) {
                const d = new Date(dateStr + 'T00:00:00');
                d.setDate(d.getDate() + days);
                return d.toISOString().split('T')[0];
            }

            return {
                currentDate: currentDate,
                today: today,
                mode: 'library',
                mealSearch: '',
                libraryMeals: [],
                selectedMeal: null,
                searching: false,
                mealType: 'Breakfast',
                customMealType: '',
                customFormMealType: 'Breakfast',
                customFormCustomType: '',
                mealTypes: ['Breakfast', 'Lunch', 'Dinner', 'Snack'],

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
                },

                async searchMeals() {
                    if (!this.mealSearch) {
                        this.libraryMeals = [];
                        this.loadAllMeals();
                        return;
                    }
                    this.searching = true;
                    try {
                        const response = await fetch(mealsUrl + '?search=' + encodeURIComponent(this.mealSearch));
                        this.libraryMeals = await response.json();
                    } finally {
                        this.searching = false;
                    }
                },

                selectLibraryMeal(meal) {
                    this.selectedMeal = this.selectedMeal?.id === meal.id ? null : meal;
                },

                loadAllMeals() {
                    fetch(mealsUrl)
                        .then(r => r.json())
                        .then(meals => { this.libraryMeals = meals; });
                },

                init() {
                    this.loadAllMeals();
                }
            };
        }
    </script>
    @endpush
</x-layouts.client>
