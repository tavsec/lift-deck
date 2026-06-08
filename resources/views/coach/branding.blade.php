<x-layouts.coach>
    <x-slot:title>{{ __('coach.branding.heading') }}</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="font-display text-[30px] font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('coach.branding.heading') }}</h1>
            <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('coach.branding.subtitle') }}</p>
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

        <form
            method="POST"
            action="{{ route('coach.branding.update') }}"
            enctype="multipart/form-data"
            x-data="{
                primaryColor: '{{ old('primary_color', $coach->primary_color ?? '#2563EB') }}',
                secondaryColor: '{{ old('secondary_color', $coach->secondary_color ?? '#1E40AF') }}',
                fields: {{ Js::from($fields->map(fn($f) => [
                    'label' => $f->label,
                    'type' => $f->type,
                    'options' => $f->type === 'select' ? implode("\n", $f->options ?? []) : '',
                    'is_required' => $f->is_required,
                ])) }},
                addField() {
                    this.fields.push({ label: '', type: 'text', options: '', is_required: true });
                },
                removeField(index) {
                    this.fields.splice(index, 1);
                },
                moveUp(index) {
                    if (index > 0) {
                        [this.fields[index - 1], this.fields[index]] = [this.fields[index], this.fields[index - 1]];
                    }
                },
                moveDown(index) {
                    if (index < this.fields.length - 1) {
                        [this.fields[index], this.fields[index + 1]] = [this.fields[index + 1], this.fields[index]];
                    }
                }
            }"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <!-- Section 1: Identity -->
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)]">
                <div class="px-6 py-4 border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                    <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.branding.identity') }}</h2>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('coach.branding.identity_description') }}</p>
                </div>

                <div class="px-6 py-5 space-y-5">
                    <!-- Gym Name -->
                    <div>
                        <label for="gym_name" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.branding.gym_name') }}</label>
                        <input type="text" name="gym_name" id="gym_name" value="{{ old('gym_name', $coach->gym_name) }}"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                            placeholder="e.g., Iron Forge Gym">
                        @error('gym_name')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div>
                        <label class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.branding.logo') }}</label>
                        @if($coach->logo)
                            <div class="mt-2 flex items-center gap-4">
                                <img src="{{ $coach->logo }}" alt="Current logo" class="h-16 w-16 rounded-xl object-cover border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)]">
                                <label class="flex items-center gap-2 text-sm text-[#555b66] dark:text-[#a4abb6] cursor-pointer">
                                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-[rgba(18,22,31,0.16)] dark:border-[rgba(255,255,255,0.16)] text-red-600 focus:ring-red-500">
                                    {{ __('coach.branding.remove_logo') }}
                                </label>
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="mt-2 w-full text-sm text-[#555b66] dark:text-[#a4abb6] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-800 file:text-[#45515e] dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-700">
                        @error('logo')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.branding.description') }}</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                            placeholder="Tell clients about your coaching business">{{ old('description', $coach->description) }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Colors -->
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)]">
                <div class="px-6 py-4 border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                    <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.branding.colors') }}</h2>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('coach.branding.colors_description') }}</p>
                </div>

                <div class="px-6 py-5 space-y-5">
                    <!-- Primary Color -->
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.branding.primary_color') }}</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="primaryColor"
                                class="h-10 w-14 cursor-pointer rounded-lg border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] p-0.5 bg-white dark:bg-[#11141a]">
                            <input type="text" name="primary_color" id="primary_color" x-model="primaryColor"
                                class="w-32 border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                placeholder="#2563EB">
                        </div>
                        @error('primary_color')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Secondary Color -->
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.branding.secondary_color') }}</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="secondaryColor"
                                class="h-10 w-14 cursor-pointer rounded-lg border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] p-0.5 bg-white dark:bg-[#11141a]">
                            <input type="text" name="secondary_color" id="secondary_color" x-model="secondaryColor"
                                class="w-32 border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                placeholder="#1E40AF">
                        </div>
                        @error('secondary_color')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Onboarding -->
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)]">
                <div class="px-6 py-4 border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                    <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.branding.onboarding') }}</h2>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('coach.branding.onboarding_description') }}</p>
                </div>

                <div class="px-6 py-5 space-y-5">
                    <!-- Welcome Text -->
                    <div>
                        <label for="onboarding_welcome_text" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.branding.welcome_text') }}</label>
                        <textarea name="onboarding_welcome_text" id="onboarding_welcome_text" rows="3"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                            placeholder="Welcome message shown to clients during onboarding">{{ old('onboarding_welcome_text', $coach->onboarding_welcome_text) }}</textarea>
                        @error('onboarding_welcome_text')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Custom Fields -->
                    <div>
                        <label class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.branding.custom_fields') }}</label>
                        <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.branding.custom_fields_description') }}</p>

                        <div class="mt-3 space-y-3">
                            <template x-for="(field, index) in fields" :key="index">
                                <div class="rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] p-4 bg-[#f3f5f7] dark:bg-[#1d2027]">
                                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-3">
                                        <!-- Label -->
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.branding.field_label') }}</label>
                                            <input type="text" x-model="field.label" :name="'fields[' + index + '][label]'"
                                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                                placeholder="e.g., Your goal?">
                                        </div>

                                        <!-- Type -->
                                        <div class="sm:col-span-1">
                                            <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.branding.field_type') }}</label>
                                            <select x-model="field.type" :name="'fields[' + index + '][type]'"
                                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150">
                                                <option value="text">{{ __('coach.branding.field_type_text') }}</option>
                                                <option value="select">{{ __('coach.branding.field_type_select') }}</option>
                                                <option value="textarea">{{ __('coach.branding.field_type_textarea') }}</option>
                                            </select>
                                        </div>

                                        <!-- Options (shown only for select) -->
                                        <div class="sm:col-span-2" x-show="field.type === 'select'" x-cloak>
                                            <label class="block text-xs font-medium text-[#555b66] dark:text-[#a4abb6] mb-1">{{ __('coach.branding.field_options') }}</label>
                                            <textarea x-model="field.options" :name="'fields[' + index + '][options]'" rows="2"
                                                class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                                                placeholder="Option 1&#10;Option 2"></textarea>
                                        </div>

                                        <!-- Hidden options field when not select -->
                                        <template x-if="field.type !== 'select'">
                                            <input type="hidden" :name="'fields[' + index + '][options]'" value="">
                                        </template>

                                        <!-- Required + Actions -->
                                        <div class="sm:col-span-1 flex items-end gap-2">
                                            <label class="flex items-center gap-1.5 text-xs text-[#555b66] dark:text-[#a4abb6] pb-2">
                                                <input type="hidden" :name="'fields[' + index + '][is_required]'" value="0">
                                                <input type="checkbox" x-model="field.is_required" :name="'fields[' + index + '][is_required]'" value="1"
                                                    class="rounded border-[rgba(18,22,31,0.16)] dark:border-[rgba(255,255,255,0.16)] text-[#5c7a10] focus:ring-[#c6f24e]">
                                                {{ __('coach.branding.field_required') }}
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Row Actions -->
                                    <div class="mt-3 flex items-center gap-1">
                                        <button type="button" @click="moveUp(index)" x-show="index > 0"
                                            class="p-1.5 text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] rounded transition-colors" title="Move up">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        </button>
                                        <button type="button" @click="moveDown(index)" x-show="index < fields.length - 1"
                                            class="p-1.5 text-[#8c93a0] dark:text-[#6b7280] hover:text-[#45515e] dark:hover:text-[#f0f2f5] rounded transition-colors" title="Move down">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <button type="button" @click="removeField(index)"
                                            class="p-1.5 text-[#8c93a0] dark:text-[#6b7280] hover:text-red-600 dark:hover:text-red-400 rounded transition-colors" title="Remove field">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        @error('fields')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('fields.*.label')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <button type="button" @click="addField()"
                            class="mt-3 inline-flex items-center text-sm font-semibold text-[#555b66] dark:text-[#a4abb6] hover:text-[#222222] dark:hover:text-[#f0f2f5] transition-colors">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('coach.branding.add_field') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section 4: Welcome Email -->
            <div class="bg-white dark:bg-[#16191f] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.04),0_4px_16px_rgba(18,22,31,.045)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_20px_rgba(0,0,0,.3)]">
                <div class="px-6 py-4 border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                    <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('coach.branding.welcome_email') }}</h2>
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('coach.branding.welcome_email_description') }}</p>
                </div>

                <div class="px-6 py-5 space-y-5">
                    <div>
                        <label for="welcome_email_text" class="block text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-1.5">{{ __('coach.branding.email_text') }}</label>
                        <textarea name="welcome_email_text" id="welcome_email_text" rows="4"
                            class="w-full border border-[rgba(18,22,31,0.14)] dark:border-[rgba(255,255,255,0.12)] bg-white dark:bg-[#11141a] text-[#181b22] dark:text-[#f0f2f5] placeholder-[#8c93a0] dark:placeholder-[#6b7280] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-[#c6f24e] focus:ring-1 focus:ring-[rgba(198,242,78,0.3)] transition-colors duration-150"
                            placeholder="Write a personal welcome message for your clients">{{ old('welcome_email_text', $coach->welcome_email_text) }}</textarea>
                        <p class="mt-1.5 text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('coach.branding.email_text_placeholder') }}</p>
                        @error('welcome_email_text')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-[#181b22] dark:bg-[#c6f24e] text-white dark:text-[#14180a] text-sm font-semibold rounded-lg hover:bg-[#2a2f3a] dark:hover:bg-[#b4e438] transition-colors">
                    {{ __('coach.branding.save') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.coach>
