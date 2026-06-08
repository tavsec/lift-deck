<x-layouts.client>
    <x-slot:title>{{ __('client.achievements.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <!-- Header -->
        <div class="mb-5">
            <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('client.achievements.heading') }}</h1>
            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.achievements.subtitle') }}</p>
        </div>

        <!-- XP Summary Bar -->
        @if($xpSummary)
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-full">
                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.achievements.total_xp') }}</p>
                            <p class="text-2xl font-bold font-mono text-[#181b22] dark:text-[#f0f2f5]">{{ number_format($xpSummary->total_xp) }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        @if($xpSummary->currentLevel)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[rgba(198,242,78,0.15)] text-[#5c7a10] dark:bg-[rgba(198,242,78,0.12)] dark:text-[#c6f24e]">
                                {{ __('client.achievements.level', ['n' => $xpSummary->currentLevel->level_number, 'name' => $xpSummary->currentLevel->name]) }}
                            </span>
                        @endif

                        <div class="text-center">
                            <p class="text-2xl font-bold font-mono text-[#181b22] dark:text-[#f0f2f5]">{{ $earnedAchievementIds->count() }} <span class="text-base font-normal text-[#8c93a0] dark:text-[#6b7280]">/ {{ $achievements->count() }}</span></p>
                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.achievements.achievements_earned') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Stats without XP summary -->
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-[#555b66] dark:text-[#a4abb6]">{{ __('client.achievements.progress') }}</p>
                    <p class="text-lg font-bold text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.achievements.n_earned', ['n' => $earnedAchievementIds->count(), 'total' => $achievements->count()]) }}</p>
                </div>
            </div>
        @endif

        <!-- Achievements Grid -->
        @if($achievements->isEmpty())
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                <div class="py-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <p class="text-base font-medium text-[#555b66] dark:text-[#a4abb6]">{{ __('client.achievements.no_achievements') }}</p>
                    <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.achievements.no_achievements_description') }}</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($achievements as $achievement)
                    @php
                        $isEarned = $earnedAchievementIds->contains($achievement->id);
                    @endphp

                    <div class="flex flex-col items-center text-center rounded-xl p-4 space-y-2 {{ $isEarned ? 'bg-white dark:bg-[#181b21] border-2 border-[#c6f24e] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)]' : 'bg-gray-50 dark:bg-gray-900/50 border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]' }}">
                        <!-- Trophy Icon -->
                        <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $isEarned ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-gray-100 dark:bg-gray-800' }}">
                            <svg class="w-6 h-6 {{ $isEarned ? 'text-yellow-500' : 'text-gray-400 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a1 1 0 010 2h-1v.5A6.5 6.5 0 0110 13a6.5 6.5 0 01-6-3.978V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm10 4H5v.522A4.5 4.5 0 0010 11a4.5 4.5 0 005-4.478V6zM7 15a1 1 0 000 2h6a1 1 0 000-2H7z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <!-- Name -->
                        <p class="text-sm font-semibold leading-tight {{ $isEarned ? 'text-[#181b22] dark:text-[#f0f2f5]' : 'text-[#8c93a0] dark:text-[#6b7280]' }}">{{ $achievement->name }}</p>

                        <!-- Description -->
                        @if($achievement->description)
                            <p class="text-xs leading-snug {{ $isEarned ? 'text-[#555b66] dark:text-[#a4abb6]' : 'text-[#8c93a0] dark:text-gray-600' }}">{{ Str::limit($achievement->description, 60) }}</p>
                        @endif

                        <!-- Type Badge -->
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $achievement->type === 'automatic' ? 'bg-[rgba(198,242,78,0.15)] text-[#5c7a10] dark:bg-[rgba(198,242,78,0.12)] dark:text-[#c6f24e]' : 'bg-gray-100 text-[#555b66] dark:bg-gray-800 dark:text-gray-400' }}">
                            {{ $achievement->type === 'automatic' ? __('client.achievements.type_automatic') : __('client.achievements.type_manual') }}
                        </span>

                        <!-- Earned / Unearned Indicator -->
                        @if($isEarned)
                            <div class="flex items-center space-x-1 text-green-600 dark:text-green-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-xs font-medium">{{ __('client.achievements.earned') }}</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-1 text-[#8c93a0] dark:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-xs">{{ __('client.achievements.locked') }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.client>
