<x-layouts.client>
    <x-slot:title>Settings</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Settings</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your account information and password.</p>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">Profile updated successfully.</p>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <p class="text-sm font-medium text-green-800 dark:text-green-300">Password updated successfully.</p>
            </div>
        @endif

        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Profile</h2>

            <form method="POST" action="{{ route('client.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xl font-semibold text-gray-500 dark:text-gray-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            <input type="file" name="avatar" accept="image/*" class="text-sm text-gray-600 dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-gray-100 dark:file:bg-gray-800 file:text-gray-700 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-700">
                            @if($user->avatar)
                                <label class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 cursor-pointer">
                                    <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    Remove photo
                                </label>
                            @endif
                        </div>
                    </div>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bio <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea id="bio" name="bio" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Save
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Card -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Change Password</h2>

            <form method="POST" action="{{ route('client.settings.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Sign Out Card -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Sign Out</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Sign out of your account on this device.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</x-layouts.client>
