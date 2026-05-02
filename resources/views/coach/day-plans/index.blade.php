<x-layouts.coach>
    <x-slot:title>{{ __('coach.day_plans.index.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.day_plans.index.heading') }}</h1>
                <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.day_plans.index.subtitle') }}</p>
            </div>
            <a href="{{ route('coach.day-plans.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('coach.day_plans.index.add') }}
            </a>
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

        <!-- Day Plan Grid -->
        @if($dayPlans->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($dayPlans as $dayPlan)
                    <a href="{{ route('coach.day-plans.edit', $dayPlan) }}" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                        <div class="p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100 truncate">{{ $dayPlan->name }}</h3>
                                    <p class="mt-0.5 text-xs text-[#8e8e93] dark:text-gray-500">
                                        {{ trans_choice('coach.day_plans.index.meal_count', $dayPlan->items->count(), ['count' => $dayPlan->items->count()]) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-baseline gap-1">
                                <span class="text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ $dayPlan->total_calories }}</span>
                                <span class="text-sm text-[#8e8e93] dark:text-gray-400">kcal</span>
                            </div>
                            <div class="mt-2 flex gap-2 flex-wrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                    P {{ number_format($dayPlan->total_protein, 1) }}g
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                    C {{ number_format($dayPlan->total_carbs, 1) }}g
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                    F {{ number_format($dayPlan->total_fat, 1) }}g
                                </span>
                            </div>
                            @if($dayPlan->description)
                                <p class="mt-2 text-sm text-[#8e8e93] dark:text-gray-400 line-clamp-2">{{ $dayPlan->description }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card">
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.day_plans.index.no_plans') }}</h3>
                    <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">
                        {{ __('coach.day_plans.index.no_plans_empty') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('coach.day-plans.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('coach.day_plans.index.add') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
