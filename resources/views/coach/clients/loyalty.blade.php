<x-layouts.coach>
    <x-slot:title>{{ __('coach.clients.loyalty.heading', ['name' => $client->name]) }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.loyalty.back', ['name' => $client->name]) }}
            </a>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.clients.loyalty.heading', ['name' => $client->name]) }}</h1>
        </div>

        <!-- Summary Card -->
        @if($xpSummary)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
                <div class="flex flex-wrap items-center gap-4">
                    @if($xpSummary->currentLevel)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                            {{ $xpSummary->currentLevel->name }}
                        </span>
                    @endif
                    <span class="text-sm text-[#45515e] dark:text-gray-400">{{ number_format($xpSummary->total_xp) }} {{ __('coach.clients.loyalty.total_xp') }}</span>
                    <span class="text-sm font-semibold text-[#1456f0]">{{ number_format($xpSummary->available_points) }} {{ __('coach.clients.loyalty.pts_available') }}</span>
                </div>
            </div>
        @endif

        <!-- XP Earned -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.clients.loyalty.xp_earned') }}</h2>

            @if($xpTransactions->isEmpty())
                <div class="text-center py-8">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.clients.loyalty.no_xp') }}</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($xpTransactions as $transaction)
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ $transaction->xpEventType?->name ?? __('coach.clients.loyalty.xp_event') }}</p>
                                <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                @if($transaction->xp_amount)
                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">+{{ number_format($transaction->xp_amount) }} XP</span>
                                @endif
                                @if($transaction->points_amount)
                                    <span class="ml-2 text-sm font-semibold text-[#1456f0]">+{{ number_format($transaction->points_amount) }} pts</span>
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
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.clients.loyalty.points_spent') }}</h2>

            @if($redemptions->isEmpty())
                <div class="text-center py-8">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.clients.loyalty.no_redemptions') }}</p>
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
                                <p class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ $redemption->reward?->name ?? __('coach.clients.loyalty.deleted_reward') }}</p>
                                <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ $redemption->created_at->diffForHumans() }}</p>
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
