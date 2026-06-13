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
    AiAgentController.php           — Generasi lirik AI
    HomeController.php              — Landing page

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
    AiGeneration.php
    Thread.php, ThreadReply.php
    SiteSetting.php

  Helpers/
    NotifHelper.php                 — NotifHelper::send() → AppNotification::create()

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
  home.blade.php, welcome.blade.php, profile.blade.php
  community/, songs/, partials/

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
- Model: `Conversation` (casts integer ✓), `Message` (fillable ✓, cast user_id ✓), `Group`, `GroupMember` (fillable ✓), `GroupMessage` (fillable ✓, cast user_id ✓)

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
- **Admin Panel**: CRUD lagu, pengaturan situs, AI agent generasi lirik
- **Community**: thread diskusi + chat publik (pakai `layouts/app`, terpisah dari fanbase)
- **Deploy**: `deploy.php?key=margono2026` (tarik ZIP GitHub) + `fixdb.php` (bersihkan cache)

---

## Hal yang Perlu Diperhatikan
1. `Post::comments()` sudah include `->with('user')` di dalam definisi relasi — jangan double-eager-load
2. Semua perbandingan `===` / `!==` dengan kolom DB integer perlu cast di model (sudah diperbaiki untuk Conversation, Message, GroupMessage)
3. Semua `Model::create()` perlu `$fillable` terdefinisi (sudah diperbaiki untuk PostComment, PostLike, AkuLike, MemberLog)
4. `NotifHelper::send()` harus selalu di dalam `try-catch` sebelum `return response()->json()`
5. Di `GoogleController::callback()`, `MemberLog::create()` diisolasi dalam try-catch sendiri agar kegagalan log tidak memblokir login. Outer catch harus `\Throwable` (bukan `\Exception`) karena `\Error` (class not found) tidak ter-catch oleh `\Exception`.
6. `KitaController` selalu kirim `$posts` (paginated) dan `$memberLogs` (Collection) terpisah ke view — interleaving dilakukan di Blade dengan `$shownLogIds` untuk menghindari duplikat.
