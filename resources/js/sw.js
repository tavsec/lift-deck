import { precacheAndRoute, cleanupOutdatedCaches } from 'workbox-precaching';
import { registerRoute } from 'workbox-routing';
import { CacheFirst, NetworkFirst } from 'workbox-strategies';
import { ExpirationPlugin } from 'workbox-expiration';
import { BackgroundSyncPlugin } from 'workbox-background-sync';

// Injected by vite-plugin-pwa at build time — must not be removed
const manifest = self.__WB_MANIFEST || [];
precacheAndRoute(manifest);
cleanupOutdatedCaches();

// --- Static assets: cache-first (Vite content-hashes filenames) ---
registerRoute(
    ({ url }) =>
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/vendor/bladewind/'),
    new CacheFirst({
        cacheName: 'static-assets',
        plugins: [
            new ExpirationPlugin({ maxEntries: 100, maxAgeSeconds: 60 * 60 * 24 * 365 }),
        ],
    })
);

// --- Fonts: cache-first ---
registerRoute(
    ({ url }) =>
        url.origin === 'https://fonts.bunny.net' ||
        url.origin === 'https://fonts.gstatic.com',
    new CacheFirst({
        cacheName: 'fonts',
        plugins: [
            new ExpirationPlugin({ maxEntries: 20, maxAgeSeconds: 60 * 60 * 24 * 365 }),
        ],
    })
);

// --- Exercises JSON API: network-first, 1-day cache ---
registerRoute(
    ({ url }) => url.pathname === '/log/exercises',
    new NetworkFirst({
        cacheName: 'api-exercises',
        plugins: [
            new ExpirationPlugin({ maxEntries: 10, maxAgeSeconds: 60 * 60 * 24 }),
        ],
    })
);

// --- Client HTML pages: network-first, 7-day cache ---
const clientRoutes = [
    '/dashboard',
    '/log',
    '/program',
    '/history',
    '/check-in',
    '/nutrition',
    '/achievements',
    '/loyalty',
    '/rewards',
];

registerRoute(
    ({ url, request }) =>
        request.mode === 'navigate' &&
        (clientRoutes.some(r => url.pathname === r || url.pathname.startsWith(r + '/')) ||
            url.pathname.startsWith('/log/')),
    new NetworkFirst({
        cacheName: 'client-pages',
        plugins: [
            new ExpirationPlugin({ maxEntries: 30, maxAgeSeconds: 60 * 60 * 24 * 7 }),
        ],
        fetchOptions: { credentials: 'include' },
    })
);

// --- Offline fallback for uncached navigation requests ---
const OFFLINE_URL = '/offline';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('offline-fallback').then(cache => cache.add(OFFLINE_URL))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Intercept navigate requests to serve cache or fallback when offline
self.addEventListener('fetch', (event) => {
    if (event.request.mode !== 'navigate') return;

    event.respondWith(
        fetch(event.request).catch(async () => {
            const cache = await caches.open('client-pages');
            const cached = await cache.match(event.request);
            if (cached) {
                const client = await self.clients.get(event.clientId);
                if (client) {
                    client.postMessage({ type: 'SERVED_FROM_CACHE', url: event.request.url });
                }
                return cached;
            }
            const fallback = await caches.match(OFFLINE_URL);
            return fallback || new Response('Offline', { status: 503 });
        })
    );
});

// --- Background Sync: workout log submissions ---
const workoutSyncPlugin = new BackgroundSyncPlugin('sync-workout-logs', {
    maxRetentionTime: 60 * 24 * 7,
});

registerRoute(
    ({ url, request }) => url.pathname === '/log' && request.method === 'POST',
    new NetworkFirst({
        cacheName: 'workout-submissions',
        plugins: [workoutSyncPlugin],
        fetchOptions: { credentials: 'include' },
    }),
    'POST'
);

// Listen for messages from the page
self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
