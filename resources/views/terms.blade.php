@php
    $currentLocale  = app()->getLocale();
    $localeToUrl    = ['en' => 'en', 'sl' => 'si', 'hr' => 'hr'];
    $currentPath    = $localeToUrl[$currentLocale] ?? 'en';

    $landingLocales = [
        'en' => ['flag' => '🇬🇧', 'url' => 'en', 'label' => 'English'],
        'sl' => ['flag' => '🇸🇮', 'url' => 'si', 'label' => 'Slovenščina'],
        'hr' => ['flag' => '🇭🇷', 'url' => 'hr', 'label' => 'Hrvatski'],
    ];

    $titles = [
        'en' => 'Terms and Conditions — LiftDeck',
        'sl' => 'Splošni pogoji uporabe — LiftDeck',
        'hr' => 'Uvjeti korištenja — LiftDeck',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $currentLocale) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $titles[$currentLocale] ?? $titles['en'] }}</title>
    <meta name="robots" content="index, follow">

    <link rel="canonical" href="{{ url('/' . $currentPath . '/terms') }}">

    <link rel="alternate" hreflang="en" href="{{ url('/en/terms') }}">
    <link rel="alternate" hreflang="sl" href="{{ url('/si/terms') }}">
    <link rel="alternate" hreflang="hr" href="{{ url('/hr/terms') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/en/terms') }}">

    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-[#222222]">

    {{-- NAVIGATION --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-sm border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center gap-8">
            <a href="{{ url('/' . $currentPath) }}" class="font-display font-bold text-xl tracking-tight flex-shrink-0" aria-label="LiftDeck home">
                Lift<span class="text-[#1456f0]">Deck</span>
            </a>
            <div class="flex items-center gap-3 ml-auto">
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
                    <div x-show="open" x-transition class="absolute right-0 top-full mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50" role="menu">
                        @foreach($landingLocales as $locale => $meta)
                            <a
                                href="/{{ $meta['url'] }}/terms"
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
        <div class="max-w-3xl mx-auto px-6 py-16">
            @include('terms.' . $currentLocale)
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-[#181e25] px-6 py-14" aria-label="Site footer">
        <div class="max-w-6xl mx-auto grid md:grid-cols-4 gap-10">
            <div class="md:col-span-1">
                <div class="font-display font-bold text-lg text-white mb-3">
                    Lift<span class="text-[#1456f0]">Deck</span>
                </div>
                <p class="text-sm text-white/50 leading-relaxed">{{ __('landing.footer.tagline') }}</p>
            </div>
            <div>
                <div class="text-xs font-bold uppercase tracking-widest text-white/40 mb-4">{{ __('landing.footer.product_label') }}</div>
                <div class="space-y-3">
                    <a href="{{ url('/' . $currentPath . '#features') }}" class="block text-sm text-white/70 hover:text-white transition-colors">{{ __('landing.footer.features_link') }}</a>
                    <a href="{{ url('/' . $currentPath . '#pricing') }}" class="block text-sm text-white/70 hover:text-white transition-colors">{{ __('landing.footer.pricing_link') }}</a>
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
