<x-layouts.coach>
    <x-slot:title>{{ $client->name }} — Points History</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to {{ $client->name }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $client->name }} — Points History</h1>
        </div>

        <!-- Summary Card -->
        @if($xpSummary)
            <x-bladewind::card>
                <div class="flex flex-wrap items-center gap-4">
                    @if($xpSummary->currentLevel)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                            {{ $xpSummary->currentLevel->name }}
                        </span>
                    @endif
                    <span class="text-sm text-gray-600">{{ number_format($xpSummary->total_xp) }} total XP</span>
                    <span class="text-sm font-semibold text-blue-600">{{ number_format($xpSummary->available_points) }} pts available</span>
                </div>
            </x-bladewind::card>
        @endif

        <!-- XP Earned -->
        <x-bladewind::card>
            <h2 class="text-base font-semibold text-gray-900 mb-4">XP Earned</h2>

            @if($xpTransactions->isEmpty())
                <div class="py-8 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <p class="text-sm">No XP earned yet</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach($xpTransactions as $transaction)
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $transaction->xpEventType?->name ?? 'XP Event' }}</p>
                                <p class="text-xs text-gray-400">{{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                @if($transaction->xp_amount)
                                    <span class="text-sm font-semibold text-green-600">+{{ number_format($transaction->xp_amount) }} XP</span>
                                @endif
                                @if($transaction->points_amount)
                                    <span class="ml-2 text-sm font-semibold text-blue-600">+{{ number_format($transaction->points_amount) }} pts</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4">
                    {{ $xpTransactions->links() }}
                </div>
            @endif
        </x-bladewind::card>

        <!-- Points Spent -->
        <x-bladewind::card>
            <h2 class="text-base font-semibold text-gray-900 mb-4">Points Spent</h2>

            @if($redemptions->isEmpty())
                <div class="py-8 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                    <p class="text-sm">No rewards redeemed yet</p>
                </div>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach($redemptions as $redemption)
                        @php
                            $statusColors = ['pending' => 'yellow', 'fulfilled' => 'green', 'rejected' => 'red'];
                            $color = $statusColors[$redemption->status] ?? 'gray';
                        @endphp
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $redemption->reward?->name ?? 'Deleted reward' }}</p>
                                <p class="text-xs text-gray-400">{{ $redemption->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-red-600">&minus;{{ number_format($redemption->points_spent) }} pts</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
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
        </x-bladewind::card>
    </div>
</x-layouts.coach>
