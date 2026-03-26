<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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

    public function index(Request $request): RedirectResponse
    {
        $locale = $this->detectLocaleFromIp($request->ip());
        $urlPath = self::LOCALE_TO_URL[$locale] ?? 'en';

        return redirect("/{$urlPath}");
    }

    public function show(string $locale): View
    {
        $internalLocale = self::URL_TO_LOCALE[$locale] ?? 'en';
        App::setLocale($internalLocale);
        session(['locale' => $internalLocale]);

        return view('welcome');
    }

    private function detectLocaleFromIp(string $ip): string
    {
        return Cache::remember("ip_locale_{$ip}", now()->addDays(30), function () use ($ip): string {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");

                if ($response->ok()) {
                    $countryCode = $response->json('countryCode', '');

                    return self::COUNTRY_TO_LOCALE[$countryCode] ?? 'en';
                }
            } catch (\Throwable) {
                // Fall through to default
            }

            return 'en';
        });
    }
}
