# SKILL.md — Panduan Kerja Margonoandi Fanbase

> Referensi cepat untuk mengembangkan project ini secara konsisten.
> Lihat juga: `project.md` (kerangka aplikasi) dan `last_update.md` (log per sesi).

---

## Cara Pakai Referensi Ini

Jadikan file ini referensi saat develop (mis. di VS Code kantor):

```bash
# 1. Di terminal, copy skill saya ke project kamu
cp /mnt/skills/user/margonoandi-fanbase/SKILL.md \
   /path/to/margonoandi-fanbase/SKILL-reference.md

# 2. Buka di VS Code
code SKILL-reference.md

# 3. Sekarang bisa reference saat develop
```

> Catatan: `SKILL.md` ini sudah ada di root repo, jadi cukup `git pull` lalu buka langsung.
> Langkah `cp` di atas berguna kalau skill-nya tersimpan di luar repo (mis. folder skill `/mnt/skills/...`).

---

## Quick Reference

### Folder structure
```
margonoandi-fanbase/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # AkuController, KamuController, KitaController, DiaController, dll.
│   │   │   └── Auth/GoogleController.php
│   │   └── Middleware/IsAdmin.php
│   ├── Models/                 # User, AkuPost, Post, KamuNote, Conversation, MemberLog, dll.
│   └── Helpers/NotifHelper.php  # NotifHelper::send(...)
├── resources/views/
│   ├── layouts/
│   │   ├── fanbase.blade.php    # Layout member (sky theme) — nav, sidebar, player, notif
│   │   └── app.blade.php        # Layout publik (dark theme)
│   ├── fanbase/                 # aku, kamu, kita, dia .blade.php
│   ├── admin/                   # index, create, edit, settings, ai-agent
│   ├── community/  songs/  partials/
│   └── home.blade.php  profile.blade.php
├── routes/web.php               # SEMUA route ada di sini
├── database/
│   ├── migrations/
│   └── database.sqlite          # DB lokal (produksi pakai MySQL)
└── public/
    ├── deploy.php  fixdb.php     # tooling deploy ke cPanel
    ├── manifest.json  sw.js      # PWA
    └── .well-known/assetlinks.json  # TWA Android (maftune)
```

### Key files location
| Butuh… | File |
|--------|------|
| Tambah/ubah route | `routes/web.php` |
| Layout member + player + notif bell | `resources/views/layouts/fanbase.blade.php` |
| CSS variables (design system) | `<style>` di `layouts/fanbase.blade.php` |
| Halaman 4 inti | `resources/views/fanbase/{aku,kamu,kita,dia}.blade.php` |
| Kirim notifikasi | `app/Helpers/NotifHelper.php` |
| Cek admin | `AkuController::isAdmin()` / `app/Http/Middleware/IsAdmin.php` |
| Login Google | `app/Http/Controllers/Auth/GoogleController.php` |
| Tuner gitar | `resources/views/fanbase/kamu.blade.php` (tab Tuner) |

### Tech stack overview
- **Laravel 11** (PHP 8.2+), Blade + **vanilla JS** (tanpa SPA framework), AJAX `fetch`
- **DB**: SQLite lokal (`database/database.sqlite`) · MySQL di produksi cPanel
- **Auth**: Google OAuth saja (Socialite) — tanpa email/password
- **Realtime**: polling `setInterval` (Dia 4 dtk, notif 30 dtk) — bukan WebSocket
- **PWA** (`manifest.json` + `sw.js`) → di-wrap jadi **APK Android maftune** (TWA, `com.maftune.app`)
- Deploy: GitHub `main` → `deploy.php` (tarik ZIP) + `fixdb.php` (bersihkan cache)

---

## Development Patterns

### How to add new feature
1. **Route** di `routes/web.php` — masukkan ke group `auth` (atau `auth`+`isAdmin` untuk admin).
2. **Controller** — method `index()` untuk tampil, `store()/update()/destroy()` untuk aksi.
3. **Model** — WAJIB definisikan `$fillable`; cast kolom FK ke `integer` bila dipakai perbandingan kepemilikan.
4. **Migration** — `php artisan make:migration`, lalu `php artisan migrate`.
5. **View** — extend `layouts.fanbase` (member) atau `layouts.app` (publik). Pakai CSS variables, jangan hardcode warna.
6. **JS** — AJAX `fetch` dengan header CSRF (lihat pola di bawah).
7. **Deploy** — commit → push `main` → `deploy.php` → `fixdb.php`.

### Controller structure
```php
class FiturController extends Controller
{
    public function index() {
        $items = Model::with(['user'])->latest()->paginate(15);
        return view('fanbase.fitur', compact('items'));
    }

    public function store(Request $request) {
        $request->validate(['body' => 'required|string|min:2']);
        $item = Model::create([
            'user_id' => Auth::id(),
            'body'    => $request->body,
        ]);
        // notifikasi SELALU sebelum return + try-catch
        try { NotifHelper::send($ownerId, Auth::id(), 'comment', 'Judul', 'Isi', '/url'); }
        catch (\Throwable $e) {}
        return response()->json(['ok' => true, 'item' => $item]);
    }

    private function isAdmin() {
        return in_array(Auth::user()->email, explode(',', env('ADMIN_EMAILS', '')));
    }
}
```
- Aksi admin diawali `if (!$this->isAdmin()) abort(403);`
- Cek kepemilikan: `if ($item->user_id !== Auth::id()) abort(403);` (butuh cast integer di model).

### Model relationships
```php
class Post extends Model {
    protected $fillable = ['user_id', 'body', 'location'];   // WAJIB
    protected $casts = ['user_id' => 'integer'];             // untuk perbandingan ===
    public function user()     { return $this->belongsTo(User::class); }
    public function comments() { return $this->hasMany(PostComment::class)->with('user'); }
    public function likes()    { return $this->hasMany(PostLike::class); }
}
```
Pola entitas: induk (`AkuPost`/`Post`) → `*Comment` (punya `parent_id` untuk reply, `likes_count`) → `*CommentLike`, plus `*Like`.

### Blade template patterns
```blade
@extends('layouts.fanbase')
@section('content')
<div class="card">
    <h2 style="color:var(--text-1)">{{ $item->title }}</h2>
    <p style="white-space:pre-wrap">{{ $item->body }}</p>  {{-- jaga newline --}}
    <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}">  {{-- fallback --}}
    <span>{{ $item->created_at?->diffForHumans() ?? '-' }}</span>  {{-- null-safe --}}
</div>
@endsection
@push('scripts') <script> /* ... */ </script> @endpush
```
- Warna SELALU via CSS variables (`var(--sky)`, `var(--text-1)`, `var(--card)`…).
- Avatar SELALU pakai fallback `asset('images/default-avatar.png')`.
- Tanggal SELALU null-safe (`?->format()` / `?->diffForHumans()`).
- Untuk pass data PHP→JS: `json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)` di dalam `@php`, **bukan** `@json()`.

### JS/AJAX pattern
CSRF meta sudah ada di `<head>` layout fanbase:
```js
fetch('/kita/' + id + '/like', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({ ... })
})
.then(r => r.json())
.then(d => { /* update DOM */ })
.catch(() => { /* fail diam-diam */ });
```

---

## Common Tasks

### Add new route
`routes/web.php` — taruh di group yang benar:
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/fitur', [FiturController::class, 'index'])->name('fitur');
    Route::post('/fitur', [FiturController::class, 'store'])->name('fitur.store');
});
```
Admin: `Route::middleware(['auth','isAdmin'])->prefix('admin')->name('admin.')->group(...)`.

### Create notification
```php
use App\Helpers\NotifHelper;
try {
    NotifHelper::send($toUserId, Auth::id(), 'like', 'Judul', 'Isi opsional', '/url-opsional');
} catch (\Throwable $e) {}
```
- Type valid (punya ikon): `like ♥`, `comment 💬`, `reply ↩️`, `mention @`, `message 📩`, `post 📝`, `invite 🤝`. Lainnya → `🔔`.
- Otomatis skip kalau `$toUserId === $fromUserId`.
- **WAJIB** dipanggil SEBELUM `return response()` dan dibungkus `try-catch(\Throwable)`.

### Add new Tab (Kamu, Kita)
Di `kamu.blade.php` polanya:
```blade
<div class="kamu-tabs">
    <button class="kamu-tab active" onclick="kamuTab('Notes', this)">Notes</button>
    <button class="kamu-tab" onclick="kamuTab('NamaBaru', this)">Label</button>
</div>
<div class="kamu-tab-content active" id="kamuTabNotes" style="display:block">…</div>
<div class="kamu-tab-content" id="kamuTabNamaBaru">…</div>
```
Fungsi `kamuTab(name, btn)` menyembunyikan semua `.kamu-tab-content`, menampilkan `#kamuTab{name}`.
Catatan: `kamuTab()` memanggil `tunerStop()` saat pindah dari tab Tuner (lepas mikrofon).

### Fix 500 error
Urutan cek (berdasarkan riwayat bug nyata):
1. **`$fillable` hilang** → `MassAssignmentException` saat `Model::create()`. Tambahkan `$fillable`.
2. **Relasi/method tidak ada** saat eager load (`with('comments.user')`) → tambahkan method relasi atau hapus eager load yang tak perlu.
3. **Cast integer** → perbandingan `'1' !== 1` selalu true → `abort(403)`. Tambahkan `$casts`.
4. **Tabel belum ada di produksi** (mis. `member_logs`) → bungkus query `try-catch(\Throwable)` + fallback, lalu jalankan migrasi.
5. **Carbon parse `last_seen`/tanggal invalid** → pakai `strtotime()` + try-catch, bukan Carbon langsung.
6. **View cache lama di server** → jalankan `fixdb.php` (hapus `storage/framework/views/*.php`).
7. Cek `storage/logs/laravel.log` di server.

### Deploy changes
```
git add … && git commit -m "…" && git push origin main
→ buka https://margonoandi.my.id/deploy.php?key=margono2026&run=1   # tarik ZIP GitHub, copy file, artisan
→ buka https://margonoandi.my.id/fixdb.php                          # migrate + bersihkan cache + diagnostik
```
- Dipreserve saat deploy: `vendor`, `.env`, `storage` (+ `node_modules`, `.git`).
- APK **maftune** otomatis menampilkan versi terbaru tanpa rebuild (kecuali ganti icon/nama/permission → lihat `build_android.md`).

### Clear cache
```bash
php artisan view:clear     # cache blade
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan migrate         # bila ada migrasi baru
```
Di produksi: `fixdb.php` melakukan pembersihan cache + migrate sekaligus.

---

## Design System

### CSS variables (`layouts/fanbase.blade.php`)
```css
--sky: #38A8CC         /* utama biru-tosca */     --sky-lt: #EEF7FB
--sky-dk: #2186A8                                 --sky-mid: #7EC8E3
--sky-glow: rgba(56,168,204,0.18)
--cream: #F6F9FC       /* latar halaman */        --card: #FFFFFF
--surface: #EEF7FB
--text-1 … --text-4    /* hierarki teks (gelap→terang) */
--border: #D4E8F0      --border-lt: #EAF3F8
--shadow-sm / -md / -lg / -xl
--orange: #F59E42      /* aksen */
```
Tuner pakai status warna: hijau `#22c55e` (pas), oranye `#fb923c` (♭ rendah), merah `#ef4444` (♯ tinggi).

### Responsive breakpoints
| Breakpoint | Untuk |
|-----------|-------|
| `max-width: 1060px` | Tablet & bawah → layout mobile (sidebar jadi list, bottom nav muncul) |
| `max-width: 768px` | Mobile umum |
| `max-width: 480px` | HP kecil (penyesuaian font/padding) |
- Mobile: topbar ~56px + bottom nav ~84px → tinggi konten `calc(100vh - 56px - 84px)`.
- Input chat Dia: `position:sticky; bottom:0` agar di atas bottom nav.

### Icon/emoji guide
- **UI icons**: pakai **SVG** (Feather/Heroicons) inline — JANGAN karakter Unicode (▶/▮▮) karena render tak konsisten lintas device.
- **Notifikasi**: emoji per type (lihat NotifHelper) — boleh emoji karena dari data.
- **Play/pause**: SVG di-swap via `outerHTML` di `fbUpdateUI()`.

---

## Troubleshooting

### Route auth issues
- Semua endpoint fanbase HARUS di dalam `Route::middleware(['auth'])->group(...)`. Sejak 2026-06-10 tidak ada lagi route fanbase di luar `auth`.
- 403 di admin → cek email ada di `ADMIN_EMAILS` (.env), comma-separated tanpa spasi.
- 419 (CSRF) → pastikan `<meta name="csrf-token">` ada & header `X-CSRF-TOKEN` dikirim.

### Model fillable errors
- Gejala: `MassAssignmentException` / data tidak tersimpan / 500 di halaman yang load relasi.
- Fix: tambah `protected $fillable = [...]` di model. Untuk reply/like komentar: kolom `parent_id`, `likes_count`, tabel `*_comment_likes`.
- Perbandingan kepemilikan gagal (selalu 403/“bukan punyamu”) → tambah cast `'user_id' => 'integer'` (juga `user_one_id`/`user_two_id` di `Conversation`).

### View compilation errors
- Cek sintaks Blade: jalankan `php artisan view:cache` (akan error jika ada `@php`/`@foreach`/`@if` tak seimbang). Lalu `php artisan view:clear`.
- SVG `<defs>`/`<gradient>`: pastikan tiap `<stop>` punya `stop-color` valid (hindari nilai placeholder).
- Data PHP→JS error parse → ganti `@json()` dengan `json_encode(..., JSON_HEX_*)` di blok `@php`.

### Deploy failures
- `deploy.php` butuh query `?key=margono2026&run=1`. Pastikan `run=1` ada.
- Jika file tak ter-update → cek output “File di-copy: N” dan jalankan `fixdb.php` untuk hapus view cache lama.
- 500 setelah deploy → biasanya view/route/config cache basi → `fixdb.php`; atau migrasi belum jalan → `php artisan migrate` (fixdb juga menjalankannya).
- **Disk penuh di mesin dev** (gejala: timeout berulang, OneDrive/VSCode I/O error, “mengulang dari awal”) → cek `Get-PSDrive C`; bersihkan temp/cache/installer. Pernah terjadi C: = 0 MB.

---

## Testing Checklist

### Desktop (Chrome/Firefox)
- [ ] Login Google berhasil, avatar tampil (atau fallback).
- [ ] 4 halaman (Aku/Kamu/Kita/Dia) load tanpa 500.
- [ ] Player musik: play/pause/stop + persist saat pindah halaman/refresh.
- [ ] Like/komentar/balas + notifikasi lonceng masuk.
- [ ] Tuner: izin mikrofon, jarum & note bergerak, in-tune jadi hijau.
- [ ] Hard refresh `Ctrl+Shift+R` setelah deploy (hindari CSS cache).

### Mobile (Android TWA / maftune)
- [ ] Bottom nav muncul, sidebar jadi list (≤1060px).
- [ ] Input chat Dia di atas bottom nav (tidak tertutup).
- [ ] Tuner jalan di APK (mikrofon di TWA).
- [ ] “Add to Home Screen” / APK menampilkan versi terbaru setelah deploy.
- [ ] Lokasi otomatis (Kita) via GPS → nama kota.

### Production verification
- [ ] `deploy.php?...&run=1` → “File di-copy: N”, artisan sukses.
- [ ] `fixdb.php` → migrate OK, cache bersih.
- [ ] Buka https://margonoandi.my.id → halaman target, hard refresh.
- [ ] Cek `storage/logs/laravel.log` bila ada error.
- [ ] Smoke test: login, posting, komentar, chat, notifikasi.
```
