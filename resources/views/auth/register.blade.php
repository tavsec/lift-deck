<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.register.step3.title') }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">

<div
    class="flex h-screen"
    x-data="{
        step: {{ $errors->any() ? 3 : 1 }},
        coachingType: 'solo',
        name: '{{ old('name') }}',
        gymName: '{{ old('gym_name') }}',
        bio: '{{ old('bio') }}',
        goTo(n) { this.step = n; },
        canAdvance1() { return this.coachingType !== ''; }
    }"
>
    {{-- LEFT PANEL --}}
    <div class="hidden md:flex md:w-[38%] flex-col justify-between bg-[#0f172a] px-10 py-12 shrink-0">
        <div>
            <div class="text-white font-extrabold text-xl tracking-tight">LiftDeck</div>
            <div class="text-slate-500 text-xs mt-1">{{ __('auth.register.actions.signin') }}</div>

            <p class="mt-10 text-slate-200 font-bold text-xl leading-snug">
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
                            <span class="text-slate-300 text-sm font-semibold">{{ __('auth.register.panel.feature_'.$i) }}</span>
                            <span class="text-slate-500 text-sm"> — {{ __('auth.register.panel.feature_'.$i.'_sub') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="text-slate-600 text-xs">{{ __('auth.register.panel.trial_note') }}</p>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="flex flex-1 flex-col overflow-y-auto bg-slate-50">
        <div class="flex flex-col flex-1 px-8 py-10 md:px-14 md:py-12 max-w-lg w-full mx-auto">

            {{-- Mobile wordmark --}}
            <div class="md:hidden mb-8 font-extrabold text-lg text-slate-900">LiftDeck</div>

            {{-- Step indicator --}}
            <div class="flex items-center gap-0 mb-10">
                @foreach ([1,2,3] as $s)
                    <div
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-colors"
                        :class="{
                            'bg-blue-600 text-white shadow-[0_0_0_3px_#bfdbfe]': step === {{ $s }},
                            'bg-blue-600 text-white': step > {{ $s }},
                            'bg-slate-200 text-slate-400': step < {{ $s }}
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
                        <div class="flex-1 h-0.5 transition-colors" :class="step > {{ $s }} ? 'bg-blue-600' : 'bg-slate-200'"></div>
                    @endif
                @endforeach
            </div>

            <form method="POST" action="{{ route('register') }}" class="flex flex-col flex-1">
                @csrf
                <input type="hidden" name="coaching_type" :value="coachingType">
                <input type="hidden" name="name" :value="name">
                <input type="hidden" name="gym_name" :value="gymName">
                <input type="hidden" name="bio" :value="bio">

                {{-- STEP 1 --}}
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-col flex-1">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('auth.register.step1.label') }}</p>
                    <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ __('auth.register.step1.title') }}</h1>
                    <p class="text-sm text-slate-500 mb-7">{{ __('auth.register.step1.subtitle') }}</p>

                    <div class="space-y-3">
                        @foreach (['solo' => '🏋️', 'growing' => '📈', 'gym' => '🏟️'] as $type => $icon)
                            <button
                                type="button"
                                @click="coachingType = '{{ $type }}'"
                                class="w-full flex items-center gap-4 rounded-xl border-2 px-4 py-3.5 text-left transition-colors"
                                :class="coachingType === '{{ $type }}' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 bg-white hover:border-slate-300'"
                            >
                                <span class="text-2xl">{{ $icon }}</span>
                                <div>
                                    <div class="text-sm font-semibold" :class="coachingType === '{{ $type }}' ? 'text-blue-800' : 'text-slate-800'">
                                        {{ __('auth.register.step1.'.$type) }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ __('auth.register.step1.'.$type.'_sub') }}</div>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-auto pt-6 flex justify-end">
                        <button
                            type="button"
                            @click="goTo(2)"
                            :disabled="!canAdvance1()"
                            class="rounded-lg bg-blue-600 px-7 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            {{ __('auth.register.actions.continue') }}
                        </button>
                    </div>

                    <p class="mt-4 text-center text-xs text-slate-400">
                        {{ __('auth.register.actions.signin') }}
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">{{ __('auth.register.actions.signin_link') }}</a>
                    </p>
                </div>

                {{-- STEP 2 --}}
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-col flex-1">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('auth.register.step2.label') }}</p>
                    <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ __('auth.register.step2.title') }}</h1>
                    <p class="text-sm text-slate-500 mb-7">{{ __('auth.register.step2.subtitle') }}</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                {{ __('auth.register.step2.name') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="text"
                                x-model="name"
                                placeholder="{{ __('auth.register.step1.solo') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                {{ __('auth.register.step2.gym_name') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="text"
                                x-model="gymName"
                                placeholder="{{ __('auth.register.step2.gym_name_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                {{ __('auth.register.step2.bio') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="text"
                                x-model="bio"
                                placeholder="{{ __('auth.register.step2.bio_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">
                                {{ __('auth.register.step2.client_count') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <input
                                type="number"
                                min="0"
                                class="w-28 rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none"
                                placeholder="0"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">
                                {{ __('auth.register.step2.tools') }}
                                <span class="font-normal text-slate-400 ml-1">({{ __('auth.register.step2.optional') }})</span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (['tool_sheets', 'tool_excel', 'tool_whatsapp', 'tool_other'] as $tool)
                                    <label class="flex cursor-pointer items-center">
                                        <input type="checkbox" name="tools[]" value="{{ $tool }}" class="sr-only peer">
                                        <span class="rounded-full border border-slate-300 bg-white px-3 py-1 text-xs text-slate-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-colors">
                                            {{ __('auth.register.step2.'.$tool) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto pt-6 flex items-center justify-between">
                        <button type="button" @click="goTo(1)" class="text-sm text-slate-500 hover:text-slate-700">
                            {{ __('auth.register.actions.back') }}
                        </button>
                        <div class="flex items-center gap-4">
                            <button type="button" @click="goTo(3)" class="text-xs text-slate-400 underline hover:text-slate-600">
                                {{ __('auth.register.step2.skip') }}
                            </button>
                            <button type="button" @click="goTo(3)" class="rounded-lg bg-blue-600 px-7 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                {{ __('auth.register.actions.continue') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3 --}}
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-col flex-1">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2">{{ __('auth.register.step3.label') }}</p>
                    <h1 class="text-2xl font-bold text-slate-900 mb-1">{{ __('auth.register.step3.title') }}</h1>
                    <p class="text-sm text-slate-500 mb-7">{{ __('auth.register.step3.subtitle') }}</p>

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-600 mb-1">{{ __('auth.register.step3.email') }}</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="{{ __('auth.register.step3.email_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none @error('email') border-red-400 @enderror"
                            >
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-600 mb-1">{{ __('auth.register.step3.password') }}</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="{{ __('auth.register.step3.password_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none @error('password') border-red-400 @enderror"
                            >
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1">{{ __('auth.register.step3.confirm') }}</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="{{ __('auth.register.step3.confirm_ph') }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-blue-500 focus:outline-none @error('password_confirmation') border-red-400 @enderror"
                            >
                            @error('password_confirmation')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-auto pt-6 flex items-center justify-between">
                        <button type="button" @click="goTo(2)" class="text-sm text-slate-500 hover:text-slate-700">
                            {{ __('auth.register.actions.back') }}
                        </button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-7 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                            {{ __('auth.register.step3.submit') }}
                        </button>
                    </div>

                    <p class="mt-4 text-center text-xs text-slate-400">
                        {{ __('auth.register.actions.signin') }}
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">{{ __('auth.register.actions.signin_link') }}</a>
                    </p>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
