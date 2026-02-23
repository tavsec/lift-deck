<x-layouts.client>
    <x-slot:title>Home</x-slot:title>

    <div class="py-6 space-y-6">
        <!-- Welcome Greeting -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Hey, {{ auth()->user()->name }}!</h1>
            @if ($coach)
                <p class="mt-1 text-sm text-gray-600">Your coach: {{ $coach->name }}</p>
            @endif
        </div>

        <!-- Active Program -->
        <x-bladewind::card>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Active Program</h2>
                    <span class="text-xs text-gray-500">{{ now()->format('D, M j') }}</span>
                </div>
                @if($activeProgram)
                    <div class="space-y-2">
                        <h3 class="text-base font-semibold text-gray-900">{{ $activeProgram->program->name }}</h3>
                        @if($activeProgram->program->description)
                            <p class="text-sm text-gray-600">{{ Str::limit($activeProgram->program->description, 100) }}</p>
                        @endif
                        <div class="flex flex-wrap gap-2 text-sm text-gray-500">
                            @if($activeProgram->program->duration_weeks)
                                <span>{{ $activeProgram->program->duration_weeks }} weeks</span>
                            @endif
                            @if($activeProgram->program->type)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($activeProgram->program->type) }}</span>
                            @endif
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('client.program') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">View full program &rarr;</a>
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm">No program assigned yet</p>
                    </div>
                @endif
            </div>
        </x-bladewind::card>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-4">
            <!-- This Week -->
            <x-bladewind::card>
                <div class="space-y-2">
                    <h3 class="text-sm font-medium text-gray-600">This Week</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $weeklyWorkoutCount }} / {{ $weeklyWorkoutTarget }}</p>
                    <p class="text-xs text-gray-500">workouts completed</p>
                </div>
            </x-bladewind::card>

            <!-- Last Workout -->
            <x-bladewind::card>
                <div class="space-y-2">
                    <h3 class="text-sm font-medium text-gray-600">Last Workout</h3>
                    @if($lastWorkout)
                        <p class="text-sm font-bold text-gray-900">{{ $lastWorkout->displayName() }}</p>
                        <p class="text-xs text-gray-500">{{ $lastWorkout->completed_at->diffForHumans() }}</p>
                    @else
                        <p class="text-sm text-gray-400">None yet</p>
                    @endif
                </div>
            </x-bladewind::card>
        </div>

        <!-- XP & Loyalty -->
        @if($xpSummary || $nextLevel)
        <x-bladewind::card>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @if($xpSummary?->currentLevel)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                {{ $xpSummary->currentLevel->name }}
                            </span>
                        @endif
                        <span class="text-sm font-medium text-gray-700">{{ number_format($xpSummary?->total_xp ?? 0) }} XP</span>
                    </div>
                    <span class="text-sm font-semibold text-blue-600">{{ number_format($xpSummary?->available_points ?? 0) }} pts</span>
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
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progress to {{ $nextLevel?->name ?? 'Max Level' }}</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                    </div>
                    @if($nextLevel)
                        <p class="text-xs text-gray-400 mt-1">{{ number_format($nextLevelXp - $currentXp) }} XP to go</p>
                    @endif
                </div>

                @if($recentAchievements->isNotEmpty())
                    <div class="flex items-center gap-2 pt-1">
                        <span class="text-xs text-gray-500">Recent:</span>
                        @foreach($recentAchievements as $achievement)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" title="{{ $achievement->name }}">
                                🏆 {{ Str::limit($achievement->name, 20) }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center gap-4 pt-2 border-t border-gray-100">
                    <a href="{{ route('client.rewards') }}" class="text-sm text-blue-600 hover:text-blue-800">Rewards Shop &rarr;</a>
                    <a href="{{ route('client.achievements') }}" class="text-sm text-blue-600 hover:text-blue-800">Achievements &rarr;</a>
                    <a href="{{ route('client.loyalty') }}" class="text-sm text-blue-600 hover:text-blue-800">Points History &rarr;</a>
                </div>
            </div>
        </x-bladewind::card>
        @endif

        <!-- Daily Check-in Widget -->
        @if($assignedMetricCount > 0)
            <a href="{{ route('client.check-in') }}" class="block">
                <x-bladewind::card>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $todayLogCount >= $assignedMetricCount ? 'bg-green-100' : 'bg-blue-100' }} flex items-center justify-center">
                                @if($todayLogCount >= $assignedMetricCount)
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    @if($todayLogCount >= $assignedMetricCount)
                                        Check-in Complete
                                    @else
                                        Daily Check-in
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ $todayLogCount }} / {{ $assignedMetricCount }} metrics logged today</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </x-bladewind::card>
            </a>
        @endif

        <!-- Coach Card -->
        @if ($coach)
            <x-bladewind::card>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 text-blue-600 rounded-full font-semibold text-lg">
                            {{ strtoupper(substr($coach->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $coach->name }}</p>
                            <p class="text-xs text-gray-500">Your Coach</p>
                        </div>
                    </div>
                    <x-bladewind::button
                        tag="a"
                        href="{{ route('client.messages') }}"
                        size="small"
                        color="blue"
                    >
                        Message
                    </x-bladewind::button>
                </div>
            </x-bladewind::card>
        @endif
    </div>
</x-layouts.client>
