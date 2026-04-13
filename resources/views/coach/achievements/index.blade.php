<x-layouts.coach>
    <x-slot:title>{{ __('coach.achievements.index.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.achievements.index.heading') }}</h1>
                <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.achievements.index.subtitle') }}</p>
            </div>
            <a href="{{ route('coach.achievements.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('coach.achievements.index.create') }}
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

        @if(session('error'))
            <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Achievement Grid -->
        @if($achievements->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($achievements as $achievement)
                    @if(is_null($achievement->coach_id))
                        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden">
                    @else
                        <a href="{{ route('coach.achievements.edit', $achievement) }}" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                    @endif
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">{{ $achievement->name }}</h3>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        @if(is_null($achievement->coach_id))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">{{ __('coach.achievements.index.system') }}</span>
                                        @endif
                                        @if($achievement->type === 'automatic')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">{{ __('coach.achievements.index.automatic') }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-[#45515e] dark:text-gray-300">{{ __('coach.achievements.index.manual') }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($achievement->description)
                                    <p class="mt-2 text-sm text-[#8e8e93] dark:text-gray-400 line-clamp-2">{{ $achievement->description }}</p>
                                @endif

                                @if($achievement->type === 'automatic' && $achievement->condition_type)
                                    <div class="mt-3 text-xs text-[#8e8e93] dark:text-gray-400">
                                        <span class="font-medium text-[#45515e] dark:text-gray-300">{{ __('coach.achievements.index.condition') }}</span>
                                        {{ ucwords(str_replace('_', ' ', $achievement->condition_type)) }}
                                        @if($achievement->condition_value)
                                            &rarr; {{ $achievement->condition_value }}
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-3 flex gap-3 text-xs text-[#8e8e93] dark:text-gray-400">
                                    @if($achievement->xp_reward)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="font-medium text-yellow-600 dark:text-yellow-400">+{{ $achievement->xp_reward }} XP</span>
                                        </span>
                                    @endif
                                    @if($achievement->points_reward)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="font-medium text-purple-600 dark:text-purple-400">+{{ $achievement->points_reward }} pts</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                    @if(is_null($achievement->coach_id))
                        </div>
                    @else
                        </a>
                    @endif
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card">
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.achievements.index.no_achievements') }}</h3>
                    <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.achievements.index.no_achievements_description') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('coach.achievements.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('coach.achievements.index.create') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
