<x-layouts.coach>
    <x-slot:title>{{ __('coach.exercises.edit.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.exercises.show', $exercise) }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.exercises.edit.back') }}
            </a>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.exercises.edit.heading') }}</h1>
            <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.exercises.edit.subtitle') }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <form method="POST" action="{{ route('coach.exercises.update', $exercise) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.exercises.edit.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $exercise->name) }}" required
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('name') border-red-300 dark:border-red-700 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="muscle_group" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.exercises.edit.muscle_group') }} <span class="text-red-500">*</span></label>
                    <select name="muscle_group" id="muscle_group" required
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('muscle_group') border-red-300 dark:border-red-700 @enderror">
                        @foreach($muscleGroups as $value => $label)
                            <option value="{{ $value }}" {{ old('muscle_group', $exercise->muscle_group) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('muscle_group')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.exercises.edit.description') }}</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('description') border-red-300 dark:border-red-700 @enderror">{{ old('description', $exercise->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="video_url" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.exercises.edit.video_url') }}</label>
                    <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $exercise->video_url) }}"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150 @error('video_url') border-red-300 dark:border-red-700 @enderror"
                        placeholder="https://youtube.com/watch?v=...">
                    @error('video_url')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-[#8e8e93] dark:text-gray-500">{{ __('coach.exercises.edit.video_hint') }}</p>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('coach.exercises.show', $exercise) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        {{ __('coach.exercises.edit.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        {{ __('coach.exercises.edit.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.coach>
