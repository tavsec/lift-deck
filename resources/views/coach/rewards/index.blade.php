<x-layouts.coach>
    <x-slot:title>Rewards</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reward Library</h1>
                <p class="mt-1 text-sm text-gray-500">Browse and manage rewards for your clients</p>
            </div>
            <a href="{{ route('coach.rewards.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Reward
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

        <!-- Reward Grid -->
        @if($rewards->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($rewards as $reward)
                    @if($reward->coach_id === null)
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                    @else
                        <a href="{{ route('coach.rewards.edit', $reward) }}" class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200 overflow-hidden">
                    @endif
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">{{ $reward->name }}</h3>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($reward->coach_id === null)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                System
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $reward->points_cost }} pts
                                        </span>
                                    </div>
                                </div>

                                @if($reward->stock !== null)
                                    <div class="mt-2">
                                        @if($reward->stock === 0)
                                            <span class="text-xs font-medium text-red-600">Out of stock</span>
                                        @else
                                            <span class="text-xs text-gray-500">{{ $reward->stock }} in stock</span>
                                        @endif
                                    </div>
                                @endif

                                @if($reward->description)
                                    <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $reward->description }}</p>
                                @endif
                            </div>
                    @if($reward->coach_id === null)
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No rewards found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding your first reward.</p>
                    <div class="mt-6">
                        <a href="{{ route('coach.rewards.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Reward
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
