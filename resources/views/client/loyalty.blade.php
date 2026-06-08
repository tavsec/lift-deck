<x-layouts.client>
    <x-slot:title>{{ __('client.loyalty.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <!-- Header -->
        <div class="mb-5">
            <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('client.loyalty.heading') }}</h1>
            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.loyalty.subtitle') }}</p>
        </div>

        <!-- Summary Card -->
        @if($xpSummary)
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                <div class="flex flex-wrap items-center gap-3">
                    @if($xpSummary->currentLevel)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">
                            {{ $xpSummary->currentLevel->name }}
                        </span>
                    @endif
                    <span class="text-sm text-[#555b66] dark:text-[#a4abb6]">{{ number_format($xpSummary->total_xp) }} {{ __('client.loyalty.total_xp') }}</span>
                    <span class="text-sm font-semibold text-[#5c7a10] dark:text-[#c6f24e]">{{ number_format($xpSummary->available_points) }} {{ __('client.loyalty.pts_available') }}</span>
                </div>
            </div>
        @endif

        <!-- XP Earned -->
        <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
            <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] mb-4">{{ __('client.loyalty.xp_earned') }}</h2>

            @if($xpTransactions->isEmpty())
                <div class="py-8 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.loyalty.no_xp') }}</p>
                </div>
            @else
                <ul class="divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                    @foreach($xpTransactions as $transaction)
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $transaction->xpEventType?->name ?? __('client.loyalty.xp_event') }}</p>
                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                @if($transaction->xp_amount)
                                    <span class="text-sm font-semibold font-mono text-green-600 dark:text-green-400">+{{ number_format($transaction->xp_amount) }} {{ __('client.loyalty.xp') }}</span>
                                @endif
                                @if($transaction->points_amount)
                                    <span class="ml-2 text-sm font-semibold font-mono text-[#5c7a10] dark:text-[#c6f24e]">+{{ number_format($transaction->points_amount) }} {{ __('client.loyalty.pts') }}</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4">
                    {{ $xpTransactions->links() }}
                </div>
            @endif
        </div>

        <!-- Points Spent -->
        <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
            <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] mb-4">{{ __('client.loyalty.points_spent') }}</h2>

            @if($redemptions->isEmpty())
                <div class="py-8 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.loyalty.no_redemptions') }}</p>
                </div>
            @else
                <ul class="divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                    @foreach($redemptions as $redemption)
                        @php
                            $statusColors = ['pending' => 'yellow', 'fulfilled' => 'green', 'rejected' => 'red'];
                            $color = $statusColors[$redemption->status] ?? 'gray';
                        @endphp
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $redemption->reward?->name ?? __('client.loyalty.deleted_reward') }}</p>
                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ $redemption->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold font-mono text-red-600 dark:text-red-400">&minus;{{ number_format($redemption->points_spent) }} {{ __('client.loyalty.pts') }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $color }}-50 text-{{ $color }}-700 dark:bg-{{ $color }}-900/20 dark:text-{{ $color }}-400">
                                    {{ ucfirst($redemption->status) }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4">
                    {{ $redemptions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.client>
