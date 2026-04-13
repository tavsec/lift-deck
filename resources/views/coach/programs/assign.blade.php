<x-layouts.coach>
    <x-slot:title>{{ __('coach.programs.assign.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.programs.assign.back') }}
            </a>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.programs.assign.heading') }}</h1>
            <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.programs.assign.subtitle', ['name' => $program->name]) }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            @if($clients->count() > 0)
                <form method="POST" action="{{ route('coach.programs.assign.store', $program) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="client_id" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.assign.select_client') }} <span class="text-red-500">*</span></label>
                        <select name="client_id" id="client_id" required
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('client_id') border-red-300 dark:border-red-700 @enderror">
                            <option value="">{{ __('coach.programs.assign.client_placeholder') }}</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} ({{ $client->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="started_at" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.assign.start_date') }} <span class="text-red-500">*</span></label>
                        <input type="date" name="started_at" id="started_at" value="{{ old('started_at', now()->format('Y-m-d')) }}" required
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('started_at') border-red-300 dark:border-red-700 @enderror">
                        @error('started_at')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ __('coach.programs.assign.note') }}</h3>
                                <div class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                    <p>{{ __('coach.programs.assign.note_text') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            {{ __('coach.programs.assign.cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                            {{ __('coach.programs.assign.assign') }}
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.programs.assign.no_clients') }}</h3>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-1">{{ __('coach.programs.assign.no_clients_description') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('coach.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                            {{ __('coach.programs.assign.invite_client') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Program Summary -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('coach.programs.assign.summary') }}</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.programs.assign.name') }}</dt>
                    <dd class="mt-1 font-medium text-[#222222] dark:text-gray-100">{{ $program->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.programs.assign.type') }}</dt>
                    <dd class="mt-1 font-medium text-[#222222] dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $program->type)) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.programs.assign.duration') }}</dt>
                    <dd class="mt-1 font-medium text-[#222222] dark:text-gray-100">{{ $program->duration_weeks ? __('coach.programs.assign.n_weeks', ['n' => $program->duration_weeks]) : __('coach.programs.assign.not_set') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#8e8e93] dark:text-gray-400 uppercase tracking-wide">{{ __('coach.programs.assign.workouts') }}</dt>
                    <dd class="mt-1 font-medium text-[#222222] dark:text-gray-100">{{ $program->workouts->count() }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-layouts.coach>
