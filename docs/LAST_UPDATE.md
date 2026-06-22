# Catatan Pengembangan Terakhir — Margonoandi Fanbase

> Diperbarui: **2026-06-22**. File ini ikut `git pull` (portabel antar-komputer). Urut: terbaru di atas.

## 🆕 Lanjutan (commit kantor, di atas 6b5a79d)
- **004b91a** — **Papan Gig / Manggung** (`GigPostController`, model `GigPost`, tabel `gig_posts`, view `fanbase/gig/create`) + "Cari Personil" auto-post ke Kita dengan popup "linked" (migration `add_linked_to_posts` di `posts`). ⚠️ **2 migration baru → butuh section fixdb / cek tabel di server.**
- **d85223f / ee54c34 / f1b5bf7** — **Restructure landing page**: ticker DIHAPUS, CTA disusun ulang; **showcase mockup HP** (scroll horizontal) + upload screenshot admin untuk 6 fitur.
- **ee1de68 / 7e8856c / 319310f** — **Google Analytics 4**: tracking CTA/login/scroll-depth/song-play (`partials/ga.blade.php`, `config services.google_analytics_id` env `GOOGLE_ANALYTICS_ID`) + kartu GA4 di admin insights (quick link realtime/events/akuisisi/konversi).
- **e86c5ff** — **SEO maksimal**: JSON-LD `@graph` (WebSite + MusicGroup + ItemList di home; MusicRecording + BreadcrumbList di lagu), sitemap pakai `image:image`, preload, lirik di schema.

## Yang sudah selesai (commit utama)

### 🔎 SEO — Fase 1, 2, 3 (8008b10 → 6b5a79d)
- **Fase 1:** `<title>` & `<meta description>` dinamis per halaman, Open Graph + Twitter Card, canonical, **JSON-LD** (MusicGroup di homepage, MusicRecording per lagu) — di `layouts/app.blade.php` via array `$seo` (default fallback). `HomeController@index` & `SongController@show` mengisi `$seo`.
- **sitemap.xml** dinamis: `HomeController@sitemap` (route `/sitemap.xml`) — homepage + semua lagu aktif.
- **robots.txt** (`public/robots.txt`) — buka publik, tutup area member/admin, tunjuk sitemap.
- **Fase 2:** Google Search Console **TERVERIFIKASI** (properti URL-prefix, metode Tag HTML). Meta `google-site-verification` dipasang di head via `config('services.google_site_verification')` (env `GOOGLE_SITE_VERIFICATION`, ada default token). **JANGAN hapus meta ini.**
- **Fase 3:** panel **"Saran SEO"** di `/admin/insights` — `AdminController@seoSuggest` + helper `aiTextComplete()`. AI (DeepSeek) buat saran dari kata kunci komunitas + lagu terpopuler (`play_count`): 3 meta description, 5 ide konten, lagu yang layak didorong, frasa kunci. **Saran saja** (terapkan manual). Cache 7 hari.

### 🔐 Fix Login Google (b574e14, db49e49)
- Blok `catch` di `GoogleController@callback` tadinya `route('login')` (route TIDAK ADA → tiap error OAuth jadi 500). Kini `route('home')` + `report($e)` (catat ke log).
- Tambah fallback `Socialite::driver('google')->stateless()->user()` saat `InvalidStateException`.
- ⚠️ **MASIH GAGAL menurut laporan user** — lihat checklist di bawah.

### 🔔 Web Push / Notifikasi Android (b18dbcf, 0ba4eeb)
- Notifikasi tray Android via Web Push (VAPID ES256 murni PHP/openssl, payloadless) + service worker (`public/sw.js`). `NotifHelper::send` → `WebPush::sendToUser` (best-effort).
- Tabel `push_subscriptions` (migration + **fixdb section 9y**). `PushController` subscribe/unsubscribe/**test**.
- Tombol **"Aktifkan"** + **"Tes"** notifikasi di dropdown lonceng (Dia) untuk diagnosa.
- ⚠️ **Belum muncul di Android** — lihat checklist.

### Lainnya (sesi ini)
- 🧠 **Analisis Komunitas** `/admin/insights` (4da963c) — kata kunci + analisa AI dari post/komentar publik. Dashboard dirapikan (hapus Pipeline & Quick Actions; Aktivitas Terbaru hide/show).
- 🤖 **Welcome Bot** chat (4ad9b61 → d3f3878) — sambutan user baru + balas dua-arah (DeepSeek grounded / fallback). On-topic, no-markdown, ringkas.
- 📊 **Statistik pemutaran lagu** `play_count` (21837d3) — tracking + admin (fixdb 9z).
- 🎸 **Tab Chord 4 instrumen** Gitar/Piano/Ukulele/Bass (6cdf63b → e9ff92f) — kamus + geser + bunyi + label senar + arah genjreng.
- 🎨 **Tema**: homepage "Aurora Studio"; fanbase **dark-mode otomatis ikut jam HP** + spotlight neon kartu.
- 🔗 **Fix mention** (d3f3878): terima undangan → buka obrolan asal.

---

## ▶ LANJUTKAN DI SINI (urut prioritas)

1. **Login Google masih gagal.** Buka `https://margonoandi.my.id/fixdb.php?key=<DEPLOY_KEY>` → bagian **"9. Log Error Laravel (50 baris terakhir)"** → cari error `GoogleController` / `Socialite` / nama exception → perbaiki akar masalahnya. (Kemungkinan: state OAuth / sesi / redirect URI / proxy HTTPS.)
2. **Notifikasi Android belum muncul.** Pastikan 3 var `VAPID_*` ada di `.env` SERVER + sudah jalankan `fixdb.php` (tabel `push_subscriptions`). Tes lewat lonceng → tombol "Aktifkan" lalu "Tes". Kalau dialog izin tak muncul / tray kosong padahal "terkirim" → **rebuild APK** (Bubblewrap terbaru, izin `POST_NOTIFICATIONS` Android 13+).
3. **Submit sitemap** di Search Console: menu "Peta Situs" → `sitemap.xml` → Kirim. (Properti sudah terverifikasi.)
4. **Backlog lama:** verifikasi fingering chord ukulele/bass, tuner untuk uke/bass, dark mode admin, AI pipeline Fase B.

## Operasional penting
- **Deploy:** `deploy.php?key=<DEPLOY_KEY>&run=1` lalu (bila ada perubahan DB) `fixdb.php?key=<DEPLOY_KEY>`. Kunci ada di `.env` (`DEPLOY_KEY`) — **tidak di git**.
- **JANGAN** `php artisan migrate` penuh di server (riwayat migrasi tak sinkron) → pakai fixdb section.
- **Rahasia** (DEPLOY_KEY, VAPID private, API key) ada di `.env` SERVER & `.env` lokal rumah — **tidak ikut git**. Kalau butuh di komputer kantor untuk dev lokal, salin manual dari `.env` server / rumah.
- AI (welcome bot, analisis, saran SEO) butuh provider **DeepSeek** aktif di Admin → Pengaturan AI.
