<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach($locales as $hreflang => $path)
    <url>
        <loc>{{ $landingUrls[$hreflang] }}</loc>
        @foreach($locales as $altHreflang => $altPath)
        <xhtml:link rel="alternate" hreflang="{{ $altHreflang }}" href="{{ $landingUrls[$altHreflang] }}"/>
        @endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $xDefault }}"/>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
@endforeach
@foreach($locales as $hreflang => $path)
    <url>
        <loc>{{ $termsUrls[$hreflang] }}</loc>
        @foreach($locales as $altHreflang => $altPath)
        <xhtml:link rel="alternate" hreflang="{{ $altHreflang }}" href="{{ $termsUrls[$altHreflang] }}"/>
        @endforeach
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $termsUrls['en'] }}"/>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
@endforeach
</urlset>
