<x-layouts.coach>
    <x-slot:title>{{ __('coach.rewards.edit.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.rewards.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.rewards.edit.back') }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('coach.rewards.edit.heading') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('coach.rewards.edit.subtitle') }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('coach.rewards.update', $reward) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('coach.rewards.edit.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $reward->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">{{ __('coach.rewards.edit.description') }}</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description', $reward->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="points_cost" class="block text-sm font-medium text-gray-700">{{ __('coach.rewards.edit.points_cost') }} <span class="text-red-500">*</span></label>
                    <input type="number" name="points_cost" id="points_cost" value="{{ old('points_cost', $reward->points_cost) }}" required min="1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('points_cost') border-red-300 @enderror">
                    @error('points_cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">{{ __('coach.rewards.edit.stock') }}</label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $reward->stock) }}" min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('stock') border-red-300 @enderror"
                        placeholder="{{ __('coach.rewards.edit.stock_placeholder') }}">
                    @error('stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">{{ __('coach.rewards.edit.image') }}</label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('image') border-red-300 @enderror">
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('coach.rewards.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-medium text-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('coach.rewards.edit.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('coach.rewards.edit.update') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Archive -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-medium text-red-900">{{ __('coach.rewards.edit.archive_heading') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('coach.rewards.edit.archive_description') }}</p>
            <div class="mt-4">
                <form method="POST" action="{{ route('coach.rewards.destroy', $reward) }}" onsubmit="return confirm('{{ __('coach.rewards.edit.archive_confirm') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('coach.rewards.edit.archive') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.coach>
