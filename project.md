# Margonoandi Fanbase — Kerangka Project

## Overview
Aplikasi web komunitas fanbase untuk Margonoandi (musisi). Dibangun dengan Laravel + Blade, autentikasi via Google OAuth. Terdiri dari 4 halaman utama (Aku, Kamu, Kita, Dia) plus admin panel dan fitur musik.

---

## Tech Stack
- **Backend**: Laravel (PHP)
- **Frontend**: Blade templates, vanilla JS, AJAX fetch
- **Database**: MySQL (via XAMPP lokal / cPanel hosting)
- **Auth**: Google OAuth (GoogleController)
- **Deploy**: GitHub → cPanel via `deploy.php` (ZIP download)

---

## Struktur Direktori Penting

```
app/
  Http/Controllers/
    Auth/GoogleController.php       — Login Google OAuth + logout
    AkuController.php               — Post admin, like, komentar
    KamuController.php              — Profil user, edit/hapus post kita milik sendiri
    KamuNoteController.php          — CRUD catatan pribadi
    KitaController.php              — Feed komunitas, like, komentar
    DiaController.php               — Chat DM + grup
    AdminController.php             — Manajemen lagu
    SiteSettingController.php       — Pengaturan situs
    NotificationController.php      — Notifikasi (baca, tandai semua)
    ProfileController.php           — Halaman profil
    SongController.php              — Halaman detail lagu + komentar lagu
    AiAgentController.php           — AI Agent: generate konten + gambar (Cloudinary) + Pemotong Lagu
    MusicianController.php          — Direktori musisi + profil + follow (ekosistem Fase 1)
    BandPostController.php          — Cari Personil (band posts)
    HomeController.php              — Landing page

  Services/
    CloudinaryService.php           — Upload/hapus media ke Cloudinary (signed; secret terenkripsi di site_settings)

  Models/
    User.php
    AkuPost.php, AkuLike.php, AkuComment.php
    Post.php, PostLike.php, PostComment.php, PostCommentLike.php
    KamuNote.php
    MemberLog.php                    — log bergabungnya member baru (user_id, timestamps)
    Conversation.php, Message.php, ConversationInvite.php
    Group.php, GroupMember.php, GroupMessage.php
    DiaMessage.php, DiaInvite.php
    AppNotification.php             — tabel: notifications
    Song.php, SongComment.php
    AiGeneration.php, AiProvider.php (kind: text|image), AiImage.php (gambar AI → Cloudinary URL)
    MusicianProfile.php, Follow.php, BandPost.php   — ekosistem Fase 1
    Thread.php, ThreadReply.php
    SiteSetting.php                 — key-value (termasuk kredensial Cloudinary)

  Helpers/
    NotifHelper.php                 — NotifHelper::send() → AppNotification::create()
    WordFilter.php                  — sensor kata makian di chat (WordFilter::clean())

resources/views/
  layouts/
    fanbase.blade.php               — Layout utama fanbase (nav, sidebar, player, CSS vars)
    app.blade.php                   — Layout umum
  fanbase/
    aku.blade.php                   — Halaman Aku
    kamu.blade.php                  — Halaman Kamu
    kita.blade.php                  — Halaman Kita
    dia.blade.php                   — Halaman Dia (chat)
  admin/
    index.blade.php, create.blade.php, edit.blade.php
    settings.blade.php, ai-agent.blade.php
    ai-settings.blade.php            — Pengaturan AI (provider teks/gambar + Cloudinary)
    audio-cut.blade.php              — Pemotong Lagu (ffmpeg.wasm di browser)
    calendar.blade.php, promo.blade.php
  fanbase/
    musisi/, partials/dia-media.blade.php
  home.blade.php, welcome.blade.php, profile.blade.php
  community/, songs/, partials/

public/
  ffmpeg/                            — ffmpeg.wasm di-host sendiri (4 file; core.wasm 32MB) untuk Pemotong Lagu
  chat_media/                        — media chat transit (auto-prune >24 jam)

routes/web.php
```

---

## Halaman Utama Fanbase

### Aku (`/aku`)
- Post eksklusif dari admin (admin ditentukan via `ADMIN_EMAILS` di .env)
- Fitur: like + siapa yang like (tooltip), komentar + balasan, pin post, upload gambar
- **Welcome banner** untuk member baru (bergabung ≤ 7 hari): gradient sky+orange, dismissable via `localStorage` key `welcome_dismissed_{uid}`
- Model: `AkuPost`, `AkuLike` (fillable ✓, relasi user ✓), `AkuComment` (fillable ✓)

### Kamu (`/kamu`)
- Profil personal: statistik (post, like, komentar), tab Notes / Postingan Kita / **Tuner Gitar**
- Notes: catatan pribadi (CRUD), bisa di-pin, max 150 char preview
- Postingan: menampilkan post kita milik user yang login
- **Tuner Gitar**: Web Audio API murni, algoritma MPM + Hann window, meter jarum + cent,
  headstock SVG realistis (3+3 chrome), A4=440Hz. Semua logika ada di `kamu.blade.php`
- Model: `KamuNote` (is_pinned ✓), `Post`

### Kita (`/kita`)
- Feed komunitas semua user, paginate 15
- Fitur: buat post, like + tooltip, komentar, edit/hapus post sendiri
- **Log bergabung member**: setiap member baru muncul sebagai card di feed, disisipkan secara kronologis (bukan dikumpulkan di atas/bawah). Source: tabel `member_logs`; fallback ke `users.created_at` jika tabel kosong/belum ada.
- Model: `Post` (fillable ✓), `PostLike` (fillable ✓, relasi user ✓), `PostComment` (fillable ✓, relasi user ✓), `MemberLog` (fillable ✓)

### Dia (`/dia`)
- Chat DM (percakapan personal) + Chat Grup
- DM: `Conversation::firstOrCreate` dengan min/max user ID
- Grup: buat grup, pilih anggota, kirim pesan
- **Media chat (ala WhatsApp)**: foto / voice note / video. Server hanya transit (`public/chat_media`, auto-prune >24 jam), DB simpan URL (`media_url`/`media_type`); tiap device cache media di IndexedDB `diaMedia` (tetap tampil walau server hapus). Endpoint `/dia/upload`.
- **Sensor kata makian** via `WordFilter::clean()` di `send`/`sendGroup`.
- **Lokasi GPS** (bukan IP): saat kirim pesan, GPS + Nominatim (≈ kecamatan) → `users.city`; info di ikon ⓘ header chat.
- Model: `Conversation` (casts integer ✓), `Message` (fillable+media ✓, cast user_id ✓), `Group`, `GroupMember` (fillable ✓), `GroupMessage` (fillable+media ✓, cast user_id ✓)

---

## Design System (CSS Variables — `fanbase.blade.php`)

```css
--sky: #38A8CC         /* warna utama biru-tosca */
--sky-lt: #EEF7FB      /* latar terang sky */
--sky-dk: #2186A8      /* sky gelap */
--sky-mid: #7EC8E3     /* sky tengah */
--sky-glow: rgba(56,168,204,0.18)
--cream: #F6F9FC       /* latar halaman */
--card: #FFFFFF        /* latar kartu */
--surface: #EEF7FB
--text-1 s/d --text-4  /* hierarki teks */
--border: #D4E8F0
--border-lt: #EAF3F8
--shadow-sm/md/lg/xl
--orange: #F59E42      /* aksen */
```

---

## Notifikasi
- `NotifHelper::send($toUserId, $fromUserId, $type, $title, $body, $url)`
- Skip otomatis jika `$toUserId === $fromUserId`
- Menulis ke tabel `notifications` via `AppNotification::create()`
- **Penting**: selalu bungkus dalam `try-catch` di controller agar kegagalan DB notifikasi tidak merusak response utama

---

## Autentikasi & Roles
- Login hanya via Google OAuth (`/auth/google`)
- Logout via `/logout`
- Admin ditentukan oleh `ADMIN_EMAILS` di `.env` (cek di `AkuController::isAdmin()`)
- Middleware: `auth` (semua halaman fanbase), `isAdmin` (admin panel)

---

## Route Groups

| Grup | Middleware | Contoh Route |
|------|-----------|--------------|
| Fanbase | `auth` | `/aku`, `/kamu`, `/kita`, `/dia`, `/kamu/note/*`, `/notifications/*` |
| Admin | `auth` + `isAdmin` | `/admin/*` (CRUD lagu, settings, ai-agent) |
| Publik | — | `/`, `/lagu/{slug}`, `/auth/google`, `/community/*`, `/chat/*` |

> **Catatan**: Sejak 2026-06-10 semua route Kamu/Kamu-note/Notifikasi sudah dipindahkan ke dalam
> group `auth` (security fix). Tidak ada lagi endpoint fanbase di luar `auth`.

---

## Fitur Lain
- **Music Player persisten**: tetap berputar saat pindah halaman/refresh
  (state di `localStorage` key `fb_state`, resume via `canplay`, save via `beforeunload`).
  Ada juga **player desktop** di sidebar kiri (play/pause/stop SVG + progress seek)
- **Tuner Gitar** (Kamu): Web Audio API + MPM, meter jarum + headstock chrome realistis
- **Online Users**: sidebar kanan + pencarian member (`fbMemberSearch` → `/dia/start/{id}`); widget selalu tampil meski tidak ada yang online
- **Welcome banner** (Aku): muncul untuk member baru ≤ 7 hari, dismissable via localStorage
- **Log member baru** (Kita): card kronologis di feed; sumber `member_logs` + fallback `users.created_at`
- **Notifikasi Bell**: unread count di topbar, poll 30 detik, suara via Web Audio API, mark read AJAX
- **Like & balas komentar**: `parent_id` + `likes_count` + tabel `post_comment_likes`
- **Lokasi otomatis** (Kita): GPS → Nominatim (OpenStreetMap, tanpa API key) → nama kota
- **Realtime**: polling `setInterval` (pesan Dia 4 detik, notifikasi 30 detik) — bukan WebSocket
- **PWA**: `public/manifest.json` + `public/sw.js` → installable "Add to Home Screen"
- **Android (maftune)**: TWA wrap PWA → APK `com.maftune.app`; auto-update saat web di-deploy.
  Lihat `build_android.md` + `public/.well-known/assetlinks.json`
- **Admin Panel**: CRUD lagu, pengaturan situs, AI agent
- **AI Agent — Pipeline Konten** (`/admin/ai-agent`): topik → caption + image_prompt → **generate gambar** (Pollinations gratis / DALL-E / Gemini Imagen) → simpan ke **Cloudinary** (DB hanya URL, tabel `ai_images`). Pengaturan provider + Cloudinary terpisah di `/admin/ai-settings`. Catatan: gambar Gemini/Imagen butuh **billing** (free tier limit 0); Pollinations gratis tanpa key.
- **Pemotong Lagu** (`/admin/audio-cut`): ambil part lagu (mis. verse) untuk video; potong via **ffmpeg.wasm 100% di browser** (server 0 beban), hasil di IndexedDB `mafAudioClips`. ffmpeg di-host sendiri di `/public/ffmpeg/` (jangan oper `classWorkerURL` → memaksa module worker; biarkan classic worker auto-load supaya `importScripts` jalan).
- **Community**: thread diskusi + chat publik (pakai `layouts/app`, terpisah dari fanbase)
- **Deploy**: `deploy.php?key=margono2026` (tarik ZIP GitHub) + `fixdb.php` (bersihkan cache; section 6i media chat, 6j ai_images)

---

## Hal yang Perlu Diperhatikan
1. `Post::comments()` sudah include `->with('user')` di dalam definisi relasi — jangan double-eager-load
2. Semua perbandingan `===` / `!==` dengan kolom DB integer perlu cast di model (sudah diperbaiki untuk Conversation, Message, GroupMessage)
3. Semua `Model::create()` perlu `$fillable` terdefinisi (sudah diperbaiki untuk PostComment, PostLike, AkuLike, MemberLog)
4. `NotifHelper::send()` harus selalu di dalam `try-catch` sebelum `return response()->json()`
5. Di `GoogleController::callback()`, `MemberLog::create()` diisolasi dalam try-catch sendiri agar kegagalan log tidak memblokir login. Outer catch harus `\Throwable` (bukan `\Exception`) karena `\Error` (class not found) tidak ter-catch oleh `\Exception`.
6. `KitaController` selalu kirim `$posts` (paginated) dan `$memberLogs` (Collection) terpisah ke view — interleaving dilakukan di Blade dengan `$shownLogIds` untuk menghindari duplikat.

---

# 🎵 Roadmap Pengembangan & Strategi Pertumbuhan

> Rencana pengembangan web ke depan (growth + admin cleanup). Bagian di atas = kerangka teknis;
> bagian ini = arah & prioritas kerja. Status: WIP (fase Tier 1).

## Kondisi & Kendala
- **Budget**: ⚠️ Kredit Claude API habis → hindari fitur yang butuh API harian; pakai pendekatan template (tanpa API).
- **Hosting**: shared cPanel (shell_exec terbatas, Imunify360) → hindari tugas level-sistem, pakai web API.
- **Waktu**: Andi ~10–15 jam/minggu. **Skill**: Andi = lirik + story; Claude = teknis + strategi.
- **Aset**: 15 lagu (Spotify `4UKrlbmAOePUkl5YAdwlDa`), Android app maftune, komunitas Discord + tab Kita.

## Target 3 Bulan (KPI)
| Metrik | Sekarang | Target 3 bln | Strategi |
|--------|----------|--------------|----------|
| Pengunjung/bulan | ~500 | 20.000 | Content calendar + promosi sosial |
| DAU | ~5–10 | 50+ | Engagement komunitas + leaderboard |
| Subscriber newsletter | 0 | 1.000+ | Email capture di Spotify/YouTube |
| Member Discord | ~20 | 200+ | Bot + kontes |
| Durasi sesi | ~2 mnt | 5+ mnt | UX lebih baik + fitur interaktif |

## Roadmap 3 Tier

### TIER 1 — Kritis (Minggu 1–2, TANPA biaya)
1. **Content Calendar** — `ContentCalendarController` + `calendar.blade.php`; UI mirip spreadsheet (tanggal, lagu, platform, status); tampil ringkas di dashboard admin.
2. **Promo Templates (tanpa API)** — `PromoTemplateController`; 5 template per lagu (TikTok hook, IG caption ×3, YouTube desc, Spotify pitch, Discord announcement); tombol copy-to-clipboard; editor template.
3. **Admin Dashboard Redesign** — panel Quick Actions di atas (Create, AI Agent, Settings, Analytics); tabel responsif mobile; search + filter (judul, key, era, status); stats yang actionable.
4. **Edit Form Cleanup** — hapus Chord Detector (over-engineered); reorganisasi section (Basic Info → Chord & Nada → Lirik → Media); tab Preview; collapse section.

### TIER 2 — Penting (Minggu 3–4, hemat biaya)
1. **Email Newsletter** — Mailchimp free tier (500 email/bln); form signup; 2× kirim/minggu.
2. **Analytics Dashboard** — koneksi Google Analytics (read-only); sumber trafik, halaman teratas, growth.
3. **Multi-Platform Posting Scheduler** — 1 caption → auto-format IG/TikTok/Twitter/Discord; jadwal.
4. **Fan Engagement** — leaderboard mingguan, milestone member, form kontes (fan art/cover).

### TIER 3 — Nice-to-have (Bulan 2+, tergantung budget)
1. **AI Agent v2** (budget-aware) — hanya 1×/bulan saat rilis lagu; SUSPEND sampai budget ada.
2. **Spotify Playlist Pitching Helper** — generate template pitch + tracking (biaya Rp 0, ROI tinggi).
3. **Discord Bot** — auto-greet, lyric of the day, leaderboard, voting.

## Prioritas Channel Pertumbuhan
1. **TikTok/IG Shorts** (ROI tertinggi) — 3×/minggu, hook + lirik.
2. **Email Newsletter** — 2×/minggu.
3. **YouTube Shorts** — 3×/minggu (reuse konten TikTok).
4. **Spotify Editorial** — pitch per rilis (impact besar).
5. **Discord** — retensi komunitas.
6. **Cross-promotion** — kolaborasi musisi indie, 1×/bulan.

## Pembagian Tugas
- **Andi**: tulis lirik/story/hook, rencana content calendar, moderasi komunitas, pitch curator. JANGAN: coding, pakai AI Agent harian.
- **Claude**: bangun semua tool (calendar, templates, dashboard, newsletter, analytics, scheduler, engagement). JANGAN: tulis lirik, moderasi.

## Audit Admin Panel & Rencana Cleanup
Urutan eksekusi: **edit → index → settings → ai-agent → create**.

| File | Prioritas | Masalah | Rencana |
|------|-----------|---------|---------|
| `admin/edit.blade.php` | 🔴 HIGH | 500+ baris; Chord Detector over-engineered (~200 baris JS, akurasi rendah); audio upload di tengah; tak ada preview | Hapus chord detector; reorganisasi + collapse section; tambah tab Preview; input key manual |
| `admin/index.blade.php` | 🔴 HIGH | Tabel tak responsif mobile; tak ada search/filter/bulk; Quick Actions tersembunyi; stats tak actionable | Tabel responsif (stack di mobile); search + filter; bulk actions; panel Quick Actions; stats yang bisa diklik |
| `admin/settings.blade.php` | 🟡 MEDIUM | Quill editor berat (~180KB); form panjang; tak ada validasi visual/undo | Ganti Quill → textarea (markdown); validasi realtime; feedback tombol Save; opsi discard |
| `admin/ai-agent.blade.php` | 🟢 LOW | Generate = panggil Claude API (biaya); hasil sulit di-copy | SUSPEND fitur sampai budget ada; perbaiki UI (loading state, copy feedback) tanpa biaya |
| `admin/create.blade.php` | 🟢 LOW | Sudah rapi | (Opsional) share partial form dengan edit (DRY) |

## Catatan Saat Mulai Coding
- Sebelum mengerjakan tiap item, konfirmasi prioritas + rencana langkah ke Andi dulu.
- Semua fitur Tier 1 wajib tanpa biaya API (pendekatan template/hardcoded yang bisa di-customize).
- Ikuti konvensi teknis di bagian atas (fillable, cast integer, try-catch NotifHelper, CSS variables, dll.).
- Test di lokal → deploy via `deploy.php` + `fixdb.php` → verifikasi mobile (TWA) & desktop.

---

# 🌐 Visi Jangka Panjang: Margonoandi Ecosystem (Fase 2–12)

> Ide baru (15 Juni 2026): setelah fanbase kuat, berkembang dari "fanbase" menjadi
> **platform ekosistem musisi Indonesia** — dari penemuan → kolaborasi → monetisasi.
> Status: **visi/rencana**, belum dimulai. Mulai setelah fanbase (Tier 1–2) stabil &
> metrik tercapai (≈20K pengunjung/bln, 1K subscriber, 50+ DAU, 200+ member aktif).

## Strategi Dua Fase
- **Fase A — Fanbase (Bulan 0–2)**: jadikan margonoandi.my.id hub distribusi konten +
  komunitas (Tier 1 sebagian besar SELESAI: admin cleanup, content calendar, promo
  templates, AI Agent v2). Biaya Rp 0.
- **Fase B — Ecosystem (Bulan 2–12)**: pivot ke platform musisi. Investasi bertahap,
  mulai dari free-tier, naik sesuai traksi.

## Roadmap Ecosystem
| Fase | Perkiraan | Fitur inti |
|------|-----------|------------|
| **Fase 1** | Bulan 2–5 | **Direktori musisi** + **pembentukan band** ("cari personil": post, lamaran, bentuk band, pesan antar-musisi) |
| **Fase 2** | Bulan 5–8 | **Direktori studio** + **booking** (kalender, review, pembayaran) |
| **Fase 3** | Bulan 8–10 | **Marketplace gear/alat musik** (listing, transaksi/escrow) |
| **Fase 4** | Bulan 10–12+ | **Kolaborasi audio**, API pihak ketiga, ekspansi regional (SEA) |

## Entitas Data Utama (rencana)
- **Fase 1**: `musician_profiles` (role, skill, genre, lokasi, sample audio, portofolio),
  `band_posts` (cari personil), `band_applications`, `bands`, `band_members`, `musician_messages`.
- **Fase 2**: `studios`, `bookings`, `reviews`, `payment_transactions`.
- **Fase 3**: `listings` (gear), `marketplace_reviews`, `escrow_transactions`.

## Catatan Arsitektur & Anggaran
- Fase 1 masih muat di cPanel + MySQL; Fase 2+ kemungkinan butuh VPS, storage audio
  (S3-like), dan search (MySQL fulltext → Elasticsearch/Algolia bila perlu).
- Estimasi biaya hemat: Rp 0 (Fanbase) → Rp 5–10 jt/bln (Ecosystem, opsional/bertahap).
- **Validasi dulu**: bangun MVP Fase 1 minimal, uji ke 50–100 musisi sebelum lanjut Fase 2.

## Keunggulan
Jaringan musisi Indonesia + brand Margonoandi + founder seorang artis (kredibel ke musisi)
+ kontrol teknis penuh. Target jangka panjang: **platform #1 musisi indie Indonesia**.
