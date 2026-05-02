<x-layouts.coach>
    <x-slot:title>{{ __('coach.day_plans.create.heading_for', ['name' => $client->name]) }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('coach.clients.nutrition', $client) }}" class="inline-flex items-center text-sm text-[#8e8e93] dark:text-gray-400 hover:text-[#45515e] dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.day_plans.create.back', ['name' => $client->name]) }}
            </a>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.day_plans.create.heading_for', ['name' => $client->name]) }}</h1>
            <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.day_plans.create.subtitle') }}</p>
        </div>

        @include('coach.day-plans._form', [
            'action' => route('coach.clients.day-plans.store', $client),
            'method' => 'POST',
            'client' => $client,
            'availableMeals' => $availableMeals,
            'defaultSections' => $defaultSections,
        ])
    </div>
</x-layouts.coach>
