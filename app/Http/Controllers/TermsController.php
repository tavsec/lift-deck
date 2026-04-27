<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class TermsController extends Controller
{
    /** @var array<string, string> Maps URL path segment to internal locale code. */
    private const URL_TO_LOCALE = [
        'en' => 'en',
        'si' => 'sl',
        'hr' => 'hr',
    ];

    public function show(string $locale): View
    {
        $internalLocale = self::URL_TO_LOCALE[$locale] ?? 'en';
        App::setLocale($internalLocale);

        return view('terms', ['urlLocale' => $locale]);
    }
}
