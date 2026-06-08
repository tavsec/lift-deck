<x-layouts.coach>
    <x-slot:title>{{ __('coach.clients.check_in.heading', ['name' => $client->name]) }}</x-slot:title>

    <div class="space-y-6" x-data="checkIn()">
        <!-- Header with Date Navigation -->
        <div>
            <a href="{{ route('coach.clients.show', $client) }}" class="inline-flex items-center text-sm text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('coach.clients.check_in.back', ['name' => $client->name]) }}
            </a>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.clients.check_in.heading', ['name' => $client->name]) }}</h1>
            <div class="mt-3 flex items-center justify-between">
                <a :href="prevUrl" class="p-2 rounded-lg text-[#555b66] dark:text-[#a4abb6] hover:text-[#222222] dark:hover:text-[#f0f2f5] hover:bg-gray-100 dark:hover:bg-[#2a2f3a] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div class="flex items-center space-x-2">
                    <input type="date" x-model="currentDate" @change="navigateToDate()"
                        class="border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                    <button @click="goToToday()" x-show="currentDate !== today"
                        class="text-xs font-medium text-[#5c7a10] dark:text-[#c6f24e] font-semibold hover:underline">
                        {{ __('coach.clients.check_in.today') }}
                    </button>
                </div>

                <a :href="nextUrl" class="p-2 rounded-lg text-[#555b66] dark:text-[#a4abb6] hover:text-[#222222] dark:hover:text-[#f0f2f5] hover:bg-gray-100 dark:hover:bg-[#2a2f3a] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
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

        @if($assignedMetrics->count() > 0)
            <form method="POST" action="{{ route('coach.clients.check-in.store', $client) }}" class="space-y-4" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                @foreach($assignedMetrics as $metric)
                    <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)] p-5">
                        <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-1">
                            {{ $metric->name }}
                            @if($metric->unit)
                                <span class="text-[#8c93a0] dark:text-[#6b7280] font-normal">({{ $metric->unit }})</span>
                            @endif
                        </label>
                        @if($metric->description)
                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mb-2">{{ $metric->description }}</p>
                        @endif

                        @if($metric->type === 'number')
                            <input type="number" step="any" name="metrics[{{ $metric->id }}]"
                                value="{{ $existingLogs->get($metric->id)?->value }}"
                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                placeholder="{{ __('coach.clients.check_in.enter_value') }}">

                        @elseif($metric->type === 'scale')
                            @php $currentVal = $existingLogs->get($metric->id)?->value; @endphp
                            <div x-data="{ value: '{{ $currentVal ?? '' }}' }" class="space-y-2">
                                <input type="hidden" name="metrics[{{ $metric->id }}]" :value="value">
                                <div class="flex items-center justify-between gap-1">
                                    @for($i = $metric->scale_min; $i <= $metric->scale_max; $i++)
                                        <button type="button" @click="value = value === '{{ $i }}' ? '' : '{{ $i }}'"
                                            :class="value === '{{ $i }}' ? 'text-white border-transparent' : 'bg-white dark:bg-[#11141a] text-[#555b66] dark:text-[#a4abb6] border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027]'"
                                            :style="value === '{{ $i }}' ? 'background-color: var(--color-primary); border-color: var(--color-primary)' : ''"
                                            class="flex-1 py-2 text-sm font-medium border rounded-lg transition-colors">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <div class="flex justify-between text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                    <span>{{ __('coach.clients.check_in.low') }}</span>
                                    <span>{{ __('coach.clients.check_in.high') }}</span>
                                </div>
                            </div>

                        @elseif($metric->type === 'boolean')
                            @php $currentVal = $existingLogs->get($metric->id)?->value; @endphp
                            <div x-data="{ value: '{{ $currentVal ?? '' }}' }" class="flex gap-3">
                                <input type="hidden" name="metrics[{{ $metric->id }}]" :value="value">
                                <button type="button" @click="value = value === '1' ? '' : '1'"
                                    :class="value === '1' ? 'bg-green-600 text-white border-green-600' : 'bg-white dark:bg-[#11141a] text-[#555b66] dark:text-[#a4abb6] border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027]'"
                                    class="flex-1 py-2 text-sm font-medium border rounded-lg transition-colors">
                                    {{ __('coach.clients.check_in.yes') }}
                                </button>
                                <button type="button" @click="value = value === '0' ? '' : '0'"
                                    :class="value === '0' ? 'bg-red-500 text-white border-red-500' : 'bg-white dark:bg-[#11141a] text-[#555b66] dark:text-[#a4abb6] border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027]'"
                                    class="flex-1 py-2 text-sm font-medium border rounded-lg transition-colors">
                                    {{ __('coach.clients.check_in.no') }}
                                </button>
                            </div>

                        @elseif($metric->type === 'text')
                            <textarea name="metrics[{{ $metric->id }}]" rows="2"
                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                placeholder="{{ __('coach.clients.check_in.write_notes') }}">{{ $existingLogs->get($metric->id)?->value }}</textarea>

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
                                        <img :src="existingThumbUrl" alt="Current photo" class="w-32 h-32 object-cover rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
                                        <button type="button" @click="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow hover:bg-red-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- New image preview --}}
                                <template x-if="previewUrl">
                                    <div class="relative inline-block">
                                        <img :src="previewUrl" alt="New photo preview" class="w-32 h-32 object-cover rounded-xl border-2" style="border-color: var(--color-primary)">
                                        <button type="button" @click="clearSelection()" class="absolute -top-2 -right-2 bg-gray-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow hover:bg-gray-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- Upload area --}}
                                <template x-if="!previewUrl && (!hasExisting || removed)">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] border-dashed rounded-xl cursor-pointer bg-[#f3f5f7] dark:bg-[#1d2027] hover:bg-[#f3f5f7] dark:hover:bg-[#1d2027] transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-2 text-[#8c93a0] dark:text-[#6b7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.clients.check_in.tap_to_upload') }}</p>
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
                                    <p class="text-xs" style="color: var(--color-primary)">{{ __('coach.clients.check_in.converting') }}</p>
                                </template>
                            </div>
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                    {{ __('coach.clients.check_in.save') }}
                </button>
            </form>
        @else
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)]">
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-[#f3f5f7] dark:bg-[#1d2027] flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-[#8c93a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.clients.check_in.no_metrics') }}</h3>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-1">{{ __('coach.clients.check_in.no_metrics_description') }}</p>
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
            const baseUrl = '{{ route("coach.clients.check-in.show", $client) }}';

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
                    return baseUrl + '?date=' + shiftDate(this.currentDate, 1);
                },
                navigateToDate() {
                    if (this.currentDate) {
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
</x-layouts.coach>
