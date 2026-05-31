<x-layouts.client>
    <x-slot:title>{{ __('client.log.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <div class="mb-5">
            <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('client.log.heading') }}</h1>
        </div>

        @if(session('error'))
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 mb-4">
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        @if($activeProgram)
            <p class="text-sm text-[#555b66] dark:text-[#a4abb6]">{{ __('client.log.select_from_program', ['name' => $activeProgram->program->name]) }}</p>

            <div class="space-y-3">
                @foreach($activeProgram->program->workouts as $workout)
                    <a href="{{ route('client.log.create', $workout) }}" class="block">
                        <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ $workout->name }}</h3>
                                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.program.day_n', ['n' => $workout->day_number]) }} &middot; {{ __('client.program.n_exercises', ['n' => $workout->exercises->count()]) }}</p>
                                    @php
                                        $muscles = $workout->exercises->map(fn($we) => ucfirst(str_replace('_', ' ', $we->exercise->muscle_group)))->unique()->take(4)->values();
                                    @endphp
                                    @if($muscles->isNotEmpty())
                                        <div class="flex gap-1.5 mt-2">
                                            @foreach($muscles as $muscle)
                                                <x-ex-thumb :muscle="$muscle" :size="24" />
                                            @endforeach
                                            @if($workout->exercises->count() > 4)
                                                <div class="w-6 h-6 rounded-md bg-[rgba(18,22,31,0.06)] dark:bg-[rgba(255,255,255,0.06)] flex items-center justify-center text-[10px] font-bold text-[#8c93a0] dark:text-[#6b7280]">
                                                    +{{ $workout->exercises->count() - 4 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
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
                <div class="flex-grow border-t border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]"></div>
                <span class="mx-4 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.log.or') }}</span>
                <div class="flex-grow border-t border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]"></div>
            </div>

            <a href="{{ route('client.log.custom') }}" class="block">
                <div class="bg-white dark:bg-[#181b21] rounded-xl border border-dashed border-gray-300 dark:border-gray-700 shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-4 h-4 text-[#555b66] dark:text-[#a4abb6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.log.custom_workout') }}</h3>
                                <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.log.custom_workout_description') }}</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
        @else
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 mb-4">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <p class="mt-4 text-[#555b66] dark:text-[#a4abb6]">{{ __('client.log.no_program') }}</p>
                    <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.log.no_program_description') }}</p>
                </div>
            </div>

            <a href="{{ route('client.log.custom') }}" class="block">
                <div class="bg-white dark:bg-[#181b21] rounded-xl border border-dashed border-gray-300 dark:border-gray-700 shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-4 h-4 text-[#555b66] dark:text-[#a4abb6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.log.custom_workout') }}</h3>
                                <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.log.custom_workout_description') }}</p>
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
