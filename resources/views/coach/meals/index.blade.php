<x-layouts.coach>
    <x-slot:title>{{ __('coach.meals.index.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.meals.index.heading') }}</h1>
                <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.meals.index.subtitle') }}</p>
            </div>
            <a href="{{ route('coach.meals.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('coach.meals.index.add') }}
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

        <!-- Search -->
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-4">
            <form method="GET" action="{{ route('coach.meals.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('coach.meals.index.search_placeholder') }}" class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                    {{ __('coach.meals.index.search') }}
                </button>
                @if(request('search'))
                    <a href="{{ route('coach.meals.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                        {{ __('coach.meals.index.clear') }}
                    </a>
                @endif
            </form>
        </div>

        <!-- Meal Grid -->
        @if($meals->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($meals as $meal)
                    <a href="{{ route('coach.meals.edit', $meal) }}" class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] overflow-hidden hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                        <div class="p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5] truncate">{{ $meal->name }}</h3>
                                </div>
                            </div>
                            <div class="mt-3 flex items-baseline gap-1">
                                <span class="font-mono text-2xl font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ $meal->calories }}</span>
                                <span class="text-sm text-[#8c93a0] dark:text-[#6b7280]">kcal</span>
                            </div>
                            <div class="mt-2 flex gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                    P {{ $meal->protein }}g
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                    C {{ $meal->carbs }}g
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                    F {{ $meal->fat }}g
                                </span>
                            </div>
                            @if($meal->description)
                                <p class="mt-2 text-sm text-[#8c93a0] dark:text-[#6b7280] line-clamp-2">{{ $meal->description }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if($meals->hasPages())
                <div class="mt-6">
                    {{ $meals->links() }}
                </div>
            @endif
        @else
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)]">
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-[#f3f5f7] dark:bg-[#1d2027] flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8c93a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.meals.index.no_meals') }}</h3>
                    <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">
                        @if(request('search'))
                            {{ __('coach.meals.index.no_meals_search') }}
                        @else
                            {{ __('coach.meals.index.no_meals_empty') }}
                        @endif
                    </p>
                    @if(!request('search'))
                        <div class="mt-6">
                            <a href="{{ route('coach.meals.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ __('coach.meals.index.add') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
