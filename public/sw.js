const CACHE = 'margonoandi-v1';
const SHELL = ['/', '/kamu', '/kita', '/dia', '/images/Margonoandi.jpeg', '/images/default-avatar.png'];

self.addEventListener('install', function(e) {
    e.waitUntil(
        caches.open(CACHE).then(function(c) {
            return c.addAll(SHELL).catch(function(){});
        }).then(function(){ return self.skipWaiting(); })
    );
});

self.addEventListener('activate', function(e) {
    e.waitUntil(
        caches.keys().then(function(keys) {
            return Promise.all(keys.filter(function(k){ return k !== CACHE; }).map(function(k){ return caches.delete(k); }));
        }).then(function(){ return self.clients.claim(); })
    );
});

self.addEventListener('fetch', function(e) {
    // Jangan intercept POST / API / WebSocket
    if (e.request.method !== 'GET') return;
    var url = new URL(e.request.url);
    if (url.pathname.startsWith('/api') || url.pathname.startsWith('/notifications')) return;

    e.respondWith(
        fetch(e.request)
            .then(function(res) {
                // Cache hanya resource statis
                if (res.ok && (url.pathname.match(/\.(js|css|png|jpg|jpeg|ico|svg|woff2?)$/) || SHELL.includes(url.pathname))) {
                    var clone = res.clone();
                    caches.open(CACHE).then(function(c){ c.put(e.request, clone); });
                }
                return res;
            })
            .catch(function() {
                return caches.match(e.request).then(function(cached) {
                    return cached || new Response('<h1>Offline</h1><p>Tidak ada koneksi internet.</p>', {
                        headers: { 'Content-Type': 'text/html' }
                    });
                });
            })
    );
});
