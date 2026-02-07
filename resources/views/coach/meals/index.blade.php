<x-layouts.coach>
    <x-slot:title>Meal Library</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Meal Library</h1>
                <p class="mt-1 text-sm text-gray-500">Browse and manage meals for your clients</p>
            </div>
            <a href="{{ route('coach.meals.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Meal
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

        <!-- Search -->
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" action="{{ route('coach.meals.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search meals..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-medium text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('coach.meals.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition ease-in-out duration-150">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Meal Grid -->
        @if($meals->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($meals as $meal)
                    <a href="{{ route('coach.meals.edit', $meal) }}" class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">{{ $meal->name }}</h3>
                                </div>
                            </div>
                            <div class="mt-3 flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-gray-900">{{ $meal->calories }}</span>
                                <span class="text-sm text-gray-500">kcal</span>
                            </div>
                            <div class="mt-2 flex gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    P {{ $meal->protein }}g
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    C {{ $meal->carbs }}g
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    F {{ $meal->fat }}g
                                </span>
                            </div>
                            @if($meal->description)
                                <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $meal->description }}</p>
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
            <div class="bg-white rounded-lg shadow">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No meals found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request('search'))
                            Try adjusting your search.
                        @else
                            Get started by adding your first meal.
                        @endif
                    </p>
                    @if(!request('search'))
                        <div class="mt-6">
                            <a href="{{ route('coach.meals.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Meal
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
