@props(['workoutLog', 'commentRoute'])

<!-- Comments -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
        Comments
        @if($workoutLog->comments->count() > 0)
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ $workoutLog->comments->count() }})</span>
        @endif
    </h2>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-3 mb-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if($workoutLog->comments->count() > 0)
        <div class="space-y-4 mb-6">
            @foreach($workoutLog->comments as $comment)
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-medium
                            {{ $comment->user->isCoach() ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                {{ $comment->user->isCoach() ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700' }}">
                                {{ $comment->user->isCoach() ? 'Coach' : 'Client' }}
                            </span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $comment->body }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Add Comment Form -->
    <form method="POST" action="{{ $commentRoute }}">
        @csrf
        <div class="space-y-3">
            <textarea
                name="body"
                rows="2"
                placeholder="Add a comment..."
                class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 @error('body') border-red-300 @enderror"
            >{{ old('body') }}</textarea>
            @error('body')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Post Comment
                </button>
            </div>
        </div>
    </form>
</div>
