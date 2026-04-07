<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LiftDeck — Coaching Platform for Fitness Professionals</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-[#222222]">

    {{-- NAVIGATION --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-sm border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center gap-8">
            <a href="/" class="font-display font-bold text-xl tracking-tight flex-shrink-0">
                Lift<span class="text-[#1456f0]">Deck</span>
            </a>
            <nav class="hidden md:flex items-center gap-1 flex-1">
                <a href="#features" class="px-4 py-2 rounded-full text-sm font-medium text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors">Features</a>
                <a href="#pricing" class="px-4 py-2 rounded-full text-sm font-medium text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors">Pricing</a>
                <a href="#coaches" class="px-4 py-2 rounded-full text-sm font-medium text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors">For Coaches</a>
            </nav>
            <div class="flex items-center gap-3 ml-auto">
                {{-- LOCALE SWITCHER --}}
                @php
                    $currentLocale = app()->getLocale();
                    $landingLocales = [
                        'en' => ['flag' => '🇬🇧', 'url' => 'en'],
                        'sl' => ['flag' => '🇸🇮', 'url' => 'si'],
                        'hr' => ['flag' => '🇭🇷', 'url' => 'hr'],
                    ];
                @endphp
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        @click.outside="open = false"
                        type="button"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-full text-sm text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors"
                    >
                        <span class="text-base leading-none">{{ $landingLocales[$currentLocale]['flag'] }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div
                        x-show="open"
                        x-transition
                        class="absolute right-0 top-full mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                    >
                        @foreach($landingLocales as $locale => $meta)
                            <a
                                href="/{{ $meta['url'] }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm {{ $currentLocale === $locale ? 'text-[#1456f0] font-medium' : 'text-[#45515e] hover:bg-gray-50' }}"
                            >
                                <span class="text-base">{{ $meta['flag'] }}</span>
                                <span>{{ ['en' => 'English', 'sl' => 'Slovenščina', 'hr' => 'Hrvatski'][$locale] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if(Route::has('login'))
                    <a href="{{ route('login') }}" class="text-sm font-medium text-[#45515e] hover:text-[#222222] transition-colors">Sign in</a>
                @endif
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="px-4 py-2.5 bg-[#181e25] text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        Get started free
                    </a>
                @endif
            </div>
        </div>
    </header>

    {{-- HERO --}}
    <section class="pt-24 pb-20 px-6 text-center">
        <div class="max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-blue-50 text-[#1456f0] rounded-full px-4 py-1.5 text-sm font-semibold mb-8 border border-blue-100">
                <span class="w-2 h-2 rounded-full bg-[#1456f0]"></span>
                Built for fitness coaches
            </div>
            <h1 class="font-display text-5xl md:text-[64px] font-medium text-[#181e25] leading-[1.10] tracking-tight mb-6">
                Your coaching.<br>Their <span class="text-[#1456f0]">progress</span>.
            </h1>
            <p class="text-lg md:text-xl text-[#45515e] leading-relaxed max-w-2xl mx-auto mb-10">
                LiftDeck gives fitness coaches a complete platform — programs, check-ins, nutrition, messaging, and rewards — all in one place, accessible from any device.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-[#181e25] text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        Start free trial →
                    </a>
                @endif
                <a href="#features" class="w-full sm:w-auto px-8 py-3.5 bg-gray-100 text-[#333333] text-base font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                    See how it works
                </a>
            </div>
        </div>
    </section>

    {{-- SOCIAL PROOF --}}
    <section class="py-12 bg-gray-50 border-y border-gray-100">
        <div class="max-w-4xl mx-auto px-6">
            <div class="grid grid-cols-3 gap-8 text-center">
                <div>
                    <div class="font-display text-4xl font-semibold text-[#181e25]">500+</div>
                    <div class="text-sm text-[#8e8e93] mt-1.5">Coaches using LiftDeck</div>
                </div>
                <div>
                    <div class="font-display text-4xl font-semibold text-[#181e25]">12k+</div>
                    <div class="text-sm text-[#8e8e93] mt-1.5">Active clients tracked</div>
                </div>
                <div>
                    <div class="font-display text-4xl font-semibold text-[#181e25]">98%</div>
                    <div class="text-sm text-[#8e8e93] mt-1.5">Client retention rate</div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <div class="text-xs font-bold uppercase tracking-widest text-[#8e8e93] mb-3">Everything you need</div>
                <h2 class="font-display text-3xl md:text-[38px] font-semibold text-[#181e25] leading-tight">
                    The complete toolkit<br>for modern coaches
                </h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="rounded-2xl p-7 text-white shadow-brand" style="background: linear-gradient(135deg, #1456f0 0%, #3b82f6 100%);">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Training Programs</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Build and assign custom programs with exercise libraries, sets, reps, and progression tracking.</p>
                </div>
                <div class="rounded-2xl p-7 text-white shadow-brand" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Client Check-ins</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Collect weekly check-ins with photos, metrics, and mood tracking — all in one dashboard.</p>
                </div>
                <div class="rounded-2xl p-7 text-white shadow-brand" style="background: linear-gradient(135deg, #181e25 0%, #2d3a4a 100%);">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Workout Logging</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Clients log workouts from their phone. You see every rep, set, and personal best in real time.</p>
                </div>
                <div class="rounded-2xl p-7 text-white shadow-brand" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Nutrition Plans</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Create meal plans and track macros. Keep nutrition and training aligned for every client.</p>
                </div>
                <div class="rounded-2xl p-7 text-white shadow-brand" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Messaging</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Direct messaging between coach and client — no switching apps, no lost threads.</p>
                </div>
                <div class="rounded-2xl p-7 text-white shadow-brand" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold mb-2">Loyalty & Rewards</h3>
                    <p class="text-sm text-white/80 leading-relaxed">Motivate clients with points, achievements, and redeemable rewards tied to their progress.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="py-20 px-6 bg-gray-50 border-t border-gray-100">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="font-display text-3xl md:text-4xl font-semibold text-[#181e25] mb-4">
                Ready to grow your coaching business?
            </h2>
            <p class="text-[#45515e] text-lg mb-8">
                Join hundreds of coaches already using LiftDeck to deliver better results.
            </p>
            @if(Route::has('register'))
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 bg-[#181e25] text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                    Start your free trial →
                </a>
            @endif
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-[#181e25] px-6 py-14">
        <div class="max-w-6xl mx-auto grid md:grid-cols-4 gap-10">
            <div class="md:col-span-1">
                <div class="font-display font-bold text-lg text-white mb-3">
                    Lift<span class="text-[#1456f0]">Deck</span>
                </div>
                <p class="text-sm text-white/50 leading-relaxed">
                    The complete platform for fitness coaches who want to deliver results at scale.
                </p>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Product</div>
                <div class="space-y-3">
                    <a href="#features" class="block text-sm text-white/70 hover:text-white transition-colors">Features</a>
                    <a href="#pricing" class="block text-sm text-white/70 hover:text-white transition-colors">Pricing</a>
                    @if(Route::has('login'))
                        <a href="{{ route('login') }}" class="block text-sm text-white/70 hover:text-white transition-colors">Sign in</a>
                    @endif
                </div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Company</div>
                <div class="space-y-3">
                    <span class="block text-sm text-white/50">About</span>
                    <span class="block text-sm text-white/50">Blog</span>
                    <span class="block text-sm text-white/50">Contact</span>
                </div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">Legal</div>
                <div class="space-y-3">
                    <span class="block text-sm text-white/50">Privacy</span>
                    <span class="block text-sm text-white/50">Terms</span>
                </div>
            </div>
        </div>
        <div class="max-w-6xl mx-auto mt-10 pt-8 border-t border-white/10">
            <p class="text-xs text-white/30">© {{ date('Y') }} LiftDeck. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
