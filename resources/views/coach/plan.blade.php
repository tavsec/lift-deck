<x-layouts.coach>
    <x-slot:title>{{ __('coach.plan.title') }}</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.plan.title') }}</h1>
            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('coach.plan.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Basic Plan -->
            <div class="relative bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6 flex flex-col">
                <div>
                    <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.plan.basic.name') }}</h3>
                    <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.plan.basic.description') }}</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-[#181b22] dark:text-[#f0f2f5]">€{{ $stripePrices['basic']['formatted'] ?? '—' }}</span>
                        <span class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.plan.per_month') }}</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.basic.feature_clients') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.basic.feature_programs') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.basic.feature_nutrition') }}
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="basic">
                        <button type="submit" @disabled($currentPlanKey === 'basic') class="w-full inline-flex justify-center items-center px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $currentPlanKey === 'basic' ? 'bg-[#f3f5f7] dark:bg-[#1d2027] text-[#8c93a0] dark:text-[#6b7280] cursor-not-allowed' : 'bg-[#181b22] dark:bg-[#c6f24e] dark:text-[#14180a] hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438]' }}">
                            {{ $currentPlanKey === 'basic' ? __('coach.plan.current_plan') : __('coach.plan.basic.cta') }}
                        </button>
                    </form>
                    <p class="mt-2 text-center text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.plan.basic.trial_note', ['price' => $stripePrices['basic']['formatted'] ?? '—']) }}</p>
                </div>
            </div>

            <!-- Advanced Plan -->
            <div class="relative bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6 flex flex-col">
                <div>
                    <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.plan.advanced.name') }}</h3>
                    <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.plan.advanced.description') }}</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-[#181b22] dark:text-[#f0f2f5]">€{{ $stripePrices['advanced']['formatted'] ?? '—' }}</span>
                        <span class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.plan.per_month') }}</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.advanced.feature_clients') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.advanced.feature_basic') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.advanced.feature_loyalty') }}
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="advanced">
                        <button type="submit" @disabled($currentPlanKey === 'advanced') class="w-full inline-flex justify-center items-center px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $currentPlanKey === 'advanced' ? 'bg-[#f3f5f7] dark:bg-[#1d2027] text-[#8c93a0] dark:text-[#6b7280] cursor-not-allowed' : 'bg-[#181b22] dark:bg-[#c6f24e] dark:text-[#14180a] hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438]' }}">
                            {{ $currentPlanKey === 'advanced' ? __('coach.plan.current_plan') : __('coach.plan.cta_subscribe') }}
                        </button>
                    </form>
                    @if($currentPlanKey !== 'advanced')
                        <p class="mt-2 text-center text-xs text-[#8c93a0] dark:text-[#6b7280]">Billed monthly — cancel anytime</p>
                    @endif
                </div>
            </div>

            <!-- Professional Plan -->
            <div class="relative bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-6 flex flex-col">
                <div>
                    <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.plan.professional.name') }}</h3>
                    <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.plan.professional.description') }}</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-[#181b22] dark:text-[#f0f2f5]">€{{ $stripePrices['professional']['formatted'] ?? '—' }}</span>
                        <span class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.plan.per_month') }}{{ isset($stripePrices['professional']['metered_formatted']) ? ' + €' . $stripePrices['professional']['metered_formatted'] . __('coach.plan.per_client') : ' + ' . __('coach.plan.metered') }}</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.professional.feature_clients') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.professional.feature_advanced') }}
                    </li>
                    <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('coach.plan.professional.feature_branding') }}
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="professional">
                        <button type="submit" @disabled($currentPlanKey === 'professional') class="w-full inline-flex justify-center items-center px-4 py-2 rounded-lg font-semibold text-sm transition-colors {{ $currentPlanKey === 'professional' ? 'bg-[#f3f5f7] dark:bg-[#1d2027] text-[#8c93a0] dark:text-[#6b7280] cursor-not-allowed' : 'bg-[#181b22] dark:bg-[#c6f24e] dark:text-[#14180a] hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438]' }}">
                            {{ $currentPlanKey === 'professional' ? __('coach.plan.current_plan') : __('coach.plan.cta_subscribe') }}
                        </button>
                    </form>
                    @if($currentPlanKey !== 'professional')
                        <p class="mt-2 text-center text-xs text-[#8c93a0] dark:text-[#6b7280]">Billed monthly — cancel anytime</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
