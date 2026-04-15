<x-layouts.client>
    <x-slot:title>{{ __('client.loyalty.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <!-- Header -->
        <div class="mb-5">
            <h1 class="font-display text-xl font-semibold text-[#222222] dark:text-gray-100">{{ __('client.loyalty.heading') }}</h1>
            <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('client.loyalty.subtitle') }}</p>
        </div>

        <!-- Summary Card -->
        @if($xpSummary)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <div class="flex flex-wrap items-center gap-3">
                    @if($xpSummary->currentLevel)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">
                            {{ $xpSummary->currentLevel->name }}
                        </span>
                    @endif
                    <span class="text-sm text-[#45515e] dark:text-gray-400">{{ number_format($xpSummary->total_xp) }} {{ __('client.loyalty.total_xp') }}</span>
                    <span class="text-sm font-semibold text-[#1456f0] dark:text-blue-400">{{ number_format($xpSummary->available_points) }} {{ __('client.loyalty.pts_available') }}</span>
                </div>
            </div>
        @endif

        <!-- XP Earned -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('client.loyalty.xp_earned') }}</h2>

            @if($xpTransactions->isEmpty())
                <div class="py-8 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.loyalty.no_xp') }}</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($xpTransactions as $transaction)
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ $transaction->xpEventType?->name ?? __('client.loyalty.xp_event') }}</p>
                                <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                @if($transaction->xp_amount)
                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">+{{ number_format($transaction->xp_amount) }} {{ __('client.loyalty.xp') }}</span>
                                @endif
                                @if($transaction->points_amount)
                                    <span class="ml-2 text-sm font-semibold text-[#1456f0] dark:text-blue-400">+{{ number_format($transaction->points_amount) }} {{ __('client.loyalty.pts') }}</span>
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
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('client.loyalty.points_spent') }}</h2>

            @if($redemptions->isEmpty())
                <div class="py-8 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.loyalty.no_redemptions') }}</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($redemptions as $redemption)
                        @php
                            $statusColors = ['pending' => 'yellow', 'fulfilled' => 'green', 'rejected' => 'red'];
                            $color = $statusColors[$redemption->status] ?? 'gray';
                        @endphp
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ $redemption->reward?->name ?? __('client.loyalty.deleted_reward') }}</p>
                                <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ $redemption->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400">&minus;{{ number_format($redemption->points_spent) }} {{ __('client.loyalty.pts') }}</span>
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
