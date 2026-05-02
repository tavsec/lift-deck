<x-layouts.coach>
    <x-slot:title>{{ __('coach.dashboard.heading') }}</x-slot:title>

    <div class="space-y-6">
        {{-- Metrics setup flash banner --}}
        @if(session('metrics_setup'))
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{!! session('metrics_setup') !!}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Onboarding Checklist --}}
        @if($onboardingChecklist['show'])
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.onboarding_checklist.heading') }}</h2>
                        <p class="text-sm text-[#8e8e93] dark:text-gray-400 mt-0.5">
                            {{ __('coach.onboarding_checklist.progress', ['completed' => $onboardingChecklist['completed_count'], 'total' => $onboardingChecklist['total_count']]) }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('coach.onboarding-checklist.dismiss') }}">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-300 transition-colors">
                            {{ __('coach.onboarding_checklist.dismiss') }}
                        </button>
                    </form>
                </div>

                {{-- Progress bar --}}
                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 mb-5">
                    <div class="h-1.5 rounded-full bg-[#1456f0] transition-all duration-500"
                         style="width: {{ ($onboardingChecklist['completed_count'] / $onboardingChecklist['total_count']) * 100 }}%"></div>
                </div>

                <ul class="space-y-3">
                    @foreach($onboardingChecklist['steps'] as $step)
                        <li class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                @if($step['complete'])
                                    <div class="flex-shrink-0 h-5 w-5 rounded-full bg-[#1456f0] flex items-center justify-center">
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="flex-shrink-0 h-5 w-5 rounded-full border-2 border-gray-300 dark:border-gray-600"></div>
                                @endif
                                <span class="text-sm {{ $step['complete'] ? 'text-[#8e8e93] dark:text-gray-500 line-through' : 'text-[#222222] dark:text-gray-100' }}">
                                    {{ $step['label'] }}
                                </span>
                            </div>
                            @if(! $step['complete'] && $step['route'])
                                <a href="{{ $step['route'] }}" class="flex-shrink-0 text-xs font-semibold text-[#1456f0] hover:underline">
                                    {{ $step['action_label'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- First-time metrics setup card --}}
        @if(auth()->user()->metrics_onboarded_at === null)
            <div x-data="{ open: true }" x-show="open" x-transition>
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 h-10 w-10 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg class="h-5 w-5 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">
                                {{ __('coach.metrics_setup.title') }}
                            </h3>
                            <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-400">
                                {{ __('coach.metrics_setup.description') }}
                            </p>
                            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                <form method="POST" action="{{ route('coach.metrics-setup') }}">
                                    @csrf
                                    <input type="hidden" name="setup" value="1">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                                        {{ __('coach.metrics_setup.yes') }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('coach.metrics-setup') }}">
                                    @csrf
                                    <input type="hidden" name="setup" value="0">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-medium text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                        {{ __('coach.metrics_setup.skip') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Welcome Message -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.dashboard.welcome', ['name' => auth()->user()->name]) }}</h1>
                <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('coach.dashboard.subtitle') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('coach.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    + {{ __('coach.dashboard.add_client') }}
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Clients -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 flex-shrink-0">
                        <svg class="h-5 w-5 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.dashboard.total_clients') }}</p>
                        <p class="text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ $stats['total_clients'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Clients -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-green-50 dark:bg-green-900/20 flex-shrink-0">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.dashboard.active_clients') }}</p>
                        <p class="text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ $stats['active_clients'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Unread Messages -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.dashboard.unread_messages') }}</p>
                        <p class="text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ $stats['unread_messages'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Programs -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex-shrink-0">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.dashboard.programs') }}</p>
                        <p class="text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ $stats['programs'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.dashboard.quick_actions') }}</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('coach.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    {{ __('coach.dashboard.add_client') }}
                </a>
                <a href="{{ route('coach.programs.create') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('coach.dashboard.create_program') }}
                </a>
            </div>
        </div>

        {{-- Needs attention --}}
        @if($needsAttention->isNotEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.dashboard.needs_attention.heading') }}</h2>
                <p class="text-sm text-[#8e8e93] dark:text-gray-400 mt-0.5">{{ __('coach.dashboard.needs_attention.subtitle') }}</p>

                <div class="divide-y divide-gray-100 dark:divide-gray-800 mt-4">
                    @foreach($needsAttention as $row)
                        @php($client = $row['client'])
                        @php($flag = $row['flag'])
                        @php($chipClasses = match($flag) {
                            'inactive' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
                            'off_target' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-300',
                            'no_goal' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                        })
                        <a href="{{ route('coach.clients.nutrition', $client) }}" class="flex items-center gap-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 -mx-2 px-2 rounded-lg transition-colors">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center overflow-hidden"
                                 style="background-color: var(--color-primary)">
                                @if($client->avatar)
                                    <img src="{{ $client->avatar }}" alt="{{ $client->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs font-semibold text-white">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0 flex items-center gap-2">
                                <p class="text-sm font-medium text-[#222222] dark:text-gray-100 truncate">{{ $client->name }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $chipClasses }}">
                                    {{ __('coach.dashboard.needs_attention.flags.'.$flag) }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Workout Logs -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.dashboard.recent_logs') }}</h2>
                @if($recentWorkoutLogs->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($recentWorkoutLogs as $log)
                            <a href="{{ route('coach.clients.workout-log', [$log->client, $log]) }}" class="flex items-center gap-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 -mx-2 px-2 rounded-lg transition-colors">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-[#222222] dark:text-gray-100 truncate">
                                        {{ $log->client->name }}
                                        <span class="font-normal text-[#8e8e93] dark:text-gray-400">{{ __('coach.dashboard.completed') }}</span>
                                        {{ $log->displayName() }}
                                    </p>
                                    <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ $log->completed_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                            <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.dashboard.no_logs') }}</p>
                    </div>
                @endif
            </div>

            <!-- Recent Comments -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.dashboard.recent_comments') }}</h2>
                @if($recentComments->isNotEmpty())
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($recentComments as $comment)
                            <a href="{{ route('coach.clients.workout-log', [$comment->workoutLog->client, $comment->workoutLog]) }}" class="flex items-start gap-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 -mx-2 px-2 rounded-lg transition-colors">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden"
                                     style="background-color: var(--color-primary)">
                                    @if($comment->user->avatar)
                                        <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs font-semibold text-white">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-[#222222] dark:text-gray-100">
                                        <span class="font-medium">{{ $comment->user->name }}</span>
                                        <span class="text-[#8e8e93] dark:text-gray-400">{{ __('coach.dashboard.on') }}</span>
                                        <span class="font-medium">{{ $comment->workoutLog->displayName() }}</span>
                                    </p>
                                    <p class="text-sm text-[#45515e] dark:text-gray-400 truncate">{{ $comment->body }}</p>
                                    <p class="text-xs text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                            <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.dashboard.no_comments') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.coach>
