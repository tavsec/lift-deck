<x-layouts.coach>
    <x-slot:title>{{ __('coach.programs.assign.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.programs.assign.back') }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('coach.programs.assign.heading') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('coach.programs.assign.subtitle', ['name' => $program->name]) }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            @if($clients->count() > 0)
                <form method="POST" action="{{ route('coach.programs.assign.store', $program) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('coach.programs.assign.select_client') }} <span class="text-red-500">*</span></label>
                        <select name="client_id" id="client_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('client_id') border-red-300 @enderror">
                            <option value="">{{ __('coach.programs.assign.client_placeholder') }}</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} ({{ $client->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="started_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('coach.programs.assign.start_date') }} <span class="text-red-500">*</span></label>
                        <input type="date" name="started_at" id="started_at" value="{{ old('started_at', now()->format('Y-m-d')) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('started_at') border-red-300 @enderror">
                        @error('started_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-yellow-50 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">{{ __('coach.programs.assign.note') }}</h3>
                                <div class="mt-1 text-sm text-yellow-700">
                                    <p>{{ __('coach.programs.assign.note_text') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <a href="{{ route('coach.programs.show', $program) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('coach.programs.assign.cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('coach.programs.assign.assign') }}
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('coach.programs.assign.no_clients') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('coach.programs.assign.no_clients_description') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('coach.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            {{ __('coach.programs.assign.invite_client') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Program Summary -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('coach.programs.assign.summary') }}</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('coach.programs.assign.name') }}</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $program->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('coach.programs.assign.type') }}</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $program->type)) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('coach.programs.assign.duration') }}</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $program->duration_weeks ? __('coach.programs.assign.n_weeks', ['n' => $program->duration_weeks]) : __('coach.programs.assign.not_set') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('coach.programs.assign.workouts') }}</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $program->workouts->count() }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-layouts.coach>
