<x-layouts.client>
    <x-slot:title>Achievements</x-slot:title>

    <div class="py-6 space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Achievements</h1>
            <p class="mt-1 text-sm text-gray-600">Track your milestones and accomplishments</p>
        </div>

        <!-- XP Summary Bar -->
        @if($xpSummary)
            <x-bladewind::card>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-full">
                            <svg class="w-7 h-7 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total XP Earned</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($xpSummary->total_xp) }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        @if($xpSummary->currentLevel)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                Level {{ $xpSummary->currentLevel->level_number }}: {{ $xpSummary->currentLevel->name }}
                            </span>
                        @endif

                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $earnedAchievementIds->count() }} <span class="text-base font-normal text-gray-500">/ {{ $achievements->count() }}</span></p>
                            <p class="text-xs text-gray-500">achievements earned</p>
                        </div>
                    </div>
                </div>
            </x-bladewind::card>
        @else
            <!-- Stats without XP summary -->
            <x-bladewind::card>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">Achievements Progress</p>
                    <p class="text-lg font-bold text-gray-900">{{ $earnedAchievementIds->count() }} / {{ $achievements->count() }} earned</p>
                </div>
            </x-bladewind::card>
        @endif

        <!-- Achievements Grid -->
        @if($achievements->isEmpty())
            <x-bladewind::card>
                <div class="py-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <p class="text-base font-medium">No achievements available yet</p>
                    <p class="mt-1 text-sm">Keep training — achievements will appear here.</p>
                </div>
            </x-bladewind::card>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($achievements as $achievement)
                    @php
                        $isEarned = $earnedAchievementIds->contains($achievement->id);
                    @endphp

                    <div class="flex flex-col items-center text-center rounded-lg p-4 space-y-2 {{ $isEarned ? 'border-2 border-green-500 bg-white shadow' : 'border-2 border-gray-200 bg-gray-50' }}">
                        <!-- Trophy Icon -->
                        <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $isEarned ? 'bg-yellow-100' : 'bg-gray-200' }}">
                            <svg class="w-7 h-7 {{ $isEarned ? 'text-yellow-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a1 1 0 010 2h-1v.5A6.5 6.5 0 0110 13a6.5 6.5 0 01-6-3.978V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm10 4H5v.522A4.5 4.5 0 0010 11a4.5 4.5 0 005-4.478V6zM7 15a1 1 0 000 2h6a1 1 0 000-2H7z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <!-- Name -->
                        <p class="text-sm font-semibold leading-tight {{ $isEarned ? 'text-gray-900' : 'text-gray-400' }}">{{ $achievement->name }}</p>

                        <!-- Description -->
                        @if($achievement->description)
                            <p class="text-xs leading-snug {{ $isEarned ? 'text-gray-500' : 'text-gray-400' }}">{{ Str::limit($achievement->description, 60) }}</p>
                        @endif

                        <!-- Type Badge -->
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $achievement->type === 'automatic' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($achievement->type) }}
                        </span>

                        <!-- Earned / Unearned Indicator -->
                        @if($isEarned)
                            <div class="flex items-center space-x-1 text-green-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-xs font-medium">Earned</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-1 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-xs">Locked</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.client>
