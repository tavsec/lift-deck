<?php

use App\Http\Middleware\RedirectTrailingSlash;
use Illuminate\Http\Request;

test('redirects root to /en for an unmapped country', function (): void {
    $this->get('/', ['CF-IPCountry' => 'US'])->assertRedirect('/en');
});

test('redirects root to /si for slovenian visitors', function (): void {
    $this->get('/', ['CF-IPCountry' => 'SI'])->assertRedirect('/si');
});

test('redirects root to /hr for croatian visitors', function (): void {
    $this->get('/', ['CF-IPCountry' => 'HR'])->assertRedirect('/hr');
});

test('falls back to /en when the country header is missing', function (): void {
    $this->get('/')->assertRedirect('/en');
});

test('always redirects crawlers to the x-default locale', function (string $userAgent): void {
    $this->get('/', ['CF-IPCountry' => 'SI', 'User-Agent' => $userAgent])->assertRedirect('/en');
})->with([
    'googlebot' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    'bingbot' => 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
]);

test('redirects trailing slash urls to the slash-less canonical url', function (string $uri, string $expected): void {
    $response = (new RedirectTrailingSlash)->handle(
        Request::create($uri),
        fn () => response('next'),
    );

    expect($response->getStatusCode())->toBe(301)
        ->and($response->headers->get('Location'))->toBe($expected);
})->with([
    'plain' => ['https://liftdeck.io/en/', 'https://liftdeck.io/en'],
    'with query' => ['https://liftdeck.io/si/?ref=newsletter', 'https://liftdeck.io/si?ref=newsletter'],
]);

test('does not redirect the root, slash-less urls or unsafe methods', function (string $uri, string $method): void {
    $response = (new RedirectTrailingSlash)->handle(
        Request::create($uri, $method),
        fn () => response('next'),
    );

    expect($response->getContent())->toBe('next');
})->with([
    'root' => ['https://liftdeck.io/', 'GET'],
    'canonical' => ['https://liftdeck.io/en', 'GET'],
    'post' => ['https://liftdeck.io/login/', 'POST'],
]);

test('serves landing page at /en', function (): void {
    $this->get('/en')->assertOk()->assertViewIs('welcome');
});

test('serves landing page at /si', function (): void {
    $this->get('/si')->assertOk()->assertViewIs('welcome');
});

test('serves landing page at /hr', function (): void {
    $this->get('/hr')->assertOk()->assertViewIs('welcome');
});

test('landing page renders favicon links and og image', function (): void {
    $this->get('/en')
        ->assertOk()
        ->assertSee('rel="icon" type="image/svg+xml" href="/favicon.svg"', false)
        ->assertSee('rel="apple-touch-icon" href="/apple-touch-icon.png"', false)
        ->assertSee('property="og:image" content="'.asset('images/og.png').'"', false)
        ->assertSee('name="twitter:image" content="'.asset('images/og.png').'"', false);
});
