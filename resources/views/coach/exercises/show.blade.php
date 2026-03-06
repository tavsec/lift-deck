<x-layouts.coach>
    <x-slot:title>{{ $exercise->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <a href="{{ route('coach.exercises.index') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Library
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $exercise->name }}</h1>
                    @if($exercise->isCustom())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Custom
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                            Global
                        </span>
                    @endif
                </div>
                <p class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                        {{ ucfirst(str_replace('_', ' ', $exercise->muscle_group)) }}
                    </span>
                </p>
            </div>
            @if($exercise->isCustom())
                <div class="flex gap-2">
                    <a href="{{ route('coach.exercises.edit', $exercise) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('coach.exercises.destroy', $exercise) }}" onsubmit="return confirm('Are you sure you want to delete this exercise?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 dark:border-red-800 rounded-md font-medium text-sm text-red-700 dark:text-red-400 bg-white dark:bg-gray-900 hover:bg-red-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Video -->
            <div class="lg:col-span-2">
                @if($exercise->getYoutubeEmbedUrl())
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                        <div class="aspect-video">
                            <iframe
                                src="{{ $exercise->getYoutubeEmbedUrl() }}"
                                class="w-full h-full"
                                title="{{ $exercise->name }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                        <div class="aspect-video flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No video available</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Details -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Details</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Muscle Group</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $exercise->muscle_group)) }}</dd>
                        </div>
                        @if($exercise->video_url)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Video Link</dt>
                                <dd class="mt-1 text-sm text-blue-600 hover:text-blue-800 break-all">
                                    <a href="{{ $exercise->video_url }}" target="_blank" rel="noopener noreferrer">
                                        {{ $exercise->video_url }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $exercise->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                @if($exercise->description)
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Description</h2>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $exercise->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.coach>
