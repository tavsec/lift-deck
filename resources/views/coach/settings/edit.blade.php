<x-layouts.coach>
    <x-slot:title>{{ __('coach.settings.heading') }}</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.settings.heading') }}</h1>
            <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('coach.settings.subtitle') }}</p>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ __('coach.settings.profile_updated') }}</p>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ __('coach.settings.password_updated') }}</p>
            </div>
        @endif

        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-5">{{ __('coach.settings.profile.heading') }}</h2>

            <form method="POST" action="{{ route('coach.settings.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div>
                    <label class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-2">{{ __('coach.settings.profile.photo') }}</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xl font-semibold text-[#8e8e93] dark:text-gray-400">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col gap-2">
                            <input type="file" name="avatar" accept="image/*" class="text-sm text-[#45515e] dark:text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 dark:file:bg-gray-800 file:text-[#45515e] dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-700">
                            @if($user->avatar)
                                <label class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 cursor-pointer">
                                    <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 dark:border-gray-700 text-red-600 focus:ring-red-500">
                                    {{ __('coach.settings.profile.remove_photo') }}
                                </label>
                            @endif
                        </div>
                    </div>
                    @error('avatar')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.settings.profile.name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.settings.profile.email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.settings.profile.phone') }} <span class="text-[#8e8e93] dark:text-gray-500 font-normal">({{ __('coach.settings.profile.optional') }})</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                    @error('phone')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.settings.profile.bio') }} <span class="text-[#8e8e93] dark:text-gray-500 font-normal">({{ __('coach.settings.profile.optional') }})</span></label>
                    <textarea id="bio" name="bio" rows="3"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        {{ __('coach.settings.profile.save') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-5">{{ __('coach.settings.password.heading') }}</h2>

            <form method="POST" action="{{ route('coach.settings.password') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.settings.password.current') }}</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                    @error('current_password')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.settings.password.new') }}</label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.settings.password.confirm') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                        class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        {{ __('coach.settings.password.update') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Subscription Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-6">
            <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100 mb-5">{{ __('coach.settings.subscription.heading') }}</h2>

            @if($isOnTrial)
                {{-- State 1: Free trial --}}
                <div class="flex items-start gap-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 mb-4">
                    <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            {{ $currentPlanKey ? ucfirst($currentPlanKey) . ' plan — ' : '' }}Free trial{{ $trialEndsAt ? ' — ends ' . $trialEndsAt->format('M j, Y') : '' }}
                        </p>
                        <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                            {{ $clientCount }} / {{ $clientLimit ?? '∞' }} {{ $clientCount === 1 ? 'client' : 'clients' }}
                        </p>
                    </div>
                </div>
                @if($hasStripeSubscription)
                    <a href="{{ route('coach.subscription.portal') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        Manage on Stripe
                    </a>
                @else
                    <a href="{{ route('coach.plan') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        Choose a plan
                    </a>
                @endif

            @elseif($isInGracePeriod)
                {{-- State 2: Grace period --}}
                <div class="flex items-start gap-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 mb-4">
                    <svg class="h-5 w-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Your subscription has ended
                        </p>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            {{ $graceDaysRemaining }} {{ $graceDaysRemaining === 1 ? 'day' : 'days' }} remaining in your grace period
                        </p>
                    </div>
                </div>
                <a href="{{ route('coach.subscription.portal') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    Manage on Stripe
                </a>

            @elseif($currentPlanKey)
                {{-- State 3: Active paid subscription --}}
                <div class="flex items-start gap-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mb-4">
                    <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ ucfirst($currentPlanKey) }} plan — Active
                        </p>
                        <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                            @if($meteredClientCount !== null)
                                {{ $clientCount }} {{ $clientCount === 1 ? 'client' : 'clients' }}
                                ({{ $meteredClientCount }} metered @ €{{ $stripePrices['professional']['metered_formatted'] ?? '?' }}/client)
                            @elseif($clientLimit !== null)
                                {{ $clientCount }} / {{ $clientLimit }} {{ $clientCount === 1 ? 'client' : 'clients' }}
                            @else
                                {{ $clientCount }} {{ $clientCount === 1 ? 'client' : 'clients' }}
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ route('coach.subscription.portal') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    Manage on Stripe
                </a>

            @else
                {{-- State 4: No active subscription --}}
                <div class="flex items-start gap-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-800 p-4 mb-4">
                    <svg class="h-5 w-5 text-[#8e8e93] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-[#45515e] dark:text-gray-400">No active subscription</p>
                </div>
                <a href="{{ route('coach.subscription') }}" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    Choose a plan
                </a>
            @endif
        </div>
    </div>
</x-layouts.coach>
