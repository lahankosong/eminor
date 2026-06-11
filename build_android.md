# Build Android APK — Maftune (Margonoandi Fanbase)

Project Android: `C:\Users\Jaguare\AndroidStudioProjects\maftune`
Package name: `com.maftune.app`
Website: `https://margonoandi.my.id`

---

## Pertama Kali (Sudah Selesai)

Setup ini hanya dilakukan sekali:

- [x] Install Android Studio
- [x] Install Android SDK (API 36 "Baklava") + Build-Tools + Platform-Tools
- [x] Buat project baru: Empty Views Activity, Kotlin, API 24, Groovy DSL
- [x] Tambah dependency TWA di `app/build.gradle`:
  ```gradle
  implementation 'com.google.androidbrowserhelper:androidbrowserhelper:2.5.0'
  ```
- [x] Setup `AndroidManifest.xml` dengan `LauncherActivity` → URL `https://margonoandi.my.id`
- [x] Ambil SHA-256 fingerprint debug keystore:
  ```
  "C:\Program Files\Android\Android Studio\jbr\bin\keytool.exe" -list -v -keystore "C:\Users\Jaguare\.android\debug.keystore" -alias androiddebugkey -storepass android -keypass android
  ```
  Fingerprint saat ini:
  ```
  55:FF:6B:F6:80:6B:AC:4A:5F:B9:60:AA:4B:1A:A4:11:93:D5:5C:74:48:ED:52:4D:A1:E7:2C:BD:BF:14:36:5F
  ```
- [x] Update `public/.well-known/assetlinks.json` dengan fingerprint di atas
- [x] Deploy ke server + fixdb

---

## Build APK Debug (untuk Testing)

Digunakan untuk install ke HP sendiri / tester.

1. Buka Android Studio → project `maftune`
2. **Build → Generate App Bundles or APKs → Generate APKs**
3. Tunggu hingga muncul notifikasi **"APK(s) generated successfully"**
4. Klik **locate** untuk buka folder APK:
   ```
   C:\Users\Jaguare\AndroidStudioProjects\maftune\app\build\outputs\apk\debug\app-debug.apk
   ```
5. Kirim `app-debug.apk` ke HP via USB / WhatsApp, lalu install

---

## Ganti Icon App

1. Di Android Studio, klik kanan folder `app/res`
2. **New → Image Asset**
3. Asset Type: **Image**
4. Path: `C:\xampp\htdocs\margonoandi-fanbase\public\images\Margonoandi.jpeg`
5. Sesuaikan crop agar wajah pas di tengah
6. **Next → Finish**
7. Rebuild APK (langkah Build APK di atas)

---

## Update Konten / Fitur Web → Android Otomatis Update

Tidak perlu rebuild APK. Cukup:

1. Edit kode di VSCode
2. `git push origin main`
3. Deploy: `https://margonoandi.my.id/deploy.php?key=margono2026`
4. Fix cache: `https://margonoandi.my.id/fixdb.php`

App Android langsung menampilkan versi terbaru.

---

## Build APK Release (untuk Play Store)

> Lakukan ini hanya jika ingin upload ke Google Play Store.

1. **Build → Generate Signed Bundle or APK**
2. Pilih **APK** → Next
3. Buat keystore baru (simpan file `.jks` dan password dengan aman, jangan sampai hilang!)
   - Key store path: buat di lokasi aman, misal `C:\Users\Jaguare\maftune-release.jks`
   - Password, alias, validity: isi sesuai kebutuhan
4. Build variant: **release**
5. Klik **Finish**
6. APK release ada di:
   ```
   C:\Users\Jaguare\AndroidStudioProjects\maftune\app\release\app-release.apk
   ```
7. **Penting**: Ambil SHA-256 fingerprint dari keystore release (bukan debug) dan update `assetlinks.json` sebelum upload ke Play Store

---

## Kapan Perlu Rebuild APK

| Perubahan | Perlu Rebuild? |
|-----------|---------------|
| Konten web (teks, CSS, fitur) | ❌ Tidak |
| Bug fix Laravel | ❌ Tidak |
| Tambah halaman baru | ❌ Tidak |
| Ganti icon app | ✅ Ya |
| Ganti nama / package app | ✅ Ya |
| Tambah permission Android | ✅ Ya |
| Update ke Play Store | ✅ Ya (signed APK) |
