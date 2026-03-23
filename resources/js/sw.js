// Service worker - populated in next task
// self.__WB_MANIFEST must survive tree-shaking for workbox injectManifest
// eslint-disable-next-line no-unused-vars
const manifest = self.__WB_MANIFEST || [];

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', () => self.clients.claim());
