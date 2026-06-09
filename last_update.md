# Catatan Update — Margonoandi Fanbase

---

## [2026-06-09] — Redesign Layout & Dark/Light Mode

**Tanggal:** Senin, 09 Juni 2026  
**Waktu:** Sesi pengembangan aktif

### Perubahan
- **`resources/views/layouts/app.blade.php`** — Redesign total layout utama:
  - Sistem CSS Variables lengkap untuk dark/light mode (`--accent`, `--bg`, `--text`, `--border`, dll.)
  - Palet warna: **biru langit** (`#38A8CC`) + **orange** (`#F07040`) + **cream/navy**
  - Toggle dark/light mode (☀️/🌙) dengan simpan preferensi di `localStorage`
  - Navbar sticky dengan glass morphism (`backdrop-filter: blur`), border muncul saat scroll
  - Brand `MARGO`*no*`ANDI` — huruf *no* memakai Playfair Display italic berwarna aksen
  - Active nav link dengan titik aksen biru di bawah
  - Footer baru: terpusat, tombol platform Spotify/YouTube/Apple Music, separator gradient sky→orange
  - Bottom nav mobile lengkap (HTML + CSS) dengan indikator aktif di atas
  - Efek grain texture halus di seluruh halaman
  - Ambient glow dari atas halaman
  - **Cursor glow effect**: kursor memancarkan cahaya radius 420px mengikuti posisi mouse (menggunakan `requestAnimationFrame`, dinonaktifkan otomatis di layar sentuh)

- **`resources/views/home.blade.php`** — Semua warna hardcoded dikonversi ke CSS variables:
  - Hero background, teks, tombol — semua pakai `var(--bg)`, `var(--text)`, dll.
  - Story cards, featured player, popup chord/story — tema-aware
  - Waveform animasi mengikuti warna aksen
  - Kompatibel penuh dengan dark/light mode switch

---

## Format Catatan

Setiap update berikutnya dicatat dengan format:

```
## [YYYY-MM-DD] — Judul Singkat Perubahan

**Tanggal:** Hari, DD Bulan YYYY
**Waktu:** (opsional)

### Perubahan
- File yang diubah dan deskripsi singkat
```
