<x-layouts.coach>
    <x-slot:title>{{ __('coach.redemptions.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.redemptions.heading') }}</h1>
            <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('coach.redemptions.subtitle') }}</p>
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

        @if(session('error'))
            <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($redemptions->count() > 0)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.redemptions.table.client') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.redemptions.table.reward') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.redemptions.table.points_spent') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.redemptions.table.status') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.redemptions.table.notes') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.redemptions.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($redemptions as $redemption)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#222222] dark:text-gray-100">
                                    {{ $redemption->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#45515e] dark:text-gray-400">
                                    {{ $redemption->reward->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#45515e] dark:text-gray-400">
                                    {{ $redemption->points_spent }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($redemption->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">{{ __('coach.redemptions.status_pending') }}</span>
                                    @elseif($redemption->status === 'fulfilled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ __('coach.redemptions.status_fulfilled') }}</span>
                                    @elseif($redemption->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">{{ __('coach.redemptions.status_rejected') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-[#45515e] dark:text-gray-300">{{ ucfirst($redemption->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-[#45515e] dark:text-gray-400 max-w-xs">
                                    @if($redemption->coach_notes)
                                        <span class="line-clamp-2">{{ $redemption->coach_notes }}</span>
                                    @else
                                        <span class="text-[#8e8e93] dark:text-gray-600">&mdash;</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($redemption->status === 'pending')
                                        <div class="flex items-center gap-2">
                                            <form method="POST" action="{{ route('coach.redemptions.update', $redemption) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="fulfilled">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white hover:bg-green-700 transition-colors">
                                                    {{ __('coach.redemptions.fulfill') }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('coach.redemptions.update', $redemption) }}" onsubmit="return confirm('{{ __('coach.redemptions.reject_confirm') }}')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white hover:bg-red-700 transition-colors">
                                                    {{ __('coach.redemptions.reject') }}
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-[#8e8e93] dark:text-gray-600">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($redemptions->hasPages())
                <div class="mt-6">
                    {{ $redemptions->links() }}
                </div>
            @endif
        @else
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card">
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.redemptions.no_redemptions') }}</h3>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-1">{{ __('coach.redemptions.no_redemptions_description') }}</p>
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
