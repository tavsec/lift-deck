<x-layouts.client>
    <x-slot:title>{{ __('client.messages.your_coach') }}</x-slot:title>

    <div class="flex flex-col h-[calc(100vh-10rem)] px-4">
        <!-- Back Navigation -->
        <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#181b22] dark:hover:text-[#f0f2f5] mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('client.messages.back') }}
        </a>

        <!-- Header -->
        <div class="flex items-center pb-4 border-b border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
            <div class="h-10 w-10 rounded-full bg-[rgba(198,242,78,0.15)] dark:bg-[rgba(198,242,78,0.12)] flex items-center justify-center overflow-hidden">
                @if($coach->avatar)
                    <img src="{{ $coach->avatar }}" alt="{{ $coach->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-lg font-medium text-[#5c7a10] dark:text-[#c6f24e]">{{ strtoupper(substr($coach->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="ml-3">
                <h1 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ $coach->name }}</h1>
                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.messages.your_coach') }}</p>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="messages-container" class="flex-1 overflow-y-auto py-4 space-y-4">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md px-4 py-2.5 rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-[#c6f24e] text-[#14180a] rounded-br-sm' : 'bg-gray-100 dark:bg-gray-800 text-[#181b22] dark:text-[#f0f2f5] rounded-bl-sm' }}">
                        <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
                        <p class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-[#14180a]/60' : 'text-[#8c93a0] dark:text-[#6b7280]' }}">
                            {{ $message->created_at->format('M d, g:i A') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="mt-2 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.messages.no_messages') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Message Input -->
        <div class="pt-4 border-t border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
            <form id="message-form" class="flex gap-2">
                <input type="text" id="message-input" required placeholder="{{ __('client.messages.type_placeholder') }}" autocomplete="off"
                    class="flex-1 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-[#c6f24e] text-[#14180a] font-semibold text-sm rounded-xl hover:bg-[#b4e438] transition-colors">
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
            const bubbleClass = msg.is_mine
                ? 'bg-[#c6f24e] text-[#14180a] rounded-br-sm'
                : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-sm';
            const timeClass = msg.is_mine ? 'text-[#14180a]/60' : 'text-gray-500 dark:text-gray-400';
            div.innerHTML = `
                <div class="max-w-xs lg:max-w-md px-4 py-2.5 rounded-2xl ${bubbleClass}">
                    <p class="text-sm whitespace-pre-wrap">${msg.body}</p>
                    <p class="text-xs mt-1 ${timeClass}">${msg.created_at}</p>
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
