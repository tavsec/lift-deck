<x-layouts.client>
    <x-slot:title>{{ __('client.log.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <div class="mb-5">
            <h1 class="font-display text-xl font-semibold text-[#222222] dark:text-gray-100">{{ __('client.log.heading') }}</h1>
        </div>

        @if(session('error'))
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 mb-4">
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        @if($activeProgram)
            <p class="text-sm text-[#45515e] dark:text-gray-400">{{ __('client.log.select_from_program', ['name' => $activeProgram->program->name]) }}</p>

            <div class="space-y-3">
                @foreach($activeProgram->program->workouts as $workout)
                    <a href="{{ route('client.log.create', $workout) }}" class="block">
                        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-[#222222] dark:text-gray-100">{{ $workout->name }}</h3>
                                    <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('client.program.day_n', ['n' => $workout->day_number]) }} &middot; {{ __('client.program.n_exercises', ['n' => $workout->exercises->count()]) }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="relative flex items-center py-2">
                <div class="flex-grow border-t border-gray-200 dark:border-gray-800"></div>
                <span class="mx-4 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.log.or') }}</span>
                <div class="flex-grow border-t border-gray-200 dark:border-gray-800"></div>
            </div>

            <a href="{{ route('client.log.custom') }}" class="block">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 shadow-card p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-4 h-4 text-[#45515e] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('client.log.custom_workout') }}</h3>
                                <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.log.custom_workout_description') }}</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
        @else
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5 mb-4">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <p class="mt-4 text-[#45515e] dark:text-gray-400">{{ __('client.log.no_program') }}</p>
                    <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.log.no_program_description') }}</p>
                </div>
            </div>

            <a href="{{ route('client.log.custom') }}" class="block">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 shadow-card p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-4 h-4 text-[#45515e] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('client.log.custom_workout') }}</h3>
                                <p class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.log.custom_workout_description') }}</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
        @endif
    </div>
</x-layouts.client>
