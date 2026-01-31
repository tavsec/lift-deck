<x-layouts.coach>
    <x-slot:title>Chat with {{ $client->name }}</x-slot:title>

    <div class="flex flex-col h-[calc(100vh-8rem)]">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div class="flex items-center">
                <a href="{{ route('coach.messages.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <span class="text-lg font-medium text-blue-700">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                </div>
                <div class="ml-3">
                    <h1 class="text-lg font-medium text-gray-900">{{ $client->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $client->email }}</p>
                </div>
            </div>
            <a href="{{ route('coach.clients.show', $client) }}" class="text-sm text-blue-600 hover:text-blue-800">
                View Profile
            </a>
        </div>

        <!-- Messages Container -->
        <div id="messages-container" class="flex-1 overflow-y-auto py-4 space-y-4">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                        <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
                        <p class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-500' }}">
                            {{ $message->created_at->format('M d, g:i A') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No messages yet. Start the conversation!</p>
                </div>
            @endforelse
        </div>

        <!-- Message Input -->
        <div class="pt-4 border-t border-gray-200">
            <form method="POST" action="{{ route('coach.messages.store', $client) }}" class="flex gap-2">
                @csrf
                <input type="text" name="body" required placeholder="Type your message..." autocomplete="off"
                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
        // Auto-scroll to bottom on load
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;

        // Poll for new messages every 10 seconds
        let lastMessageId = {{ $messages->last()?->id ?? 0 }};

        setInterval(async () => {
            try {
                const response = await fetch(`{{ route('coach.messages.poll', $client) }}?last_id=${lastMessageId}`);
                const data = await response.json();

                if (data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        const div = document.createElement('div');
                        div.className = `flex ${msg.is_mine ? 'justify-end' : 'justify-start'}`;
                        div.innerHTML = `
                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${msg.is_mine ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900'}">
                                <p class="text-sm whitespace-pre-wrap">${msg.body}</p>
                                <p class="text-xs mt-1 ${msg.is_mine ? 'text-blue-200' : 'text-gray-500'}">${msg.created_at}</p>
                            </div>
                        `;
                        container.appendChild(div);
                        lastMessageId = msg.id;
                    });
                    container.scrollTop = container.scrollHeight;
                }
            } catch (error) {
                console.error('Failed to poll messages:', error);
            }
        }, 10000);
    </script>
    @endpush
</x-layouts.coach>
