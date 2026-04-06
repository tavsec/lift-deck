<x-layouts.coach>
    <x-slot:title>Choose Your Plan</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Choose Your Plan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select the plan that best fits your coaching needs. You can upgrade at any time.</p>
        </div>

        <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
            <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>Basic plan includes a 7-day Free Trial</strong> — no credit card required. Advanced and Professional plans start immediately with payment.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Basic Plan -->
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Basic</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For coaches just getting started.</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€2.50</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">/mo</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Up to 5 clients
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Programs &amp; workout logs
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Nutrition tracking
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="basic">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Start Free Trial
                        </button>
                    </form>
                    <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">No credit card required</p>
                </div>
            </div>

            <!-- Advanced Plan -->
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Advanced</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For growing coaches.</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€10</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">/mo</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Up to 15 clients
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Everything in Basic
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Loyalty &amp; achievements
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="advanced">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Subscribe Now
                        </button>
                    </form>
                </div>
            </div>

            <!-- Professional Plan -->
            <div class="relative rounded-lg border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Professional</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For professional coaches at scale.</p>
                    <p class="mt-4">
                        <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">€15</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">/mo + metered</span>
                    </p>
                </div>
                <ul class="mt-6 space-y-2 flex-1">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        30 clients included
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Everything in Advanced
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Custom branding
                    </li>
                </ul>
                <div class="mt-6">
                    <form method="POST" action="{{ route('coach.plan.store') }}">
                        @csrf
                        <input type="hidden" name="plan" value="professional">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Subscribe Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.coach>
