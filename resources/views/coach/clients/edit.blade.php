<x-layouts.coach>
    <x-slot:title>Edit {{ $client->name }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Client
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Client</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update client information.</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <form method="POST" action="{{ route('coach.clients.update', $client) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $client->name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" id="email" value="{{ $client->email }}" disabled
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 shadow-sm sm:text-sm text-gray-500 dark:text-gray-400">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email cannot be changed.</p>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $client->phone) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('phone') border-red-300 @enderror"
                        placeholder="+1 (555) 123-4567">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.coach>
