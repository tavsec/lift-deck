<x-layouts.coach>
    <x-slot:title>{{ __('coach.rewards.index.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.rewards.index.heading') }}</h1>
                <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('coach.rewards.index.subtitle') }}</p>
            </div>
            <a href="{{ route('coach.rewards.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('coach.rewards.index.add') }}
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reward Grid -->
        @if($rewards->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($rewards as $reward)
                    @if($reward->coach_id === null)
                        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] overflow-hidden">
                    @else
                        <a href="{{ route('coach.rewards.edit', $reward) }}" class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] overflow-hidden hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                    @endif
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5] truncate">{{ $reward->name }}</h3>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($reward->coach_id === null)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">
                                                {{ __('coach.rewards.index.system') }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                            {{ __('coach.rewards.index.pts', ['n' => $reward->points_cost]) }}
                                        </span>
                                    </div>
                                </div>

                                @if($reward->stock !== null)
                                    <div class="mt-2">
                                        @if($reward->stock === 0)
                                            <span class="text-xs font-medium text-red-600 dark:text-red-400">{{ __('coach.rewards.index.out_of_stock') }}</span>
                                        @else
                                            <span class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.rewards.index.in_stock', ['n' => $reward->stock]) }}</span>
                                        @endif
                                    </div>
                                @endif

                                @if($reward->description)
                                    <p class="mt-2 text-sm text-[#555b66] dark:text-[#a4abb6] line-clamp-2">{{ $reward->description }}</p>
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
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)]">
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-[#f3f5f7] dark:bg-[#1d2027] flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8c93a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.rewards.index.no_rewards') }}</h3>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-1">{{ __('coach.rewards.index.no_rewards_description') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('coach.rewards.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('coach.rewards.index.add') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
