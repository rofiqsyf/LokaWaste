# LokaWaste — Platform Penyelamat Pangan & Distribusi Donasi

LokaWaste adalah platform web berbasis geolokasi dan hierarki wilayah administratif Indonesia untuk menyelamatkan makanan berlebih (*food waste reduction*) dari restoran, katering, dan pelaku usaha makanan, lalu mendistribusikannya secara aman dan bermartabat kepada warga yang membutuhkan maupun panti asuhan melalui jaringan relawan terverifikasi.

---

## Daftar Akun Demo (Email & Password)

Untuk keperluan pengujian, demo, dan evaluasi fitur, gunakan akun-akun bawaan (*seeders*) berikut. Semua akun demo menggunakan kata sandi (*password*) yang seragam:

> **Password default untuk seluruh akun demo:** `password`

| No | Peran (Role) | Nama Akun | Email | Password | Hak Akses & Fitur Utama |
|---|---|---|---|---|---|
| 1 | **Admin** | Admin LokaWaste | `admin@lokawaste.test` | `password` | Moderasi galang makanan, persetujuan pendaftaran relawan, manajemen wilayah cakupan, pemantauan laporan & pengguna. |
| 2 | **Bisnis / Donatur** | Warung Bu Sari | `warung@lokawaste.test` | `password` | Menambahkan donasi makanan berlebih (*Listing*), mengatur porsi & kedaluwarsa, mengelola pengambilan, dan verifikasi serah terima via Smart QR Code. |
| 3 | **Relawan** | Rina Relawan | `relawan@lokawaste.test` | `password` | Membuat inisiatif galang pangan (*Campaign*), klaim donasi makanan dengan kuota lebih besar untuk target panti asuhan/komunitas di wilayah terdaftar. |
| 4 | **Warga / Penerima** | Budi Santoso | `warga@lokawaste.test` | `password` | Menjelajahi peta interaktif & katalog makanan dengan filter hierarki wilayah (Provinsi, Kota/Kabupaten, Kecamatan), klaim porsi makanan, dan navigasi rute penjemputan. |

---

## Ringkasan Skenario Pengujian

### Skenario 1: Admin (`admin@lokawaste.test`)
1. Masuk ke halaman [Masuk](http://127.0.0.1:8000/login) dengan email `admin@lokawaste.test` dan kata sandi `password`.
2. Klik tombol **Admin** di bilah navigasi atas untuk membuka [Panel Admin](http://127.0.0.1:8000/admin).
3. Kelola pengajuan relawan, moderasi galang makanan, dan verifikasi status donatur.

### Skenario 2: Donatur Makanan (`warung@lokawaste.test`)
1. Masuk menggunakan akun `warung@lokawaste.test`.
2. Klik tombol **Bagikan Makanan** untuk mempublikasikan makanan berlebih baru.
3. Buka **Dashboard** untuk melihat status klaim dari warga/relawan dan melakukan verifikasi penyerahan menggunakan kode serah terima / Smart QR Code.

### Skenario 3: Relawan (`relawan@lokawaste.test`)
1. Masuk menggunakan akun `relawan@lokawaste.test`.
2. Buka menu **Galang** untuk membuat atau memperbarui kampanye bantuan makanan panti asuhan.
3. Klaim makanan berlebih yang diprioritaskan untuk kampanye bantuan sosial.

### Skenario 4: Warga (`warga@lokawaste.test`)
1. Masuk menggunakan akun `warga@lokawaste.test`.
2. Buka menu **Peta Interaktif** atau **Katalog Makanan**.
3. Gunakan filter hierarki wilayah (Provinsi Jawa Tengah -> Kabupaten Wonosobo / Kota Semarang -> Kecamatan).
4. Pilih makanan yang diinginkan dan lakukan **Klaim Makanan**.

---

## Instalasi & Menjalankan Proyek

### Prasyarat
- PHP >= 8.2 (dengan ekstensi SQLite, cURL, OpenSSL, Mbstring)
- Composer
- Node.js & NPM

### Langkah Menjalankan
```bash
# 1. Pasang dependensi PHP & JavaScript
composer install
npm install

# 2. Siapkan file konfigurasi environment
cp .env.example .env
php artisan key:generate

# 3. Jalankan migrasi dan isi database dengan data demo
php artisan migrate:fresh --seed

# 4. Bangun aset frontend
npm run build

# 5. Jalankan server lokal
php artisan serve
```

Aplikasi dapat diakses melalui browser pada alamat: `http://127.0.0.1:8000`.

---

## Menjalankan Pengujian Otomatis

Proyek ini dilengkapi dengan pengujian fitur dan unit (*feature & unit tests*) komprehensif menggunakan PHPUnit:

```bash
php artisan test
```
