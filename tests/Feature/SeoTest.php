<?php

test('robots.txt is served by the route, references the sitemap, and restricts crawling to landing pages', function (): void {
    $response = $this->get('/robots.txt');

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=utf-8')
        ->assertSee('Allow: /en', false)
        ->assertSee('Allow: /si', false)
        ->assertSee('Allow: /hr', false)
        ->assertSee('Disallow: /', false)
        ->assertSee('Sitemap: '.url('/sitemap.xml'), false);
});

test('robots.txt allows render-critical assets so Google can render the landing page', function (): void {
    $this->get('/robots.txt')
        ->assertSee('Allow: /build/', false)
        ->assertSee('Allow: /images/', false);
});

test('sitemap is valid xml and x-default points to an allowed, indexable url', function (): void {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=utf-8')
        ->assertSee('<loc>'.url('/en').'</loc>', false)
        ->assertSee('hreflang="x-default" href="'.url('/en').'"', false)
        ->assertSee('<loc>'.url('/en/terms').'</loc>', false);
});
