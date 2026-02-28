<x-layouts.coach>
    <x-slot:title>Exercise Library</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Exercise Library</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Browse and manage exercises for your programs</p>
            </div>
            <a href="{{ route('coach.exercises.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Exercise
            </a>
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

        <!-- Search & Filter -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
            <form method="GET" action="{{ route('coach.exercises.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search exercises..." class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="sm:w-48">
                    <select name="muscle_group" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Muscle Groups</option>
                        @foreach($muscleGroups as $group)
                            <option value="{{ $group }}" {{ request('muscle_group') === $group ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $group)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-medium text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Filter
                </button>
                @if(request('search') || request('muscle_group'))
                    <a href="{{ route('coach.exercises.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md font-medium text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-800 transition ease-in-out duration-150">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Exercise Grid -->
        @if($exercises->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($exercises as $exercise)
                    <a href="{{ route('coach.exercises.show', $exercise) }}" class="bg-white dark:bg-gray-900 rounded-lg shadow hover:shadow-md transition-shadow duration-200 overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $exercise->name }}</h3>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                                            {{ ucfirst(str_replace('_', ' ', $exercise->muscle_group)) }}
                                        </span>
                                    </p>
                                </div>
                                @if($exercise->isCustom())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Custom
                                    </span>
                                @endif
                            </div>
                            @if($exercise->description)
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $exercise->description }}</p>
                            @endif
                            @if($exercise->video_url)
                                <div class="mt-2 flex items-center text-xs text-gray-400 dark:text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Video available
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
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No exercises found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if(request('search') || request('muscle_group'))
                            Try adjusting your search or filters.
                        @else
                            Get started by adding your first exercise.
                        @endif
                    </p>
                    @if(!request('search') && !request('muscle_group'))
                        <div class="mt-6">
                            <a href="{{ route('coach.exercises.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Exercise
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
