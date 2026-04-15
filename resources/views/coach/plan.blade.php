<x-layouts.coach>
    <x-slot:title>{{ __('coach.plan.title') }}</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.plan.title') }}</h1>
            <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('coach.plan.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Basic Plan -->
            <div class="relative bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6 flex flex-col">
                <div>
                    <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.plan.basic.name') }}</h3>
                    <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.plan.basic.description') }}</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-[#222222] dark:text-gray-100">€{{ $stripePrices['basic']['formatted'] ?? '—' }}</span>
                        <span class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.plan.per_month') }}</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-[#45515e] dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.basic.feature_clients') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#45515e] dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.basic.feature_programs') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#45515e] dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.basic.feature_nutrition') }}
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="basic">
                        <button type="submit" @disabled($currentPlanKey === 'basic') class="w-full inline-flex justify-center items-center px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $currentPlanKey === 'basic' ? 'bg-gray-100 dark:bg-gray-800 text-[#8e8e93] dark:text-gray-500 cursor-not-allowed' : 'bg-[#181e25] dark:bg-gray-700 text-white hover:bg-gray-800' }}">
                            {{ $currentPlanKey === 'basic' ? __('coach.plan.current_plan') : __('coach.plan.basic.cta') }}
                        </button>
                    </form>
                    <p class="mt-2 text-center text-xs text-[#8e8e93] dark:text-gray-500">{{ __('coach.plan.basic.trial_note', ['price' => $stripePrices['basic']['formatted'] ?? '—']) }}</p>
                </div>
            </div>

            <!-- Advanced Plan -->
            <div class="relative bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6 flex flex-col">
                <div>
                    <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.plan.advanced.name') }}</h3>
                    <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.plan.advanced.description') }}</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-[#222222] dark:text-gray-100">€{{ $stripePrices['advanced']['formatted'] ?? '—' }}</span>
                        <span class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.plan.per_month') }}</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-[#45515e] dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.advanced.feature_clients') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#45515e] dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.advanced.feature_basic') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#45515e] dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.advanced.feature_loyalty') }}
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="advanced">
                        <button type="submit" @disabled($currentPlanKey === 'advanced') class="w-full inline-flex justify-center items-center px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $currentPlanKey === 'advanced' ? 'bg-gray-100 dark:bg-gray-800 text-[#8e8e93] dark:text-gray-500 cursor-not-allowed' : 'bg-[#181e25] dark:bg-gray-700 text-white hover:bg-gray-800' }}">
                            {{ $currentPlanKey === 'advanced' ? __('coach.plan.current_plan') : __('coach.plan.cta_subscribe') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Professional Plan -->
            <div class="relative bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6 flex flex-col">
                <div>
                    <h3 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.plan.professional.name') }}</h3>
                    <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.plan.professional.description') }}</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-[#222222] dark:text-gray-100">€{{ $stripePrices['professional']['formatted'] ?? '—' }}</span>
                        <span class="text-sm text-[#8e8e93] dark:text-gray-500">{{ __('coach.plan.per_month') }}{{ isset($stripePrices['professional']['metered_formatted']) ? ' + €' . $stripePrices['professional']['metered_formatted'] . __('coach.plan.per_client') : ' + ' . __('coach.plan.metered') }}</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-[#45515e] dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.professional.feature_clients') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#45515e] dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.professional.feature_advanced') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#45515e] dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.professional.feature_branding') }}
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="professional">
                        <button type="submit" @disabled($currentPlanKey === 'professional') class="w-full inline-flex justify-center items-center px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $currentPlanKey === 'professional' ? 'bg-gray-100 dark:bg-gray-800 text-[#8e8e93] dark:text-gray-500 cursor-not-allowed' : 'bg-[#181e25] dark:bg-gray-700 text-white hover:bg-gray-800' }}">
                            {{ $currentPlanKey === 'professional' ? __('coach.plan.current_plan') : __('coach.plan.cta_subscribe') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
