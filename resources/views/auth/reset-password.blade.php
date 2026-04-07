<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-[#222222]">Set new password</h1>
        <p class="mt-1 text-sm text-[#8e8e93]">Enter your new password below to complete the reset.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-4">
            <div>
                <x-input-label for="email" :value="__('auth.reset_password.email')" />
                <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" readonly />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password" :value="__('auth.reset_password.password')" />
                <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('auth.reset_password.confirm_password')" />
                <x-text-input id="password_confirmation" class="mt-1" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <x-primary-button class="mt-6 w-full justify-center">
            {{ __('auth.reset_password.button') }}
        </x-primary-button>
    </form>
</x-guest-layout>
