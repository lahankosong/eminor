# 🌐 Margonoandi Ecosystem — Spesifikasi Fase 1–4

> Pengembangan dari fanbase menjadi **platform ekosistem musisi Indonesia**.
> Dokumen ini detail untuk eksekusi; visi ringkasnya ada di `project.md`.
> **Status:** Fase 1 dimulai 2026-06-15.

---

## Prinsip

- **Manfaatkan yang sudah ada** — jangan bangun ulang:
  - User = akun Google OAuth yang sudah ada (musisi = user dengan profil musisi).
  - **Kontak antar-musisi pakai DM "Dia"** yang sudah jalan (`/dia/start/{userId}`), bukan tabel pesan baru.
  - Deploy: `deploy.php?key=margono2026` + `fixdb.php` (tambah tabel via SQL mentah).
  - Ikuti konvensi: `$fillable`, cast integer/bool, CSS variables fanbase, layout `layouts.fanbase`.
- **Validasi dulu** — MVP minimal, uji ke 50–100 musisi sebelum lanjut fase berikutnya.
- **Hemat biaya** — masih di cPanel + MySQL; fitur berat (storage audio, search) ditunda.

---

## FASE 1 — Direktori Musisi + Cari Personil (Bulan 2–5)

**Tujuan:** musisi bisa bikin profil, ditemukan orang lain, dan saling cari personil band.

### Urutan Build (MVP)
1. **Profil Musisi** ✅ (fondasi) — buat/edit profil sendiri.
2. **Direktori Musisi** ✅ — daftar + filter (role, genre, lokasi) + cari.
3. **Detail Musisi** ✅ — lihat profil + tombol **Hubungi** (buka DM Dia).
4. **Cari Personil (band posts)** ⏳ — post "butuh [role]", daftar, detail, lamar/kontak.
5. **Lamaran** ⏳ — kelola pelamar (terima/tolak) — atau cukup via DM dulu.

### Entitas Data
```
musician_profiles
  id, user_id (FK users, unique)
  roles         (string, koma: "vokalis,gitaris")
  skill_level   (string: pemula|menengah|mahir|profesional)
  genres        (string, koma: "indie,rock")
  location      (string)
  bio           (text)
  looking_for   (string: "cari band indie", "open job session", dll)
  spotify_url, youtube_url, instagram (nullable)
  open_to_band  (bool), open_to_collab (bool)
  is_active     (bool, default true)
  timestamps

band_posts  (Tahap 4)
  id, user_id (FK), title, description
  roles_needed (string koma), genres (string koma)
  location, status (open|closed|filled), urgent (bool)
  timestamps

band_applications  (Tahap 5, opsional — bisa diganti DM dulu)
  id, band_post_id (FK), user_id (FK), message, status (pending|accepted|rejected)
  timestamps
```

### Halaman & Route (group `auth`)
| Route | Nama | Fungsi |
|-------|------|--------|
| `GET /musisi` | `musisi.index` | Direktori musisi + filter |
| `GET /musisi/profil` | `musisi.edit` | Form profil sendiri (buat/edit) |
| `POST /musisi/profil` | `musisi.save` | Simpan profil |
| `GET /musisi/{id}` | `musisi.show` | Detail musisi + tombol Hubungi |
| `GET /band` | `band.index` | Daftar cari-personil *(Tahap 4)* |
| `GET /band/create`, `POST /band` | `band.create/store` | Buat post *(Tahap 4)* |
| `GET /band/{id}` | `band.show` | Detail + lamar/kontak *(Tahap 4)* |

> Catatan: route `/musisi/profil` HARUS didefinisikan sebelum `/musisi/{id}`.

### Alur Pengguna
1. Login → buka **Musisi** → "Lengkapi profil musisimu".
2. Isi role, genre, lokasi, bio, looking_for, link portofolio → Simpan.
3. Browse direktori → filter (mis. "gitaris" + "Jakarta") → buka profil → **Hubungi** (DM).
4. Atau buat post **Cari Personil** → musisi lain melamar/kontak.

### Kriteria Sukses Fase 1
- Profil musisi selesai dibuat < 10 menit.
- 80%+ profil punya bio + minimal 1 link portofolio.
- 40%+ lamaran/kontak band menghasilkan band terbentuk (diukur manual dulu).
- Retensi mingguan > 20%.

---

## FASE 2 — Direktori Studio + Booking (Bulan 5–8)
- `studios` (nama, lokासi, fasilitas, tarif/jam, foto), `bookings` (slot, status, pembayaran), `reviews`.
- Kalender ketersediaan + booking + ulasan. Pembayaran menyusul (Fase 3).

## FASE 3 — Marketplace Gear (Bulan 8–10)
- `listings` (alat musik: jual/sewa), `marketplace_reviews`, `escrow_transactions`.
- Butuh: storage gambar (S3-like), kemungkinan VPS, integrasi pembayaran.

## FASE 4 — Kolaborasi Audio + API (Bulan 10–12+)
- Upload/track sharing, kolaborasi, API pihak ketiga, ekspansi regional.

---

## Catatan Teknis Ecosystem
- Setiap tabel baru: tambahkan juga ke `public/fixdb.php` (pola `tableExists` + `CREATE TABLE`) untuk produksi.
- Migrasi lokal pakai `php artisan migrate --path=...` (DB lokal MySQL, ada migrasi lama pending).
- Filter direktori: client-side dulu (data kecil); pindah ke query/Elasticsearch bila >ribuan.
- Kontak musisi: arahkan ke `/dia/start/{userId}` (DM yang sudah ada).

---

## Progress Fase 1
- [x] `ecosystem.md` dibuat (2026-06-15)
- [x] Profil Musisi (migration, model, controller, form) — `musician_profiles`
- [x] Direktori Musisi (list + search + filter chip role)
- [x] Detail Musisi + tombol Hubungi (reuse DM Dia)
- [x] Nav "Musisi" di sidebar + ikon topbar (mobile)
- [x] Integrasi ke Kamu (kartu profil musisi + edit) & Kita (badge musisi + popup, badge berwarna per level)
- [x] **Sistem Follow** (`follows`) + notifikasi saat di-follow
- [x] **Cari Personil (band posts)** — `band_posts` + index/create/show; lamar via DM Dia
- [ ] (opsional) Tabel lamaran terstruktur (saat ini via DM)
- [ ] (opsional) Profil musisi: badge "open to band/collab", jumlah pengikut di detail

**Fase 1 MVP pada dasarnya SELESAI.** Berikutnya: validasi ke musisi nyata, lalu pertimbangkan Fase 2 (studio).
