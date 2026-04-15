<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('client.onboarding.heading') }}</h1>
        <p class="mt-2 text-sm text-[#45515e] dark:text-gray-400">{{ __('client.onboarding.subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('client.onboarding.store') }}">
        @csrf

        @forelse($fields as $field)
            <div class="mb-6">
                <x-input-label :for="'field_' . $field->id" :value="$field->label . ($field->is_required ? '' : ' ' . __('client.onboarding.optional'))" />

                @if($field->type === 'select')
                    <div class="mt-2 space-y-2">
                        @foreach($field->options ?? [] as $option)
                            <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ old('fields.' . $field->id) === $option ? 'border-[#1456f0] bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-800' }}">
                                <input type="radio" name="fields[{{ $field->id }}]" value="{{ $option }}"
                                    class="text-[#1456f0] focus:ring-[#1456f0]"
                                    {{ old('fields.' . $field->id) === $option ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-[#222222] dark:text-gray-100">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($field->type === 'textarea')
                    <textarea id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" rows="3"
                        class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm">{{ old('fields.' . $field->id) }}</textarea>
                @else
                    <x-text-input :id="'field_' . $field->id" name="fields[{{ $field->id }}]"
                        class="block mt-1 w-full" type="text" :value="old('fields.' . $field->id)" />
                @endif

                <x-input-error :messages="$errors->get('fields.' . $field->id)" class="mt-2" />
            </div>
        @empty
            <p class="text-sm text-[#8e8e93] dark:text-gray-500 mb-6">{{ __('client.onboarding.no_questions') }}</p>
        @endforelse

        <div class="flex items-center justify-between mt-8">
            <form method="POST" action="{{ route('client.onboarding.skip') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-300 transition-colors">
                    {{ __('client.onboarding.skip') }}
                </button>
            </form>

            <x-primary-button>
                {{ __('client.onboarding.complete') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
