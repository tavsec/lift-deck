<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-[#222222]">{{ __('auth.join.heading') }}</h1>
        <p class="mt-1 text-sm text-[#8e8e93]">{{ __('auth.join.description') }}</p>
    </div>

    <form method="GET" action="" id="code-form">
        <div class="space-y-4">
            <div>
                <x-input-label for="code" :value="__('auth.join.code_label')" />
                <x-text-input
                    id="code"
                    class="mt-1 text-center text-2xl font-mono tracking-widest uppercase"
                    type="text"
                    name="code"
                    :value="old('code')"
                    required
                    autofocus
                    maxlength="8"
                    placeholder="XXXXXXXX"
                />
                <x-input-error :messages="$errors->get('code')" class="mt-1" />
            </div>
        </div>

        <x-primary-button class="mt-6 w-full justify-center" id="continue-btn">
            {{ __('auth.join.button') }}
        </x-primary-button>
    </form>

    <script>
        document.getElementById('code-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const code = document.getElementById('code').value.toUpperCase();
            if (code.length === 8) {
                window.location.href = '/join/' + code;
            }
        });
    </script>
</x-guest-layout>
