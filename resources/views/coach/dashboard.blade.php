<x-layouts.coach>
    <x-slot:title>{{ __('coach.dashboard.heading') }}</x-slot:title>

    <div class="space-y-6">
        {{-- Metrics setup flash banner --}}
        @if(session('metrics_setup'))
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{!! session('metrics_setup') !!}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- First-time metrics setup popup --}}
        @if(auth()->user()->metrics_onboarded_at === null)
            <div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="metrics-setup-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                    <div class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div>
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="metrics-setup-title">
                                    {{ __('coach.metrics_setup.title') }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('coach.metrics_setup.description') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 flex flex-col sm:flex-row gap-3">
                            <form method="POST" action="{{ route('coach.metrics-setup') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="setup" value="1">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                                    {{ __('coach.metrics_setup.yes') }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('coach.metrics-setup') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="setup" value="0">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-700 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                                    {{ __('coach.metrics_setup.skip') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Welcome Message -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('coach.dashboard.welcome', ['name' => auth()->user()->name]) }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('coach.dashboard.subtitle') }}</p>
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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('coach.dashboard.total_clients') }}</p>
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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('coach.dashboard.active_clients') }}</p>
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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('coach.dashboard.unread_messages') }}</p>
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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('coach.dashboard.programs') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['programs'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('coach.dashboard.quick_actions') }}</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('coach.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('coach.dashboard.add_client') }}
                </a>
                <a href="{{ route('coach.programs.create') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('coach.dashboard.create_program') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Workout Logs -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('coach.dashboard.recent_logs') }}</h2>
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
                                        <span class="font-normal text-gray-500 dark:text-gray-400">{{ __('coach.dashboard.completed') }}</span>
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
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('coach.dashboard.no_logs') }}</p>
                    </div>
                @endif
            </div>

            <!-- Recent Comments -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ __('coach.dashboard.recent_comments') }}</h2>
                @if($recentComments->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($recentComments as $comment)
                            <a href="{{ route('coach.clients.workout-log', [$comment->workoutLog->client, $comment->workoutLog]) }}" class="flex items-start gap-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 -mx-2 px-2 rounded transition-colors">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center overflow-hidden">
                                    @if($comment->user->avatar)
                                        <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs font-medium text-blue-700">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        <span class="font-medium">{{ $comment->user->name }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('coach.dashboard.on') }}</span>
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
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('coach.dashboard.no_comments') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.coach>
