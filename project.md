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
    Post.php, PostLike.php, PostComment.php
    KamuNote.php
    Conversation.php, Message.php
    Group.php, GroupMember.php, GroupMessage.php
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
- Model: `AkuPost`, `AkuLike` (fillable ✓, relasi user ✓), `AkuComment` (fillable ✓)

### Kamu (`/kamu`)
- Profil personal: statistik (post, like, komentar), tab Notes dan Postingan Kita
- Notes: catatan pribadi (CRUD), bisa di-pin, max 150 char preview
- Postingan: menampilkan post kita milik user yang login
- Model: `KamuNote` (is_pinned ✓), `Post`

### Kita (`/kita`)
- Feed komunitas semua user, paginate 15
- Fitur: buat post, like + tooltip, komentar, edit/hapus post sendiri
- Model: `Post` (fillable ✓), `PostLike` (fillable ✓, relasi user ✓), `PostComment` (fillable ✓, relasi user ✓)

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
| Fanbase | `auth` | `/aku`, `/kamu`, `/kita`, `/dia` |
| Admin | `auth` + `isAdmin` | `/admin/*` |
| Publik | — | `/`, `/lagu/{slug}`, `/auth/google` |
| ⚠️ Di luar auth | — | `/kamu/note` (POST/PUT/DELETE), `/kamu/{id}` (PUT/DELETE) |

> **Catatan**: Beberapa route kamu dan kamu-note tidak ada di dalam group `auth` — perlu diperhatikan saat pengembangan lanjutan.

---

## Fitur Lain
- **Music Player**: sidebar kiri, autoplay dari tabel `songs`, filter `is_active = true`
- **Online Users**: sidebar kanan, 8 user terbaru
- **Notifikasi Bell**: unread count di topbar, mark as read via AJAX
- **Admin Panel**: CRUD lagu, pengaturan situs, AI agent generasi lirik
- **Community**: thread diskusi (terpisah dari fanbase)
- **Deploy**: `deploy.php` di root — download ZIP dari GitHub dan ekstrak

---

## Hal yang Perlu Diperhatikan
1. `Post::comments()` sudah include `->with('user')` di dalam definisi relasi — jangan double-eager-load
2. Semua perbandingan `===` / `!==` dengan kolom DB integer perlu cast di model (sudah diperbaiki untuk Conversation, Message, GroupMessage)
3. Semua `Model::create()` perlu `$fillable` terdefinisi (sudah diperbaiki untuk PostComment, PostLike, AkuLike)
4. `NotifHelper::send()` harus selalu di dalam `try-catch` sebelum `return response()->json()`
