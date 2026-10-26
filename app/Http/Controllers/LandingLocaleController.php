<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class LandingLocaleController extends Controller
{
    /** @var array<string, string> Maps URL path segments to internal locale codes. */
    private const URL_TO_LOCALE = [
        'en' => 'en',
        'si' => 'sl',
        'hr' => 'hr',
    ];

    /** @var array<string, string> Maps ISO country codes to internal locale codes. */
    private const COUNTRY_TO_LOCALE = [
        'SI' => 'sl',
        'HR' => 'hr',
    ];

    /** @var array<string, string> Maps internal locale codes to URL path segments. */
    private const LOCALE_TO_URL = [
        'en' => 'en',
        'sl' => 'si',
        'hr' => 'hr',
    ];

    /**
     * Redirect the root URL to a localized landing page.
     *
     * Crawlers always get the x-default locale so search engines see a stable target;
     * humans are routed by the country Cloudflare resolves for their IP.
     */
    public function index(Request $request): RedirectResponse
    {
        $locale = $this->isCrawler($request)
            ? 'en'
            : self::COUNTRY_TO_LOCALE[strtoupper((string) $request->header('CF-IPCountry'))] ?? 'en';

        return redirect('/'.self::LOCALE_TO_URL[$locale]);
    }

    public function show(string $locale): View
    {
        $internalLocale = self::URL_TO_LOCALE[$locale] ?? 'en';
        App::setLocale($internalLocale);
        session(['locale' => $internalLocale]);

        return view('welcome');
    }

    private function isCrawler(Request $request): bool
    {
        return (bool) preg_match('/bot|crawl|spider|slurp|mediapartners|facebookexternalhit|embedly|preview/i', (string) $request->userAgent());
    }
}
