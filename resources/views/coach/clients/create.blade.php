<x-layouts.coach>
    <x-slot:title>{{ __('coach.clients.create.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.index') }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.create.back') }}
            </a>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.clients.create.heading') }}</h1>
            <p class="mt-0.5 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.clients.create.subtitle') }}</p>
        </div>

        <!-- Generate Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-gray-100 dark:bg-gray-800 mb-4">
                    <svg class="h-8 w-8 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>

                <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-2">{{ __('coach.clients.create.prompt_heading') }}</h2>
                <p class="text-sm text-[#8e8e93] dark:text-gray-400 mb-6">{{ __('coach.clients.create.prompt_description') }}</p>

                <form method="POST" action="{{ route('coach.clients.store') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('coach.clients.create.generate') }}
                    </button>
                </form>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
                <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ __('coach.clients.create.how_it_works') }}</h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>{{ __('coach.clients.create.step_1') }}</li>
                                    <li>{{ __('coach.clients.create.step_2') }}</li>
                                    <li>{{ __('coach.clients.create.step_3') }}</li>
                                    <li>{{ __('coach.clients.create.step_4') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
