@php
    $currentLocale = auth()->user()->locale ?? 'en';
    $locales = [
        'en' => ['flag' => '🇬🇧', 'label' => 'English'],
        'sl' => ['flag' => '🇸🇮', 'label' => 'Slovenščina'],
        'hr' => ['flag' => '🇭🇷', 'label' => 'Hrvatski'],
    ];
@endphp

<div x-data="{ open: false }" class="relative">
    <button
        @click="open = !open"
        @click.outside="open = false"
        type="button"
        class="flex items-center gap-2 text-base text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 rounded px-2 py-1.5"
    >
        <span class="text-lg">{{ $locales[$currentLocale]['flag'] }}</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition
        class="absolute right-0 top-full mt-1 w-40 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-50"
    >
        @foreach($locales as $locale => $meta)
            <form method="POST" action="{{ route('user.locale.update') }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="locale" value="{{ $locale }}">
                <button
                    type="submit"
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-left {{ $currentLocale === $locale ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                >
                    <span class="text-base">{{ $meta['flag'] }}</span>
                    <span>{{ $meta['label'] }}</span>
                </button>
            </form>
        @endforeach
    </div>
</div>
