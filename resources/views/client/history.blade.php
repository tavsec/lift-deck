<x-layouts.client>
    <x-slot:title>Workout History</x-slot:title>

    <div class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">Workout History</h1>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if($workoutLogs->count() > 0)
            <div class="space-y-3">
                @foreach($workoutLogs as $log)
                    <a href="{{ route('client.history.show', $log) }}" class="block">
                        <x-bladewind::card class="!p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-semibold text-gray-900">{{ $log->displayName() }}</h3>
                                        @if($unreadWorkoutLogIds->contains($log->id))
                                            <span class="flex h-2 w-2 rounded-full bg-blue-500" title="Unread comments"></span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500">{{ $log->completed_at->format('D, M j, Y \a\t g:i A') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($log->comments_count > 0)
                                        <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            {{ $log->comments_count }}
                                        </span>
                                    @endif
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </x-bladewind::card>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $workoutLogs->links() }}
            </div>
        @else
            <x-bladewind::card class="!p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-4 text-lg text-gray-600">No workouts logged yet</p>
                    <p class="mt-1 text-sm text-gray-500">Complete a workout from the Log tab to see it here.</p>
                </div>
            </x-bladewind::card>
        @endif
    </div>
</x-layouts.client>
