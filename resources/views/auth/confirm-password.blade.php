<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-[#222222]">Confirm your password</h1>
        <p class="mt-1 text-sm text-[#8e8e93]">{{ __('auth.confirm_password.description') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <x-input-label for="password" :value="__('auth.confirm_password.password')" />
                <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
        </div>

        <x-primary-button class="mt-6 w-full justify-center">
            {{ __('auth.confirm_password.button') }}
        </x-primary-button>
    </form>
</x-guest-layout>
