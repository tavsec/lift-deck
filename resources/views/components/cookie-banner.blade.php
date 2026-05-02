@unless(request()->routeIs('register', 'join', 'join.code', 'coach.plan'))
<div
    x-data="cookieBanner()"
    x-init="init()"
    x-cloak
>
    {{-- Floating card --}}
    <div
        x-show="showBanner"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 left-4 right-4 sm:left-6 sm:right-auto z-50 w-auto sm:max-w-sm bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl p-5"
    >
        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ __('cookies.banner.title') }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ __('cookies.banner.description') }}</p>

        {{-- Granular toggles (shown when expanded) --}}
        <div x-show="expanded" x-transition class="mb-4 space-y-3">
            {{-- Necessary (always on) --}}
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('cookies.categories.necessary.title') }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('cookies.categories.necessary.description') }}</p>
                </div>
                <div class="shrink-0 mt-0.5">
                    <div class="w-9 h-5 rounded-full bg-blue-600 flex items-center justify-end px-0.5 cursor-not-allowed opacity-60">
                        <div class="w-4 h-4 rounded-full bg-white shadow"></div>
                    </div>
                </div>
            </div>

            {{-- Analytics --}}
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('cookies.categories.analytics.title') }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('cookies.categories.analytics.description') }}</p>
                </div>
                <button
                    type="button"
                    @click="choices.analytics = !choices.analytics"
                    :aria-pressed="choices.analytics"
                    class="shrink-0 mt-0.5 w-9 h-5 rounded-full transition-colors duration-200 flex items-center px-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                    :class="choices.analytics ? 'bg-blue-600 justify-end' : 'bg-gray-300 dark:bg-gray-600 justify-start'"
                >
                    <div class="w-4 h-4 rounded-full bg-white shadow"></div>
                </button>
            </div>

            {{-- Marketing --}}
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('cookies.categories.marketing.title') }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('cookies.categories.marketing.description') }}</p>
                </div>
                <button
                    type="button"
                    @click="choices.marketing = !choices.marketing"
                    :aria-pressed="choices.marketing"
                    class="shrink-0 mt-0.5 w-9 h-5 rounded-full transition-colors duration-200 flex items-center px-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                    :class="choices.marketing ? 'bg-blue-600 justify-end' : 'bg-gray-300 dark:bg-gray-600 justify-start'"
                >
                    <div class="w-4 h-4 rounded-full bg-white shadow"></div>
                </button>
            </div>
        </div>

        {{-- Collapsed action row --}}
        <div x-show="!expanded" class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                @click="acceptAll()"
                class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-colors"
            >{{ __('cookies.banner.accept_all') }}</button>
            <button
                type="button"
                @click="rejectAll()"
                class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
            >{{ __('cookies.banner.reject_all') }}</button>
            <button
                type="button"
                @click="expanded = true"
                class="w-full text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 underline text-center transition-colors"
            >{{ __('cookies.banner.manage') }}</button>
        </div>

        {{-- Expanded action row --}}
        <div x-show="expanded" class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                @click="save()"
                class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-colors"
            >{{ __('cookies.banner.save') }}</button>
            <button
                type="button"
                @click="acceptAll()"
                class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
            >{{ __('cookies.banner.accept_all') }}</button>
            <button
                type="button"
                @click="rejectAll()"
                class="w-full text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 underline text-center transition-colors"
            >{{ __('cookies.banner.reject_all') }}</button>
        </div>
    </div>

    {{-- Persistent "Cookie preferences" re-entry link --}}
    <button
        type="button"
        x-show="!showBanner"
        x-transition:enter="transition ease-out duration-300 delay-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        @click="showBanner = true; expanded = true"
        class="hidden md:block fixed bottom-4 left-4 z-30 text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 underline transition-colors"
    >{{ __('cookies.manage_link') }}</button>
</div>

<script>
    function cookieBanner() {
        return {
            showBanner: false,
            expanded: false,
            choices: {
                analytics: false,
                marketing: false,
            },

            init() {
                const saved = this.readCookie('consent_choices');
                if (saved) {
                    try {
                        this.choices = JSON.parse(saved);
                        this.applyConsent();
                    } catch {}
                } else {
                    setTimeout(() => { this.showBanner = true; }, 2000);
                }
            },

            acceptAll() {
                this.choices = { analytics: true, marketing: true };
                this.persist();
            },

            rejectAll() {
                this.choices = { analytics: false, marketing: false };
                this.persist();
            },

            save() {
                this.persist();
            },

            persist() {
                this.writeCookie('consent_choices', JSON.stringify(this.choices), 365);
                this.applyConsent();
                this.showBanner = false;
                this.expanded = false;
            },

            applyConsent() {
                if (typeof gtag !== 'function') return;
                gtag('consent', 'update', {
                    analytics_storage: this.choices.analytics ? 'granted' : 'denied',
                    ad_storage: this.choices.marketing ? 'granted' : 'denied',
                    ad_user_data: this.choices.marketing ? 'granted' : 'denied',
                    ad_personalization: this.choices.marketing ? 'granted' : 'denied',
                });
            },

            readCookie(name) {
                const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
                return match ? decodeURIComponent(match[1]) : null;
            },

            writeCookie(name, value, days) {
                const expires = new Date(Date.now() + days * 864e5).toUTCString();
                document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/; SameSite=Lax';
            },
        };
    }
</script>
@endunless
