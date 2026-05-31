<x-layouts.client>
    <x-slot:title>{{ __('client.dashboard.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <!-- Welcome Greeting -->
        <div class="mb-5">
            <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('client.dashboard.hey', ['name' => auth()->user()->name]) }}</h1>
            @if ($coach)
                <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.dashboard.your_coach', ['coach_name' => $coach->name]) }}</p>
            @endif
        </div>

        <!-- Active Program -->
        <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 mb-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold tracking-widest uppercase text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.dashboard.active_program') }}</span>
                <span class="font-mono text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ now()->format('D, M j') }}</span>
            </div>
            @if($activeProgram)
                <h2 class="font-display text-[21px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight leading-tight mb-1.5">{{ $activeProgram->program->name }}</h2>
                @if($activeProgram->program->description)
                    <p class="text-sm text-[#555b66] dark:text-[#a4abb6] mb-3">{{ Str::limit($activeProgram->program->description, 100) }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($activeProgram->program->duration_weeks)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[rgba(18,22,31,0.06)] dark:bg-[rgba(255,255,255,0.07)] text-[#555b66] dark:text-[#a4abb6]">{{ __('client.program.weeks', ['n' => $activeProgram->program->duration_weeks]) }}</span>
                    @endif
                    @if($activeProgram->program->type)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[rgba(59,130,246,0.12)] text-[#3b82f6]">{{ ucfirst($activeProgram->program->type) }}</span>
                    @endif
                </div>
                <a href="{{ route('client.program') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#5c7a10] dark:text-[#c6f24e] hover:opacity-80">
                    {{ __('client.dashboard.view_full_program') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <div class="py-8 text-center text-[#8c93a0] dark:text-[#6b7280]">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-sm">{{ __('client.dashboard.no_program') }}</p>
                </div>
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-3">
            <!-- This Week -->
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-4">
                <p class="text-xs font-semibold text-[#8c93a0] dark:text-[#6b7280] mb-1.5">{{ __('client.dashboard.this_week') }}</p>
                <p class="font-mono text-[30px] font-bold leading-none text-[#181b22] dark:text-[#f0f2f5] mb-1">{{ $weeklyWorkoutCount }} / {{ $weeklyWorkoutTarget }}</p>
                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.dashboard.workouts_completed') }}</p>
            </div>

            <!-- Last Workout -->
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-4">
                <p class="text-xs font-semibold text-[#8c93a0] dark:text-[#6b7280] mb-1.5">{{ __('client.dashboard.last_workout') }}</p>
                @if($lastWorkout)
                    <p class="font-display text-[17px] font-bold leading-snug text-[#181b22] dark:text-[#f0f2f5] mb-1">{{ $lastWorkout->displayName() }}</p>
                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ $lastWorkout->completed_at->diffForHumans() }}</p>
                @else
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.dashboard.no_workouts') }}</p>
                @endif
            </div>
        </div>

        @if($loyaltyEnabled)
        <!-- XP & Loyalty -->
        <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 mb-4">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    @if($xpSummary?->currentLevel)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                            {{ $xpSummary->currentLevel->name }}
                        </span>
                    @endif
                    <span class="font-mono text-lg font-bold text-[#181b22] dark:text-[#f0f2f5]">{{ number_format($xpSummary?->total_xp ?? 0) }} <span class="text-xs font-semibold text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.dashboard.xp') }}</span></span>
                    <span class="font-mono font-bold text-[#5c7a10] dark:text-[#c6f24e] ml-auto">{{ number_format($xpSummary?->available_points ?? 0) }} <span class="text-xs">{{ __('client.dashboard.pts') }}</span></span>
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
                    <div class="flex justify-between text-xs text-[#8c93a0] dark:text-[#6b7280] mb-1">
                        <span>{{ __('client.dashboard.progress_to', ['level' => $nextLevel?->name ?? __('client.dashboard.max_level')]) }}</span>
                        <span class="font-mono">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full bg-[#c6f24e] transition-all" style="width: {{ $progress }}%"></div>
                    </div>
                    @if($nextLevel)
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-1">{{ number_format($nextLevelXp - $currentXp) }} {{ __('client.dashboard.xp_to_go') }}</p>
                    @endif
                </div>

                @if($recentAchievements->isNotEmpty())
                    <div class="flex items-center gap-2 pt-1">
                        <span class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.dashboard.recent') }}</span>
                        @foreach($recentAchievements as $achievement)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400" title="{{ $achievement->name }}">
                                🏆 {{ Str::limit($achievement->name, 20) }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center gap-4 pt-2 border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                    <a href="{{ route('client.rewards') }}" class="text-sm font-semibold text-[#5c7a10] dark:text-[#c6f24e] hover:opacity-80">{{ __('client.dashboard.rewards_shop') }}</a>
                    <a href="{{ route('client.achievements') }}" class="text-sm font-semibold text-[#5c7a10] dark:text-[#c6f24e] hover:opacity-80">{{ __('client.dashboard.achievements') }}</a>
                    <a href="{{ route('client.loyalty') }}" class="text-sm font-semibold text-[#5c7a10] dark:text-[#c6f24e] hover:opacity-80">{{ __('client.dashboard.points_history') }}</a>
                </div>
            </div>
        </div>
        @endif

        <!-- Daily Check-in Widget -->
        @if($assignedMetricCount > 0)
            <a href="{{ route('client.check-in') }}" class="block">
                <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-11 h-11 rounded-full {{ $todayLogCount >= $assignedMetricCount ? 'bg-green-100 dark:bg-green-900/30' : 'bg-[rgba(18,22,31,0.06)] dark:bg-[rgba(255,255,255,0.07)]' }} flex items-center justify-center">
                                @if($todayLogCount >= $assignedMetricCount)
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-[#555b66] dark:text-[#a4abb6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5]">
                                    @if($todayLogCount >= $assignedMetricCount)
                                        {{ __('client.dashboard.check_in_complete') }}
                                    @else
                                        {{ __('client.dashboard.daily_check_in') }}
                                    @endif
                                </p>
                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.dashboard.check_in_progress', ['n' => $todayLogCount, 'total' => $assignedMetricCount]) }}</p>
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
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-[rgba(124,92,255,0.15)] dark:bg-[rgba(124,92,255,0.18)] text-[#7c5cff] rounded-full font-display font-bold text-lg flex-shrink-0">
                            {{ strtoupper(substr($coach->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ $coach->name }}</p>
                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.dashboard.your_coach_section') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('client.messages') }}"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#c6f24e] text-[#14180a] hover:bg-[#b4e438] transition-colors">
                        {{ __('client.dashboard.message') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layouts.client>
