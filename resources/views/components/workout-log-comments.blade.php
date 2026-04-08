@props(['workoutLog', 'commentRoute'])

<!-- Comments -->
<div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
    <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">
        Comments
        @if($workoutLog->comments->count() > 0)
            <span class="text-sm font-normal text-[#8e8e93] dark:text-gray-500">({{ $workoutLog->comments->count() }})</span>
        @endif
    </h2>

    @if(session('success'))
        <div class="rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3 mb-4">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if($workoutLog->comments->count() > 0)
        <div class="space-y-4 mb-5">
            @foreach($workoutLog->comments as $comment)
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-medium overflow-hidden
                            {{ $comment->user->isCoach() ? 'bg-blue-50 dark:bg-blue-900/30 text-[#1456f0] dark:text-blue-300' : 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' }}">
                            @if($comment->user->avatar)
                                <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ $comment->user->name }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                {{ $comment->user->isCoach() ? 'bg-blue-50 dark:bg-blue-900/30 text-[#1456f0] dark:text-blue-300' : 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' }}">
                                {{ $comment->user->isCoach() ? 'Coach' : 'Client' }}
                            </span>
                            <span class="text-xs text-[#8e8e93] dark:text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1 text-sm text-[#45515e] dark:text-gray-300">{{ $comment->body }}</p>
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
                class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm text-sm focus:border-[#1456f0] focus:ring-[#1456f0] @error('body') border-red-300 @enderror"
            >{{ old('body') }}</textarea>
            @error('body')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors"
                >
                    Post Comment
                </button>
            </div>
        </div>
    </form>
</div>
