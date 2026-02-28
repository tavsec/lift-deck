<x-layouts.coach>
    <x-slot:title>Tracking Metrics</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tracking Metrics</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define what your clients track daily (weight, steps, mood, etc.)</p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add New Metric -->
        <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-6 py-4 text-left">
                <span class="flex items-center text-sm font-medium text-blue-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Metric
                </span>
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-cloak class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                <form method="POST" action="{{ route('coach.tracking-metrics.store') }}" x-data="{ type: 'number' }" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="e.g., Body Weight">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type <span class="text-red-500">*</span></label>
                            <select name="type" id="type" x-model="type" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="number">Number (e.g., weight, steps)</option>
                                <option value="scale">Scale (e.g., 1-5 rating)</option>
                                <option value="boolean">Yes / No</option>
                                <option value="text">Text (free-form notes)</option>
                                <option value="image">Image (progress photo)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <input type="text" name="description" id="description" value="{{ old('description') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Optional hint shown to clients, e.g., 'Weigh yourself first thing in the morning'">
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div x-show="type === 'number'">
                            <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit</label>
                            <input type="text" name="unit" id="unit" value="{{ old('unit') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="e.g., kg, steps, ml">
                        </div>

                        <div x-show="type === 'scale'">
                            <label for="scale_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scale Min</label>
                            <input type="number" name="scale_min" id="scale_min" value="{{ old('scale_min', 1) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div x-show="type === 'scale'">
                            <label for="scale_max" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scale Max</label>
                            <input type="number" name="scale_max" id="scale_max" value="{{ old('scale_max', 5) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Add Metric
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Metrics List -->
        @php $activeMetrics = $metrics->where('is_active', true); @endphp
        @php $inactiveMetrics = $metrics->where('is_active', false); @endphp

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Active Metrics ({{ $activeMetrics->count() }})</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">These metrics are available for client assignment.</p>
            </div>

            @if($activeMetrics->count() > 0)
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($activeMetrics as $index => $metric)
                        <li x-data="{ editing: false }" class="px-6 py-4">
                            <!-- Display Mode -->
                            <div x-show="!editing" class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm font-medium text-gray-400 dark:text-gray-500 w-6 text-right">{{ $index + 1 }}.</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $metric->name }}</p>
                                        @if($metric->description)
                                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $metric->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            @if($metric->type === 'number')
                                                Number{{ $metric->unit ? " ({$metric->unit})" : '' }}
                                            @elseif($metric->type === 'scale')
                                                Scale ({{ $metric->scale_min }}-{{ $metric->scale_max }})
                                            @elseif($metric->type === 'boolean')
                                                Yes / No
                                            @elseif($metric->type === 'text')
                                                Text
                                            @elseif($metric->type === 'image')
                                                Image (progress photo)
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-1">
                                    <!-- Move Up -->
                                    @if(!$loop->first)
                                        <form method="POST" action="{{ route('coach.tracking-metrics.move-up', $metric) }}">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded" title="Move up">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Move Down -->
                                    @if(!$loop->last)
                                        <form method="POST" action="{{ route('coach.tracking-metrics.move-down', $metric) }}">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 rounded" title="Move down">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit -->
                                    <button @click="editing = true" class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-blue-600 rounded" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Deactivate -->
                                    <form method="POST" action="{{ route('coach.tracking-metrics.destroy', $metric) }}" onsubmit="return confirm('Deactivate this metric? It will no longer be available for new assignments.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 dark:text-gray-500 hover:text-red-600 rounded" title="Deactivate">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Name</label>
                                            <input type="text" name="name" value="{{ $metric->name }}" required
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Type</label>
                                            <select name="type" x-model="type" required
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                <option value="number">Number</option>
                                                <option value="scale">Scale</option>
                                                <option value="boolean">Yes / No</option>
                                                <option value="text">Text</option>
                                                <option value="image">Image</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Description</label>
                                        <input type="text" name="description" value="{{ $metric->description }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="Optional hint for clients">
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div x-show="type === 'number'">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Unit</label>
                                            <input type="text" name="unit" value="{{ $metric->unit }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                        <div x-show="type === 'scale'">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Scale Min</label>
                                            <input type="number" name="scale_min" value="{{ $metric->scale_min }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                        <div x-show="type === 'scale'">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Scale Max</label>
                                            <input type="number" name="scale_max" value="{{ $metric->scale_max }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
                                            Save
                                        </button>
                                        <button @click="editing = false" type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No tracking metrics</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add your first metric above to get started.</p>
                </div>
            @endif
        </div>

        <!-- Inactive Metrics -->
        @if($inactiveMetrics->count() > 0)
            <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Inactive Metrics ({{ $inactiveMetrics->count() }})</span>
                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-cloak class="border-t border-gray-200 dark:border-gray-700">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($inactiveMetrics as $metric)
                            <li class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 line-through">{{ $metric->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        @if($metric->type === 'number')
                                            Number{{ $metric->unit ? " ({$metric->unit})" : '' }}
                                        @elseif($metric->type === 'scale')
                                            Scale ({{ $metric->scale_min }}-{{ $metric->scale_max }})
                                        @elseif($metric->type === 'boolean')
                                            Yes / No
                                        @elseif($metric->type === 'text')
                                            Text
                                        @elseif($metric->type === 'image')
                                            Image (progress photo)
                                        @endif
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('coach.tracking-metrics.restore', $metric) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                                        Reactivate
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
