<x-layouts.coach>
    <x-slot:title>Achievements</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Achievement Library</h1>
                <p class="mt-1 text-sm text-gray-500">Manage achievements to reward your clients' progress.</p>
            </div>
            <a href="{{ route('coach.achievements.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Achievement
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

        @if(session('error'))
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Achievement Grid -->
        @if($achievements->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($achievements as $achievement)
                    @if(is_null($achievement->coach_id))
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                    @else
                        <a href="{{ route('coach.achievements.edit', $achievement) }}" class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 overflow-hidden">
                    @endif
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $achievement->name }}</h3>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        @if(is_null($achievement->coach_id))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">System</span>
                                        @endif
                                        @if($achievement->type === 'automatic')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Automatic</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Manual</span>
                                        @endif
                                    </div>
                                </div>

                                @if($achievement->description)
                                    <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $achievement->description }}</p>
                                @endif

                                @if($achievement->type === 'automatic' && $achievement->condition_type)
                                    <div class="mt-3 text-xs text-gray-400">
                                        <span class="font-medium text-gray-600">Condition:</span>
                                        {{ ucwords(str_replace('_', ' ', $achievement->condition_type)) }}
                                        @if($achievement->condition_value)
                                            &rarr; {{ $achievement->condition_value }}
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-3 flex gap-3 text-xs text-gray-500">
                                    @if($achievement->xp_reward)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="font-medium text-yellow-600">+{{ $achievement->xp_reward }} XP</span>
                                        </span>
                                    @endif
                                    @if($achievement->points_reward)
                                        <span class="inline-flex items-center gap-1">
                                            <span class="font-medium text-indigo-600">+{{ $achievement->points_reward }} pts</span>
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
            <div class="bg-white rounded-lg shadow">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No achievements yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first achievement.</p>
                    <div class="mt-6">
                        <a href="{{ route('coach.achievements.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Achievement
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
