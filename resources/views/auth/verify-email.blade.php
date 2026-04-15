<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-[#222222]">Verify your email</h1>
        <p class="mt-1 text-sm text-[#8e8e93]">{{ __('auth.verify_email.description') }}</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ __('auth.verify_email.link_sent') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                {{ __('auth.verify_email.resend') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm font-medium text-[#1456f0] hover:underline">
                {{ __('auth.verify_email.logout') }}
            </button>
        </form>
    </div>
</x-guest-layout>
