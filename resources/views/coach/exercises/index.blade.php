<x-layouts.coach>
    <x-slot:title>{{ __('coach.exercises.index.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.exercises.index.heading') }}</h1>
                <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.exercises.index.subtitle') }}</p>
            </div>
            <a href="{{ route('coach.exercises.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('coach.exercises.index.add') }}
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

        <!-- Search & Filter -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-4">
            <form method="GET" action="{{ route('coach.exercises.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('coach.exercises.index.search_placeholder') }}" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                </div>
                <div class="sm:w-48">
                    <select name="muscle_group" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                        <option value="">{{ __('coach.exercises.index.all_muscle_groups') }}</option>
                        @foreach($muscleGroups as $group)
                            <option value="{{ $group }}" {{ request('muscle_group') === $group ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $group)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('coach.exercises.index.filter') }}
                </button>
                @if(request('search') || request('muscle_group'))
                    <a href="{{ route('coach.exercises.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        {{ __('coach.exercises.index.clear') }}
                    </a>
                @endif
            </form>
        </div>

        <!-- Exercise Grid -->
        @if($exercises->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($exercises as $exercise)
                    <a href="{{ route('coach.exercises.show', $exercise) }}" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                        <div class="p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100 truncate">{{ $exercise->name }}</h3>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-[#45515e] dark:text-gray-300">
                                            {{ ucfirst(str_replace('_', ' ', $exercise->muscle_group)) }}
                                        </span>
                                    </p>
                                </div>
                                @if($exercise->isCustom())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                        {{ __('coach.exercises.index.custom') }}
                                    </span>
                                @endif
                            </div>
                            @if($exercise->description)
                                <p class="mt-2 text-sm text-[#8e8e93] dark:text-gray-400 line-clamp-2">{{ $exercise->description }}</p>
                            @endif
                            @if($exercise->video_url)
                                <div class="mt-2 flex items-center text-xs text-[#8e8e93] dark:text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('coach.exercises.index.video_available') }}
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if($exercises->hasPages())
                <div class="mt-6">
                    {{ $exercises->links() }}
                </div>
            @endif
        @else
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card">
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.exercises.index.no_exercises') }}</h3>
                    <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">
                        @if(request('search') || request('muscle_group'))
                            {{ __('coach.exercises.index.no_exercises_search') }}
                        @else
                            {{ __('coach.exercises.index.no_exercises_empty') }}
                        @endif
                    </p>
                    @if(!request('search') && !request('muscle_group'))
                        <div class="mt-6">
                            <a href="{{ route('coach.exercises.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ __('coach.exercises.index.add') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
