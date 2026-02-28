<x-layouts.client>
    <x-slot:title>Messages</x-slot:title>

    <div class="flex flex-col h-[calc(100vh-10rem)]">
        <!-- Header -->
        <div class="flex items-center pb-4 border-b border-gray-200 dark:border-gray-700">
            <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <span class="text-lg font-medium text-blue-700 dark:text-blue-300">{{ strtoupper(substr($coach->name, 0, 1)) }}</span>
            </div>
            <div class="ml-3">
                <h1 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $coach->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your Coach</p>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="messages-container" class="flex-1 overflow-y-auto py-4 space-y-4">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-blue-600 dark:bg-blue-700 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
                        <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
                        <p class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $message->created_at->format('M d, g:i A') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No messages yet. Start a conversation with your coach!</p>
                </div>
            @endforelse
        </div>

        <!-- Message Input -->
        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <form id="message-form" class="flex gap-2">
                <input type="text" id="message-input" required placeholder="Type your message..." autocomplete="off"
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${msg.is_mine ? 'bg-blue-600 dark:bg-blue-700 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100'}">
                    <p class="text-sm whitespace-pre-wrap">${msg.body}</p>
                    <p class="text-xs mt-1 ${msg.is_mine ? 'text-blue-200' : 'text-gray-500 dark:text-gray-400'}">${msg.created_at}</p>
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
                const response = await fetch(`{{ route('client.messages.store') }}`, {
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
                const response = await fetch(`{{ route('client.messages.poll') }}?last_id=${lastMessageId}`);
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
</x-layouts.client>
