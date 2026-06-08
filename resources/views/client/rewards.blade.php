<x-layouts.client>
    <x-slot:title>{{ __('client.rewards.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <!-- Header -->
        <div class="mb-5">
            <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('client.rewards.heading') }}</h1>
            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.rewards.subtitle') }}</p>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mb-4">
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 mb-4">
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Points Balance Card -->
        <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-full">
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.rewards.available_points') }}</p>
                        <p class="text-3xl font-bold font-mono text-[#181b22] dark:text-[#f0f2f5]">{{ $xpSummary?->available_points ?? 0 }}</p>
                    </div>
                </div>

                @if($xpSummary?->currentLevel)
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[rgba(198,242,78,0.15)] text-[#5c7a10] dark:bg-[rgba(198,242,78,0.12)] dark:text-[#c6f24e]">
                            {{ __('client.rewards.level', ['n' => $xpSummary->currentLevel->level_number, 'name' => $xpSummary->currentLevel->name]) }}
                        </span>
                        <p class="mt-1 text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.rewards.total_xp', ['n' => number_format($xpSummary->total_xp)]) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-right -mt-2 mb-2">
            <a href="{{ route('client.loyalty') }}" class="text-sm font-semibold text-[#5c7a10] dark:text-[#c6f24e] hover:opacity-80">{{ __('client.rewards.view_history') }}</a>
        </div>

        <!-- Rewards Grid -->
        @if($rewards->isEmpty())
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                <div class="py-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                    <p class="text-base font-medium text-[#555b66] dark:text-[#a4abb6]">{{ __('client.rewards.no_rewards') }}</p>
                    <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.rewards.no_rewards_description') }}</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach($rewards as $reward)
                    @php
                        $canAfford = $xpSummary && $xpSummary->available_points >= $reward->points_cost;
                        $hasStock = $reward->hasStock();
                        $canRedeem = $canAfford && $hasStock;
                    @endphp

                    <div class="flex flex-col bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 space-y-3">
                        <!-- Reward Header -->
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] leading-tight">{{ $reward->name }}</h3>
                            <div class="flex flex-shrink-0 flex-col items-end gap-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">
                                    <svg class="w-3 h-3 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    {{ number_format($reward->points_cost) }} pts
                                </span>
                                @if(is_null($reward->coach_id))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400">{{ __('client.rewards.system') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        @if($reward->description)
                            <p class="text-sm text-[#555b66] dark:text-[#a4abb6] leading-snug">{{ Str::limit($reward->description, 80) }}</p>
                        @endif

                        <!-- Stock -->
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">
                            @if(is_null($reward->stock))
                                {{ __('client.rewards.unlimited') }}
                            @elseif($reward->stock === 0)
                                <span class="text-red-600 dark:text-red-400 font-medium">{{ __('client.rewards.out_of_stock') }}</span>
                            @else
                                {{ __('client.rewards.n_available', ['n' => $reward->stock]) }}
                            @endif
                        </p>

                        <!-- Redeem Button -->
                        <div class="mt-auto pt-1">
                            @if($canRedeem)
                                <form
                                    method="POST"
                                    action="{{ route('client.rewards.redeem', $reward) }}"
                                    onsubmit="return confirm('{{ __('client.rewards.spend_confirm', ['points' => $reward->points_cost, 'name' => addslashes($reward->name)]) }}')"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-[#c6f24e] text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#b4e438] transition-colors"
                                    >
                                        {{ __('client.rewards.redeem') }}
                                    </button>
                                </form>
                            @else
                                <button
                                    type="button"
                                    disabled
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium text-[#8c93a0] dark:text-[#6b7280] bg-gray-100 dark:bg-gray-800 cursor-not-allowed"
                                >
                                    @if(!$hasStock)
                                        {{ __('client.rewards.out_of_stock_button') }}
                                    @elseif(!$xpSummary)
                                        {{ __('client.rewards.no_xp') }}
                                    @else
                                        {{ __('client.rewards.not_enough_points') }}
                                    @endif
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.client>
