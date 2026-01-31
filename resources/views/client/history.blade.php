<x-layouts.client>
    <x-slot:title>Workout History</x-slot:title>

    <div class="space-y-6">
        <h1 class="text-3xl font-bold text-gray-900">Workout History</h1>

        <x-bladewind::card class="!p-6">
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-4 text-lg text-gray-600">Your workout history will appear here</p>
            </div>
        </x-bladewind::card>
    </div>
</x-layouts.client>
