<x-layouts.coach>
    <x-slot:title>{{ __('coach.achievements.create.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.achievements.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.achievements.create.back') }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('coach.achievements.create.heading') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('coach.achievements.create.subtitle') }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form x-data="{ type: '{{ old('type', '') }}' }" method="POST" action="{{ route('coach.achievements.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('coach.achievements.create.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror"
                        placeholder="e.g., First Workout">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">{{ __('coach.achievements.create.description') }}</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('description') border-red-300 @enderror"
                        placeholder="Describe what the client needs to do to earn this achievement...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">{{ __('coach.achievements.create.type') }} <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required x-model="type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('type') border-red-300 @enderror">
                        <option value="">{{ __('coach.achievements.create.type_placeholder') }}</option>
                        <option value="automatic" @selected(old('type') === 'automatic')>{{ __('coach.achievements.create.type_automatic') }}</option>
                        <option value="manual" @selected(old('type') === 'manual')>{{ __('coach.achievements.create.type_manual') }}</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="type === 'automatic'" x-cloak>
                    <label for="condition_type" class="block text-sm font-medium text-gray-700">{{ __('coach.achievements.create.condition_type') }}</label>
                    <select name="condition_type" id="condition_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('condition_type') border-red-300 @enderror">
                        <option value="">{{ __('coach.achievements.create.condition_placeholder') }}</option>
                        <option value="workout_count" @selected(old('condition_type') === 'workout_count')>{{ __('coach.achievements.create.condition_workout_count') }}</option>
                        <option value="checkin_count" @selected(old('condition_type') === 'checkin_count')>{{ __('coach.achievements.create.condition_checkin_count') }}</option>
                        <option value="xp_total" @selected(old('condition_type') === 'xp_total')>{{ __('coach.achievements.create.condition_xp_total') }}</option>
                        <option value="streak_days" @selected(old('condition_type') === 'streak_days')>{{ __('coach.achievements.create.condition_streak_days') }}</option>
                    </select>
                    @error('condition_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="type === 'automatic'" x-cloak>
                    <label for="condition_value" class="block text-sm font-medium text-gray-700">{{ __('coach.achievements.create.condition_value') }}</label>
                    <input type="number" name="condition_value" id="condition_value" value="{{ old('condition_value') }}" min="1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('condition_value') border-red-300 @enderror"
                        placeholder="e.g., 100">
                    @error('condition_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="xp_reward" class="block text-sm font-medium text-gray-700">{{ __('coach.achievements.create.xp_reward') }}</label>
                        <input type="number" name="xp_reward" id="xp_reward" value="{{ old('xp_reward', 0) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('xp_reward') border-red-300 @enderror"
                            placeholder="0">
                        @error('xp_reward')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="points_reward" class="block text-sm font-medium text-gray-700">{{ __('coach.achievements.create.points_reward') }}</label>
                        <input type="number" name="points_reward" id="points_reward" value="{{ old('points_reward', 0) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('points_reward') border-red-300 @enderror"
                            placeholder="0">
                        @error('points_reward')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700">{{ __('coach.achievements.create.icon') }} <span class="text-gray-400 font-normal">{{ __('coach.achievements.create.icon_optional') }}</span></label>
                    <input type="file" name="icon" id="icon" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('coach.achievements.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('coach.achievements.create.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('coach.achievements.create.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.coach>
