@php $currentLocale = auth()->user()->locale ?? 'en'; @endphp

<div class="flex items-center gap-1">
    @foreach(['en' => 'EN', 'sl' => 'SL', 'hr' => 'HR'] as $locale => $label)
        <form method="POST" action="{{ route('user.locale.update') }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="locale" value="{{ $locale }}">
            <button
                type="submit"
                class="text-xs font-semibold px-2 py-1 rounded {{ $currentLocale === $locale ? 'bg-blue-600 text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}"
            >{{ $label }}</button>
        </form>
    @endforeach
</div>
