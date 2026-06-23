# Catatan Pengembangan Terakhir — Margonoandi Fanbase

> Diperbarui: **2026-06-23**. File ini ikut `git pull` (portabel antar-komputer). Urut: terbaru di atas.

## 🆕 Audio Tools + Karaoke (lanjutan 2026-06-23 · kantor + rumah)

**Tools audio (commit kantor 02b0e62 → e4b4eb0)**
- **02b0e62** — **Pemotong Lagu Online** publik tanpa login (`ToolController@audioCutter`, route `/tools/potong-lagu`, view `tools/audio-cutter.blade`) — client-side, SEO (schema WebApplication). Masuk **sitemap** (d0fc783).
- **f1651dd / 9523d83** — popup waveform: drag handle, zoom, play/pause, fix mobile + fix 500.
- **3f93fd7 / cf1c6ce** — audio cutter "flat" di `/kamu` + output **MP3** (lamejs) + tab **Konversi Audio**.
- **run_patch.php** (kembaran deploy.php, ZIP-GitHub + `migrate --force`) buatan sesi kantor → **DIHAPUS** (publik + overwrite + shell = bahaya; pakai `deploy.php` saja). Catatan: AI kantor juga keliru bikin `project_last_update.md` (tak ter-push) → changelog itu hilang, kini direkam di sini.

**Hapus Vokal / Karaoke (commit rumah e6cc76d → f0fca10)**
- "Split Instrumen 4 stem" via **Demucs ONNX di browser** = model **289 MB** → OOM/lambat di HP, **tak pernah jalan** → dibuang.
- **e6cc76d** — diganti **phase-cancellation** (Instrumen L-R + Vokal) — instan, jalan di HP (bukan 4 stem AI).
- **f0fca10** — ditingkatkan ke **STFT bertingkat** (`public/js/vocal-remover.js`, dipakai home/kamu/admin): FFT 4096 + Hann WOLA + center-extraction koherensi L/R + **gate frekuensi** (bass <170Hz dipertahankan, vokal 320-7kHz dibuang, highs ditaper). Bass/drum tak drop, vokal lebih bersih. Butuh lagu **stereo**. Parameter mudah disetel di file modul.

---

## 🆕 Sesi 2026-06-23 — Ekosistem musisi, EPK, matchmaking, One Tap (8ded9a9 → eb618c0)

**Profil musisi & EPK**
- **8ded9a9** — `musisi.show` jadi **PUBLIK** (keluar grup `auth`): guest klik link share → **halaman teaser** (`fanbase/musisi/public.blade`, layout `app`, + OG/SEO untuk preview WA/medsos); member tetap lihat profil lengkap. **Portofolio/kontak/Tip Jar tetap wajib login** (anti-bypass). Route publik `GET /musisi/{id}`.
- **d26f82b / 57fabb1** — **Kartu portofolio sebagai GAMBAR** (Canvas 1080×1350: foto/initials, nama, peran, genre, bio, **QR code** via api.qrserver.com, branding). Tombol "📸 Kartu Gambar". Logika diekstrak ke **`public/js/musician-card.js`** (dipakai profil member & publik; guest pun bisa sebar).
- **6f30450** — **Upload + crop foto profil**: kolom **`musician_profiles.photo`** (fixdb **section 9u** + migration `2026_06_23_000001`), helper **`MusicianProfile::photoUrl()`** (foto manual override avatar Google), simpan ke `public_path('images/avatars')`. Cropper modal (geser + zoom, output 512×512 via canvas + DataTransfer). Foto kustom dipakai di SEMUA kartu musisi (show/public/landing/matchmaking/direktori).

**Matchmaking 2 arah (pasar dua sisi)**
- **4cb9ee0** — sisi A: **"Musisi yang cocok"** di detail Cari Personil (`BandPostController@show`, `band/show.blade`). Skor = peran×3 + genre×1 + lokasi×2. Tombol **"Ajak"** (pembuat lowongan, kirim pesan pembuka) / **"Lihat"**.
- **4f7a0e6** — sisi B: **"Peluang untukmu"** di direktori (`MusicianController@index` hitung `$opportunities` dari band post cocok + `$cityGigs`). Kartu scroll horizontal di `musisi/index.blade`.
- **6f30450** — **sinkron peran onboarding→direktori**: `User::rolesToCleanLabels()`; OnboardingController isi `profile.roles` bila kosong; form edit pre-fill peran.

**Landing → mesin konversi cold-start**
- **ec09cb8 / c6a7b30** — showcase musisi **SELALU tampil** (dulu hanya kalau sudah ada musisi). Reframe **"Komunitas musisi yang sedang tumbuh"** + pitch nilai + **perks chips** (kartu QR / matchmaking / Tip Jar) + kartu **"Jadi musisi di sini"** (pulse) + **glow aurora** + CTA primer. Ber-event GA.
- **57fabb1** — ganti **"kamar tidur" → "kamarmu"** di seluruh web (SEO desc, bot, eyebrow, kartu).
- **3d00005 → ba82c7d** — efek **3D tilt** kartu musisi (landing + profil publik): ikut kursor / jari (touch-drag) / giroskop HP.

**🔑 Google One Tap (login sekali tap)**
- **c9b3a7a / aa071f3** — popup "Lanjut sebagai…" → login tanpa redirect. `GoogleController@oneTap` verifikasi ID token **server-side** via endpoint resmi Google `tokeninfo` (cek `aud`=client_id kita + `iss` Google), lalu `updateOrCreate` + `Auth::login` (mirror callback: MemberLog + WelcomeBot). Route **`POST /auth/google/onetap`** (`google.onetap`). Skrip GSI di `layouts/app` (guest), dimuat **PASCA `window.load`** (cegah spinner). CSRF aktif via header.
- ⚠️ **Server `.env` WAJIB pakai client `753501956819-…`** (yang punya **Authorized JavaScript origins** = `https://margonoandi.my.id`). Lihat checklist.

**🐞 Fix penting**
- **99c829f** — **`public/images/default-avatar.png` HILANG** (404) → tiap fallback `<img>` memicu `onerror` → set src ke file 404 lagi → **loop tak terhingga** (1399 request) → **tab "loading" abadi / favicon tak muncul**. Dibuat file-nya (GD) + **guard `this.onerror=null`** di 10 view (anti-loop permanen).
- **eb618c0** — hapus preload `Margonoandi.jpeg` (tak dipakai di home, cuma OG meta) → bersihkan warning console.

**✅ LOGIN GOOGLE SUDAH JALAN** — tak lagi gagal (user berhasil login & kelola profil sepanjang sesi). Item "login gagal" lama dianggap **selesai**.

---

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

1. **Google One Tap — set `.env` SERVER ke client `753501956819-…`** (`GOOGLE_CLIENT_ID` + `GOOGLE_CLIENT_SECRET` dari JSON OAuth yang punya **Authorized JS origin** `margonoandi.my.id`) → `php artisan config:clear`. Tes One Tap di **browser bersih** (FedCM masuk cooldown kalau popup sering di-dismiss). Tombol "Masuk" tetap fallback. (Login redirect biasa **sudah jalan**.)
2. **fixdb section 9u** di server — tambah kolom `musician_profiles.photo` (WAJIB untuk fitur upload/crop foto). Jalankan `fixdb.php?key=<DEPLOY_KEY>`. Pastikan folder `public/images/avatars/` bisa ditulis (upload foto).
3. **Notifikasi Android belum muncul.** Pastikan 3 var `VAPID_*` ada di `.env` SERVER + sudah jalankan `fixdb.php` (tabel `push_subscriptions`). Tes lewat lonceng → "Aktifkan" lalu "Tes". Kalau dialog izin tak muncul / tray kosong padahal "terkirim" → **rebuild APK** (Bubblewrap, izin `POST_NOTIFICATIONS` Android 13+).
4. **Submit sitemap** di Search Console: menu "Peta Situs" → `sitemap.xml` → Kirim. (Properti sudah terverifikasi.)
5. **Backlog lama:** fingering chord ukulele/bass, tuner uke/bass, dark mode admin, AI pipeline Fase B.

## Operasional penting
- **Deploy:** `deploy.php?key=<DEPLOY_KEY>&run=1` lalu (bila ada perubahan DB) `fixdb.php?key=<DEPLOY_KEY>`. Kunci ada di `.env` (`DEPLOY_KEY`) — **tidak di git**.
- **JANGAN** `php artisan migrate` penuh di server (riwayat migrasi tak sinkron) → pakai fixdb section.
- **Rahasia** (DEPLOY_KEY, VAPID private, API key) ada di `.env` SERVER & `.env` lokal rumah — **tidak ikut git**. Kalau butuh di komputer kantor untuk dev lokal, salin manual dari `.env` server / rumah.
- AI (welcome bot, analisis, saran SEO) butuh provider **DeepSeek** aktif di Admin → Pengaturan AI.
