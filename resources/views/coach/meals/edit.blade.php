<x-layouts.coach>
    <x-slot:title>{{ __('coach.meals.edit.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.meals.index') }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.meals.edit.back') }}
            </a>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.meals.edit.heading') }}</h1>
            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.meals.edit.subtitle') }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
            <form method="POST" action="{{ route('coach.meals.update', $meal) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.meals.edit.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $meal->name) }}" required
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('name') border-red-300 dark:border-red-700 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.meals.edit.description') }}</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('description') border-red-300 dark:border-red-700 @enderror">{{ old('description', $meal->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="calories" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.meals.edit.calories') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="calories" id="calories" value="{{ old('calories', $meal->calories) }}" required min="0"
                        class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('calories') border-red-300 dark:border-red-700 @enderror">
                    @error('calories')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="protein" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.meals.edit.protein') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="protein" id="protein" value="{{ old('protein', $meal->protein) }}" required min="0" step="0.1"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('protein') border-red-300 dark:border-red-700 @enderror">
                        @error('protein')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="carbs" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.meals.edit.carbs') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="carbs" id="carbs" value="{{ old('carbs', $meal->carbs) }}" required min="0" step="0.1"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('carbs') border-red-300 dark:border-red-700 @enderror">
                        @error('carbs')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fat" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.meals.edit.fat') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="fat" id="fat" value="{{ old('fat', $meal->fat) }}" required min="0" step="0.1"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150 @error('fat') border-red-300 dark:border-red-700 @enderror">
                        @error('fat')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                    <a href="{{ route('coach.meals.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-[#11141a] border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] rounded-lg text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                        {{ __('coach.meals.edit.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                        {{ __('coach.meals.edit.update') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Archive -->
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6">
            <h2 class="font-display text-base font-semibold text-red-700 dark:text-red-400">{{ __('coach.meals.edit.archive_heading') }}</h2>
            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.meals.edit.archive_description') }}</p>
            <div class="mt-4">
                <form method="POST" action="{{ route('coach.meals.destroy', $meal) }}" onsubmit="return confirm('{{ __('coach.meals.edit.archive_confirm') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                        {{ __('coach.meals.edit.archive') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.coach>
