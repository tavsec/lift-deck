<x-layouts.coach>
    <x-slot:title>Programs</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Training Programs</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create and manage training programs for your clients</p>
            </div>
            <a href="{{ route('coach.programs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Program
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
            <form method="GET" action="{{ route('coach.programs.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search programs..." class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="sm:w-40">
                    <select name="type" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">All Types</option>
                        <option value="strength" {{ request('type') === 'strength' ? 'selected' : '' }}>Strength</option>
                        <option value="hypertrophy" {{ request('type') === 'hypertrophy' ? 'selected' : '' }}>Hypertrophy</option>
                        <option value="fat_loss" {{ request('type') === 'fat_loss' ? 'selected' : '' }}>Fat Loss</option>
                        <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>General</option>
                    </select>
                </div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="templates_only" value="1" {{ request('templates_only') ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Templates only</span>
                </label>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-medium text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Filter
                </button>
            </form>
        </div>

        <!-- Program Grid -->
        @if($programs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($programs as $program)
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow hover:shadow-md transition-shadow duration-200 overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $program->name }}</h3>
                                @if($program->is_template)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        Template
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                                    {{ ucfirst(str_replace('_', ' ', $program->type)) }}
                                </span>
                                @if($program->duration_weeks)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $program->duration_weeks }} weeks
                                    </span>
                                @endif
                            </div>
                            @if($program->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">{{ $program->description }}</p>
                            @endif
                            <div class="flex items-center text-xs text-gray-400 dark:text-gray-500 mb-4">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                {{ $program->workouts->count() }} workouts
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('coach.programs.show', $program) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    View
                                </a>
                                <a href="{{ route('coach.programs.edit', $program) }}" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-white dark:bg-gray-800 hover:bg-blue-50">
                                    Edit
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
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No programs yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first training program.</p>
                    <div class="mt-6">
                        <a href="{{ route('coach.programs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Program
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
