<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('client.onboarding.heading') }}</h1>
        <p class="mt-2 text-sm text-[#555b66] dark:text-[#a4abb6]">{{ __('client.onboarding.subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('client.onboarding.store') }}">
        @csrf

        @forelse($fields as $field)
            <div class="mb-6">
                <x-input-label :for="'field_' . $field->id" :value="$field->label . ($field->is_required ? '' : ' ' . __('client.onboarding.optional'))" />

                @if($field->type === 'select')
                    <div class="mt-2 space-y-2">
                        @foreach($field->options ?? [] as $option)
                            <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ old('fields.' . $field->id) === $option ? 'border-[#c6f24e] bg-[rgba(198,242,78,0.12)] dark:bg-[rgba(198,242,78,0.08)]' : 'border-gray-200 dark:border-gray-800' }}">
                                <input type="radio" name="fields[{{ $field->id }}]" value="{{ $option }}"
                                    class="text-[#5c7a10] focus:ring-[#c6f24e]"
                                    {{ old('fields.' . $field->id) === $option ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($field->type === 'textarea')
                    <textarea id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" rows="3"
                        class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm">{{ old('fields.' . $field->id) }}</textarea>
                @else
                    <x-text-input :id="'field_' . $field->id" name="fields[{{ $field->id }}]"
                        class="block mt-1 w-full" type="text" :value="old('fields.' . $field->id)" />
                @endif

                <x-input-error :messages="$errors->get('fields.' . $field->id)" class="mt-2" />
            </div>
        @empty
            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mb-6">{{ __('client.onboarding.no_questions') }}</p>
        @endforelse

        <div class="flex items-center justify-between mt-8">
            <form method="POST" action="{{ route('client.onboarding.skip') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#555b66] dark:hover:text-gray-300 transition-colors">
                    {{ __('client.onboarding.skip') }}
                </button>
            </form>

            <x-primary-button>
                {{ __('client.onboarding.complete') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
