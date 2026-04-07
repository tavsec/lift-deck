@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center text-sm font-medium text-[#222222] dark:text-gray-100 border-b-2 border-[#1456f0] pb-1 transition-colors duration-150'
    : 'inline-flex items-center text-sm font-medium text-[#45515e] dark:text-gray-400 border-b-2 border-transparent pb-1 hover:text-[#222222] dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600 transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
