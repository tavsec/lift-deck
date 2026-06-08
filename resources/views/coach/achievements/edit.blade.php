<x-layouts.coach>
    <x-slot:title>{{ __('coach.achievements.edit.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.achievements.index') }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.achievements.edit.back') }}
            </a>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.achievements.edit.heading') }}</h1>
            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.achievements.edit.subtitle') }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
            <form x-data="{ type: '{{ old('type', $achievement->type) }}' }" method="POST" action="{{ route('coach.achievements.update', $achievement) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.achievements.edit.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $achievement->name) }}" required
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('name') border-red-300 dark:border-red-700 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.achievements.edit.description') }}</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('description') border-red-300 dark:border-red-700 @enderror">{{ old('description', $achievement->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.achievements.edit.type') }} <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required x-model="type"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('type') border-red-300 dark:border-red-700 @enderror">
                        <option value="automatic" @selected(old('type', $achievement->type) === 'automatic')>{{ __('coach.achievements.edit.type_automatic') }}</option>
                        <option value="manual" @selected(old('type', $achievement->type) === 'manual')>{{ __('coach.achievements.edit.type_manual') }}</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="type === 'automatic'" x-cloak>
                    <label for="condition_type" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.achievements.edit.condition_type') }}</label>
                    <select name="condition_type" id="condition_type"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('condition_type') border-red-300 dark:border-red-700 @enderror">
                        <option value="">{{ __('coach.achievements.edit.condition_placeholder') }}</option>
                        <option value="workout_count" @selected(old('condition_type', $achievement->condition_type) === 'workout_count')>{{ __('coach.achievements.edit.condition_workout_count') }}</option>
                        <option value="checkin_count" @selected(old('condition_type', $achievement->condition_type) === 'checkin_count')>{{ __('coach.achievements.edit.condition_checkin_count') }}</option>
                        <option value="xp_total" @selected(old('condition_type', $achievement->condition_type) === 'xp_total')>{{ __('coach.achievements.edit.condition_xp_total') }}</option>
                        <option value="streak_days" @selected(old('condition_type', $achievement->condition_type) === 'streak_days')>{{ __('coach.achievements.edit.condition_streak_days') }}</option>
                    </select>
                    @error('condition_type')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="type === 'automatic'" x-cloak>
                    <label for="condition_value" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.achievements.edit.condition_value') }}</label>
                    <input type="number" name="condition_value" id="condition_value" value="{{ old('condition_value', $achievement->condition_value) }}" min="1"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('condition_value') border-red-300 dark:border-red-700 @enderror">
                    @error('condition_value')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="xp_reward" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.achievements.edit.xp_reward') }}</label>
                        <input type="number" name="xp_reward" id="xp_reward" value="{{ old('xp_reward', $achievement->xp_reward) }}" min="0"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('xp_reward') border-red-300 dark:border-red-700 @enderror">
                        @error('xp_reward')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="points_reward" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.achievements.edit.points_reward') }}</label>
                        <input type="number" name="points_reward" id="points_reward" value="{{ old('points_reward', $achievement->points_reward) }}" min="0"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('points_reward') border-red-300 dark:border-red-700 @enderror">
                        @error('points_reward')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="icon" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.achievements.edit.icon') }} <span class="text-[#8c93a0] dark:text-[#6b7280] font-normal">{{ __('coach.achievements.edit.icon_optional') }}</span></label>
                    @if($achievement->icon)
                        <div class="mt-1 mb-3">
                            <img src="{{ Storage::url($achievement->icon) }}" alt="Current icon" class="h-12 w-12 rounded-xl object-cover">
                        </div>
                    @endif
                    <input type="file" name="icon" id="icon" accept="image/*"
                        class="mt-1 block w-full text-sm text-[#8c93a0] dark:text-[#6b7280] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 dark:file:bg-gray-800 file:text-[#45515e] dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-700">
                    @error('icon')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                    <a href="{{ route('coach.achievements.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                        {{ __('coach.achievements.edit.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                        {{ __('coach.achievements.edit.update') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Archive -->
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
            <h2 class="font-display text-base font-semibold text-red-700 dark:text-red-400">{{ __('coach.achievements.edit.archive_heading') }}</h2>
            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.achievements.edit.archive_description') }}</p>
            <div class="mt-4">
                <form method="POST" action="{{ route('coach.achievements.destroy', $achievement) }}" onsubmit="return confirm('{{ __('coach.achievements.edit.archive_confirm') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                        {{ __('coach.achievements.edit.archive') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.coach>
