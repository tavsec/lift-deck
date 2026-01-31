<x-layouts.coach>
    <x-slot:title>{{ $program->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <a href="{{ route('coach.programs.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Programs
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $program->name }}</h1>
                    @if($program->is_template)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Template
                        </span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ ucfirst(str_replace('_', ' ', $program->type)) }}
                    </span>
                    @if($program->duration_weeks)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $program->duration_weeks }} weeks
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $program->workouts->count() }} workouts
                    </span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('coach.programs.assign', $program) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Assign to Client
                </a>
                <a href="{{ route('coach.programs.edit', $program) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form method="POST" action="{{ route('coach.programs.destroy', $program) }}" onsubmit="return confirm('Are you sure you want to delete this program?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md font-medium text-sm text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
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

        @if($program->description)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-2">Description</h2>
                <p class="text-gray-700">{{ $program->description }}</p>
            </div>
        @endif

        <!-- Assigned Clients -->
        @if($assignedClients->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Assigned Clients ({{ $assignedClients->count() }})</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach($assignedClients as $assignment)
                        <a href="{{ route('coach.clients.show', $assignment->client) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                <span class="text-sm font-medium text-blue-700">{{ strtoupper(substr($assignment->client->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $assignment->client->name }}</p>
                                <p class="text-xs text-gray-500">
                                    @if($assignment->isActive())
                                        <span class="text-green-600">Active</span>
                                    @elseif($assignment->isPaused())
                                        <span class="text-yellow-600">Paused</span>
                                    @else
                                        <span class="text-gray-600">Completed</span>
                                    @endif
                                    - Started {{ $assignment->started_at->format('M d, Y') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Workouts -->
        <div class="space-y-4">
            <h2 class="text-lg font-medium text-gray-900">Workouts</h2>

            @if($program->workouts->count() > 0)
                @foreach($program->workouts as $workout)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-md font-medium text-gray-900">{{ $workout->name }}</h3>
                                    <p class="text-sm text-gray-500">Day {{ $workout->day_number }} &middot; {{ $workout->exercises->count() }} exercises</p>
                                </div>
                            </div>
                            @if($workout->notes)
                                <p class="mt-2 text-sm text-gray-600">{{ $workout->notes }}</p>
                            @endif
                        </div>

                        @if($workout->exercises->count() > 0)
                            <div class="divide-y divide-gray-200">
                                @foreach($workout->exercises as $workoutExercise)
                                    <div class="px-6 py-4 flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $workoutExercise->exercise->name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $workoutExercise->sets }} sets &times; {{ $workoutExercise->reps }} reps
                                                @if($workoutExercise->formatted_rest)
                                                    &middot; {{ $workoutExercise->formatted_rest }} rest
                                                @endif
                                            </p>
                                            @if($workoutExercise->notes)
                                                <p class="text-xs text-gray-400 mt-1">{{ $workoutExercise->notes }}</p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                            {{ ucfirst(str_replace('_', ' ', $workoutExercise->exercise->muscle_group)) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-8 text-center text-sm text-gray-500">
                                No exercises added yet
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="bg-white rounded-lg shadow">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No workouts yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Add workouts and exercises to complete this program.</p>
                        <div class="mt-6">
                            <a href="{{ route('coach.programs.edit', $program) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Edit Program
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.coach>
