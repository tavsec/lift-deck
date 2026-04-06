<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('auth.join.heading') }}</h1>
        <p class="mt-2 text-sm text-gray-600">{{ __('auth.join.description') }}</p>
    </div>

    <form method="GET" action="" id="code-form">
        <div>
            <x-input-label for="code" :value="__('auth.join.code_label')" />
            <x-text-input
                id="code"
                class="block mt-1 w-full text-center text-2xl font-mono tracking-widest uppercase"
                type="text"
                name="code"
                :value="old('code')"
                required
                autofocus
                maxlength="8"
                placeholder="XXXXXXXX"
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center" id="continue-btn">
                {{ __('auth.join.button') }}
            </x-primary-button>
        </div>


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
