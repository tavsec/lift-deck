<x-guest-layout>
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-[#222222]">Sign in</h1>
        <p class="mt-1 text-sm text-[#8e8e93]">Welcome back to LiftDeck</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <x-input-label for="email" :value="__('auth.login.email')" />
                <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password" :value="__('auth.login.password')" />
                <div x-data="{ showPassword: false }" class="relative mt-1">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 pr-10 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
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
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between">
            <label class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#1456f0] focus:ring-[#1456f0]" name="remember">
                <span class="text-sm text-[#45515e]">{{ __('auth.login.remember_me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-[#1456f0] hover:underline" href="{{ route('password.request') }}">
                    {{ __('auth.login.forgot_password') }}
                </a>
            @endif
        </div>

        <x-primary-button class="mt-6 w-full justify-center">
            {{ __('auth.login.button') }}
        </x-primary-button>
    </form>
</x-guest-layout>
