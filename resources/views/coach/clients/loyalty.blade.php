<x-layouts.coach>
    <x-slot:title>{{ __('coach.clients.loyalty.heading', ['name' => $client->name]) }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.loyalty.back', ['name' => $client->name]) }}
            </a>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.clients.loyalty.heading', ['name' => $client->name]) }}</h1>
        </div>

        <!-- Summary Card -->
        @if($xpSummary)
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
                <div class="flex flex-wrap items-center gap-4">
                    @if($xpSummary->currentLevel)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                            {{ $xpSummary->currentLevel->name }}
                        </span>
                    @endif
                    <span class="text-sm text-[#555b66] dark:text-[#a4abb6]">{{ number_format($xpSummary->total_xp) }} {{ __('coach.clients.loyalty.total_xp') }}</span>
                    <span class="text-sm font-semibold text-[#5c7a10] dark:text-[#c6f24e]">{{ number_format($xpSummary->available_points) }} {{ __('coach.clients.loyalty.pts_available') }}</span>
                </div>
            </div>
        @endif

        <!-- XP Earned -->
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
            <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] mb-4">{{ __('coach.clients.loyalty.xp_earned') }}</h2>

            @if($xpTransactions->isEmpty())
                <div class="text-center py-8">
                    <div class="w-12 h-12 rounded-2xl bg-[#f3f5f7] dark:bg-[#1d2027] flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8c93a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.loyalty.no_xp') }}</p>
                </div>
            @else
                <ul class="divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                    @foreach($xpTransactions as $transaction)
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $transaction->xpEventType?->name ?? __('coach.clients.loyalty.xp_event') }}</p>
                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                @if($transaction->xp_amount)
                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">+{{ number_format($transaction->xp_amount) }} XP</span>
                                @endif
                                @if($transaction->points_amount)
                                    <span class="ml-2 text-sm font-semibold text-[#5c7a10] dark:text-[#c6f24e]">+{{ number_format($transaction->points_amount) }} pts</span>
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
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
            <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] mb-4">{{ __('coach.clients.loyalty.points_spent') }}</h2>

            @if($redemptions->isEmpty())
                <div class="text-center py-8">
                    <div class="w-12 h-12 rounded-2xl bg-[#f3f5f7] dark:bg-[#1d2027] flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8c93a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.loyalty.no_redemptions') }}</p>
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
                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $redemption->reward?->name ?? __('coach.clients.loyalty.deleted_reward') }}</p>
                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ $redemption->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400">&minus;{{ number_format($redemption->points_spent) }} pts</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 text-{{ $color }}-700 dark:text-{{ $color }}-400">
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
</x-layouts.coach>
