<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tell us about yourself</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">This helps your coach create the perfect program for you.</p>
    </div>

    <form method="POST" action="{{ route('client.onboarding.store') }}">
        @csrf

        @forelse($fields as $field)
            <div class="mb-6">
                <x-input-label :for="'field_' . $field->id" :value="$field->label . ($field->is_required ? '' : ' (optional)')" />

                @if($field->type === 'select')
                    <div class="mt-2 space-y-2">
                        @foreach($field->options ?? [] as $option)
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 {{ old('fields.' . $field->id) === $option ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                <input type="radio" name="fields[{{ $field->id }}]" value="{{ $option }}"
                                    class="text-blue-600 focus:ring-blue-500"
                                    {{ old('fields.' . $field->id) === $option ? 'checked' : '' }}>
                                <span class="ml-3 font-medium text-gray-900 dark:text-gray-100">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($field->type === 'textarea')
                    <textarea id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('fields.' . $field->id) }}</textarea>
                @else
                    <x-text-input :id="'field_' . $field->id" name="fields[{{ $field->id }}]"
                        class="block mt-1 w-full" type="text" :value="old('fields.' . $field->id)" />
                @endif

                <x-input-error :messages="$errors->get('fields.' . $field->id)" class="mt-2" />
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">No onboarding questions have been set up yet.</p>
        @endforelse

        <div class="flex items-center justify-between mt-8">
            <form method="POST" action="{{ route('client.onboarding.skip') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    Skip for now
                </button>
            </form>

            <x-primary-button>
                {{ __('Complete Setup') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
