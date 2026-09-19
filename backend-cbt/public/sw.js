const CACHE_NAME = 'cbt-kartika-pwa-v1';
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/images/logo-kartika.png',
    '/images/pwa-icon-192.png',
    '/images/pwa-icon-512.png',
    '/images/pwa-icon-maskable.png',
    '/images/apple-touch-icon.png'
];

// Install Event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate Event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch Event
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Hanya tangani GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Navigasi halaman HTML: Network-first dengan fallback ke offline.html
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    return response;
                })
                .catch(() => {
                    return caches.match('/offline.html');
                })
        );
        return;
    }

    // Aset statis (CSS, JS, Fonts, Images): Stale-while-revalidate
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font'
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                }).catch(() => cachedResponse);

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // Default fetch
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
