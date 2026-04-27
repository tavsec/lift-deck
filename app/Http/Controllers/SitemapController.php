<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /** @var array<string, string> Maps hreflang code to URL path segment. */
    private const LOCALES = [
        'en' => 'en',
        'sl' => 'si',
        'hr' => 'hr',
    ];

    public function sitemap(): Response
    {
        $landingUrls = array_map(
            fn (string $path) => url("/{$path}"),
            self::LOCALES,
        );

        $termsUrls = array_map(
            fn (string $path) => url("/{$path}/terms"),
            self::LOCALES,
        );

        return response()
            ->view('sitemap', [
                'landingUrls' => $landingUrls,
                'termsUrls' => $termsUrls,
                'locales' => self::LOCALES,
                'xDefault' => url('/'),
            ])
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function robots(): Response
    {
        return response()
            ->view('robots', ['sitemapUrl' => url('/sitemap.xml')])
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
