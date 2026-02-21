<x-layouts.coach>
    <x-slot:title>Branding</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Branding</h1>
            <p class="mt-1 text-sm text-gray-500">Customize how your coaching business appears to clients.</p>
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
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Identity</h2>
                    <p class="mt-1 text-sm text-gray-500">Your gym name, logo, and description.</p>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <!-- Gym Name -->
                    <div>
                        <label for="gym_name" class="block text-sm font-medium text-gray-700">Gym Name</label>
                        <input type="text" name="gym_name" id="gym_name" value="{{ old('gym_name', $coach->gym_name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="e.g., Iron Forge Gym">
                        @error('gym_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Logo</label>
                        @if($coach->logo)
                            <div class="mt-2 flex items-center gap-4">
                                <img src="{{ $coach->logo }}" alt="Current logo" class="h-16 w-16 rounded-md object-cover">
                                <label class="flex items-center gap-2 text-sm text-gray-600">
                                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    Remove logo
                                </label>
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                            class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Tell clients about your coaching business">{{ old('description', $coach->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Colors -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Colors</h2>
                    <p class="mt-1 text-sm text-gray-500">Choose brand colors for your client-facing pages.</p>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <!-- Primary Color -->
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700">Primary Color</label>
                        <div class="mt-1 flex items-center gap-3">
                            <input type="color" x-model="primaryColor"
                                class="h-10 w-14 cursor-pointer rounded border border-gray-300 p-0.5">
                            <input type="text" name="primary_color" id="primary_color" x-model="primaryColor"
                                class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="#2563EB">
                        </div>
                        @error('primary_color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Secondary Color -->
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700">Secondary Color</label>
                        <div class="mt-1 flex items-center gap-3">
                            <input type="color" x-model="secondaryColor"
                                class="h-10 w-14 cursor-pointer rounded border border-gray-300 p-0.5">
                            <input type="text" name="secondary_color" id="secondary_color" x-model="secondaryColor"
                                class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="#1E40AF">
                        </div>
                        @error('secondary_color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Onboarding -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Onboarding</h2>
                    <p class="mt-1 text-sm text-gray-500">Customize the onboarding experience for new clients.</p>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <!-- Welcome Text -->
                    <div>
                        <label for="onboarding_welcome_text" class="block text-sm font-medium text-gray-700">Welcome Text</label>
                        <textarea name="onboarding_welcome_text" id="onboarding_welcome_text" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Welcome message shown to clients during onboarding">{{ old('onboarding_welcome_text', $coach->onboarding_welcome_text) }}</textarea>
                        @error('onboarding_welcome_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Custom Fields -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Custom Fields</label>
                        <p class="mt-1 text-sm text-gray-500">Add fields that clients fill out during onboarding.</p>

                        <div class="mt-3 space-y-3">
                            <template x-for="(field, index) in fields" :key="index">
                                <div class="rounded-md border border-gray-200 p-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-3">
                                        <!-- Label -->
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-medium text-gray-700">Label</label>
                                            <input type="text" x-model="field.label" :name="'fields[' + index + '][label]'"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                placeholder="e.g., Your goal?">
                                        </div>

                                        <!-- Type -->
                                        <div class="sm:col-span-1">
                                            <label class="block text-xs font-medium text-gray-700">Type</label>
                                            <select x-model="field.type" :name="'fields[' + index + '][type]'"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                <option value="text">Text</option>
                                                <option value="select">Select</option>
                                                <option value="textarea">Textarea</option>
                                            </select>
                                        </div>

                                        <!-- Options (shown only for select) -->
                                        <div class="sm:col-span-2" x-show="field.type === 'select'" x-cloak>
                                            <label class="block text-xs font-medium text-gray-700">Options (one per line)</label>
                                            <textarea x-model="field.options" :name="'fields[' + index + '][options]'" rows="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                placeholder="Option 1&#10;Option 2"></textarea>
                                        </div>

                                        <!-- Hidden options field when not select -->
                                        <template x-if="field.type !== 'select'">
                                            <input type="hidden" :name="'fields[' + index + '][options]'" value="">
                                        </template>

                                        <!-- Required + Actions -->
                                        <div class="sm:col-span-1 flex items-end gap-2">
                                            <label class="flex items-center gap-1.5 text-xs text-gray-600 pb-2">
                                                <input type="hidden" :name="'fields[' + index + '][is_required]'" value="0">
                                                <input type="checkbox" x-model="field.is_required" :name="'fields[' + index + '][is_required]'" value="1"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                Required
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Row Actions -->
                                    <div class="mt-3 flex items-center gap-1">
                                        <button type="button" @click="moveUp(index)" x-show="index > 0"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 rounded" title="Move up">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        </button>
                                        <button type="button" @click="moveDown(index)" x-show="index < fields.length - 1"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 rounded" title="Move down">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <button type="button" @click="removeField(index)"
                                            class="p-1.5 text-gray-400 hover:text-red-600 rounded" title="Remove field">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        @error('fields')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('fields.*.label')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <button type="button" @click="addField()"
                            class="mt-3 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Field
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section 4: Welcome Email -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Welcome Email</h2>
                    <p class="mt-1 text-sm text-gray-500">Customize the email sent to new clients.</p>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="welcome_email_text" class="block text-sm font-medium text-gray-700">Email Text</label>
                        <textarea name="welcome_email_text" id="welcome_email_text" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Write a personal welcome message for your clients">{{ old('welcome_email_text', $coach->welcome_email_text) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">This text will be included in the welcome email sent to clients after they register.</p>
                        @error('welcome_email_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div>
                <button type="submit"
                    class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Save Branding
                </button>
            </div>
        </form>
    </div>
</x-layouts.coach>
