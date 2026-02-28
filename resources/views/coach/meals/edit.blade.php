<x-layouts.coach>
    <x-slot:title>Edit {{ $meal->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.meals.index') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Meal Library
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Meal</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update meal information.</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <form method="POST" action="{{ route('coach.meals.update', $meal) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meal Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $meal->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description', $meal->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="calories" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Calories <span class="text-red-500">*</span></label>
                    <input type="number" name="calories" id="calories" value="{{ old('calories', $meal->calories) }}" required min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('calories') border-red-300 @enderror">
                    @error('calories')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="protein" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Protein (g) <span class="text-red-500">*</span></label>
                        <input type="number" name="protein" id="protein" value="{{ old('protein', $meal->protein) }}" required min="0" step="0.1"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('protein') border-red-300 @enderror">
                        @error('protein')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="carbs" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carbs (g) <span class="text-red-500">*</span></label>
                        <input type="number" name="carbs" id="carbs" value="{{ old('carbs', $meal->carbs) }}" required min="0" step="0.1"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('carbs') border-red-300 @enderror">
                        @error('carbs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fat (g) <span class="text-red-500">*</span></label>
                        <input type="number" name="fat" id="fat" value="{{ old('fat', $meal->fat) }}" required min="0" step="0.1"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('fat') border-red-300 @enderror">
                        @error('fat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('coach.meals.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Update Meal
                    </button>
                </div>
            </form>
        </div>

        <!-- Archive -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-red-900">Archive Meal</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Archiving this meal will remove it from the library. Existing meal logs will not be affected.</p>
            <div class="mt-4">
                <form method="POST" action="{{ route('coach.meals.destroy', $meal) }}" onsubmit="return confirm('Are you sure you want to archive this meal?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Archive Meal
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.coach>
