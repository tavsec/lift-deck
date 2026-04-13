<x-layouts.coach>
    <x-slot:title>{{ __('coach.programs.index.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.programs.index.heading') }}</h1>
                <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-400">{{ __('coach.programs.index.subtitle') }}</p>
            </div>
            <a href="{{ route('coach.programs.create') }}" class="inline-flex items-center px-4 py-2.5 bg-[#181e25] dark:bg-gray-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-[#2d3748] dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('coach.programs.index.create') }}
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
                        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search & Filter -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-4">
            <form method="GET" action="{{ route('coach.programs.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('coach.programs.index.search_placeholder') }}" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                </div>
                <div class="sm:w-40">
                    <select name="type" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                        <option value="">{{ __('coach.programs.index.all_types') }}</option>
                        <option value="strength" {{ request('type') === 'strength' ? 'selected' : '' }}>{{ __('coach.programs.index.strength') }}</option>
                        <option value="hypertrophy" {{ request('type') === 'hypertrophy' ? 'selected' : '' }}>{{ __('coach.programs.index.hypertrophy') }}</option>
                        <option value="fat_loss" {{ request('type') === 'fat_loss' ? 'selected' : '' }}>{{ __('coach.programs.index.fat_loss') }}</option>
                        <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>{{ __('coach.programs.index.general') }}</option>
                    </select>
                </div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="templates_only" value="1" {{ request('templates_only') ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-[#1456f0]/20" style="color: var(--color-primary)">
                    <span class="ml-2 text-sm text-[#45515e] dark:text-gray-400">{{ __('coach.programs.index.templates_only') }}</span>
                </label>
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg font-medium text-sm text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none transition ease-in-out duration-150">
                    {{ __('coach.programs.index.filter') }}
                </button>
            </form>
        </div>

        <!-- Program Grid -->
        @if($programs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($programs as $program)
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-base font-semibold text-[#222222] dark:text-gray-100">{{ $program->name }}</h3>
                                @if($program->is_template)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">
                                        {{ __('coach.programs.index.template') }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-[#45515e] dark:text-gray-300">
                                    {{ ucfirst(str_replace('_', ' ', $program->type)) }}
                                </span>
                                @if($program->duration_weeks)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                        {{ __('coach.programs.index.n_weeks', ['n' => $program->duration_weeks]) }}
                                    </span>
                                @endif
                            </div>
                            @if($program->description)
                                <p class="text-sm text-[#8e8e93] dark:text-gray-400 line-clamp-2 mb-3">{{ $program->description }}</p>
                            @endif
                            <div class="flex items-center text-xs text-[#8e8e93] dark:text-gray-500 mb-4">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                {{ __('coach.programs.index.n_workouts', ['n' => $program->workouts->count()]) }}
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('coach.programs.show', $program) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-[#45515e] dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/70 transition-colors">
                                    {{ __('coach.programs.index.view') }}
                                </a>
                                <a href="{{ route('coach.programs.edit', $program) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 border rounded-lg text-sm font-medium transition-colors" style="border-color: var(--color-primary); color: var(--color-primary)">
                                    {{ __('coach.programs.index.edit') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($programs->hasPages())
                <div class="mt-6">
                    {{ $programs->links() }}
                </div>
            @endif
        @else
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card">
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.programs.index.no_programs') }}</h3>
                    <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-400">{{ __('coach.programs.index.no_programs_description') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('coach.programs.create') }}" class="inline-flex items-center px-4 py-2.5 bg-[#181e25] dark:bg-gray-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-[#2d3748] dark:hover:bg-gray-600 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('coach.programs.index.create') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
