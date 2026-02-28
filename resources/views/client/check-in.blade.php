<x-layouts.client>
    <x-slot:title>Daily Check-in</x-slot:title>

    <div class="py-6 space-y-6" x-data="checkIn()">
        <!-- Header with Date Navigation -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Daily Check-in</h1>
            <div class="mt-3 flex items-center justify-between">
                <a :href="prevUrl" class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div class="flex items-center space-x-2">
                    <input type="date" x-model="currentDate" @change="navigateToDate()" max="{{ now()->format('Y-m-d') }}"
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <button @click="goToToday()" x-show="currentDate !== today"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        Today
                    </button>
                </div>

                <template x-if="currentDate < today">
                    <a :href="nextUrl" class="p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </template>
                <template x-if="currentDate >= today">
                    <div class="w-9"></div>
                </template>
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

        @if($assignedMetrics->count() > 0)
            <form method="POST" action="{{ route('client.check-in.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                @foreach($assignedMetrics as $metric)
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                        <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                            {{ $metric->name }}
                            @if($metric->unit)
                                <span class="text-gray-400 dark:text-gray-500 font-normal">({{ $metric->unit }})</span>
                            @endif
                        </label>
                        @if($metric->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $metric->description }}</p>
                        @endif

                        @if($metric->type === 'number')
                            <input type="number" step="any" name="metrics[{{ $metric->id }}]"
                                value="{{ $existingLogs->get($metric->id)?->value }}"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Enter value...">

                        @elseif($metric->type === 'scale')
                            @php $currentVal = $existingLogs->get($metric->id)?->value; @endphp
                            <div x-data="{ value: '{{ $currentVal ?? '' }}' }" class="space-y-2">
                                <input type="hidden" name="metrics[{{ $metric->id }}]" :value="value">
                                <div class="flex items-center justify-between gap-1">
                                    @for($i = $metric->scale_min; $i <= $metric->scale_max; $i++)
                                        <button type="button" @click="value = value === '{{ $i }}' ? '' : '{{ $i }}'"
                                            :class="value === '{{ $i }}' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                            class="flex-1 py-2 text-sm font-medium border rounded-md transition-colors">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500">
                                    <span>Low</span>
                                    <span>High</span>
                                </div>
                            </div>

                        @elseif($metric->type === 'boolean')
                            @php $currentVal = $existingLogs->get($metric->id)?->value; @endphp
                            <div x-data="{ value: '{{ $currentVal ?? '' }}' }" class="flex gap-3">
                                <input type="hidden" name="metrics[{{ $metric->id }}]" :value="value">
                                <button type="button" @click="value = value === '1' ? '' : '1'"
                                    :class="value === '1' ? 'bg-green-600 text-white border-green-600' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="flex-1 py-2 text-sm font-medium border rounded-md transition-colors">
                                    Yes
                                </button>
                                <button type="button" @click="value = value === '0' ? '' : '0'"
                                    :class="value === '0' ? 'bg-red-500 text-white border-red-500' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="flex-1 py-2 text-sm font-medium border rounded-md transition-colors">
                                    No
                                </button>
                            </div>

                        @elseif($metric->type === 'text')
                            <textarea name="metrics[{{ $metric->id }}]" rows="2"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Write notes...">{{ $existingLogs->get($metric->id)?->value }}</textarea>

                        @elseif($metric->type === 'image')
                            @php
                                $existingLog = $existingLogs->get($metric->id);
                                $hasMedia = $existingLog && $existingLog->getFirstMedia('check-in-image');
                                $thumbUrl = $hasMedia ? route('media.daily-log', [$existingLog, 'thumb']) : '';
                            @endphp
                            <div x-data="imageUpload({{ $metric->id }}, {{ $hasMedia ? 'true' : 'false' }}, '{{ $thumbUrl }}')" class="space-y-2">
                                {{-- Existing image preview --}}
                                <template x-if="hasExisting && !removed && !previewUrl">
                                    <div class="relative inline-block">
                                        <img :src="existingThumbUrl" alt="Current photo" class="w-32 h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-800">
                                        <button type="button" @click="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow hover:bg-red-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- New image preview --}}
                                <template x-if="previewUrl">
                                    <div class="relative inline-block">
                                        <img :src="previewUrl" alt="New photo preview" class="w-32 h-32 object-cover rounded-lg border border-blue-300">
                                        <button type="button" @click="clearSelection()" class="absolute -top-2 -right-2 bg-gray-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow hover:bg-gray-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- Upload area --}}
                                <template x-if="!previewUrl && (!hasExisting || removed)">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Tap to upload photo</p>
                                        </div>
                                        <input type="file" class="hidden" accept="image/jpeg,image/png,image/webp,image/heic,image/heif" @change="handleFileSelect($event)">
                                    </label>
                                </template>

                                {{-- Hidden file input for form submission --}}
                                <input type="file" x-ref="fileInput" :name="'images[' + metricId + ']'" class="hidden">
                                <template x-if="removed">
                                    <input type="hidden" :name="'remove_images[' + metricId + ']'" value="1">
                                </template>

                                {{-- Converting indicator --}}
                                <template x-if="converting">
                                    <p class="text-xs text-blue-600">Converting image...</p>
                                </template>
                            </div>
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save Check-in
                </button>
            </form>
        @else
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No metrics assigned</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your coach hasn't assigned any tracking metrics yet.</p>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function imageUpload(metricId, hasExisting, existingThumbUrl) {
            return {
                metricId,
                hasExisting,
                existingThumbUrl,
                previewUrl: null,
                removed: false,
                converting: false,

                async handleFileSelect(event) {
                    let file = event.target.files[0];
                    if (!file) return;

                    // HEIC conversion
                    if (file.type === 'image/heic' || file.type === 'image/heif' || file.name.toLowerCase().endsWith('.heic') || file.name.toLowerCase().endsWith('.heif')) {
                        if (window.heic2any) {
                            this.converting = true;
                            try {
                                const blob = await window.heic2any({ blob: file, toType: 'image/jpeg', quality: 0.9 });
                                file = new File([blob], file.name.replace(/\.heic$/i, '.jpg').replace(/\.heif$/i, '.jpg'), { type: 'image/jpeg' });
                            } catch (e) {
                                console.error('HEIC conversion failed:', e);
                                alert('Could not convert this image. Please try a JPEG or PNG instead.');
                                this.converting = false;
                                return;
                            }
                            this.converting = false;
                        }
                    }

                    // Set the file on the hidden file input
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    this.$refs.fileInput.files = dt.files;

                    // Show preview
                    this.previewUrl = URL.createObjectURL(file);
                    this.removed = false;
                },

                removeImage() {
                    this.removed = true;
                    this.previewUrl = null;
                    this.$refs.fileInput.value = '';
                },

                clearSelection() {
                    this.previewUrl = null;
                    this.$refs.fileInput.value = '';
                }
            };
        }

        function checkIn() {
            const currentDate = '{{ $date }}';
            const today = '{{ now()->format("Y-m-d") }}';
            const baseUrl = '{{ route("client.check-in") }}';

            function shiftDate(dateStr, days) {
                const d = new Date(dateStr + 'T00:00:00');
                d.setDate(d.getDate() + days);
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            return {
                currentDate: currentDate,
                today: today,
                get prevUrl() {
                    return baseUrl + '?date=' + shiftDate(this.currentDate, -1);
                },
                get nextUrl() {
                    const next = shiftDate(this.currentDate, 1);
                    return next <= today ? baseUrl + '?date=' + next : '#';
                },
                navigateToDate() {
                    if (this.currentDate && this.currentDate <= today) {
                        window.location.href = baseUrl + '?date=' + this.currentDate;
                    }
                },
                goToToday() {
                    window.location.href = baseUrl;
                }
            };
        }
    </script>
    @endpush
</x-layouts.client>
