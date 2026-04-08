<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full {{ auth()->check() && auth()->user()->dark_mode ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.register.step3.title') }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">

<div
    class="flex h-screen"
    x-data="{
        step: {{ $errors->any() ? 3 : 1 }},
        coachingType: 'solo',
        name: @js(old('name', '')),
        gymName: @js(old('gym_name', '')),
        bio: @js(old('bio', '')),
        goTo(n) { this.step = n; },
        canAdvance1() { return this.coachingType !== ''; }
    }"
>
    {{-- LEFT PANEL --}}
    <div class="hidden md:flex md:w-[38%] flex-col justify-between bg-[#0f172a] px-10 py-12 shrink-0">
        <div>
            <div class="font-display text-xl font-extrabold tracking-tight text-white">LiftDeck</div>
            <div class="mt-1 text-xs text-slate-500">{{ __('auth.register.actions.signin') }}</div>

            <p class="mt-10 text-xl font-bold leading-snug text-slate-200">
                {{ __('auth.register.panel.heading') }}
            </p>

            <ul class="mt-8 space-y-4">
                @foreach ([1,2,3,4] as $i)
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-900">
                            <svg class="h-2.5 w-2.5 text-blue-400" viewBox="0 0 10 10" fill="none">
                                <path d="M2 5l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <span class="text-sm font-semibold text-slate-300">{{ __('auth.register.panel.feature_'.$i) }}</span>
                            <span class="text-sm text-slate-500"> — {{ __('auth.register.panel.feature_'.$i.'_sub') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="text-xs text-slate-600">{{ __('auth.register.panel.trial_note') }}</p>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="flex flex-1 flex-col overflow-y-auto bg-gray-50 dark:bg-gray-950">
        <div class="mx-auto flex w-full max-w-lg flex-1 flex-col px-8 py-10 md:px-14 md:py-12">

            {{-- Mobile wordmark --}}
            <div class="mb-8 font-display text-lg font-extrabold text-[#222222] dark:text-gray-100 md:hidden">LiftDeck</div>

            {{-- Step indicator --}}
            <div class="mb-10 flex items-center gap-0">
                @foreach ([1,2,3] as $s)
                    <div
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-colors"
                        :class="{
                            'bg-[#1456f0] text-white shadow-[0_0_0_3px_#bfdbfe]': step === {{ $s }},
                            'bg-[#1456f0] text-white': step > {{ $s }},
                            'bg-gray-200 text-gray-400 dark:bg-gray-700 dark:text-gray-500': step < {{ $s }}
                        }"
                    >
                        <span x-show="step <= {{ $s }}">{{ $s }}</span>
                        <span x-show="step > {{ $s }}">
                            <svg class="h-3 w-3" viewBox="0 0 10 10" fill="none">
                                <path d="M2 5l2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                    @if ($s < 3)
                        <div class="h-0.5 flex-1 transition-colors" :class="step > {{ $s }} ? 'bg-[#1456f0]' : 'bg-gray-200 dark:bg-gray-700'"></div>
                    @endif
                @endforeach
            </div>

            <form method="POST" action="{{ route('register') }}" class="flex flex-1 flex-col">
                @csrf
                <input type="hidden" name="coaching_type" :value="coachingType">
                <input type="hidden" name="name" :value="name">
                <input type="hidden" name="gym_name" :value="gymName">
                <input type="hidden" name="bio" :value="bio">

                {{-- STEP 1 --}}
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-1 flex-col">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-[#8e8e93]">{{ __('auth.register.step1.label') }}</p>
                    <h1 class="mb-1 font-display text-2xl font-semibold text-[#222222]">{{ __('auth.register.step1.title') }}</h1>
                    <p class="mb-7 text-sm text-[#8e8e93]">{{ __('auth.register.step1.subtitle') }}</p>

                    <div class="space-y-3">
                        @foreach (['solo' => '🏋️', 'growing' => '📈', 'gym' => '🏟️'] as $type => $icon)
                            <button
                                type="button"
                                @click="coachingType = '{{ $type }}'"
                                class="w-full flex items-center gap-4 rounded-xl border-2 px-4 py-3.5 text-left transition-colors"
                                :class="coachingType === '{{ $type }}' ? 'border-[#1456f0] bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300 dark:border-gray-700 dark:bg-gray-900'"
                            >
                                <span class="text-2xl">{{ $icon }}</span>
                                <div>
                                    <div class="text-sm font-semibold dark:text-gray-100" :class="coachingType === '{{ $type }}' ? 'text-[#1456f0] dark:text-blue-400' : 'text-[#222222]'">
                                        {{ __('auth.register.step1.'.$type) }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-[#8e8e93]">{{ __('auth.register.step1.'.$type.'_sub') }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-auto flex justify-end pt-6">
                        <button
                            type="button"
                            @click="goTo(2)"
                            :disabled="!canAdvance1()"
                            class="rounded-lg bg-[#1456f0] px-7 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-40"
                        >
                            {{ __('auth.register.actions.continue') }}
                        </button>
                    </div>

                    <p class="mt-4 text-center text-xs text-[#8e8e93]">
                        {{ __('auth.register.actions.signin') }}
                        <a href="{{ route('login') }}" class="font-medium text-[#1456f0] hover:underline">{{ __('auth.register.actions.signin_link') }}</a>
                    </p>
                </div>

                {{-- STEP 2 --}}
                <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-1 flex-col">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-[#8e8e93]">{{ __('auth.register.step2.label') }}</p>
                    <h1 class="mb-1 font-display text-2xl font-semibold text-[#222222]">{{ __('auth.register.step2.title') }}</h1>
                    <p class="mb-7 text-sm text-[#8e8e93]">{{ __('auth.register.step2.subtitle') }}</p>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#45515e]">
                                {{ __('auth.register.step2.name') }}
                                <span class="ml-1 font-normal text-[#8e8e93]">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="text"
                                x-model="name"
                                {{-- no placeholder: name is a free-text personal name --}}
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-[#222222] dark:text-gray-100 focus:border-[#1456f0] focus:outline-none focus:ring-2 focus:ring-[#1456f0]/20"
                            >
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#45515e]">
                                {{ __('auth.register.step2.gym_name') }}
                                <span class="ml-1 font-normal text-[#8e8e93]">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="text"
                                x-model="gymName"
                                placeholder="{{ __('auth.register.step2.gym_name_ph') }}"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-[#222222] dark:text-gray-100 focus:border-[#1456f0] focus:outline-none focus:ring-2 focus:ring-[#1456f0]/20"
                            >
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#45515e]">
                                {{ __('auth.register.step2.bio') }}
                                <span class="ml-1 font-normal text-[#8e8e93]">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="text"
                                x-model="bio"
                                placeholder="{{ __('auth.register.step2.bio_ph') }}"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-[#222222] dark:text-gray-100 focus:border-[#1456f0] focus:outline-none focus:ring-2 focus:ring-[#1456f0]/20"
                            >
                        </div>

                        {{-- client_count is a UX-only field — not submitted or persisted. It is used to help users self-identify before plan selection. --}}
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#45515e]">
                                {{ __('auth.register.step2.client_count') }}
                                <span class="ml-1 font-normal text-[#8e8e93]">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="number"
                                min="0"
                                class="w-28 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-[#222222] dark:text-gray-100 focus:border-[#1456f0] focus:outline-none focus:ring-2 focus:ring-[#1456f0]/20"
                                placeholder="0"
                            >
                        </div>

                        {{-- tools[] is submitted but not persisted. It is a UX signal for future onboarding personalization. --}}
                        <div>
                            <label class="mb-2 block text-xs font-semibold text-[#45515e]">
                                {{ __('auth.register.step2.tools') }}
                                <span class="ml-1 font-normal text-[#8e8e93]">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (['tool_sheets', 'tool_excel', 'tool_whatsapp', 'tool_other'] as $tool)
                                    <label class="flex cursor-pointer items-center">
                                        <input type="checkbox" name="tools[]" value="{{ $tool }}" class="peer sr-only">
                                        <span class="rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-1 text-xs text-[#45515e] dark:text-gray-300 transition-colors peer-checked:border-[#1456f0] peer-checked:bg-blue-50 peer-checked:text-[#1456f0]">
                                            {{ __('auth.register.step2.'.$tool) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto flex items-center justify-between pt-6">
                        <button type="button" @click="goTo(1)" class="text-sm text-[#45515e] hover:text-[#222222]">
                            {{ __('auth.register.actions.back') }}
                        </button>
                        <div class="flex items-center gap-4">
                            <button type="button" @click="goTo(3)" class="text-xs text-[#8e8e93] underline hover:text-[#45515e]">
                                {{ __('auth.register.step2.skip') }}
                            </button>
                            <button type="button" @click="goTo(3)" class="rounded-lg bg-[#1456f0] px-7 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                {{ __('auth.register.actions.continue') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3 --}}
                <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-1 flex-col">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-[#8e8e93]">{{ __('auth.register.step3.label') }}</p>
                    <h1 class="mb-1 font-display text-2xl font-semibold text-[#222222]">{{ __('auth.register.step3.title') }}</h1>
                    <p class="mb-7 text-sm text-[#8e8e93]">{{ __('auth.register.step3.subtitle') }}</p>

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="mb-1 block text-xs font-semibold text-[#45515e]">{{ __('auth.register.step3.email') }}</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="{{ __('auth.register.step3.email_ph') }}"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-[#222222] dark:text-gray-100 focus:border-[#1456f0] focus:outline-none focus:ring-2 focus:ring-[#1456f0]/20 @error('email') border-red-400 @enderror"
                            >
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-1 block text-xs font-semibold text-[#45515e]">{{ __('auth.register.step3.password') }}</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="{{ __('auth.register.step3.password_ph') }}"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-[#222222] dark:text-gray-100 focus:border-[#1456f0] focus:outline-none focus:ring-2 focus:ring-[#1456f0]/20 @error('password') border-red-400 @enderror"
                            >
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-1 block text-xs font-semibold text-[#45515e]">{{ __('auth.register.step3.confirm') }}</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="{{ __('auth.register.step3.confirm_ph') }}"
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-[#222222] dark:text-gray-100 focus:border-[#1456f0] focus:outline-none focus:ring-2 focus:ring-[#1456f0]/20 @error('password_confirmation') border-red-400 @enderror"
                            >
                            @error('password_confirmation')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-auto flex items-center justify-between pt-6">
                        <button type="button" @click="goTo(2)" class="text-sm text-[#45515e] hover:text-[#222222]">
                            {{ __('auth.register.actions.back') }}
                        </button>
                        <button type="submit" class="rounded-lg bg-[#1456f0] px-7 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                            {{ __('auth.register.step3.submit') }}
                        </button>
                    </div>

                    <p class="mt-4 text-center text-xs text-[#8e8e93]">
                        {{ __('auth.register.actions.signin') }}
                        <a href="{{ route('login') }}" class="font-medium text-[#1456f0] hover:underline">{{ __('auth.register.actions.signin_link') }}</a>
                    </p>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
