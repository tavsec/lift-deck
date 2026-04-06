<x-guest-layout>
    <div class="text-center mb-6">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('auth.join_register.heading') }}</h1>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('auth.join_register.joining', ['gym_name' => $invitation->coach->gym_name ?? $invitation->coach->name]) }}
        </p>
    </div>

    <form method="POST" action="{{ route('join.register') }}">
        @csrf
        <input type="hidden" name="code" value="{{ $code }}">

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('auth.join_register.name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('auth.join_register.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('auth.join_register.password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('auth.join_register.confirm_password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('auth.join_register.button') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('join') }}" class="text-sm text-gray-600 hover:text-gray-500">
                {{ __('auth.join_register.use_different_code') }}
            </a>
        </div>
    </form>
</x-guest-layout>
