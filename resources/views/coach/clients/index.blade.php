<x-layouts.coach>
    <x-slot:title>{{ __('coach.clients.index.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.clients.index.heading') }}</h1>
                <p class="mt-0.5 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.clients.index.subtitle') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('coach.clients.create-track-only') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('coach.clients.index.add_track_only') }}
                </a>
                <a href="{{ route('coach.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('coach.clients.index.generate_code') }}
                </a>
            </div>
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

        @if(session('invitation_code'))
            <div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div x-show="open" class="fixed inset-0 bg-gray-500/60 dark:bg-gray-950/70 transition-opacity" @click="open = false"></div>

                    <div x-show="open" class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 px-4 pt-5 pb-4 text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6" style="box-shadow: rgba(44,30,116,0.12) 0px 0px 24px;">
                        <div>
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-2xl bg-green-100 dark:bg-green-900/30">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="font-display text-lg font-semibold text-[#222222] dark:text-gray-100" id="modal-title">{{ __('coach.clients.index.code_generated') }}</h3>
                                <div class="mt-4">
                                    <p class="text-sm text-[#8e8e93] dark:text-gray-400 mb-4">{{ __('coach.clients.index.code_description') }}</p>
                                    <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4 mb-4">
                                        <p id="invitation-code" class="text-3xl font-mono font-bold tracking-wider text-[#222222] dark:text-gray-100">{{ session('invitation_code') }}</p>
                                    </div>
                                    <button onclick="navigator.clipboard.writeText('{{ session('invitation_code') }}')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        {{ __('coach.clients.index.copy_code') }}
                                    </button>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                                    <p class="text-sm text-[#8e8e93] dark:text-gray-400 mb-2">{{ __('coach.clients.index.or_share_link') }}</p>
                                    <div class="flex items-center gap-2">
                                        <input type="text" readonly value="{{ url('/join/' . session('invitation_code')) }}" class="flex-1 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 dark:text-gray-100 px-3 py-2 focus:outline-none">
                                        <button onclick="navigator.clipboard.writeText('{{ url('/join/' . session('invitation_code')) }}')" class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6">
                            <button @click="open = false" class="w-full inline-flex justify-center rounded-lg px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-sm font-semibold text-white hover:bg-gray-800 transition-colors">
                                {{ __('coach.clients.index.done') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-4">
            <form method="GET" action="{{ route('coach.clients.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('coach.clients.index.search_placeholder') }}" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('coach.clients.index.search') }}
                </button>
            </form>
        </div>

        <!-- Pending Invitations -->
        @if($pendingInvitations->count() > 0)
            <div class="rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-3">{{ __('coach.clients.index.pending_invitations') }} ({{ $pendingInvitations->count() }})</h3>
                <div class="space-y-2">
                    @foreach($pendingInvitations as $invitation)
                        <div class="flex items-center justify-between bg-white dark:bg-gray-900 rounded-lg p-3 border border-yellow-200 dark:border-yellow-800/50">
                            <div class="flex items-center gap-4">
                                <span class="font-mono font-bold text-[#222222] dark:text-gray-100 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-lg text-sm">{{ route("join.code", ["code" => $invitation->token ])}}</span>
                                <button onclick="navigator.clipboard.writeText('{{ route("join.code", ["code" => $invitation->token ])}}')" class="text-[#8e8e93] dark:text-gray-500 hover:text-[#45515e] dark:hover:text-gray-300" title="Copy code">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="text-xs text-[#8e8e93] dark:text-gray-400">
                                {{ __('coach.clients.index.expires', ['time' => $invitation->expires_at->diffForHumans()]) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Clients List -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card overflow-hidden">
            @if($clients->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.clients.index.table.client') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.clients.index.table.goal') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.clients.index.table.status') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.clients.index.table.joined') }}</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">{{ __('coach.clients.index.table.actions') }}</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($clients as $client)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('coach.clients.show', $client) }}" class="flex items-center group">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center overflow-hidden" style="background-color: var(--color-primary)">
                                                    @if($client->avatar)
                                                        <img src="{{ $client->avatar }}" alt="{{ $client->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-sm font-semibold text-white">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-[#222222] dark:text-gray-100 group-hover:underline">{{ $client->name }}</span>
                                                    @if($client->is_track_only)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-[#45515e] dark:text-gray-300">{{ __('coach.clients.index.track_only') }}</span>
                                                    @endif
                                                    @if($clientIdsWithUnread->contains($client->id))
                                                        <span class="flex h-2 w-2 rounded-full" style="background-color: var(--color-primary)" title="Unread comments"></span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-[#8e8e93] dark:text-gray-400">{{ $client->email }}</div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($client->clientProfile?->goal)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($client->clientProfile->goal === 'fat_loss') bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400
                                                @elseif($client->clientProfile->goal === 'strength') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                                                @else bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 @endif">
                                                {{ str_replace('_', ' ', ucfirst($client->clientProfile->goal)) }}
                                            </span>
                                        @else
                                            <span class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.clients.index.goal_not_set') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($client->clientProfile?->isOnboardingComplete())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ __('coach.clients.index.status_active') }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">{{ __('coach.clients.index.status_pending') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-[#45515e] dark:text-gray-400">
                                        {{ $client->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-4">
                                            <a href="{{ route('coach.clients.show', $client) }}" class="text-sm font-medium text-[#1456f0] hover:underline">{{ __('coach.clients.index.view') }}</a>
                                            <form method="POST" action="{{ route('coach.clients.destroy', $client) }}" onsubmit="return confirm('{{ __('coach.clients.show.remove_confirm') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 transition-colors">{{ __('coach.clients.show.remove') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($clients->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                        {{ $clients->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.clients.index.no_clients') }}</h3>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-1">{{ __('coach.clients.index.no_clients_description') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('coach.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('coach.clients.index.generate_code') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.coach>
