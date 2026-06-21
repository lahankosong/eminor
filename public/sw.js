const CACHE = 'margonoandi-v2';
const SHELL = ['/', '/kamu', '/kita', '/dia', '/images/Margonoandi.jpeg', '/images/default-avatar.png'];

// ===== WEB PUSH: notifikasi sistem (muncul di tray Android, walau app tertutup) =====
self.addEventListener('push', function(e) {
    e.waitUntil(
        fetch('/notifications/latest', { credentials: 'include', headers: { 'Accept': 'application/json' } })
            .then(function(r){ return r.ok ? r.json() : null; })
            .then(function(d){
                var title = (d && d.title) ? d.title : 'Margonoandi';
                var body  = (d && d.body)  ? d.body  : 'Ada notifikasi baru';
                var url   = (d && d.url)   ? d.url   : '/dia';
                var tag   = (d && d.id)    ? ('maf-' + d.id) : 'maf-notif';
                return self.registration.showNotification(title, {
                    body: body,
                    icon: '/images/Margonoandi.jpeg',
                    badge: '/images/Margonoandi.jpeg',
                    tag: tag,
                    renotify: true,
                    data: { url: url }
                });
            })
            .catch(function(){
                return self.registration.showNotification('Margonoandi', {
                    body: 'Ada notifikasi baru', icon: '/images/Margonoandi.jpeg', data: { url: '/dia' }
                });
            })
    );
});

self.addEventListener('notificationclick', function(e) {
    e.notification.close();
    var target = (e.notification.data && e.notification.data.url) || '/';
    e.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(list){
            for (var i = 0; i < list.length; i++) {
                var c = list[i];
                if ('focus' in c) { try { c.navigate(target); } catch (err) {} return c.focus(); }
            }
            if (self.clients.openWindow) return self.clients.openWindow(target);
        })
    );
});

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
