@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3']) }}>
        <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ $status }}</p>
    </div>
@endif
