<?php

test('robots.txt is served by the route, references the sitemap, and blocks only private app areas', function (): void {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=utf-8')
        ->assertSee('Disallow: /coach/', false)
        ->assertSee('Disallow: /client/', false)
        ->assertSee('Disallow: /admin/', false)
        ->assertDontSee("Disallow: /\n", false)
        ->assertDontSee('Disallow: /login', false)
        ->assertDontSee('Disallow: /register', false)
        ->assertSee('Sitemap: '.url('/sitemap.xml'), false);
});

test('auth pages are noindexed so they can be crawled but stay out of search results', function (string $uri): void {
    $this->get($uri)
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex, follow">', false);
})->with(['/login', '/register', '/forgot-password']);

test('landing pages remain indexable', function (): void {
    $this->get('/en')->assertDontSee('noindex', false);
});

test('sitemap is valid xml and x-default points to an allowed, indexable url', function (): void {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=utf-8')
        ->assertSee('<loc>'.url('/en').'</loc>', false)
        ->assertSee('hreflang="x-default" href="'.url('/en').'"', false)
        ->assertSee('<loc>'.url('/en/terms').'</loc>', false);
});

test('sitemap omits changefreq and priority, which search engines ignore', function (): void {
    $this->get('/sitemap.xml')
        ->assertDontSee('<changefreq>', false)
        ->assertDontSee('<priority>', false);
});

test('landing page json-ld uses a google-eligible logo and a per-locale webpage node', function (): void {
    $html = $this->get('/si')->assertOk()->getContent();

    preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);
    $graph = collect(json_decode($matches[1], true)['@graph'])->keyBy('@type');

    expect($graph['Organization']['logo']['url'])->toEndWith('favicon-512.png')
        ->and($graph['Organization']['legalName'])->toBe('LiftDeck d.o.o.')
        ->and($graph['WebSite']['inLanguage'])->toBe(['en', 'sl', 'hr'])
        ->and($graph['WebPage']['url'])->toBe(url('/si'))
        ->and($graph['WebPage']['inLanguage'])->toBe('sl')
        ->and($graph['SoftwareApplication']['applicationCategory'])->toBe('BusinessApplication');
});

test('landing page self-hosts fonts instead of loading google fonts', function (): void {
    $this->get('/en')
        ->assertOk()
        ->assertDontSee('fonts.googleapis.com', false)
        ->assertSee('rel="preload" as="font"', false);
});
