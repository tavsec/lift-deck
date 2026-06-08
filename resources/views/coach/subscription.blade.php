<x-layouts.coach>
    <x-slot:title>Subscription</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">Subscription</h1>
            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">Manage your LiftDeck subscription and billing.</p>
        </div>

        @if(session('feature_required'))
            <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                <p class="text-sm text-red-800 dark:text-red-200">
                    @php
                        $featureNames = ['loyalty' => 'Loyalty System', 'custom_branding' => 'Custom Branding'];
                        $featureName = $featureNames[session('feature_required')] ?? ucwords(str_replace('_', ' ', session('feature_required')));
                    @endphp
                    The <strong>{{ $featureName }}</strong> feature requires an Advanced or Professional plan.
                </p>
            </div>
        @endif

        @if($isOnTrial)
            <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            You are currently on a free trial. Your trial ends on {{ $trialEndsAt->format('M j, Y') }}.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($isInGracePeriod)
            <div class="rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Your subscription has ended. You have {{ $graceDaysRemaining }} day{{ $graceDaysRemaining !== 1 ? 's' : '' }} remaining in your grace period.
                            <a href="{{ route('coach.subscription.portal') }}" class="underline hover:no-underline">Manage your subscription</a> to continue.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($currentPlanKey && !$isOnTrial)
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                Active plan: <span class="capitalize">{{ $currentPlanKey }}</span>
                                @if($clientLimit !== null)
                                    &mdash; {{ $clientCount }} / {{ $clientLimit }} {{ $clientCount === 1 ? 'client' : 'clients' }}
                                @else
                                    &mdash; {{ $clientCount }} {{ $clientCount === 1 ? 'client' : 'clients' }} (unlimited)
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('coach.subscription.portal') }}"
                        class="ml-4 inline-flex items-center px-3 py-1.5 bg-[#181b22] dark:bg-[#c6f24e] dark:text-[#14180a] text-xs font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                        Manage
                    </a>
                </div>
            </div>
        @endif

        <!-- Plans -->
        <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)]">
            <div class="px-6 py-4 border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">Choose a Plan</h2>
                <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">Select the plan that best fits your coaching needs.</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <!-- Basic Plan -->
                    <div class="relative rounded-xl border {{ $currentPlanKey === 'basic' ? 'border-[#c6f24e] ring-2 ring-[rgba(198,242,78,0.3)]' : 'border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)]' }} p-6">
                        @if($currentPlanKey === 'basic')
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Current Plan</span>
                        @endif
                        <div>
                            <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">Basic</h3>
                            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">For coaches just getting started.</p>
                            <p class="mt-4">
                                <span class="text-3xl font-bold text-[#181b22] dark:text-[#f0f2f5]">€10</span>
                                <span class="text-sm text-[#8c93a0] dark:text-[#6b7280]">/mo</span>
                            </p>
                        </div>
                        <ul class="mt-6 space-y-2">
                            <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Up to 5 clients
                            </li>
                            <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Programs & workout logs
                            </li>
                            <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Nutrition tracking
                            </li>
                        </ul>
                    </div>

                    <!-- Advanced Plan -->
                    <div class="relative rounded-xl border {{ $currentPlanKey === 'advanced' ? 'border-[#c6f24e] ring-2 ring-[rgba(198,242,78,0.3)]' : 'border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)]' }} p-6">
                        @if($currentPlanKey === 'advanced')
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Current Plan</span>
                        @endif
                        <div>
                            <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">Advanced</h3>
                            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">For growing coaches.</p>
                            <p class="mt-4">
                                <span class="text-3xl font-bold text-[#181b22] dark:text-[#f0f2f5]">€45</span>
                                <span class="text-sm text-[#8c93a0] dark:text-[#6b7280]">/mo</span>
                            </p>
                        </div>
                        <ul class="mt-6 space-y-2">
                            <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Up to 15 clients
                            </li>
                            <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Everything in Basic
                            </li>
                            <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Loyalty & achievements
                            </li>
                        </ul>
                    </div>

                    <!-- Professional Plan -->
                    <div class="relative rounded-xl border {{ $currentPlanKey === 'professional' ? 'border-[#c6f24e] ring-2 ring-[rgba(198,242,78,0.3)]' : 'border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)]' }} p-6">
                        @if($currentPlanKey === 'professional')
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">Current Plan</span>
                        @endif
                        <div>
                            <h3 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">Professional</h3>
                            <p class="mt-1 text-sm text-[#8c93a0] dark:text-[#6b7280]">For professional coaches at scale.</p>
                            <p class="mt-4">
                                <span class="text-3xl font-bold text-[#181b22] dark:text-[#f0f2f5]">€79</span>
                                <span class="text-sm text-[#8c93a0] dark:text-[#6b7280]">/mo</span>
                            </p>
                            <p class="mt-1 text-xs font-medium text-[#5c7a10] dark:text-[#c6f24e]">+ €0.50 per client over 30</p>
                        </div>
                        <ul class="mt-6 space-y-2">
                            <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                30 clients included
                            </li>
                            <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Everything in Advanced
                            </li>
                            <li class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6]">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Custom branding
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- CTA -->
                <div class="mt-8 flex justify-center">
                    @if($subscription && $subscription->active())
                        <a href="{{ route('coach.subscription.portal') }}"
                            class="inline-flex items-center px-6 py-2.5 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                            Manage Subscription
                        </a>
                    @elseif($selectedPlan)
                        <div class="flex flex-col items-center gap-1.5">
                            <a href="{{ route('coach.subscription.checkout') }}"
                                class="inline-flex items-center px-6 py-2.5 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                                Subscribe to {{ ucfirst($selectedPlan) }}
                            </a>
                            @if(in_array($selectedPlan, ['advanced', 'professional']))
                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">Billed monthly — cancel anytime</p>
                            @endif
                        </div>
                    @else
                        <a href="{{ route('coach.plan') }}"
                            class="inline-flex items-center px-6 py-2.5 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                            Choose a Plan
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
