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
                <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="current-password" />
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
