<x-layouts.client>
    <x-slot:title>Rewards</x-slot:title>

    <div class="py-6 space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reward Shop</h1>
            <p class="mt-1 text-sm text-gray-600">Spend your earned XP points on rewards</p>
        </div>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="ml-3"><p class="text-sm font-medium text-green-800">{{ session('success') }}</p></div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="ml-3"><p class="text-sm font-medium text-red-800">{{ session('error') }}</p></div>
                </div>
            </div>
        @endif

        <!-- Points Balance Card -->
        <x-bladewind::card>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-full">
                        <svg class="w-7 h-7 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Available Points</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $xpSummary?->available_points ?? 0 }}</p>
                    </div>
                </div>

                @if($xpSummary?->currentLevel)
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Level {{ $xpSummary->currentLevel->level_number }}: {{ $xpSummary->currentLevel->name }}
                        </span>
                        <p class="mt-1 text-xs text-gray-500">{{ number_format($xpSummary->total_xp) }} total XP</p>
                    </div>
                @endif
            </div>
        </x-bladewind::card>

        <div class="text-right -mt-2 mb-2">
            <a href="{{ route('client.loyalty') }}" class="text-sm text-blue-600 hover:text-blue-800">View Points History &rarr;</a>
        </div>

        <!-- Rewards Grid -->
        @if($rewards->isEmpty())
            <x-bladewind::card>
                <div class="py-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                    <p class="text-base font-medium">No rewards available yet</p>
                    <p class="mt-1 text-sm">Check back later for new rewards from your coach.</p>
                </div>
            </x-bladewind::card>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($rewards as $reward)
                    @php
                        $canAfford = $xpSummary && $xpSummary->available_points >= $reward->points_cost;
                        $hasStock = $reward->hasStock();
                        $canRedeem = $canAfford && $hasStock;
                    @endphp

                    <div class="flex flex-col bg-white rounded-lg shadow p-4 space-y-3">
                        <!-- Reward Header -->
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-base font-semibold text-gray-900 leading-tight">{{ $reward->name }}</h3>
                            <div class="flex flex-shrink-0 flex-col items-end gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    {{ number_format($reward->points_cost) }} pts
                                </span>
                                @if(is_null($reward->coach_id))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">System</span>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        @if($reward->description)
                            <p class="text-sm text-gray-600 leading-snug">{{ Str::limit($reward->description, 80) }}</p>
                        @endif

                        <!-- Stock -->
                        <p class="text-xs text-gray-500">
                            @if(is_null($reward->stock))
                                Unlimited availability
                            @elseif($reward->stock === 0)
                                <span class="text-red-600 font-medium">Out of stock</span>
                            @else
                                {{ $reward->stock }} available
                            @endif
                        </p>

                        <!-- Redeem Button -->
                        <div class="mt-auto pt-1">
                            @if($canRedeem)
                                <form
                                    method="POST"
                                    action="{{ route('client.rewards.redeem', $reward) }}"
                                    onsubmit="return confirm('Spend {{ $reward->points_cost }} points on {{ addslashes($reward->name) }}?')"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                    >
                                        Redeem
                                    </button>
                                </form>
                            @else
                                <button
                                    type="button"
                                    disabled
                                    class="w-full inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed"
                                >
                                    @if(!$hasStock)
                                        Out of Stock
                                    @elseif(!$xpSummary)
                                        No XP Yet
                                    @else
                                        Not Enough Points
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
