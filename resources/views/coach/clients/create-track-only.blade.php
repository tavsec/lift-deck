<x-layouts.coach>
    <x-slot:title>{{ __('coach.clients.create_track_only.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.index') }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.create_track_only.back') }}
            </a>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.clients.create_track_only.heading') }}</h1>
            <p class="mt-0.5 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.clients.create_track_only.subtitle') }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <form method="POST" action="{{ route('coach.clients.store-track-only') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.create_track_only.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('name') border-red-500 @enderror"
                        placeholder="{{ __('coach.clients.create_track_only.name_placeholder') }}">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.clients.create_track_only.phone') }} <span class="text-[#8e8e93] dark:text-gray-500 font-normal">{{ __('coach.clients.create_track_only.optional') }}</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('phone') border-red-500 @enderror"
                        placeholder="e.g. 555-1234">
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        {{ __('coach.clients.create_track_only.add') }}
                    </button>
                    <a href="{{ route('coach.clients.index') }}" class="text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-200">{{ __('coach.clients.create_track_only.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.coach>
