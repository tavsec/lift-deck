<x-guest-layout>
    <div class="mb-6">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="font-display text-2xl font-semibold text-[#222222]">{{ __('auth.join_register.heading') }}</h1>
        <p class="mt-1 text-sm text-[#8e8e93]">
            {{ __('auth.join_register.joining', ['gym_name' => $invitation->coach->gym_name ?? $invitation->coach->name]) }}
        </p>
    </div>

    <form method="POST" action="{{ route('join.register') }}">
        @csrf
        <input type="hidden" name="code" value="{{ $code }}">

        <div class="space-y-4">
            <div>
                <x-input-label for="name" :value="__('auth.join_register.name')" />
                <x-text-input id="name" class="mt-1" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="email" :value="__('auth.join_register.email')" />
                <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password" :value="__('auth.join_register.password')" />
                <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('auth.join_register.confirm_password')" />
                <x-text-input id="password_confirmation" class="mt-1" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <x-primary-button class="mt-6 w-full justify-center">
            {{ __('auth.join_register.button') }}
        </x-primary-button>

        <div class="mt-4 text-center">
            <a href="{{ route('join') }}" class="text-sm font-medium text-[#1456f0] hover:underline">
                {{ __('auth.join_register.use_different_code') }}
            </a>
        </div>
    </form>
</x-guest-layout>
