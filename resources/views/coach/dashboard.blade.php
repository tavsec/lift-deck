<x-layouts.coach>
    <x-slot:title>Dashboard</x-slot:title>

    <div class="space-y-6">
        <!-- Welcome Message -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Here's an overview of your coaching activity.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Clients -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Clients</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_clients'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Clients -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Clients</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['active_clients'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Unread Messages -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unread Messages</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['unread_messages'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Programs -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Programs</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['programs'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('coach.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Add Client
                </a>
                <a href="{{ route('coach.programs.create') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Create Program
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Workout Logs -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Workout Logs</h2>
                @if($recentWorkoutLogs->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($recentWorkoutLogs as $log)
                            <a href="{{ route('coach.clients.workout-log', [$log->client, $log]) }}" class="flex items-center gap-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded transition-colors">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ $log->client->name }}
                                        <span class="font-normal text-gray-500 dark:text-gray-400">completed</span>
                                        {{ $log->displayName() }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->completed_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No workout logs yet</p>
                    </div>
                @endif
            </div>

            <!-- Recent Comments -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Comments</h2>
                @if($recentComments->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($recentComments as $comment)
                            <a href="{{ route('coach.clients.workout-log', [$comment->workoutLog->client, $comment->workoutLog]) }}" class="flex items-start gap-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded transition-colors">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-xs font-medium text-blue-700">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        <span class="font-medium">{{ $comment->user->name }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">on</span>
                                        <span class="font-medium">{{ $comment->workoutLog->displayName() }}</span>
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $comment->body }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No comments yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.coach>
