<x-layouts.coach>
    <x-slot:title>{{ __('coach.plan.title') }}</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('coach.plan.title') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('coach.plan.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Basic Plan -->
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('coach.plan.basic.name') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('coach.plan.basic.description') }}</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€{{ $stripePrices['basic']['formatted'] ?? '—' }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('coach.plan.per_month') }}</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.basic.feature_clients') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.basic.feature_programs') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.basic.feature_nutrition') }}
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="basic">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('coach.plan.basic.cta') }}
                        </button>
                    </form>
                    <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">{{ __('coach.plan.basic.trial_note', ['price' => $stripePrices['basic']['formatted'] ?? '—']) }}</p>
                </div>
            </div>

            <!-- Advanced Plan -->
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('coach.plan.advanced.name') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('coach.plan.advanced.description') }}</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€{{ $stripePrices['advanced']['formatted'] ?? '—' }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('coach.plan.per_month') }}</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.advanced.feature_clients') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.advanced.feature_basic') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.advanced.feature_loyalty') }}
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="advanced">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('coach.plan.cta_subscribe') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Professional Plan -->
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('coach.plan.professional.name') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('coach.plan.professional.description') }}</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€{{ $stripePrices['professional']['formatted'] ?? '—' }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('coach.plan.per_month') }}{{ isset($stripePrices['professional']['metered_formatted']) ? ' + €' . $stripePrices['professional']['metered_formatted'] . __('coach.plan.per_client') : ' + ' . __('coach.plan.metered') }}</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.professional.feature_clients') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.professional.feature_advanced') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.professional.feature_branding') }}
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="professional">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('coach.plan.cta_subscribe') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
