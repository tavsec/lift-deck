<x-layouts.client>
    <x-slot:title>{{ __('client.settings.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4">
        <div class="mb-5">
            <h1 class="font-display text-xl font-semibold text-[#222222] dark:text-gray-100">{{ __('client.settings.heading') }}</h1>
            <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('client.settings.subtitle') }}</p>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mb-4">
                <p class="text-sm text-green-800 dark:text-green-200">{{ __('client.settings.profile_updated') }}</p>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mb-4">
                <p class="text-sm text-green-800 dark:text-green-200">{{ __('client.settings.password_updated') }}</p>
            </div>
        @endif

        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('client.settings.profile.heading') }}</h2>

            <form method="POST" action="{{ route('client.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div>
                    <label class="block text-sm font-medium text-[#222222] dark:text-gray-100 mb-2">{{ __('client.settings.profile.photo') }}</label>
                    <div x-data="{ preview: null }" class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                            <template x-if="preview">
                                <img :src="preview" class="w-full h-full object-cover" alt="Preview">
                            </template>
                            <template x-if="!preview">
                                <span>
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl font-semibold text-[#8e8e93] dark:text-gray-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    @endif
                                </span>
                            </template>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div>
                                <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Upload photo
                                    <input type="file" name="avatar" accept="image/*" class="sr-only"
                                           @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                </label>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPG, PNG up to 2MB</p>
                            </div>
                            @if($user->avatar)
                                <label class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 cursor-pointer">
                                    <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    {{ __('client.settings.profile.remove_photo') }}
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
                    <label for="name" class="block text-sm font-medium text-[#222222] dark:text-gray-100">{{ __('client.settings.profile.name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#222222] dark:text-gray-100">{{ __('client.settings.profile.email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-[#222222] dark:text-gray-100">{{ __('client.settings.profile.phone') }} <span class="text-[#8e8e93] dark:text-gray-500 font-normal">({{ __('client.settings.profile.optional') }})</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-[#222222] dark:text-gray-100">{{ __('client.settings.profile.bio') }} <span class="text-[#8e8e93] dark:text-gray-500 font-normal">({{ __('client.settings.profile.optional') }})</span></label>
                    <textarea id="bio" name="bio" rows="3"
                        class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                        {{ __('client.settings.profile.save') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-4">{{ __('client.settings.password.heading') }}</h2>

            <form method="POST" action="{{ route('client.settings.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div x-data="{ showPassword: false }">
                    <label for="current_password" class="block text-sm font-medium text-[#222222] dark:text-gray-100">{{ __('client.settings.password.current') }}</label>
                    <div class="relative mt-1">
                        <input
                            id="current_password"
                            :type="showPassword ? 'text' : 'password'"
                            name="current_password"
                            autocomplete="current-password"
                            class="block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm pr-10"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        >
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-medium text-[#222222] dark:text-gray-100">{{ __('client.settings.password.new') }}</label>
                    <div class="relative mt-1">
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            autocomplete="new-password"
                            class="block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm pr-10"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        >
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showPassword: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-[#222222] dark:text-gray-100">{{ __('client.settings.password.confirm') }}</label>
                    <div class="relative mt-1">
                        <input
                            id="password_confirmation"
                            :type="showPassword ? 'text' : 'password'"
                            name="password_confirmation"
                            autocomplete="new-password"
                            class="block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm pr-10"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        >
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                        {{ __('client.settings.password.update') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Sign Out Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-1">{{ __('client.settings.sign_out.heading') }}</h2>
            <p class="text-sm text-[#8e8e93] dark:text-gray-500 mb-4">{{ __('client.settings.sign_out.subtitle') }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-medium text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('client.settings.sign_out.button') }}
                </button>
            </form>
        </div>
    </div>
</x-layouts.client>
