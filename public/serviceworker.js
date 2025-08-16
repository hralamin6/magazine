var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    // '/offline',
    // '/build/manifest.json',
    // '/images/icons/icon-72x72.png',
    // '/images/icons/icon-96x96.png',
    // '/images/icons/icon-128x128.png',
    // '/images/icons/icon-144x144.png',
    // '/images/icons/icon-152x152.png',
    // '/images/icons/icon-192x192.png',
    // '/images/icons/icon-384x384.png',
    // '/images/icons/icon-512x512.png',
];

// Cache on install
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => cache.addAll(filesToCache))
            .catch(err => console.error('Error caching files on install:', err))
    );
});

// Clear old caches on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(cacheName => cacheName.startsWith("pwa-") && cacheName !== staticCacheName)
                    .map(cacheName => caches.delete(cacheName))
            );
        }).catch(err => console.error('Error clearing old caches on activate:', err))
    );
});

// Serve from Cache
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
            .catch(() => caches.match('/offline'))
    );
});

// Handle Push Notifications
self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'Push Notification';
    const options = {
        body: data.body || 'You have a new notification!',
        icon: data.icon || '/images/icons/icon-192x192.png',
        badge: data.icon || '/images/icons/icon-72x72.png',
        data: {
            url: data.url || '/'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
    const urlToOpen = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            if (windowClients.length > 0) {
                // প্রথম tab কে use করে redirect
                const client = windowClients[0];
                client.navigate(urlToOpen); // navigate same tab
                return client.focus();
            } else {
                // যদি কোনো tab না থাকে, নতুন tab open করা যাবে (optional)
                return clients.openWindow(urlToOpen);
            }
        })
    );});
