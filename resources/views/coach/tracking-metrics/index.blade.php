<x-layouts.coach>
    <x-slot:title>{{ __('coach.tracking_metrics.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.tracking_metrics.heading') }}</h1>
                <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('coach.tracking_metrics.subtitle') }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add New Metric -->
        <div x-data="{ open: false }" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card">
            <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-6 py-4 text-left">
                <span class="flex items-center text-sm font-semibold text-[#181e25] dark:text-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('coach.tracking_metrics.add') }}
                </span>
                <svg class="w-5 h-5 text-[#8e8e93] dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-cloak class="border-t border-gray-100 dark:border-gray-800 px-6 py-5">
                <form method="POST" action="{{ route('coach.tracking-metrics.store') }}" x-data="{ type: 'number' }" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.tracking_metrics.name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150"
                                placeholder="e.g., Body Weight">
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.tracking_metrics.type') }} <span class="text-red-500">*</span></label>
                            <select name="type" id="type" x-model="type" required
                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                <option value="number">{{ __('coach.tracking_metrics.type_number') }}</option>
                                <option value="scale">{{ __('coach.tracking_metrics.type_scale') }}</option>
                                <option value="boolean">{{ __('coach.tracking_metrics.type_boolean') }}</option>
                                <option value="text">{{ __('coach.tracking_metrics.type_text') }}</option>
                                <option value="image">{{ __('coach.tracking_metrics.type_image') }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.tracking_metrics.description') }}</label>
                        <input type="text" name="description" id="description" value="{{ old('description') }}"
                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150"
                            placeholder="Optional hint shown to clients, e.g., 'Weigh yourself first thing in the morning'">
                        @error('description')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div x-show="type === 'number'">
                            <label for="unit" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.tracking_metrics.unit') }}</label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit') }}"
                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150"
                                placeholder="e.g., kg, steps, ml">
                        </div>

                        <div x-show="type === 'scale'">
                            <label for="scale_min" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.tracking_metrics.scale_min') }}</label>
                            <input type="number" name="scale_min" id="scale_min" value="{{ old('scale_min', 1) }}"
                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                        </div>

                        <div x-show="type === 'scale'">
                            <label for="scale_max" class="block text-sm font-medium text-[#45515e] dark:text-gray-300 mb-1.5">{{ __('coach.tracking_metrics.scale_max') }}</label>
                            <input type="number" name="scale_max" id="scale_max" value="{{ old('scale_max', 5) }}"
                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                            {{ __('coach.tracking_metrics.add_button') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Metrics List -->
        @php $activeMetrics = $metrics->where('is_active', true); @endphp
        @php $inactiveMetrics = $metrics->where('is_active', false); @endphp

        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="font-display text-base font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.tracking_metrics.active_heading', ['n' => $activeMetrics->count()]) }}</h2>
                <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-0.5">{{ __('coach.tracking_metrics.active_description') }}</p>
            </div>

            @if($activeMetrics->count() > 0)
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($activeMetrics as $index => $metric)
                        <li x-data="{ editing: false }" class="px-6 py-4">
                            <!-- Display Mode -->
                            <div x-show="!editing" class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-[#8e8e93] dark:text-gray-500 w-6 text-right">{{ $index + 1 }}.</span>
                                    <div>
                                        <p class="text-sm font-medium text-[#222222] dark:text-gray-100">{{ $metric->name }}</p>
                                        @if($metric->description)
                                            <p class="text-xs text-[#45515e] dark:text-gray-400">{{ $metric->description }}</p>
                                        @endif
                                        <p class="text-xs text-[#8e8e93] dark:text-gray-500">
                                            @if($metric->type === 'number')
                                                {{ __('coach.tracking_metrics.type_number_label') }}{{ $metric->unit ? " ({$metric->unit})" : '' }}
                                            @elseif($metric->type === 'scale')
                                                {{ __('coach.tracking_metrics.type_scale_label') }} ({{ $metric->scale_min }}-{{ $metric->scale_max }})
                                            @elseif($metric->type === 'boolean')
                                                {{ __('coach.tracking_metrics.type_boolean') }}
                                            @elseif($metric->type === 'text')
                                                {{ __('coach.tracking_metrics.type_text_label') }}
                                            @elseif($metric->type === 'image')
                                                {{ __('coach.tracking_metrics.type_image_label') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <!-- Move Up -->
                                    @if(!$loop->first)
                                        <form method="POST" action="{{ route('coach.tracking-metrics.move-up', $metric) }}">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-[#8e8e93] dark:text-gray-500 hover:text-[#45515e] dark:hover:text-gray-300 rounded transition-colors" title="Move up">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Move Down -->
                                    @if(!$loop->last)
                                        <form method="POST" action="{{ route('coach.tracking-metrics.move-down', $metric) }}">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-[#8e8e93] dark:text-gray-500 hover:text-[#45515e] dark:hover:text-gray-300 rounded transition-colors" title="Move down">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit -->
                                    <button @click="editing = true" class="p-1.5 text-[#8e8e93] dark:text-gray-500 hover:text-[#45515e] dark:hover:text-gray-300 rounded transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Deactivate -->
                                    <form method="POST" action="{{ route('coach.tracking-metrics.destroy', $metric) }}" onsubmit="return confirm('{{ __('coach.tracking_metrics.deactivate_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-[#8e8e93] dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded transition-colors" title="Deactivate">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Edit Mode -->
                            <div x-show="editing" x-cloak>
                                <form method="POST" action="{{ route('coach.tracking-metrics.update', $metric) }}" x-data="{ type: '{{ $metric->type }}' }" class="space-y-3">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-[#45515e] dark:text-gray-300 mb-1">{{ __('coach.tracking_metrics.name') }}</label>
                                            <input type="text" name="name" value="{{ $metric->name }}" required
                                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-[#45515e] dark:text-gray-300 mb-1">{{ __('coach.tracking_metrics.type') }}</label>
                                            <select name="type" x-model="type" required
                                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                                <option value="number">{{ __('coach.tracking_metrics.type_number_label') }}</option>
                                                <option value="scale">{{ __('coach.tracking_metrics.type_scale_label') }}</option>
                                                <option value="boolean">{{ __('coach.tracking_metrics.type_boolean') }}</option>
                                                <option value="text">{{ __('coach.tracking_metrics.type_text_label') }}</option>
                                                <option value="image">{{ __('coach.tracking_metrics.type_image_label') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-[#45515e] dark:text-gray-300 mb-1">{{ __('coach.tracking_metrics.description') }}</label>
                                        <input type="text" name="description" value="{{ $metric->description }}"
                                            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 placeholder-[#8e8e93] dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150"
                                            placeholder="Optional hint for clients">
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div x-show="type === 'number'">
                                            <label class="block text-xs font-medium text-[#45515e] dark:text-gray-300 mb-1">{{ __('coach.tracking_metrics.unit') }}</label>
                                            <input type="text" name="unit" value="{{ $metric->unit }}"
                                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                        </div>
                                        <div x-show="type === 'scale'">
                                            <label class="block text-xs font-medium text-[#45515e] dark:text-gray-300 mb-1">{{ __('coach.tracking_metrics.scale_min') }}</label>
                                            <input type="number" name="scale_min" value="{{ $metric->scale_min }}"
                                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                        </div>
                                        <div x-show="type === 'scale'">
                                            <label class="block text-xs font-medium text-[#45515e] dark:text-gray-300 mb-1">{{ __('coach.tracking_metrics.scale_max') }}</label>
                                            <input type="number" name="scale_max" value="{{ $metric->scale_max }}"
                                                class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-[#222222] dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#1456f0] focus:ring-2 focus:ring-[#1456f0]/20 transition-colors duration-150">
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-[#181e25] dark:bg-gray-700 text-white text-xs font-semibold rounded-lg hover:bg-gray-800 transition-colors">
                                            {{ __('coach.programs.edit.save') }}
                                        </button>
                                        <button @click="editing = false" type="button" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            {{ __('coach.meals.edit.cancel') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-10">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8e8e93]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('coach.tracking_metrics.no_metrics') }}</h3>
                    <p class="text-sm text-[#8e8e93] dark:text-gray-500 mt-1">{{ __('coach.tracking_metrics.no_metrics_description') }}</p>
                </div>
            @endif
        </div>

        <!-- Inactive Metrics -->
        @if($inactiveMetrics->count() > 0)
            <div x-data="{ open: false }" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-sm font-medium text-[#8e8e93] dark:text-gray-400">{{ __('coach.tracking_metrics.inactive_heading', ['n' => $inactiveMetrics->count()]) }}</span>
                    <svg class="w-5 h-5 text-[#8e8e93] dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-cloak class="border-t border-gray-100 dark:border-gray-800">
                    <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($inactiveMetrics as $metric)
                            <li class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-[#8e8e93] dark:text-gray-400 line-through">{{ $metric->name }}</p>
                                    <p class="text-xs text-[#8e8e93] dark:text-gray-500">
                                        @if($metric->type === 'number')
                                            {{ __('coach.tracking_metrics.type_number_label') }}{{ $metric->unit ? " ({$metric->unit})" : '' }}
                                        @elseif($metric->type === 'scale')
                                            {{ __('coach.tracking_metrics.type_scale_label') }} ({{ $metric->scale_min }}-{{ $metric->scale_max }})
                                        @elseif($metric->type === 'boolean')
                                            {{ __('coach.tracking_metrics.type_boolean') }}
                                        @elseif($metric->type === 'text')
                                            {{ __('coach.tracking_metrics.type_text_label') }}
                                        @elseif($metric->type === 'image')
                                            {{ __('coach.tracking_metrics.type_image_label') }}
                                        @endif
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('coach.tracking-metrics.restore', $metric) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-[#45515e] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        {{ __('coach.tracking_metrics.reactivate') }}
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</x-layouts.coach>
