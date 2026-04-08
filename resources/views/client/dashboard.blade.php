<x-layouts.client>
    <x-slot:title>{{ __('client.dashboard.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <!-- Welcome Greeting -->
        <div class="mb-5">
            <h1 class="font-display text-xl font-semibold text-[#222222] dark:text-gray-100">{{ __('client.dashboard.hey', ['name' => auth()->user()->name]) }}</h1>
            @if ($coach)
                <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('client.dashboard.your_coach', ['coach_name' => $coach->name]) }}</p>
            @endif
        </div>

        <!-- Active Program -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5 mb-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('client.dashboard.active_program') }}</h2>
                    <span class="text-xs text-[#8e8e93] dark:text-gray-500">{{ now()->format('D, M j') }}</span>
                </div>
                @if($activeProgram)
                    <div class="space-y-2">
                        <h3 class="text-base font-semibold text-[#222222] dark:text-gray-100">{{ $activeProgram->program->name }}</h3>
                        @if($activeProgram->program->description)
                            <p class="text-sm text-[#45515e] dark:text-gray-400">{{ Str::limit($activeProgram->program->description, 100) }}</p>
                        @endif
                        <div class="flex flex-wrap gap-2 text-sm text-[#8e8e93] dark:text-gray-500">
                            @if($activeProgram->program->duration_weeks)
                                <span>{{ __('client.program.weeks', ['n' => $activeProgram->program->duration_weeks]) }}</span>
                            @endif
                            @if($activeProgram->program->type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-[#1456f0] dark:bg-blue-900/30 dark:text-blue-400">{{ ucfirst($activeProgram->program->type) }}</span>
                            @endif
                        </div>
                        <div class="pt-1">
                            <a href="{{ route('client.program') }}" class="text-sm font-medium text-[#1456f0] hover:opacity-80">{{ __('client.dashboard.view_full_program') }}</a>
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center text-[#8e8e93] dark:text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm">{{ __('client.dashboard.no_program') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-4">
            <!-- This Week -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <div class="space-y-1">
                    <h3 class="text-xs font-medium text-[#8e8e93] dark:text-gray-500">{{ __('client.dashboard.this_week') }}</h3>
                    <p class="text-2xl font-bold text-[#222222] dark:text-gray-100">{{ $weeklyWorkoutCount }} / {{ $weeklyWorkoutTarget }}</p>
                    <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ __('client.dashboard.workouts_completed') }}</p>
                </div>
            </div>

            <!-- Last Workout -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <div class="space-y-1">
                    <h3 class="text-xs font-medium text-[#8e8e93] dark:text-gray-500">{{ __('client.dashboard.last_workout') }}</h3>
                    @if($lastWorkout)
                        <p class="text-sm font-bold text-[#222222] dark:text-gray-100">{{ $lastWorkout->displayName() }}</p>
                        <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ $lastWorkout->completed_at->diffForHumans() }}</p>
                    @else
                        <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.dashboard.no_workouts') }}</p>
                    @endif
                </div>
            </div>
        </div>

        @if($loyaltyEnabled)
        <!-- XP & Loyalty -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5 mb-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @if($xpSummary?->currentLevel)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                {{ $xpSummary->currentLevel->name }}
                            </span>
                        @endif
                        <span class="text-sm font-medium text-[#45515e] dark:text-gray-400">{{ number_format($xpSummary?->total_xp ?? 0) }} {{ __('client.dashboard.xp') }}</span>
                    </div>
                    <span class="text-sm font-semibold text-[#1456f0] dark:text-blue-400">{{ number_format($xpSummary?->available_points ?? 0) }} {{ __('client.dashboard.pts') }}</span>
                </div>

                @php
                    $currentXp = $xpSummary?->total_xp ?? 0;
                    $currentLevelXp = $xpSummary?->currentLevel?->xp_required ?? 0;
                    $nextLevelXp = $nextLevel?->xp_required ?? null;
                    $progress = $nextLevelXp && $nextLevelXp > $currentLevelXp
                        ? min(100, round(($currentXp - $currentLevelXp) / ($nextLevelXp - $currentLevelXp) * 100))
                        : 100;
                @endphp

                <div>
                    <div class="flex justify-between text-xs text-[#8e8e93] dark:text-gray-500 mb-1">
                        <span>{{ __('client.dashboard.progress_to', ['level' => $nextLevel?->name ?? __('client.dashboard.max_level')]) }}</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full transition-all" style="width: {{ $progress }}%; background-color: var(--color-primary)"></div>
                    </div>
                    @if($nextLevel)
                        <p class="text-xs text-[#8e8e93] dark:text-gray-500 mt-1">{{ number_format($nextLevelXp - $currentXp) }} {{ __('client.dashboard.xp_to_go') }}</p>
                    @endif
                </div>

                @if($recentAchievements->isNotEmpty())
                    <div class="flex items-center gap-2 pt-1">
                        <span class="text-xs text-[#8e8e93] dark:text-gray-500">{{ __('client.dashboard.recent') }}</span>
                        @foreach($recentAchievements as $achievement)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400" title="{{ $achievement->name }}">
                                🏆 {{ Str::limit($achievement->name, 20) }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center gap-4 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('client.rewards') }}" class="text-sm text-[#1456f0] hover:opacity-80">{{ __('client.dashboard.rewards_shop') }}</a>
                    <a href="{{ route('client.achievements') }}" class="text-sm text-[#1456f0] hover:opacity-80">{{ __('client.dashboard.achievements') }}</a>
                    <a href="{{ route('client.loyalty') }}" class="text-sm text-[#1456f0] hover:opacity-80">{{ __('client.dashboard.points_history') }}</a>
                </div>
            </div>
        </div>
        @endif

        <!-- Daily Check-in Widget -->
        @if($assignedMetricCount > 0)
            <a href="{{ route('client.check-in') }}" class="block">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5 mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $todayLogCount >= $assignedMetricCount ? 'bg-green-100 dark:bg-green-900/30' : 'bg-blue-50 dark:bg-blue-900/30' }} flex items-center justify-center">
                                @if($todayLogCount >= $assignedMetricCount)
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-[#1456f0] dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[#222222] dark:text-gray-100">
                                    @if($todayLogCount >= $assignedMetricCount)
                                        {{ __('client.dashboard.check_in_complete') }}
                                    @else
                                        {{ __('client.dashboard.daily_check_in') }}
                                    @endif
                                </p>
                                <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ __('client.dashboard.check_in_progress', ['n' => $todayLogCount, 'total' => $assignedMetricCount]) }}</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
        @endif

        <!-- Coach Card -->
        @if ($coach)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-[#1456f0] dark:text-blue-300 rounded-full font-semibold text-lg">
                            {{ strtoupper(substr($coach->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-[#222222] dark:text-gray-100">{{ $coach->name }}</p>
                            <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ __('client.dashboard.your_coach_section') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('client.messages') }}"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg text-white transition-colors"
                        style="background-color: var(--color-primary)">
                        {{ __('client.dashboard.message') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layouts.client>
