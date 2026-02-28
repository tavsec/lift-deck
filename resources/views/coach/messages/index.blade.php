<x-layouts.coach>
    <x-slot:title>Messages</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Messages</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Communicate with your clients</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Conversations List -->
            <div class="lg:col-span-2 space-y-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Conversations</h2>

                @if($conversations->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($conversations as $conversation)
                            <a href="{{ route('coach.messages.show', $conversation['client']) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="px-4 py-4 flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-lg font-medium text-blue-700">{{ strtoupper(substr($conversation['client']->name, 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $conversation['client']->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $conversation['latest_message']->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <div class="flex items-center justify-between mt-1">
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                @if($conversation['latest_message']->sender_id === auth()->id())
                                                    <span class="text-gray-400 dark:text-gray-500">You: </span>
                                                @endif
                                                {{ Str::limit($conversation['latest_message']->body, 50) }}
                                            </p>
                                            @if($conversation['unread_count'] > 0)
                                                <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-blue-600 text-xs font-medium text-white">
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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No conversations yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start a conversation with one of your clients.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Start New Conversation -->
            <div class="space-y-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Start New Conversation</h2>

                @if($clientsWithoutMessages->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($clientsWithoutMessages as $client)
                            <a href="{{ route('coach.messages.show', $client) }}" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $client->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $client->email }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center">All clients have active conversations.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.coach>
