<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tell us about yourself</h1>
        <p class="mt-2 text-sm text-gray-600">This helps your coach create the perfect program for you.</p>
    </div>

    <form method="POST" action="{{ route('client.onboarding.store') }}">
        @csrf

        <!-- Goal -->
        <div class="mb-6">
            <x-input-label :value="__('What is your primary goal?')" class="mb-3" />
            <div class="space-y-2">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('goal') === 'fat_loss' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="goal" value="fat_loss" class="text-blue-600 focus:ring-blue-500" {{ old('goal') === 'fat_loss' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Fat Loss</span>
                        <span class="block text-sm text-gray-500">Lose weight and get leaner</span>
                    </span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('goal') === 'strength' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="goal" value="strength" class="text-blue-600 focus:ring-blue-500" {{ old('goal') === 'strength' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Build Strength</span>
                        <span class="block text-sm text-gray-500">Get stronger and build muscle</span>
                    </span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('goal') === 'general_fitness' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="goal" value="general_fitness" class="text-blue-600 focus:ring-blue-500" {{ old('goal') === 'general_fitness' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">General Fitness</span>
                        <span class="block text-sm text-gray-500">Improve overall health and fitness</span>
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('goal')" class="mt-2" />
        </div>

        <!-- Experience Level -->
        <div class="mb-6">
            <x-input-label :value="__('What is your experience level?')" class="mb-3" />
            <div class="space-y-2">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('experience_level') === 'beginner' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="experience_level" value="beginner" class="text-blue-600 focus:ring-blue-500" {{ old('experience_level') === 'beginner' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Beginner</span>
                        <span class="block text-sm text-gray-500">New to working out or less than 6 months</span>
                    </span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('experience_level') === 'intermediate' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="experience_level" value="intermediate" class="text-blue-600 focus:ring-blue-500" {{ old('experience_level') === 'intermediate' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Intermediate</span>
                        <span class="block text-sm text-gray-500">6 months to 2 years of consistent training</span>
                    </span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ old('experience_level') === 'advanced' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <input type="radio" name="experience_level" value="advanced" class="text-blue-600 focus:ring-blue-500" {{ old('experience_level') === 'advanced' ? 'checked' : '' }}>
                    <span class="ml-3">
                        <span class="block font-medium text-gray-900">Advanced</span>
                        <span class="block text-sm text-gray-500">More than 2 years of serious training</span>
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('experience_level')" class="mt-2" />
        </div>

        <!-- Injuries -->
        <div class="mb-6">
            <x-input-label for="injuries" :value="__('Any injuries or limitations? (optional)')" />
            <textarea id="injuries" name="injuries" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="E.g., bad knee, lower back issues...">{{ old('injuries') }}</textarea>
            <x-input-error :messages="$errors->get('injuries')" class="mt-2" />
        </div>

        <!-- Equipment Access -->
        <div class="mb-6">
            <x-input-label for="equipment_access" :value="__('What equipment do you have access to? (optional)')" />
            <textarea id="equipment_access" name="equipment_access" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="E.g., full gym, home dumbbells only...">{{ old('equipment_access') }}</textarea>
            <x-input-error :messages="$errors->get('equipment_access')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-8">
            <form method="POST" action="{{ route('client.onboarding.skip') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                    Skip for now
                </button>
            </form>

            <x-primary-button>
                {{ __('Complete Setup') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
