@php
    $currentLocale = app()->getLocale();
    $localeToUrl   = ['en' => 'en', 'sl' => 'si', 'hr' => 'hr'];
    $currentPath   = $localeToUrl[$currentLocale] ?? 'en';

    $faqKeys = ['right_for_me', 'cost', 'nutrition', 'branding', 'app', 'get_started'];

    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'               => 'SoftwareApplication',
                'name'                => 'LiftDeck',
                'description'         => __('landing.meta.description'),
                'applicationCategory' => 'HealthApplication',
                'operatingSystem'     => 'Web',
                'offers'              => [
                    ['@type' => 'Offer', 'price' => '10', 'priceCurrency' => 'EUR', 'name' => __('landing.pricing.basic.name')],
                    ['@type' => 'Offer', 'price' => '45', 'priceCurrency' => 'EUR', 'name' => __('landing.pricing.advanced.name')],
                    ['@type' => 'Offer', 'price' => '79', 'priceCurrency' => 'EUR', 'name' => __('landing.pricing.professional.name')],
                ],
            ],
            [
                '@type'      => 'FAQPage',
                'mainEntity' => array_map(function (string $key) {
                    return [
                        '@type'          => 'Question',
                        'name'           => __("landing.faq.questions.{$key}.question"),
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => __("landing.faq.questions.{$key}.answer"),
                        ],
                    ];
                }, $faqKeys),
            ],
        ],
    ];

    $landingLocales = [
        'en' => ['flag' => '🇬🇧', 'url' => 'en', 'label' => 'English'],
        'sl' => ['flag' => '🇸🇮', 'url' => 'si', 'label' => 'Slovenščina'],
        'hr' => ['flag' => '🇭🇷', 'url' => 'hr', 'label' => 'Hrvatski'],
    ];

    $featureCards = [
        ['key' => 'programs',    'gradient' => 'linear-gradient(135deg, #1456f0 0%, #3b82f6 100%)', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
        ['key' => 'check_ins',   'gradient' => 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'],
        ['key' => 'workout_log', 'gradient' => 'linear-gradient(135deg, #181e25 0%, #2d3a4a 100%)', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'],
        ['key' => 'nutrition',   'gradient' => 'linear-gradient(135deg, #0891b2 0%, #06b6d4 100%)', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
        ['key' => 'messaging',   'gradient' => 'linear-gradient(135deg, #ea580c 0%, #f97316 100%)', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>'],
        ['key' => 'rewards',     'gradient' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $currentLocale) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO: Title & Meta Description --}}
    <title>{{ __('landing.meta.title') }}</title>
    <meta name="description" content="{{ __('landing.meta.description') }}">

    {{-- SEO: Canonical --}}
    <link rel="canonical" href="{{ url('/' . $currentPath) }}">

    {{-- SEO: Hreflang for multilingual pages --}}
    <link rel="alternate" hreflang="en" href="{{ url('/en') }}">
    <link rel="alternate" hreflang="sl" href="{{ url('/si') }}">
    <link rel="alternate" hreflang="hr" href="{{ url('/hr') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/' . $currentPath) }}">
    <meta property="og:title" content="{{ __('landing.meta.title') }}">
    <meta property="og:description" content="{{ __('landing.meta.description') }}">
    <meta property="og:locale" content="{{ $currentLocale === 'sl' ? 'sl_SI' : ($currentLocale === 'hr' ? 'hr_HR' : 'en_US') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('landing.meta.title') }}">
    <meta name="twitter:description" content="{{ __('landing.meta.description') }}">

    {{-- Sitemap discovery --}}
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-[#222222]">

    {{-- NAVIGATION --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-sm border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center gap-8">
            <a href="{{ url('/' . $currentPath) }}" class="font-display font-bold text-xl tracking-tight flex-shrink-0" aria-label="LiftDeck home">
                Lift<span class="text-[#1456f0]">Deck</span>
            </a>
            <nav class="hidden md:flex items-center gap-1 flex-1" aria-label="Main navigation">
                <a href="#features" class="px-4 py-2 rounded-full text-sm font-medium text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors">{{ __('landing.nav.features') }}</a>
                <a href="#pricing" class="px-4 py-2 rounded-full text-sm font-medium text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors">{{ __('landing.nav.pricing') }}</a>
            </nav>
            <div class="flex items-center gap-3 ml-auto">
                {{-- Locale switcher --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        @click.outside="open = false"
                        type="button"
                        aria-label="Select language"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-full text-sm text-[#45515e] hover:text-[#222222] hover:bg-black/5 transition-colors"
                    >
                        <span class="text-base leading-none">{{ $landingLocales[$currentLocale]['flag'] }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div
                        x-show="open"
                        x-transition
                        class="absolute right-0 top-full mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                        role="menu"
                    >
                        @foreach($landingLocales as $locale => $meta)
                            <a
                                href="/{{ $meta['url'] }}"
                                role="menuitem"
                                hreflang="{{ $locale }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-sm {{ $currentLocale === $locale ? 'text-[#1456f0] font-medium' : 'text-[#45515e] hover:bg-gray-50' }}"
                            >
                                <span class="text-base">{{ $meta['flag'] }}</span>
                                <span>{{ $meta['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if(Route::has('login'))
                    <a href="{{ route('login') }}" class="text-sm font-medium text-[#45515e] hover:text-[#222222] transition-colors">{{ __('landing.nav.sign_in') }}</a>
                @endif
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="px-4 py-2.5 bg-[#181e25] text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        {{ __('landing.nav.get_started') }}
                    </a>
                @endif
            </div>
        </div>
    </header>

    <main>
        {{-- HERO --}}
        <section class="pt-24 pb-20 px-6 text-center">
            <div class="max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 bg-blue-50 text-[#1456f0] rounded-full px-4 py-1.5 text-sm font-semibold mb-8 border border-blue-100">
                    <span class="w-2 h-2 rounded-full bg-[#1456f0]" aria-hidden="true"></span>
                    {{ __('landing.hero.badge') }}
                </div>
                <h1 class="font-display text-5xl md:text-[64px] font-medium text-[#181e25] leading-[1.10] tracking-tight mb-6">
                    {{ __('landing.hero.heading_1') }}<br>{{ __('landing.hero.heading_2') }}
                </h1>
                <p class="text-lg md:text-xl text-[#45515e] leading-relaxed max-w-2xl mx-auto mb-10">
                    {{ __('landing.hero.subheading') }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-[#181e25] text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                            {{ __('landing.hero.cta_primary') }} →
                        </a>
                    @endif
                    <a href="#features" class="w-full sm:w-auto px-8 py-3.5 bg-gray-100 text-[#333333] text-base font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                        {{ __('landing.hero.cta_secondary') }}
                    </a>
                </div>
            </div>
        </section>

        {{-- PRODUCT HIGHLIGHTS --}}
        <section class="py-12 bg-gray-50 border-y border-gray-100" aria-label="Product highlights">
            <div class="max-w-4xl mx-auto px-6">
                <div class="grid grid-cols-3 gap-8 text-center">
                    <div>
                        <div class="font-display text-4xl font-semibold text-[#181e25]">{{ __('landing.stats.trial_value') }}</div>
                        <div class="text-sm text-[#8e8e93] mt-1.5">{{ __('landing.stats.trial_label') }}</div>
                    </div>
                    <div>
                        <div class="font-display text-4xl font-semibold text-[#181e25]">{{ __('landing.stats.exercises_value') }}</div>
                        <div class="text-sm text-[#8e8e93] mt-1.5">{{ __('landing.stats.exercises_label') }}</div>
                    </div>
                    <div>
                        <div class="font-display text-4xl font-semibold text-[#181e25]">{{ __('landing.stats.setup_value') }}</div>
                        <div class="text-sm text-[#8e8e93] mt-1.5">{{ __('landing.stats.setup_label') }}</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- FEATURES --}}
        <section id="features" class="py-20 px-6">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-14">
                    <div class="text-xs font-bold uppercase tracking-widest text-[#8e8e93] mb-3">{{ __('landing.features.label') }}</div>
                    <h2 class="font-display text-3xl md:text-[38px] font-semibold text-[#181e25] leading-tight">
                        {{ __('landing.features.heading_1') }}<br>{{ __('landing.features.heading_2') }}
                    </h2>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($featureCards as $card)
                        <div class="rounded-2xl p-7 text-white shadow-brand" style="background: {{ $card['gradient'] }}">
                            <div class="w-10 h-10 rounded-xl bg-white/20 mb-5 flex items-center justify-center" aria-hidden="true">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $card['icon'] !!}
                                </svg>
                            </div>
                            <h3 class="font-display text-lg font-semibold mb-2">{{ __('landing.features.' . $card['key'] . '.title') }}</h3>
                            <p class="text-sm text-white/80 leading-relaxed">{{ __('landing.features.' . $card['key'] . '.description') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- PRICING --}}
        <section id="pricing" class="py-20 px-6 bg-gray-50 border-t border-gray-100">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-10">
                    <div class="text-xs font-bold uppercase tracking-widest text-[#8e8e93] mb-3">{{ __('landing.pricing.label') }}</div>
                    <h2 class="font-display text-3xl md:text-[38px] font-semibold text-[#181e25] leading-tight">
                        {{ __('landing.pricing.heading') }}
                    </h2>
                    <p class="mt-4 text-[#45515e]">{{ __('landing.pricing.subheading') }}</p>
                </div>

                <div class="text-center mb-10">
                    <span class="inline-flex items-center gap-2 bg-amber-50 text-amber-800 rounded-full px-5 py-2 text-sm font-semibold border border-amber-200">
                        {{ __('landing.pricing.founding_offer') }}
                    </span>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    {{-- Basic --}}
                    <div class="rounded-2xl p-8 bg-white border border-gray-200 flex flex-col">
                        <div class="mb-6">
                            <div class="font-semibold text-[#181e25] mb-1">{{ __('landing.pricing.basic.name') }}</div>
                            <div class="flex items-end gap-1 mb-2">
                                <span class="font-display text-4xl font-bold text-[#181e25]">{{ __('landing.pricing.basic.price') }}</span>
                                <span class="text-sm text-[#8e8e93] mb-1">{{ __('landing.pricing.per_month') }}</span>
                            </div>
                            <div class="text-sm text-[#8e8e93]">{{ __('landing.pricing.basic.description') }}</div>
                        </div>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full py-3 text-center bg-gray-100 text-[#181e25] font-semibold rounded-lg hover:bg-gray-200 transition-colors mb-6">
                                {{ __('landing.pricing.get_started') }}
                            </a>
                        @endif
                        <ul class="space-y-3 mt-auto">
                            @foreach(trans('landing.pricing.basic.features') as $feature)
                                <li class="flex items-center gap-3 text-sm text-[#45515e]">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Advanced (popular) --}}
                    <div class="rounded-2xl p-8 bg-white border-2 border-[#1456f0] flex flex-col relative">
                        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                            <span class="bg-[#1456f0] text-white text-xs font-bold rounded-full px-4 py-1.5">{{ __('landing.pricing.popular') }}</span>
                        </div>
                        <div class="mb-6">
                            <div class="font-semibold text-[#181e25] mb-1">{{ __('landing.pricing.advanced.name') }}</div>
                            <div class="flex items-end gap-1 mb-2">
                                <span class="font-display text-4xl font-bold text-[#181e25]">{{ __('landing.pricing.advanced.price') }}</span>
                                <span class="text-sm text-[#8e8e93] mb-1">{{ __('landing.pricing.per_month') }}</span>
                            </div>
                            <div class="text-sm text-[#8e8e93]">{{ __('landing.pricing.advanced.description') }}</div>
                        </div>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full py-3 text-center bg-[#1456f0] text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors mb-6">
                                {{ __('landing.pricing.get_started') }}
                            </a>
                        @endif
                        <ul class="space-y-3 mt-auto">
                            @foreach(trans('landing.pricing.advanced.features') as $feature)
                                <li class="flex items-center gap-3 text-sm text-[#45515e]">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Professional --}}
                    <div class="rounded-2xl p-8 bg-white border border-gray-200 flex flex-col">
                        <div class="mb-6">
                            <div class="font-semibold text-[#181e25] mb-1">{{ __('landing.pricing.professional.name') }}</div>
                            <div class="flex items-end gap-1 mb-2">
                                <span class="font-display text-4xl font-bold text-[#181e25]">{{ __('landing.pricing.professional.price') }}</span>
                                <span class="text-sm text-[#8e8e93] mb-1">{{ __('landing.pricing.per_month') }}</span>
                            </div>
                            <div class="text-sm text-[#8e8e93]">{{ __('landing.pricing.professional.description') }}</div>
                        </div>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full py-3 text-center bg-gray-100 text-[#181e25] font-semibold rounded-lg hover:bg-gray-200 transition-colors mb-6">
                                {{ __('landing.pricing.get_started') }}
                            </a>
                        @endif
                        <ul class="space-y-3 mt-auto">
                            @foreach(trans('landing.pricing.professional.features') as $feature)
                                <li class="flex items-center gap-3 text-sm text-[#45515e]">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- FAQ --}}
        <section id="faq" class="py-20 px-6">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-14">
                    <div class="text-xs font-bold uppercase tracking-widest text-[#8e8e93] mb-3">{{ __('landing.faq.label') }}</div>
                    <h2 class="font-display text-3xl md:text-[38px] font-semibold text-[#181e25]">
                        {{ __('landing.faq.heading') }}
                    </h2>
                </div>
                <div x-data="{ open: null }" class="divide-y divide-gray-200 border-t border-gray-200">
                    @foreach($faqKeys as $key)
                        <div>
                            <button
                                @click="open = open === '{{ $key }}' ? null : '{{ $key }}'"
                                type="button"
                                class="w-full flex justify-between items-center py-5 text-left gap-4"
                                :aria-expanded="open === '{{ $key }}'"
                            >
                                <span class="font-medium text-[#181e25]">{{ __("landing.faq.questions.{$key}.question") }}</span>
                                <svg
                                    :class="open === '{{ $key }}' ? 'rotate-180' : ''"
                                    class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div
                                x-show="open === '{{ $key }}'"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="pb-5 text-[#45515e] text-sm leading-relaxed"
                            >
                                {{ __("landing.faq.questions.{$key}.answer") }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="py-20 px-6 bg-gray-50 border-t border-gray-100">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="font-display text-3xl md:text-4xl font-semibold text-[#181e25] mb-4">
                    {{ __('landing.cta.heading') }}
                </h2>
                <p class="text-[#45515e] text-lg mb-8">
                    {{ __('landing.cta.description') }}
                </p>
                @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 bg-[#181e25] text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                        {{ __('landing.cta.button') }} →
                    </a>
                @endif
            </div>
        </section>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-[#181e25] px-6 py-14" aria-label="Site footer">
        <div class="max-w-6xl mx-auto grid md:grid-cols-4 gap-10">
            <div class="md:col-span-1">
                <div class="font-display font-bold text-lg text-white mb-3">
                    Lift<span class="text-[#1456f0]">Deck</span>
                </div>
                <p class="text-sm text-white/50 leading-relaxed">
                    {{ __('landing.footer.tagline') }}
                </p>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">{{ __('landing.footer.product_label') }}</div>
                <div class="space-y-3">
                    <a href="#features" class="block text-sm text-white/70 hover:text-white transition-colors">{{ __('landing.footer.features_link') }}</a>
                    <a href="#pricing" class="block text-sm text-white/70 hover:text-white transition-colors">{{ __('landing.footer.pricing_link') }}</a>
                    @if(Route::has('login'))
                        <a href="{{ route('login') }}" class="block text-sm text-white/70 hover:text-white transition-colors">{{ __('landing.footer.sign_in_link') }}</a>
                    @endif
                </div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">{{ __('landing.footer.company_label') }}</div>
                <div class="space-y-3">
                    <a href="mailto:info@liftdeck.io" class="block text-sm text-white/50 hover:text-white/70 transition-colors">{{ __('landing.footer.contact_link') }}</a>
                </div>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">{{ __('landing.footer.legal_label') }}</div>
                <div class="space-y-3">
                    <a href="{{ route('terms', ['locale' => $currentPath]) }}" class="block text-sm text-white/70 hover:text-white transition-colors">{{ __('landing.footer.terms_link') }}</a>
                </div>
            </div>
        </div>
        <div class="max-w-6xl mx-auto mt-10 pt-8 border-t border-white/10">
            <p class="text-xs text-white/30">{{ __('landing.footer.copyright', ['year' => date('Y')]) }}</p>
        </div>
    </footer>

</body>
</html>
