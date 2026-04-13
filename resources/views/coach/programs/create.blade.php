<x-layouts.coach>
    <x-slot:title>{{ __('coach.programs.create.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.programs.index') }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.programs.create.back') }}
            </a>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.programs.create.heading') }}</h1>
            <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-400">{{ __('coach.programs.create.subtitle') }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <form method="POST" action="{{ route('coach.programs.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.create.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('name') border-red-300 dark:border-red-700 @enderror"
                        placeholder="e.g., 12-Week Strength Builder">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.create.type') }} <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('type') border-red-300 dark:border-red-700 @enderror">
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('type', 'general') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_weeks" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.create.duration') }}</label>
                        <input type="number" name="duration_weeks" id="duration_weeks" value="{{ old('duration_weeks') }}" min="1" max="52"
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('duration_weeks') border-red-300 dark:border-red-700 @enderror"
                            placeholder="e.g., 12">
                        @error('duration_weeks')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.programs.create.description') }}</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('description') border-red-300 dark:border-red-700 @enderror"
                        placeholder="Describe the program goals, target audience, and what makes it unique...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_template" id="is_template" value="1" {{ old('is_template') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-[#1456f0]/20" style="color: var(--color-primary)">
                    <label for="is_template" class="ml-2 block text-sm text-[#45515e] dark:text-gray-300">
                        {{ __('coach.programs.create.is_template') }}
                    </label>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('coach.programs.index') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg font-medium text-sm text-[#45515e] dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                        {{ __('coach.programs.create.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-[#181e25] dark:bg-gray-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-[#2d3748] dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                        {{ __('coach.programs.create.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.coach>
