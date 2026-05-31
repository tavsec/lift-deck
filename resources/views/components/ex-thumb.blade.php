{{-- Usage: <x-ex-thumb muscle="Back" :size="40" /> --}}
@props(['muscle' => 'default', 'size' => 40])

@php
$themes = [
    'back'        => ['from' => '#3b82f6', 'to' => '#1e40af', 'ic' => 'back'],
    'chest'       => ['from' => '#f0653e', 'to' => '#b8311a', 'ic' => 'chest'],
    'shoulders'   => ['from' => '#a06bff', 'to' => '#6d28d9', 'ic' => 'shoulder'],
    'shoulder'    => ['from' => '#a06bff', 'to' => '#6d28d9', 'ic' => 'shoulder'],
    'core'        => ['from' => '#2dd4bf', 'to' => '#0d9488', 'ic' => 'core'],
    'quadriceps'  => ['from' => '#34d27b', 'to' => '#15803d', 'ic' => 'legs'],
    'legs'        => ['from' => '#34d27b', 'to' => '#15803d', 'ic' => 'legs'],
    'glutes'      => ['from' => '#f472b6', 'to' => '#be185d', 'ic' => 'legs'],
    'biceps'      => ['from' => '#f5b53d', 'to' => '#c2790a', 'ic' => 'arm'],
    'triceps'     => ['from' => '#f59e3d', 'to' => '#c2620a', 'ic' => 'arm'],
    'arms'        => ['from' => '#f5b53d', 'to' => '#c2790a', 'ic' => 'arm'],
    'hamstrings'  => ['from' => '#34d27b', 'to' => '#15803d', 'ic' => 'legs'],
    'calves'      => ['from' => '#34d27b', 'to' => '#15803d', 'ic' => 'legs'],
    'default'     => ['from' => '#94a3b8', 'to' => '#475569', 'ic' => 'dumbbell'],
];

$key = strtolower(str_replace([' ', '-'], '_', $muscle));
$theme = $themes[$key] ?? $themes['default'];

$glyphs = [
    'back'     => '<path d="M12 3v18" /><path d="M12 6c-2.5 0-5 1.5-5 4M12 6c2.5 0 5 1.5 5 4" /><path d="M7 10c0 3 2 5 5 5s5-2 5-5" />',
    'chest'    => '<path d="M4 8c2-1.5 5-2 8-2s6 .5 8 2" /><path d="M4 8v4c0 3 3.5 5 8 5s8-2 8-5V8" /><path d="M12 6v11" />',
    'shoulder' => '<circle cx="12" cy="8" r="3.2" /><path d="M5 20c.5-4 3-6 7-6s6.5 2 7 6" />',
    'core'     => '<rect x="7" y="4" width="10" height="16" rx="3" /><path d="M7 9h10M7 13h10M12 4v16" />',
    'legs'     => '<path d="M9 3v7l-2 11M15 3v7l2 11" /><path d="M9 10h6" />',
    'arm'      => '<path d="M6 6v5a4 4 0 0 0 4 4h2" /><path d="M12 15a3 3 0 0 0 6 0v-2" /><circle cx="6" cy="5" r="1.5" fill="currentColor" stroke="none" />',
    'dumbbell' => '<path d="M6.5 6.5l11 11" /><path d="M3 10l-1-1a2 2 0 0 1 3-3l1 1M14 21l1 1a2 2 0 0 0 3-3l-1-1" />',
];

$glyph = $glyphs[$theme['ic']] ?? $glyphs['dumbbell'];
$borderRadius = round($size * 0.25);
$iconSize = round($size * 0.56);
@endphp

<div
    style="
        width: {{ $size }}px;
        height: {{ $size }}px;
        border-radius: {{ $borderRadius }}px;
        background: linear-gradient(150deg, {{ $theme['from'] }}, {{ $theme['to'] }});
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
        display: grid;
        place-items: center;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.12), inset 0 -10px 18px rgba(0,0,0,.22);
    "
>
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="white"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
        style="width: {{ $iconSize }}px; height: {{ $iconSize }}px; position: relative; z-index: 1; filter: drop-shadow(0 1px 2px rgba(0,0,0,.35));"
    >{!! $glyph !!}</svg>
    <div style="
        position: absolute; inset: 0;
        background: radial-gradient(120% 80% at 25% 15%, rgba(255,255,255,.28), transparent 55%);
        pointer-events: none;
    "></div>
</div>
