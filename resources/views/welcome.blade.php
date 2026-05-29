@php
    $currentLocale = app()->getLocale();
    $localeToUrl   = ['en' => 'en', 'sl' => 'si', 'hr' => 'hr'];
    $currentPath   = $localeToUrl[$currentLocale] ?? 'en';

    $faqKeys = ['right_for_me', 'cost', 'nutrition', 'nutrition_food_database', 'branding', 'app', 'get_started'];

    $nutritionHighlights = ['day_plans', 'food_database', 'smart_logging', 'macro_calculator', 'feedback', 'attention_dashboard'];

    $siteUrl      = url('/');
    $canonicalUrl = url('/' . $currentPath);

    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type' => 'Organization',
                '@id'   => $siteUrl . '/#organization',
                'name'  => 'LiftDeck',
                'url'   => $siteUrl,
                'email' => 'info@liftdeck.io',
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => asset('favicon-32.png'),
                ],
                'image' => asset('images/og.png'),
            ],
            [
                '@type'      => 'WebSite',
                '@id'        => $siteUrl . '/#website',
                'url'        => $siteUrl,
                'name'       => 'LiftDeck',
                'inLanguage' => str_replace('_', '-', $currentLocale),
                'publisher'  => ['@id' => $siteUrl . '/#organization'],
            ],
            [
                '@type'               => 'SoftwareApplication',
                '@id'                 => $siteUrl . '/#software',
                'name'                => 'LiftDeck',
                'url'                 => $canonicalUrl,
                'description'         => __('landing.meta.description'),
                'applicationCategory' => 'HealthApplication',
                'operatingSystem'     => 'Web',
                'publisher'           => ['@id' => $siteUrl . '/#organization'],
                'offers'              => [
                    '@type'         => 'AggregateOffer',
                    'priceCurrency' => 'EUR',
                    'lowPrice'      => '10',
                    'highPrice'     => '79',
                    'offerCount'    => '3',
                    'offers'        => [
                        ['@type' => 'Offer', 'price' => '10', 'priceCurrency' => 'EUR', 'name' => __('landing.pricing.basic.name')],
                        ['@type' => 'Offer', 'price' => '45', 'priceCurrency' => 'EUR', 'name' => __('landing.pricing.advanced.name')],
                        ['@type' => 'Offer', 'price' => '79', 'priceCurrency' => 'EUR', 'name' => __('landing.pricing.professional.name')],
                    ],
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

    {{-- SEO: Crawling directives — allow full snippets & large image previews in SERPs --}}
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="theme-color" content="#1456f0">

    {{-- Favicons --}}
    <x-favicons />

    {{-- SEO: Canonical --}}
    <link rel="canonical" href="{{ url('/' . $currentPath) }}">

    {{-- SEO: Hreflang for multilingual pages --}}
    <link rel="alternate" hreflang="en" href="{{ url('/en') }}">
    <link rel="alternate" hreflang="sl" href="{{ url('/si') }}">
    <link rel="alternate" hreflang="hr" href="{{ url('/hr') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/en') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/' . $currentPath) }}">
    <meta property="og:title" content="{{ __('landing.meta.title') }}">
    <meta property="og:description" content="{{ __('landing.meta.description') }}">
    <meta property="og:locale" content="{{ $currentLocale === 'sl' ? 'sl_SI' : ($currentLocale === 'hr' ? 'hr_HR' : 'en_US') }}">
    <meta property="og:image" content="{{ asset('images/og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ __('landing.meta.title') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('landing.meta.title') }}">
    <meta name="twitter:description" content="{{ __('landing.meta.description') }}">
    <meta name="twitter:image" content="{{ asset('images/og.png') }}">

    {{-- Sitemap discovery --}}
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4NVX4MTRKN"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('consent', 'default', {
            analytics_storage: 'denied',
            ad_storage: 'denied',
            ad_user_data: 'denied',
            ad_personalization: 'denied',
            wait_for_update: 500,
        });
        gtag('js', new Date());
        gtag('config', 'G-4NVX4MTRKN');
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-[#222222]">

    {{-- ANNOUNCEMENT BAR --}}
    @if(Route::has('register'))
        <a
            href="{{ route('register') }}"
            class="block bg-[#1456f0] text-white text-center text-xs sm:text-sm font-medium px-4 py-2.5 hover:bg-blue-700 transition-colors"
        >
            <span class="font-semibold">{{ __('landing.announcement.prefix') }}</span>
            <span class="opacity-90">{{ __('landing.announcement.body') }}</span>
            <span class="font-mono bg-white/15 border border-white/30 rounded px-1.5 py-0.5 mx-1">{{ __('landing.announcement.code') }}</span>
            <span class="font-semibold underline-offset-2 hover:underline">{{ __('landing.announcement.cta') }}</span>
        </a>
    @endif

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
        <section class="relative overflow-hidden pt-20 pb-12 lg:pt-24 lg:pb-20 px-6">
            {{-- subtle background gradient --}}
            <div aria-hidden="true" class="absolute inset-x-0 top-0 -z-10 h-[640px] bg-gradient-to-b from-blue-50/60 via-white to-transparent"></div>

            <div class="max-w-6xl mx-auto">
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 bg-blue-50 text-[#1456f0] rounded-full px-4 py-1.5 text-sm font-semibold mb-7 border border-blue-100">
                        <span class="w-2 h-2 rounded-full bg-[#1456f0]" aria-hidden="true"></span>
                        {{ __('landing.hero.badge') }}
                    </div>
                    <h1 class="font-display text-5xl md:text-[64px] font-medium text-[#181e25] leading-[1.10] tracking-tight mb-6">
                        {{ __('landing.hero.heading_1') }}<br>{{ __('landing.hero.heading_2') }}
                    </h1>
                    <p class="text-lg md:text-xl text-[#45515e] leading-relaxed max-w-2xl mx-auto mb-9">
                        {{ __('landing.hero.subheading') }}
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-[#181e25] text-white text-base font-semibold rounded-lg hover:bg-gray-800 transition-colors shadow-lg shadow-black/5">
                                {{ __('landing.hero.cta_primary') }} →
                            </a>
                        @endif
                        <a href="#features" class="w-full sm:w-auto px-8 py-3.5 bg-gray-100 text-[#333333] text-base font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                            {{ __('landing.hero.cta_secondary') }}
                        </a>
                    </div>
                    <p class="mt-5 text-sm text-[#8e8e93]">{{ __('landing.hero.cta_reassurance') }}</p>
                </div>

                {{-- Product mockup --}}
                @php
                    $heroSlides = [
                        ['file' => 'dashboard', 'alt_desktop' => 'LiftDeck coaching platform dashboard for personal trainers', 'alt_mobile' => 'LiftDeck coaching platform dashboard on mobile'],
                        ['file' => 'clients', 'alt_desktop' => 'LiftDeck client management roster for online coaches', 'alt_mobile' => 'LiftDeck client roster on mobile'],
                        ['file' => 'programs', 'alt_desktop' => 'LiftDeck training program builder with exercise library', 'alt_mobile' => 'LiftDeck training programs on mobile'],
                    ];
                @endphp

                <div class="mt-16 lg:mt-20 relative">
                    {{-- soft glow under the mockup --}}
                    <div aria-hidden="true" class="absolute inset-x-10 -bottom-6 h-24 bg-[#1456f0]/20 blur-3xl rounded-full"></div>

                    {{-- Desktop browser-frame mockup (hidden on mobile) --}}
                    <div class="hidden lg:block">
                        <div class="relative mx-auto max-w-5xl rounded-xl overflow-hidden bg-white border border-gray-200 shadow-2xl shadow-gray-900/10">
                            {{-- Browser chrome --}}
                            <div class="flex items-center gap-2 px-4 py-3 bg-gray-100 border-b border-gray-200">
                                <span class="w-3 h-3 rounded-full bg-[#ff5f57]" aria-hidden="true"></span>
                                <span class="w-3 h-3 rounded-full bg-[#febc2e]" aria-hidden="true"></span>
                                <span class="w-3 h-3 rounded-full bg-[#28c840]" aria-hidden="true"></span>
                                <div class="flex flex-1 justify-center">
                                    <div class="bg-white rounded-md px-3 py-1 text-xs text-[#8e8e93] border border-gray-200 inline-flex items-center gap-1.5 max-w-md w-full justify-center">
                                        <svg class="w-3 h-3 text-[#28c840]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                                        liftdeck.io
                                    </div>
                                </div>
                                <div class="w-16" aria-hidden="true"></div>
                            </div>
                            {{-- Slideshow --}}
                            <div class="relative w-full" style="aspect-ratio: 1440 / 900;">
                                @foreach($heroSlides as $i => $slide)
                                    <picture class="ld-slide ld-slide-{{ $i + 1 }}">
                                        <source type="image/webp" srcset="{{ asset('images/landing/' . $slide['file'] . '-full@1x.webp') }} 1x, {{ asset('images/landing/' . $slide['file'] . '-full@2x.webp') }} 2x">
                                        <img
                                            src="{{ asset('images/landing/' . $slide['file'] . '-full@1x.png') }}"
                                            srcset="{{ asset('images/landing/' . $slide['file'] . '-full@1x.png') }} 1x, {{ asset('images/landing/' . $slide['file'] . '-full@2x.png') }} 2x"
                                            alt="{{ $slide['alt_desktop'] }}"
                                            width="1440" height="900"
                                            class="absolute inset-0 block w-full h-full object-cover object-top"
                                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                            @if($i === 0) fetchpriority="high" @endif
                                        >
                                    </picture>
                                @endforeach
                            </div>
                        </div>

                        {{-- Static phone mockup overlapping bottom-right --}}
                        <div class="absolute -right-2 -bottom-10 w-[200px]">
                            <div class="rounded-[28px] bg-[#181e25] p-2 shadow-2xl shadow-gray-900/30">
                                <div class="rounded-[22px] overflow-hidden bg-white">
                                    <picture>
                                        <source type="image/webp" srcset="{{ asset('images/landing/dashboard-mobile@1x.webp') }} 1x, {{ asset('images/landing/dashboard-mobile@2x.webp') }} 2x">
                                        <img
                                            src="{{ asset('images/landing/dashboard-mobile@1x.png') }}"
                                            srcset="{{ asset('images/landing/dashboard-mobile@1x.png') }} 1x, {{ asset('images/landing/dashboard-mobile@2x.png') }} 2x"
                                            alt="LiftDeck mobile coach view"
                                            width="390" height="844"
                                            class="block w-full h-auto"
                                            loading="lazy"
                                        >
                                    </picture>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile phone-only showcase (hidden on lg+) --}}
                    <div class="lg:hidden mx-auto w-[260px] sm:w-[280px]">
                        <div class="rounded-[36px] bg-[#181e25] p-2 shadow-2xl shadow-gray-900/30">
                            <div class="relative rounded-[28px] overflow-hidden bg-white" style="aspect-ratio: 390 / 844;">
                                @foreach($heroSlides as $i => $slide)
                                    <picture class="ld-slide ld-slide-{{ $i + 1 }}">
                                        <source type="image/webp" srcset="{{ asset('images/landing/' . $slide['file'] . '-mobile@1x.webp') }} 1x, {{ asset('images/landing/' . $slide['file'] . '-mobile@2x.webp') }} 2x">
                                        <img
                                            src="{{ asset('images/landing/' . $slide['file'] . '-mobile@1x.png') }}"
                                            srcset="{{ asset('images/landing/' . $slide['file'] . '-mobile@1x.png') }} 1x, {{ asset('images/landing/' . $slide['file'] . '-mobile@2x.png') }} 2x"
                                            alt="{{ $slide['alt_mobile'] }}"
                                            width="390" height="844"
                                            class="absolute inset-0 block w-full h-full object-cover object-top"
                                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                        >
                                    </picture>
                                @endforeach
                            </div>
                        </div>
                    </div>
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

        {{-- HOW IT WORKS --}}
        <section class="py-20 px-6 bg-white" aria-label="How it works">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-14">
                    <div class="text-xs font-bold uppercase tracking-widest text-[#8e8e93] mb-3">{{ __('landing.how_it_works.label') }}</div>
                    <h2 class="font-display text-3xl md:text-[38px] font-semibold text-[#181e25] leading-tight max-w-2xl mx-auto">
                        {{ __('landing.how_it_works.heading') }}
                    </h2>
                </div>
                <div class="grid md:grid-cols-3 gap-x-8 gap-y-12 relative">
                    {{-- connecting dotted line on desktop --}}
                    <div aria-hidden="true" class="hidden md:block absolute top-12 left-[16.66%] right-[16.66%] h-px border-t-2 border-dashed border-blue-200/70"></div>

                    @php
                        $stepIllustrations = [
                            1 => '<svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <rect x="6" y="14" width="84" height="68" rx="10" fill="#EFF4FF" stroke="#1456f0" stroke-width="2"/>
                                <rect x="6" y="14" width="84" height="14" rx="10" fill="#1456f0"/>
                                <circle cx="13" cy="21" r="1.5" fill="#fff" opacity=".6"/>
                                <circle cx="19" cy="21" r="1.5" fill="#fff" opacity=".6"/>
                                <circle cx="25" cy="21" r="1.5" fill="#fff" opacity=".6"/>
                                <rect x="18" y="38" width="60" height="6" rx="2" fill="#1456f0" opacity=".25"/>
                                <rect x="18" y="50" width="60" height="20" rx="4" fill="#fff" stroke="#1456f0" stroke-width="1.5"/>
                                <circle cx="74" cy="60" r="6" fill="#1456f0"/>
                                <path d="M71.5 60l2 2 3.5-3.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>',
                            2 => '<svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <circle cx="32" cy="38" r="11" fill="#1456f0"/>
                                <path d="M16 76c0-9 7-15 16-15s16 6 16 15" fill="#1456f0" opacity=".25"/>
                                <circle cx="64" cy="32" r="8" fill="#fff" stroke="#1456f0" stroke-width="2"/>
                                <path d="M52 64c0-7 5-12 12-12s12 5 12 12" stroke="#1456f0" stroke-width="2" fill="none"/>
                                <circle cx="78" cy="20" r="9" fill="#fff" stroke="#1456f0" stroke-width="2"/>
                                <path d="M74 20h8M78 16v8" stroke="#1456f0" stroke-width="2" stroke-linecap="round"/>
                            </svg>',
                            3 => '<svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <rect x="8" y="16" width="80" height="64" rx="8" fill="#EFF4FF" stroke="#1456f0" stroke-width="2"/>
                                <path d="M16 64l14-16 12 8 14-22 14 14" stroke="#1456f0" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                <circle cx="30" cy="48" r="3" fill="#1456f0"/>
                                <circle cx="42" cy="56" r="3" fill="#1456f0"/>
                                <circle cx="56" cy="34" r="3" fill="#1456f0"/>
                                <circle cx="70" cy="48" r="3" fill="#1456f0"/>
                                <rect x="14" y="22" width="20" height="3" rx="1.5" fill="#1456f0" opacity=".3"/>
                            </svg>',
                        ];
                    @endphp

                    @foreach([1, 2, 3] as $step)
                        <div class="relative bg-white">
                            <div class="w-24 h-24 mb-5 mx-auto md:mx-0 relative">
                                {!! $stepIllustrations[$step] !!}
                                <div class="absolute -top-2 -left-2 flex h-8 w-8 items-center justify-center rounded-full bg-[#181e25] text-white font-display font-semibold text-sm shadow-lg ring-4 ring-white">
                                    {{ $step }}
                                </div>
                            </div>
                            <h3 class="font-display text-lg font-semibold text-[#181e25] mb-2 text-center md:text-left">
                                {{ __('landing.how_it_works.step_' . $step . '_title') }}
                            </h3>
                            <p class="text-sm text-[#45515e] leading-relaxed text-center md:text-left">
                                {{ __('landing.how_it_works.step_' . $step . '_body') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- FEATURES --}}
        <section id="features" class="py-20 px-6 bg-gray-50 border-t border-gray-100">
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

        {{-- NUTRITION DEEP-DIVE --}}
        @php
            $nutritionIcons = [
                'day_plans' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                'food_database' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
                'smart_logging' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                'macro_calculator' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m-6 4h6m-6 4h4m1 5H5a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>',
                'feedback' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-1 8a8 8 0 100-16 8 8 0 000 16z"/>',
                'attention_dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.48 0L3.16 16.25A2 2 0 005 19z"/>',
            ];
        @endphp
        <section id="nutrition" class="py-20 px-6 bg-white border-t border-gray-100" aria-label="Nutrition coaching">
            <div class="max-w-6xl mx-auto">
                <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-start">
                    <div class="lg:col-span-5 lg:sticky lg:top-24">
                        <div class="inline-flex items-center gap-2 bg-cyan-50 text-[#0891b2] rounded-full px-3.5 py-1 text-xs font-bold uppercase tracking-widest mb-5 border border-cyan-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#0891b2]" aria-hidden="true"></span>
                            {{ __('landing.nutrition_section.label') }}
                        </div>
                        <h2 class="font-display text-3xl md:text-[38px] font-semibold text-[#181e25] leading-tight mb-5">
                            {{ __('landing.nutrition_section.heading') }}
                        </h2>
                        <p class="text-[#45515e] leading-relaxed mb-6">
                            {{ __('landing.nutrition_section.subheading') }}
                        </p>
                        {{-- TODO: screenshot of day-plan editor / coach nutrition view --}}
                        <a href="#features" class="inline-flex items-center text-sm font-semibold text-[#1456f0] hover:text-blue-700 transition-colors">
                            {{ __('landing.nutrition_section.back_to_features') }} ↑
                        </a>
                    </div>

                    <div class="lg:col-span-7">
                        <ul class="grid sm:grid-cols-2 gap-4">
                            @foreach($nutritionHighlights as $key)
                                <li class="rounded-2xl bg-gray-50 border border-gray-100 p-5 flex flex-col gap-3 hover:border-cyan-200 hover:bg-white transition-colors">
                                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#0891b2] to-[#06b6d4] flex items-center justify-center" aria-hidden="true">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $nutritionIcons[$key] !!}
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-display text-sm font-semibold text-[#181e25] mb-1.5">
                                            {{ __('landing.nutrition_section.items.' . $key . '.title') }}
                                        </h3>
                                        <p class="text-sm text-[#45515e] leading-relaxed">
                                            {{ __('landing.nutrition_section.items.' . $key . '.description') }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- TESTIMONIALS --}}
        <section class="py-20 px-6 bg-white border-t border-gray-100">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-14">
                    <h2 class="font-display text-3xl md:text-[38px] font-semibold text-[#181e25] leading-tight">
                        {{ __('landing.testimonials.heading') }}
                    </h2>
                </div>
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach(['sarah', 'james', 'ana'] as $testimonial)
                        @php
                            $initials = collect(explode(' ', __('landing.testimonials.' . $testimonial . '.name')))
                                ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
                                ->implode('');
                        @endphp
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-7 flex flex-col gap-5">
                            <p class="text-[#45515e] text-sm leading-relaxed flex-1">&ldquo;{{ __('landing.testimonials.' . $testimonial . '.quote') }}&rdquo;</p>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#1456f0]/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-[#1456f0]">{{ $initials }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-[#181e25]">{{ __('landing.testimonials.' . $testimonial . '.name') }}</div>
                                    <div class="text-xs text-[#8e8e93]">{{ __('landing.testimonials.' . $testimonial . '.role') }}</div>
                                </div>
                            </div>
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

                <div class="mb-10 rounded-xl bg-blue-50 border border-blue-200 px-6 py-4 flex flex-col sm:flex-row items-center justify-center gap-3 text-center">
                    <span class="text-base" aria-hidden="true">🎉</span>
                    <span class="text-sm font-semibold text-blue-900">
                        {{ __('landing.pricing.founding_offer_prefix') }}
                        <span class="font-mono bg-white border border-blue-200 rounded px-2 py-0.5 text-[#1456f0] mx-1">FOUNDING70</span>
                        {{ __('landing.pricing.founding_offer_suffix') }}
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
                                {{ __('landing.pricing.subscribe') }}
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
                            <div class="text-xs font-medium text-[#1456f0] mb-2">{{ __('landing.pricing.professional.metered_note') }}</div>
                            <div class="text-sm text-[#8e8e93]">{{ __('landing.pricing.professional.description') }}</div>
                        </div>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full py-3 text-center bg-gray-100 text-[#181e25] font-semibold rounded-lg hover:bg-gray-200 transition-colors mb-6">
                                {{ __('landing.pricing.subscribe') }}
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
                <p class="mt-5 text-sm text-[#8e8e93]">{{ __('landing.cta.reassurance') }}</p>
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

    @include('components.cookie-banner')
</body>
</html>
