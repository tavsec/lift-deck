<x-layouts.client>
    <x-slot:title>{{ __('client.check_in.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4" x-data="checkIn()">
        <!-- Header with Date Navigation -->
        <div class="mb-5">
            <div class="flex items-center justify-between">
                <h1 class="font-display text-xl font-semibold text-[#222222] dark:text-gray-100">{{ __('client.check_in.heading') }}</h1>
                <a href="{{ route('client.check-in.history') }}" class="text-sm font-medium text-[#1456f0] hover:opacity-80">
                    {{ __('client.check_in_history.view_history') }} →
                </a>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <a :href="prevUrl" class="p-2 rounded-lg text-[#45515e] dark:text-gray-400 hover:text-[#222222] dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div class="flex items-center space-x-2">
                    <input type="date" x-model="currentDate" @change="navigateToDate()" max="{{ now()->format('Y-m-d') }}"
                        class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm text-[#222222] dark:text-gray-100">
                    <button @click="goToToday()" x-show="currentDate !== today"
                        class="text-xs text-[#1456f0] hover:opacity-80 font-medium">
                        {{ __('client.check_in.today') }}
                    </button>
                </div>

                <template x-if="currentDate < today">
                    <a :href="nextUrl" class="p-2 rounded-lg text-[#45515e] dark:text-gray-400 hover:text-[#222222] dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
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
            <div class="rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mb-4">
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if($assignedMetrics->count() > 0)
            <form method="POST" action="{{ route('client.check-in.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                @foreach($assignedMetrics as $metric)
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                        <label class="block text-sm font-semibold text-[#222222] dark:text-gray-100 mb-1">
                            {{ $metric->name }}
                            @if($metric->unit)
                                <span class="text-[#8e8e93] dark:text-gray-500 font-normal">({{ $metric->unit }})</span>
                            @endif
                        </label>
                        @if($metric->description)
                            <p class="text-xs text-[#8e8e93] dark:text-gray-500 mb-3">{{ $metric->description }}</p>
                        @endif

                        @if($metric->type === 'number')
                            <input type="number" step="any" name="metrics[{{ $metric->id }}]"
                                value="{{ $existingLogs->get($metric->id)?->value }}"
                                class="block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm text-[#222222]"
                                placeholder="{{ __('client.check_in.enter_value') }}">

                        @elseif($metric->type === 'scale')
                            @php $currentVal = $existingLogs->get($metric->id)?->value; @endphp
                            <div x-data="{ value: '{{ $currentVal ?? '' }}' }" class="space-y-2">
                                <input type="hidden" name="metrics[{{ $metric->id }}]" :value="value">
                                <div class="flex items-center justify-between gap-1">
                                    @for($i = $metric->scale_min; $i <= $metric->scale_max; $i++)
                                        <button type="button" @click="value = value === '{{ $i }}' ? '' : '{{ $i }}'"
                                            :class="value === '{{ $i }}' ? 'text-white border-[#1456f0]' : 'bg-white dark:bg-gray-800 text-[#45515e] dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                            :style="value === '{{ $i }}' ? 'background-color: #1456f0' : ''"
                                            class="flex-1 py-2 text-sm font-medium border rounded-lg transition-colors">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <div class="flex justify-between text-xs text-[#8e8e93] dark:text-gray-500">
                                    <span>{{ __('client.check_in.low') }}</span>
                                    <span>{{ __('client.check_in.high') }}</span>
                                </div>
                            </div>

                        @elseif($metric->type === 'boolean')
                            @php $currentVal = $existingLogs->get($metric->id)?->value; @endphp
                            <div x-data="{ value: '{{ $currentVal ?? '' }}' }" class="flex gap-3">
                                <input type="hidden" name="metrics[{{ $metric->id }}]" :value="value">
                                <button type="button" @click="value = value === '1' ? '' : '1'"
                                    :class="value === '1' ? 'bg-green-600 text-white border-green-600' : 'bg-white dark:bg-gray-800 text-[#45515e] dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="flex-1 py-2 text-sm font-medium border rounded-lg transition-colors">
                                    {{ __('client.check_in.yes') }}
                                </button>
                                <button type="button" @click="value = value === '0' ? '' : '0'"
                                    :class="value === '0' ? 'bg-red-500 text-white border-red-500' : 'bg-white dark:bg-gray-800 text-[#45515e] dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="flex-1 py-2 text-sm font-medium border rounded-lg transition-colors">
                                    {{ __('client.check_in.no') }}
                                </button>
                            </div>

                        @elseif($metric->type === 'text')
                            <textarea name="metrics[{{ $metric->id }}]" rows="2"
                                class="block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#1456f0] focus:ring-[#1456f0] text-sm"
                                placeholder="{{ __('client.check_in.write_notes') }}">{{ $existingLogs->get($metric->id)?->value }}</textarea>

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
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-200 dark:border-gray-700 border-dashed rounded-xl cursor-pointer bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-2 text-[#8e8e93] dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <p class="text-xs text-[#8e8e93] dark:text-gray-500">{{ __('client.check_in.tap_to_upload') }}</p>
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
                                    <p class="text-xs text-[#1456f0]">Converting image...</p>
                                </template>
                            </div>
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#181e25] dark:bg-gray-700 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                    {{ __('client.check_in.save') }}
                </button>
            </form>
        @else
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-card p-5">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="mt-3 text-sm font-semibold text-[#222222] dark:text-gray-100">{{ __('client.check_in.no_metrics') }}</h3>
                    <p class="mt-1 text-sm text-[#8e8e93] dark:text-gray-500">{{ __('client.check_in.no_metrics_description') }}</p>
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
