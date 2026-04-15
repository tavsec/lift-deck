<x-guest-layout>
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-[#222222]">Reset your password</h1>
        <p class="mt-1 text-sm text-[#8e8e93]">{{ __('auth.forgot_password.description') }}</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <x-input-label for="email" :value="__('auth.forgot_password.email')" />
                <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
        </div>

        <x-primary-button class="mt-6 w-full justify-center">
            {{ __('auth.forgot_password.button') }}
        </x-primary-button>
    </form>
</x-guest-layout>
