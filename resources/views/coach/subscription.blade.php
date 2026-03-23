<x-layouts.coach>
    <x-slot:title>Subscription</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Subscription</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your LiftDeck subscription and billing.</p>
        </div>

        @if(session('feature_required'))
            <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ session('feature_required') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($isOnTrial)
            <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="rounded-md bg-orange-50 dark:bg-orange-900/20 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-orange-800 dark:text-orange-200">
                            Your subscription has ended. You have {{ $graceDaysRemaining }} day{{ $graceDaysRemaining !== 1 ? 's' : '' }} remaining in your grace period.
                            <a href="{{ route('coach.subscription.portal') }}" class="underline hover:no-underline">Manage your subscription</a> to continue.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($currentPlanKey && !$isOnTrial)
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                Active plan: <span class="capitalize">{{ $currentPlanKey }}</span>
                                @if($clientLimit !== null)
                                    &mdash; {{ $clientCount }} / {{ $clientLimit }} clients
                                @else
                                    &mdash; {{ $clientCount }} clients (unlimited)
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('coach.subscription.portal') }}"
                        class="ml-4 inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Manage
                    </a>
                </div>
            </div>
        @endif

        <!-- Plans -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Choose a Plan</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select the plan that best fits your coaching needs.</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <!-- Basic Plan -->
                    <div class="relative rounded-lg border {{ $currentPlanKey === 'basic' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700' }} p-6">
                        @if($currentPlanKey === 'basic')
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Current Plan</span>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Basic</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For coaches just getting started.</p>
                            <p class="mt-4">
                                <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€2.50</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">/mo</span>
                            </p>
                        </div>
                        <ul class="mt-6 space-y-2">
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Up to 5 clients
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Programs & workout logs
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Nutrition tracking
                            </li>
                        </ul>
                    </div>

                    <!-- Advanced Plan -->
                    <div class="relative rounded-lg border {{ $currentPlanKey === 'advanced' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700' }} p-6">
                        @if($currentPlanKey === 'advanced')
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Current Plan</span>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Advanced</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For growing coaches.</p>
                            <p class="mt-4">
                                <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€10</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">/mo</span>
                            </p>
                        </div>
                        <ul class="mt-6 space-y-2">
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Up to 15 clients
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Everything in Basic
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Loyalty & achievements
                            </li>
                        </ul>
                    </div>

                    <!-- Professional Plan -->
                    <div class="relative rounded-lg border {{ $currentPlanKey === 'professional' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700' }} p-6">
                        @if($currentPlanKey === 'professional')
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Current Plan</span>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Professional</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For professional coaches at scale.</p>
                            <p class="mt-4">
                                <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€15</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">/mo + metered</span>
                            </p>
                        </div>
                        <ul class="mt-6 space-y-2">
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                30 clients included
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Everything in Advanced
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Manage Subscription
                        </a>
                    @else
                        <a href="{{ route('coach.subscription.portal') }}"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Subscribe Now
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
