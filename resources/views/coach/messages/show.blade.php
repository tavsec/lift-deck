<x-layouts.coach>
    <x-slot:title>{{ __('coach.messages.show.title', ['name' => $client->name]) }}</x-slot:title>

    <div class="flex flex-col h-[calc(100vh-8rem)]">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center">
                <a href="{{ route('coach.messages.index') }}" class="mr-4 text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="h-10 w-10 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0" style="background-color: var(--color-primary)">
                    @if($client->avatar)
                        <img src="{{ $client->avatar }}" alt="{{ $client->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-lg font-semibold text-white">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="ml-3">
                    <h1 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ $client->name }}</h1>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-400">{{ $client->email }}</p>
                </div>
            </div>
            <a href="{{ route('coach.clients.show', $client) }}" class="text-sm font-medium text-[#1456f0] hover:underline">
                {{ __('coach.messages.show.view_profile') }}
            </a>
        </div>

        <!-- Messages Container -->
        <div id="messages-container" class="flex-1 overflow-y-auto py-4 space-y-4">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-xl {{ $message->sender_id === auth()->id() ? 'text-white' : 'bg-gray-100 dark:bg-gray-800 text-[#222222] dark:text-gray-100' }}" @if($message->sender_id === auth()->id()) style="background-color: var(--color-primary)" @endif>
                        <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
                        <p class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-white/70' : 'text-[#8e8e93] dark:text-gray-400' }}">
                            {{ $message->created_at->format('M d, g:i A') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.messages.show.no_messages') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Message Input -->
        <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
            <form id="message-form" class="flex gap-2">
                <input type="text" id="message-input" required placeholder="{{ __('coach.messages.show.type_placeholder') }}" autocomplete="off"
                    class="flex-1 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const container = document.getElementById('messages-container');
        const form = document.getElementById('message-form');
        const input = document.getElementById('message-input');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        container.scrollTop = container.scrollHeight;

        let lastMessageId = {{ $messages->last()?->id ?? 0 }};

        function appendMessage(msg) {
            // Remove empty state if present
            const emptyState = container.querySelector('.text-center.py-12');
            if (emptyState) {
                emptyState.remove();
            }

            const div = document.createElement('div');
            div.className = `flex ${msg.is_mine ? 'justify-end' : 'justify-start'}`;
            div.innerHTML = `
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-xl ${msg.is_mine ? 'text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100'}" ${msg.is_mine ? 'style="background-color: var(--color-primary)"' : ''}>
                    <p class="text-sm whitespace-pre-wrap">${msg.body}</p>
                    <p class="text-xs mt-1 ${msg.is_mine ? 'text-white/70' : 'text-gray-500 dark:text-gray-400'}">${msg.created_at}</p>
                </div>
            `;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }

        // Send message via AJAX
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = input.value.trim();
            if (!body) return;

            input.value = '';
            input.focus();

            try {
                const response = await fetch(`{{ route('coach.messages.store', $client) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ body }),
                });

                const data = await response.json();
                appendMessage(data.message);
                lastMessageId = data.message.id;
            } catch (error) {
                console.error('Failed to send message:', error);
                input.value = body;
            }
        });

        // Poll for new messages every 5 seconds
        setInterval(async () => {
            try {
                const response = await fetch(`{{ route('coach.messages.poll', $client) }}?last_id=${lastMessageId}`);
                const data = await response.json();

                if (data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        appendMessage(msg);
                        lastMessageId = msg.id;
                    });
                }
            } catch (error) {
                console.error('Failed to poll messages:', error);
            }
        }, 5000);
    </script>
    @endpush
</x-layouts.coach>
