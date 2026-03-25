<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('redirects root to /en when ip-api returns unknown country', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(['countryCode' => 'US'], 200)]);
    Cache::flush();

    $this->get('/')->assertRedirect('/en');
});

test('redirects root to /si for slovenian IP', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(['countryCode' => 'SI'], 200)]);
    Cache::flush();

    $this->get('/')->assertRedirect('/si');
});

test('redirects root to /hr for croatian IP', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(['countryCode' => 'HR'], 200)]);
    Cache::flush();

    $this->get('/')->assertRedirect('/hr');
});

test('falls back to /en when ip-api request fails', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(null, 500)]);
    Cache::flush();

    $this->get('/')->assertRedirect('/en');
});

test('caches ip locale lookup so ip-api is only called once', function (): void {
    Http::fake(['ip-api.com/*' => Http::response(['countryCode' => 'SI'], 200)]);
    Cache::flush();

    $this->get('/');
    $this->get('/');

    Http::assertSentCount(1);
});

test('serves landing page at /en', function (): void {
    $this->get('/en')->assertOk()->assertViewIs('welcome');
});

test('serves landing page at /si', function (): void {
    $this->get('/si')->assertOk()->assertViewIs('welcome');
});

test('serves landing page at /hr', function (): void {
    $this->get('/hr')->assertOk()->assertViewIs('welcome');
});
