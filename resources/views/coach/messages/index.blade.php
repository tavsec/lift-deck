<x-layouts.coach>
    <x-slot:title>{{ __('coach.messages.index.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.messages.index.heading') }}</h1>
            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.messages.index.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Conversations List -->
            <div class="lg:col-span-2 space-y-4">
                <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.messages.index.conversations') }}</h2>

                @if($conversations->count() > 0)
                    <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] overflow-hidden divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                        @foreach($conversations as $conversation)
                            <a href="{{ route('coach.messages.show', $conversation['client']) }}" class="block hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                                <div class="px-4 py-4 flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-full flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#7c5cff] to-[#c6f24e]">
                                            @if($conversation['client']->avatar)
                                                <img src="{{ $conversation['client']->avatar }}" alt="{{ $conversation['client']->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-lg font-semibold text-white">{{ strtoupper(substr($conversation['client']->name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] truncate">
                                                {{ $conversation['client']->name }}
                                            </p>
                                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                                {{ $conversation['latest_message']->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex items-center justify-between mt-1">
                                            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] truncate">
                                                @if($conversation['latest_message']->sender_id === auth()->id())
                                                    <span class="text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.messages.index.you') }} </span>
                                                @endif
                                                {{ Str::limit($conversation['latest_message']->body, 50) }}
                                            </p>
                                            @if($conversation['unread_count'] > 0)
                                                <span class="inline-flex items-center justify-center h-5 w-5 rounded-full text-xs font-semibold text-[#14180a] flex-shrink-0 ml-2 bg-[#c6f24e]">
                                                    {{ $conversation['unread_count'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)]">
                        <div class="text-center py-12">
                            <div class="w-12 h-12 rounded-2xl bg-[#f3f5f7] dark:bg-[#1d2027] flex items-center justify-center mx-auto mb-3">
                                <svg class="h-6 w-6 text-[#8c93a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.messages.index.no_conversations') }}</h3>
                            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.messages.index.no_conversations_description') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Start New Conversation -->
            <div class="space-y-4">
                <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.messages.index.start_new') }}</h2>

                @if($clientsWithoutMessages->count() > 0)
                    <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] overflow-hidden divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                        @foreach($clientsWithoutMessages as $client)
                            <a href="{{ route('coach.messages.show', $client) }}" class="block px-4 py-3 hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-[#f3f5f7] dark:bg-[#1d2027] flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if($client->avatar)
                                            <img src="{{ $client->avatar }}" alt="{{ $client->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-sm font-semibold text-[#555b66] dark:text-[#a4abb6]">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $client->name }}</p>
                                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ $client->email }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-4">
                        <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] text-center">{{ __('coach.messages.index.all_active') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.coach>
