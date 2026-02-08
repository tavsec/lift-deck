<x-layouts.coach>
    <x-slot:title>{{ $client->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('coach.clients.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Clients
                </a>
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-2xl font-bold text-blue-700">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h1>
                        <p class="text-sm text-gray-500">{{ $client->email }}</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('coach.clients.analytics', $client) }}" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md font-medium text-sm text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Analytics
                </a>
                <a href="{{ route('coach.clients.edit', $client) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('coach.clients.destroy', $client) }}" onsubmit="return confirm('Are you sure you want to remove this client?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md font-medium text-sm text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Remove
                    </button>
                </form>
            </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Client Info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Status</h2>
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-gray-500">Onboarding</span>
                            <div class="mt-1">
                                @if($client->clientProfile?->isOnboardingComplete())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Complete
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Member since</span>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $client->created_at->format('M d, Y') }}</p>
                        </div>
                        @if($client->phone)
                        <div>
                            <span class="text-sm text-gray-500">Phone</span>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $client->phone }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Profile Card -->
                @if($client->clientProfile)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Profile</h2>
                    <div class="space-y-4">
                        @if($client->clientProfile->goal)
                        <div>
                            <span class="text-sm text-gray-500">Goal</span>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($client->clientProfile->goal === 'fat_loss') bg-orange-100 text-orange-800
                                    @elseif($client->clientProfile->goal === 'strength') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ str_replace('_', ' ', ucfirst($client->clientProfile->goal)) }}
                                </span>
                            </p>
                        </div>
                        @endif
                        @if($client->clientProfile->experience_level)
                        <div>
                            <span class="text-sm text-gray-500">Experience Level</span>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ ucfirst($client->clientProfile->experience_level) }}</p>
                        </div>
                        @endif
                        @if($client->clientProfile->injuries)
                        <div>
                            <span class="text-sm text-gray-500">Injuries / Limitations</span>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->clientProfile->injuries }}</p>
                        </div>
                        @endif
                        @if($client->clientProfile->equipment_access)
                        <div>
                            <span class="text-sm text-gray-500">Equipment Access</span>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->clientProfile->equipment_access }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

                <!-- Tracking Metrics Assignment -->
                @if($coachMetrics->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Tracking Metrics</h2>
                    <div class="space-y-2">
                        @foreach($coachMetrics as $metric)
                            <form method="POST" action="{{ route('coach.clients.toggle-metric', $client) }}">
                                @csrf
                                <input type="hidden" name="tracking_metric_id" value="{{ $metric->id }}">
                                <button type="submit" class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50 text-left transition-colors">
                                    <span class="text-sm text-gray-700">{{ $metric->name }}</span>
                                    @if($assignedMetricIds->contains($metric->id))
                                        <span class="flex-shrink-0 w-8 h-5 bg-blue-600 rounded-full relative">
                                            <span class="absolute right-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow"></span>
                                        </span>
                                    @else
                                        <span class="flex-shrink-0 w-8 h-5 bg-gray-300 rounded-full relative">
                                            <span class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow"></span>
                                        </span>
                                    @endif
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Active Program -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Active Program</h2>
                        @if($activeProgram)
                            <a href="{{ route('coach.programs.show', $activeProgram->program) }}" class="text-sm text-blue-600 hover:text-blue-800">View Program</a>
                        @endif
                    </div>
                    @if($activeProgram)
                        <div class="space-y-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ $activeProgram->program->name }}</h3>
                                @if($activeProgram->program->description)
                                    <p class="mt-1 text-sm text-gray-600">{{ Str::limit($activeProgram->program->description, 120) }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                                @if($activeProgram->program->duration_weeks)
                                    <span>{{ $activeProgram->program->duration_weeks }} weeks</span>
                                @endif
                                @if($activeProgram->program->type)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($activeProgram->program->type) }}</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400">
                                Started {{ $activeProgram->started_at->format('M d, Y') }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No active program assigned</p>
                        </div>
                    @endif
                </div>

                <!-- Recent Workouts -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Workouts</h2>
                    @if($recentWorkoutLogs->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($recentWorkoutLogs as $log)
                                <a href="{{ route('coach.clients.workout-log', [$client, $log]) }}" class="flex items-center justify-between py-3 hover:bg-gray-50 -mx-2 px-2 rounded transition-colors">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-medium text-gray-900">{{ $log->displayName() }}</p>
                                            @if($unreadWorkoutLogIds->contains($log->id))
                                                <span class="flex h-2 w-2 rounded-full bg-blue-500" title="Unread comments"></span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $log->completed_at->format('M j, Y \a\t g:i A') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($log->comments_count > 0)
                                            <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                </svg>
                                                {{ $log->comments_count }}
                                            </span>
                                        @endif
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No workouts logged yet</p>
                        </div>
                    @endif
                </div>
                <!-- Nutrition Summary -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Nutrition</h2>
                        <a href="{{ route('coach.clients.nutrition', $client) }}" class="text-sm text-blue-600 hover:text-blue-800">View Details</a>
                    </div>
                    @if($currentMacroGoal)
                        <div class="grid grid-cols-4 gap-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase">Calories</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($todayMealTotals->calories) }}</p>
                                <p class="text-xs text-gray-400">/ {{ number_format($currentMacroGoal->calories) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase">Protein</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($todayMealTotals->protein, 1) }}g</p>
                                <p class="text-xs text-gray-400">/ {{ $currentMacroGoal->protein }}g</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase">Carbs</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($todayMealTotals->carbs, 1) }}g</p>
                                <p class="text-xs text-gray-400">/ {{ $currentMacroGoal->carbs }}g</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase">Fat</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($todayMealTotals->fat, 1) }}g</p>
                                <p class="text-xs text-gray-400">/ {{ $currentMacroGoal->fat }}g</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">No macro goals set</p>
                            <a href="{{ route('coach.clients.nutrition', $client) }}" class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                Set macro goals
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Daily Check-in Logs (Last 7 Days) -->
                @if($assignedMetricIds->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Daily Check-ins (Last 7 Days)</h2>

                    @php
                        $assignedMetrics = $coachMetrics->whereIn('id', $assignedMetricIds);
                        $dates = collect();
                        for ($i = 6; $i >= 0; $i--) {
                            $dates->push(now()->subDays($i)->format('Y-m-d'));
                        }
                    @endphp

                    <div class="overflow-x-auto -mx-6 px-6">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-2 pr-4 font-medium text-gray-500 text-xs uppercase">Metric</th>
                                    @foreach($dates as $date)
                                        <th class="text-center py-2 px-2 font-medium text-gray-500 text-xs">
                                            {{ \Carbon\Carbon::parse($date)->format('D') }}<br>
                                            <span class="text-gray-400">{{ \Carbon\Carbon::parse($date)->format('j') }}</span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($assignedMetrics as $metric)
                                    <tr>
                                        <td class="py-2 pr-4 text-gray-700 whitespace-nowrap">{{ $metric->name }}</td>
                                        @foreach($dates as $date)
                                            @php
                                                $log = $recentDailyLogs->get($date)?->firstWhere('tracking_metric_id', $metric->id);
                                            @endphp
                                            <td class="text-center py-2 px-2">
                                                @if($log)
                                                    @if($metric->type === 'boolean')
                                                        @if($log->value === '1' || $log->value === 'true')
                                                            <span class="text-green-600">
                                                                <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            </span>
                                                        @else
                                                            <span class="text-red-400">
                                                                <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                            </span>
                                                        @endif
                                                    @elseif($metric->type === 'text')
                                                        <span class="text-blue-600 cursor-help" title="{{ $log->value }}">
                                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        </span>
                                                    @else
                                                        <span class="text-gray-900 font-medium">{{ $log->value }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-300">&mdash;</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.coach>
