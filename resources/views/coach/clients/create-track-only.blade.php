<x-layouts.coach>
    <x-slot:title>{{ __('coach.clients.create_track_only.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.index') }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.create_track_only.back') }}
            </a>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.clients.create_track_only.heading') }}</h1>
            <p class="mt-0.5 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.create_track_only.subtitle') }}</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
            <form method="POST" action="{{ route('coach.clients.store-track-only') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.create_track_only.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('name') border-red-500 @enderror"
                        placeholder="{{ __('coach.clients.create_track_only.name_placeholder') }}">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.clients.create_track_only.phone') }} <span class="text-[#8c93a0] dark:text-[#6b7280] font-normal">{{ __('coach.clients.create_track_only.optional') }}</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('phone') border-red-500 @enderror"
                        placeholder="e.g. 555-1234">
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                        {{ __('coach.clients.create_track_only.add') }}
                    </button>
                    <a href="{{ route('coach.clients.index') }}" class="text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-gray-200">{{ __('coach.clients.create_track_only.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.coach>
