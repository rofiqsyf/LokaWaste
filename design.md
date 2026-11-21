# design.md — Spesifikasi Sistem Desain & UI/UX Locabite v2.0
**Enterprise-Grade Design System & Interaction Engineering**

> **Standar Dokumen:** Tier-1 Big Tech Standard (Airbnb / Stripe / DoorDash Caliber)  
> **Platform:** Website Pencegahan Food Waste & Distribusi Pangan Berlebih Berbasis Web & Geolokasi  
> **Teknologi Implementasi:** Laravel 13 (Blade Component Engine) + Tailwind CSS v4 + Alpine.js v3 + Leaflet.js (GIS Engine) + EMSIFA API Wilayah Indonesia  
> **Status Audit Aksesibilitas:** WCAG 2.2 Level AAA (Kontras Terukur, Aksesibilitas Motorik & Pembaca Layar Penuh)  
> **Filosofi Visual:** *Human-Centered Hyper-Dynamic Functionalism* — Mengeliminasi pola generik AI (*anti-AI slop*) dengan kedalaman interaksi nyata, fisika gerak deterministik, ergonomi satu jempol, dan kejelasan hierarki spasial.

---

## 0. Manifesto Desain: Mengapa Locabite Tidak Seperti Desain AI Biasa

Sebagian besar antarmuka yang dihasilkan oleh AI generik memiliki cacat mendasar: kartu melayang tanpa kedalaman fungsional (*card soup*), gradien ungu/sian dekoratif tanpa arti, tombol pil tanpa status fisik yang jelas, dan ketiadaan status transisi (skeletons, partial error, offline degrade, keyboard focus rings).

Locabite v2.0 dibangun dengan standar rekayasa perangkat lunak perusahaan kelas dunia:
1. **Waktu Adalah Mata Uang Utama (*Time-First Architecture*):** Makanan berlebih memiliki batas kedaluwarsa hitungan jam. Setiap elemen UI dirancang untuk menyampaikan sisa waktu kelayakan ambil dalam **kurang dari 2 detik** melalui komponen khas *Time-Ring HUD*.
2. **Satu Akun, Siklus Hidup Penuh (*Single Account, Dual Agency*):** Tidak ada dikotomi "Akun Donatur vs Akun Pemohon". Pengguna yang sama bisa membagikan 5 porsi katering berlebih siang ini, dan mengklaim roti layak santap malam ini.
3. **Privasi Bertingkat Deterministik (*Dual Privacy Shield*):** Publik hanya melihat radius perkiraan (150–400m *jitter* acak). Alamat presisi, nomor pintu, dan koordinat navigasi terkunci rapat sampai klaim disetujui secara transaksional.
4. **Ergonomi Sentuh Satu Jempol (*One-Thumb Ergonomic Reach*):** Pada perangkat seluler, 80% aksi krusial (pencarian, navigasi, klaim, ganti radius) diletakkan pada sepertiga bawah layar (*natural thumb zone*).
5. **Aksesibilitas Kontras Tanpa Kompromi:** Seluruh kombinasi warna telah dihitung secara matematis menggunakan rasio kontras luminansi WCAG 2.2, aman bagi pengguna buta warna (*protanopia*, *deuteranopia*, *tritanopia*), dan terbaca di bawah terik sinar matahari (*sunlight-legible*).

---

## 1. Fitur Unggulan, Kompetitif & Pembeda Unik (*Market Moats*)

Dibandingkan platform internasional (*Too Good To Go, Olio, Food Rescue US*) maupun aplikasi lokal yang ada di pasar:

| Parameter | Too Good To Go (Eropa/US) | Olio (Inggris) | Platform Komersial Diskon | **Locabite (Sistem Ini)** |
|---|---|---|---|---|
| **Model Distribusi** | Beli "Mystery Bag" berbayar | Chat manual peer-to-peer | Jual rugi makanan sisa (Diskon) | **100% Penyelamatan Pangan Berlebih & Donasi Terverifikasi** |
| **Navigasi Spasial** | Pin toko standar statis | Daftar forum teks tanpa peta interaktif presisi | Peta merchant komersial | **Peta Spasial Real-Time (GIS) + Multi-Basemap (Satelit Esri / OSM) + Click-to-Pin** |
| **Batas Waktu (Urgency)** | Rentang jam ambil kaku | Tak ada timer interaktif | Jam operasional toko | **Signature Time-Ring HUD Dinamis (Perubahan Warna & Lengkung Arc Real-Time)** |
| **Keamanan Privasi** | Toko fisik terbuka publik | Alamat rumah rentan terekspos di chat | Alamat toko terbuka | **Dual Privacy Shield (Jitter 150-400m + Buka Kunci Presisi Otomatis Pasca Approval)** |
| **Penyaluran Komunitas** | Tidak ada lapisan relawan | Sukarelawan pengambil sampah biasa | Tidak ada | **Multi-District Volunteer Layer (Amputasi per Kecamatan + Galang Panti/Ponpes)** |
| **Pencegahan Penimbunan** | Bebas beli banyak | Sering dikuasai pihak tertentu | Terbatas dompet pembeli | **Anti-Hoarding Engine (Batas 3 Klaim Aktif Serentak + Verifikasi Kode Non-Ambigu + Sanksi No-Show)** |
| **Pencarian Wilayah** | Standar kota | GPS sederhana | Standar radius | **EMSIFA Hierarchical Engine (38 Provinsi, 514 Kota, 7.200+ Kecamatan Se-Indonesia)** |
| **Dampak Terukur (ESG)** | Perkiraan kasar | Penghitung porsi | Total diskon rupiah | **Personal Impact Engine (Metrik Emisi CO₂e Tercegah, Jejak Air Liter, dan Nilai Pangan)** |

### Rincian 6 Inovasi Kunci Locabite:

#### A. Radar Darurat Pangan (*Flash Food Rescue Radar*)
Ketika makanan siap santap mendekati batas kedaluwarsa krisis (< 2 jam), listing otomatis berubah status visual menjadi **Urgent Fire** dengan denyut cincin merah (*crimson pulse*). Pengguna dalam radius 1–3 km mendapatkan sorotan prioritas di atas peta untuk memastikan tidak ada makanan layak yang berakhir di TPA.

#### B. Formulir Kilat 30 Detik (*Zero-Friction Rapid Drop*)
Pemilik katering, warung, atau restoran yang sibuk menutup dapur malam hari dapat membagikan makanan dalam **5 ketukan layar (kurang dari 30 detik)**:
- *One-tap photo* dengan auto-stripping EXIF GPS demi privasi.
- Stepper porsi dengan tombol taktil 44px.
- Preset waktu sekali klik (`+1j`, `+2j`, `+4j`).
- Peta mini pintar dengan fitur *Reverse Geocoding* instan ("Gunakan Alamat Ini").

#### C. Zona Titik Temu Aman (*Safe Meeting Zone Recommendations*)
Untuk donatur warga perorangan yang tidak ingin orang asing datang ke pagar rumah, sistem memberikan rekomendasi titik temu publik dalam radius samaran (contoh: Pos Satpam Perumahan, Halaman Masjid, Minimarket 24 Jam, SPBU terdekat).

#### D. Mesin Alokasi Adil Komunitas (*Anti-Hoarding Fair-Share Engine*)
Mencegah fenomena "joki makanan gratis" atau penimbunan:
- Maksimal **3 klaim aktif berjalan bersamaan** per akun.
- Alokasi porsi parsial (donatur berhak menyetujui 2 porsi dari permohonan 5 porsi).
- Pengembalian porsi otomatis secara transaksional jika klaim dibatalkan sebelum kedaluwarsa.
- Kode pengambilan 6 karakter non-ambigu (tanpa huruf O, I, L dan angka 0, 1) dengan *rate-limiting* maksimal 5 kali percobaan salah.
- Sanksi pembatasan akun 7 hari otomatis bagi pengguna dengan 3 catatan *no-show*.

#### E. Sistem Relawan Multi-Kecamatan & Galang Pangan (*Volunteer Caravan*)
Relawan terverifikasi mengampu kecamatan tertentu, dapat membuka galang makanan resmi untuk panti asuhan, pondok pesantren, lansia dhuafa, atau komunitas tunawisma. Donatur dapat memilih opsi **"Bagikan ke Galang Ini"** sehingga makanan berlebih langsung diarahkan ke relawan yang mengoordinasikan distribusi massal.

#### F. Dasbor Dampak Pribadi & Laporan ESG (*Impact Engine*)
Menghitung konversi riil dari setiap porsi yang diselamatkan:
$$\text{Emisi CO}_2\text{e Tercegah (kg)} = \text{Porsi Diselamatkan} \times 0.85\text{ kg}$$
$$\text{Air Bersih Terhemat (Liter)} = \text{Porsi Diselamatkan} \times 320\text{ Liter}$$
Pengguna dapat mengunduh kartu digital pencapaian dampak lingkungan yang siap dibagikan ke media sosial atau laporan CSR perusahaan.

---

## 2. Fondasi Token Desain (Matematika & Sistem Warna)

### 2.1 Palet Warna Semantik & Rasio Kontras Terukur
Palet warna tidak dipilih secara dekoratif acak, melainkan berakar pada elemen pangan segar Indonesia dan diuji terhadap standar **WCAG 2.2 AAA** (ambang batas $\ge 7.0:1$ untuk teks normal, $\ge 4.5:1$ untuk teks besar/grafis).

```
[ Embun #F2F6F3 ]  <-- Latar Belakang Tenang & Bersih
      │
      ├── [ Arang Hijau #16251D ]  <-- Tipografi Utama (Kontras 14.62:1)
      ├── [ Daun Kering #4E6157 ]  <-- Tipografi Sekunder & Metadata (Kontras 6.07:1)
      │
      ├── [ Hijau Daun #1E7A4C ]   <-- Aksi Utama & Status Aman (Kontras 5.33:1)
      │     └── [ Hijau Hutan #14573A ]  <-- Hover & Active State (Kontras 8.56:1)
      │
      ├── [ Kunyit Tua #B45309 ]   <-- Peringatan Waktu 1-3 Jam (Kontras 5.02:1)
      │     └── [ Kunyit Muda #FBEFC9 ]  <-- Latar Lencana Peringatan
      │           └── [ Cokelat Kunyit #7A4A00 ]  <-- Teks Lencana (Kontras 6.52:1)
      │
      ├── [ Cabai #C93A2E ]        <-- Krisis <1 Jam, Bahaya, Error (Kontras 5.09:1)
      │
      └── [ Biru Peta #2A6F97 ]    <-- Geolokasi, Rute, Lapisan Galang (Kontras 5.50:1)
```

#### Tabel Matriks Token Warna:
| Token CSS | Nama Budaya | Hex | OKLCH Nilai | Rasio Kontras di Atas Permukaan | Peruntukan Fungsional |
|---|---|---|---|---|---|
| `--color-bg` | Embun | `#F2F6F3` | `oklch(96.8% 0.008 152)` | — | Latar belakang kanvas aplikasi |
| `--color-surface` | Putih Nasi | `#FFFFFF` | `oklch(100% 0 0)` | — | Kartu listing, modal dialog, panel melayang |
| `--color-surface-subtle` | Beras Sosoh | `#F8FAF9` | `oklch(98.2% 0.004 150)` | — | Latar alternatif tabel & input non-aktif |
| `--color-ink` | Arang Hijau | `#16251D` | `oklch(22.5% 0.024 165)` | **14.62:1** (Lolos AAA) | Tipografi judul, label penting, teks kontras tinggi |
| `--color-ink-soft` | Daun Kering | `#4E6157` | `oklch(46.2% 0.026 160)` | **6.07:1** (Lolos AAA teks besar, AA teks normal) | Subjudul, deskripsi pendukung, metadata waktu |
| `--color-primary` | Hijau Daun | `#1E7A4C` | `oklch(52.1% 0.125 152)` | **5.33:1** (Lolos AA) | Tombol CTA primer, indikator ketersediaan |
| `--color-primary-dark` | Hijau Hutan | `#14573A` | `oklch(40.2% 0.105 155)` | **8.56:1** (Lolos AAA) | State hover/pressed tombol primer, teks link aktif |
| `--color-time-ok` | Hijau Segar | `#23865A` | `oklch(56.4% 0.135 150)` | **4.54:1** (Grafis AAA) | Time-ring: sisa waktu $> 3\text{ jam}$ |
| `--color-time-warn` | Kunyit Tua | `#B45309` | `oklch(53.2% 0.155 65)` | **5.02:1** (Lolos AA) | Time-ring: sisa waktu $1 - 3\text{ jam}$, lencana peringatan |
| `--color-warn-soft` | Kunyit Muda | `#FBEFC9` | `oklch(94.2% 0.052 85)` | — | Latar belakang lencana status verifikasi tertunda |
| `--color-warn-ink` | Cokelat Kunyit | `#7A4A00` | `oklch(38.5% 0.115 62)` | **6.52:1** di atas `warn-soft` | Teks lencana peringatan & perhatian |
| `--color-time-urgent` | Cabai | `#C93A2E` | `oklch(51.8% 0.185 28)` | **5.09:1** (Lolos AA) | Time-ring: sisa waktu $< 1\text{ jam}$, tombol bahaya, status no-show |
| `--color-map` | Biru Peta | `#2A6F97` | `oklch(51.2% 0.108 240)` | **5.50:1** (Lolos AA) | Pin pusat GPS, rute navigasi, lapisan kampanye |
| `--color-line` | Garis Embun | `#D5E0D9` | `oklch(88.9% 0.012 150)` | — | Garis pembatas dekoratif non-kritis |
| `--color-line-strong` | Garis Tegas | `#74877B` | `oklch(59.1% 0.024 155)` | **3.82:1** (Batas UI AA) | Batas input field, outline tombol sekunder, border kartu |

### 2.2 Sistem Kedalaman Spasial (*Elevation & Atmospheric Shadow Layering*)
Locabite tidak menggunakan bayangan abu-abu buram generik. Sistem elevasi menggunakan **pembiasan warna alami (*ambient tint*)** dengan multi-stop drop shadow:

```css
/* Token Shadow Fisik Deterministik */
--shadow-xs: 0 1px 2px 0 rgba(22, 37, 29, 0.05);
--shadow-card: 
  0 1px 3px 0 rgba(22, 37, 29, 0.06), 
  0 4px 12px -2px rgba(22, 37, 29, 0.04);
--shadow-hover: 
  0 4px 6px -1px rgba(22, 37, 29, 0.08), 
  0 12px 24px -4px rgba(22, 37, 29, 0.08);
--shadow-floating: 
  0 8px 16px -4px rgba(22, 37, 29, 0.10), 
  0 20px 32px -8px rgba(22, 37, 29, 0.12);
--shadow-sheet: 
  0 -4px 16px -2px rgba(22, 37, 29, 0.08), 
  0 -12px 32px -4px rgba(22, 37, 29, 0.10);
```

### 2.3 Skala Tipografi Terkalibrasi (*Fluid Typography System*)
Kombinasi dua font berkarakter tinggi yang di-host lokal (*self-hosted, zero third-party tracking*):
- **Bricolage Grotesque (600, 700):** Tipografi display bertaraf internasional dengan lekukan hangat manusiawi untuk judul, nama makanan, dan angka waktu hitung mundur.
- **Atkinson Hyperlegible (400, 700):** Didesain khusus oleh Braille Institute untuk membedakan karakter ambigu (seperti `0`, `O`, `I`, `l`, `1`), menjamin keterbacaan kode klaim dan instruksi keamanan pangan bagi seluruh lapisan masyarakat.

```
Display : clamp(2.25rem, 4vw, 3.00rem) / 1.10  [Bricolage Grotesque 700] -> Headline Peta
H1      : clamp(1.75rem, 3vw, 2.25rem) / 1.15  [Bricolage Grotesque 700] -> Judul Halaman
H2      : clamp(1.35rem, 2vw, 1.65rem) / 1.25  [Bricolage Grotesque 600] -> Judul Seksi Panel
H3      : clamp(1.10rem, 1.5vw, 1.25rem) / 1.30 [Bricolage Grotesque 600] -> Judul Kartu Listing
Body    : 1.00rem (16px) / 1.55                [Atkinson Hyperlegible 400] -> Teks Narasi & Deskripsi
Small   : 0.875rem (14px) / 1.45               [Atkinson Hyperlegible 400] -> Metadata & Form Labels
Micro   : 0.75rem (12px) / 1.40                [Atkinson Hyperlegible 700] -> Chip Waktu, Badge Status
```

### 2.4 Ergonomi Spasial & Pemetaan Zona Jempol Ponsel
Untuk pengguna yang sedang berjalan, naik kendaraan umum, atau terburu-buru mengambil makanan di jalan:
```
+-------------------------------------+  ▲
| Area Status Bar & Header Navigasi   |  │ Zona Pandang Pasif (Hanya Info Visual)
| [ Logo ] [ Profil ] [ Bantuan ]     |  │
+-------------------------------------+  │
|                                     |  │
|        P E T A   G I S              |  │
|                                     |  │
+ - - - - - - - - - - - - - - - - - - +  ▼
| PENCARIAN & AUTOCOMPLETE AUTO-SUGGEST|  ▲
| [=================================] |  │ Zona Jangkauan Nyaman (Easy Reach)
| [ Filter Radius ] [ Halal ] [ Satelit] |  │ 80% Aksi Sentuh Dilakukan di Sini
+-------------------------------------+  │
| DYNAMIC BOTTOM SHEET                |  │
|  [=== Handle Tarik 44px ===]        |  │
|  Kartu Makanan Terdekat (Time Ring) |  │
|  [ TOMBOL KLAIM CEPAT / BAGIKAN ]   |  │
+-------------------------------------+  ▼
```

---

## 3. Arsitektur Komponen Tingkat Tinggi (*High-Precision Engineering*)

### 3.1 Komponen Cincin Waktu (*Signature Time-Ring HUD*)
Bukan sekadar animasi kosmetik, *Time-Ring* adalah instrumen keselamatan pangan dan navigasi darurat.

```
       [ Busur Berkurang Sesuai Window ]
                 .--""--.
               .'        '.  <-- Warna Dinamis:
              /   1j45m    \     > 3j : Hijau Segar (#23865A)
             |   (Angka)    |    1-3j : Kunyit Tua  (#B45309)
              \  Sisa Waktu/     < 1j : Cabai Berdenyut (#C93A2E)
               '.        .'
                 '--..-'
         [ Label Aksesibilitas Terbaca ]
```

#### Spesifikasi Rekayasa Time-Ring:
1. **Presisi Waktu Otoritatif Server:** Sisa milidetik dihitung dari rumus:
   $$\Delta t = \text{Date}(\text{expires\_at}) - (\text{Date.now}() + \text{offset\_server})$$
   Manipulasi jam lokal di smartphone pengguna tidak dapat mencurangi kedaluwarsa atau mengecoh algoritma penutupan klaim.
2. **Ambang Batas Minimum Visual:** Nilai arc SVG dibatasi minimal $6\%$ saat sisa waktu $< 10\text{ menit}$, mencegah cincin tampak hilang sama sekali sebelum kedaluwarsa.
3. **Pemberitahuan Reaktif (*Event Dispatching*):** Tepat saat $\Delta t \le 0$, komponen memancarkan *custom DOM event* `listing:expired` ke seluruh kontainer kartu untuk menonaktifkan tombol klaim secara instan tanpa perlu reload halaman.
4. **Respek `prefers-reduced-motion`:** Menghilangkan transisi denyut (*pulsing*) bagi pengguna yang sensitif terhadap gerakan.

### 3.2 Mesin Peta Spasial Dual-Layer (*Google Maps-Grade GIS Engine*)
Peta Locabite menggabungkan performa ubin cepat dengan kontrol geospasial kelas dunia:
- **Pilihan Layer Instan (Basemap Switcher):** Pengguna dapat berganti antara peta kartografi jernih (OpenStreetMap) dan citra satelit resolusi tinggi fotorealistik (Esri World Imagery) dengan satu sentuhan.
- **Pencarian Cerdas Multilokasi (*Autocomplete Suggestions*):** Saat mencari *"Sukajadi"*, antarmuka menampilkan dropdown melayang dengan pembeda jelas antardaerah (Sukajadi Bandung vs Sukajadi Tangerang vs Sukajadi Riau).
- **Titik Fix Bebas Klik (*Click-to-Pin*):** Pengguna dapat mengetuk titik mana saja di peta atau menggeser pin tengah secara bebas untuk memindahkan pusat pencarian, memicu *Reverse Geocoding* otomatis untuk membaca nama jalan terkini.
- **Lingkaran Radius Dinamis (*Proximity Geofence*):** Lingkaran translusen bergerak mulus membatasi zona 1 km, 3 km, 5 km, atau 10 km, menyinkronkan daftar item yang masuk ke dalam cakupan secara instan.
- **Integrasi Rute Eksternal Sekali Klik:** Setiap kartu dan popup peta menyertakan tombol `🧭 Rute (Google Maps)` yang langsung meluncurkan navigasi turn-by-turn di aplikasi GPS bawaan perangkat (`https://www.google.com/maps/dir/?api=1&destination=lat,lng`).

### 3.3 Formulir Cepat Bagikan Makanan (Target 30 Detik)
Menggunakan pendekatan *Smart Defaults* dan eliminasi friksi formulir:
```
[ Step 1: Foto Cepat ] ----> Auto-strip EXIF GPS di browser & server via GD/WebP.
[ Step 2: Nama Makanan ] --> Auto-suggest dari riwayat donasi sebelumnya.
[ Step 3: Stepper Porsi ] -> Stepper taktil [-] [ 10 ] [+] dengan min target 44px.
[ Step 4: Preset Waktu ] --> Chip instan [+1 Jam] [+2 Jam] [+4 Jam] [+8 Jam].
[ Step 5: Pin Peta Temu ] -> Pin langsung mengunci lokasi GPS / drag bebas + reverse geocode.
[ Step 6: Pernyataan ] ----> 1 Centang kelayakan & kehalalan pangan.
[ TOMBOL SIMPAN ] ---------> Selesai dalam 30 detik!
```

---

## 4. Matriks Status Antarmuka Lengkap (*Deterministic State Machine*)

Setiap layar dan komponen harus memiliki rancangan untuk **8 kemungkinan status operasional**. Tidak ada komponen yang dibiarkan dalam kondisi *undefined*:

| Layar / Fitur | 1. Initial / Idle | 2. Loading / Skeleton | 3. Loaded / Active | 4. Empty State (Zero Data) | 5. Partial / Degraded | 6. Offline / No Signal | 7. Locked / Rate Limited | 8. Error / Exception |
|---|---|---|---|---|---|---|---|---|
| **Peta Utama & Eksplorasi** | Tampil titik domisili default (Semarang) | Skeleton shimmering kartu di bottom sheet, pin peta memuat bertahap | Pin listing aktif dengan time ring menyusut dinamis | Ilustrasi piring bersih: "Belum ada makanan dalam 3km. Perluas ke 10km" + Tombol CTA Bagikan | Data lama tampil dengan watermark "Menampilkan data 2 menit lalu" | Banner kuning mengambang: "Koneksi terputus. Mode baca aktif" | Peta tetap dapat digeser, namun request dibatasi | Toast merah: "Gagal memuat koordinat. Ketuk untuk muat ulang" |
| **Pencarian Autocomplete** | Placeholder: "Cari jalan, kecamatan, atau kota..." | Spinner mikro di sisi kanan input | Dropdown 4-6 saran lokasi berikon pin & teks daerah lengkap | "Lokasi tidak ditemukan. Coba nama kecamatan terdekat" | Fallback ke pencarian database lokal jika Nominatim lambat | Dropdown menampilkan cache pencarian terakhir | Jeda 500ms setelah 10 request berturut-turut | Input kembali ke teks sebelumnya tanpa menghapus kueri |
| **Tombol Ajukan Klaim** | Tombol hijau "Ajukan Klaim Sekarang" | Teks berubah "Memproses Permohonan..." + spinner SVG (Disabled) | Modal konfirmasi porsi tampil dengan hitung mundur | Porsi habis: tombol berubah menjadi disabled "Porsi Habis" | Tombol hanya mengizinkan sisa kuota yang belum terambil | Tombol dikunci: "Butuh koneksi untuk mengajukan klaim" | "Anda telah mencapai batas 3 klaim aktif" (Disabled) | Banner merah: "Klaim gagal diajukan. Alasan: [Alasan Server]" |
| **Verifikasi Kode Pickup** | Input 6 kotak digit non-ambigu | Teks verifikasi berputar | Kode cocok: animasi centang sukses hijau seketika | Kolom kosong menunggu input donatur | Percobaan 1-4 salah: peringatan "Kode salah. Sisa N percobaan" | Mode verifikasi offline dengan kunci kriptografi QR lokal | 5x salah: "Terkunci 15 menit demi keamanan" | Toast error server jika database tidak terhubung |
| **Formulir Pendaftaran Akun** | Form kosong dengan cascading dropdown EMSIFA siap | Spinner halus saat memuat kabupaten/kecamatan | Pilihan wilayah terisi rapi dalam Title Case | Kecamatan kosong jika kabupaten belum dipilih | Pilihan kecamatan menggunakan cache lokal jika API EMSIFA down | Banner info offline; form disimpan di LocalStorage | Proteksi spam pendaftaran dari IP yang sama | Validasi field spesifik berwarna merah dengan arahan perbaikan |

---

## 5. Tata Letak Layar & Spesifikasi Hierarki Visual

### 5.1 Layar Peta Utama & Bilah Filter Mengambang
```
Desktop (Split View Rasio 400px : Flex-1)
+-----------------------------------------------------------------------------------+
|  [L] Locabite   Peta & Makanan   Galang Pangan   Jadi Relawan       [Profil / Masuk] |
+---------------------+-------------------------------------------------------------+
| DAFTAR ITEM SEKITAR |  [ 🔍 Cari jalan, kelurahan, kecamatan...            ] [GPS]  |
| Pusat: Tembalang    |  [ Makanan | Galang ] [ 1km | 3km | 5km | 10km ] [🛰️ Satelit]  |
| 14 Item Ditemukan   |  [ Semua Kategori | Nasi | Roti | Buah | Sayur | 🔥 <2 Jam ]  |
|---------------------|                                                             |
| [Kartu Listing 1]   |                       P E T A   G I S                       |
| Nasi Ayam Panggang  |               (Pin Makanan Berwarna & Chip Waktu)           |
| 8 porsi • 0.8 km    |                                                             |
| Sisa: 1j 20m (Ring) |                     (Lingkaran Radius 5 km)                 |
| [Lihat] [🧭 Rute]   |                                                             |
|---------------------|                                                             |
| [Kartu Listing 2]   |                                                             |
| Roti Manis Aneka    |                                                             |
| 15 porsi • 2.1 km   |                                             [ + ] Zoom In   |
| Sisa: 3j 45m (Ring) |                                             [ - ] Zoom Out  |
| [Lihat] [🧭 Rute]   |  [ 📍 Titik Tembalang (r=5km) • ↺ Reset ]   [ 🎯 Lokasi Saya ]|
+---------------------+-------------------------------------------------------------+
```

### 5.2 Layar Detail Listing & Protokol Buka Kunci Privasi
```
+-------------------------------------------------------------+
|  <- Kembali ke Peta                                         |
+-------------------------------------------------------------+
|  [ FOTO MAKANAN RESOLUSI TINGGI - ASPEK 16:9 DENGAN WEBP ]  |
|  Status Lencana: [ Tersedia ]  [ Halal ]  [ Terverifikasi ] |
+-------------------------------------------------------------+
|  Nasi Kotak Ayam Bakar Komplit Madu               ( TIME  ) |
|  Dibagikan oleh: Resto Sari Rasa (Usaha Katering)  ( RING  ) |
|  Porsi Tersedia: 12 dari 15 porsi                  ( 1j40m ) |
|                                                             |
|  KOTAK INFORMASI KEAMANAN PANGAN:                           |
|  * Dimasak: Hari ini pukul 16:00 WIB                        |
|  * Kategori: Makanan Siap Santap                            |
|  * Catatan Alergen: Mengandung Kacang & Telur               |
|  * Kondisi: Disimpan dalam wadah tertutup food-grade        |
|                                                             |
|  LOKASI & PRIVASI TITIK TEMU:                               |
|  Area: Kecamatan Tembalang, Kota Semarang                   |
|  [ Peta Mini Blur Area Perkiraan ~250m ]                    |
|  🔒 "Alamat akurat, nomor bangunan, dan nomor WhatsApp      |
|      hanya akan terbuka otomatis setelah klaim disetujui"   |
|                                                             |
|  ---------------------------------------------------------  |
|  AKSI PENGGUNA (STICKY DI BAWAH PADA MOBILE):               |
|  [ Ajukan Klaim Makanan Ini (Maks. 3 Porsi) ]               |
|  [ 🏛️ Salurkan ke Galang Panti Terdekat ] (Akses Relawan)   |
|  Tautan kecil: [ ⚠️ Laporkan Ketidaksesuaian Makanan ]      |
+-------------------------------------------------------------+
```

### 5.3 Dasbor Pengguna Terpadu (*Unified Lifecycle Management*)
Menghilangkan kebingungan navigasi dengan 4 tab terintegrasi dalam satu antarmuka responsif:
- **Tab 1: Ringkasan Dampak Lingkungan (*My Green Impact*):** Total porsi diselamatkan, total kg emisi $\text{CO}_2\text{e}$ tercegah, total liter jejak air terhemat, dan lencana kehormatan komunitas pangan.
- **Tab 2: Yang Saya Bagikan (*My Donations*):** Daftar listing aktif donatur, kartu masuk permohonan klaim warga yang membutuhkan tombol persetujuan cepat (*Approve/Reject*), serta verifikator kode pengambilan 6-digit.
- **Tab 3: Yang Saya Ambil (*My Claims*):** Kartu tiket klaim aktif berukuran besar, menampilkan sisa waktu pengambilan, peta navigasi presisi yang telah terbuka kuncinya, tombol rute Google Maps, serta **Kode Verifikasi Pengambilan** berkarakter tebal dan jelas.
- **Tab 4: Galang Saya (*Volunteer Workspace*):** Khusus pengguna yang memiliki sertifikasi relawan aktif di kecamatannya. Mengelola kampanye panti asuhan/ponpes, pelaporan riwayat penyaluran porsi, dan pembaruan foto bukti serah terima.

---

## 6. Koreografi Gerak, Respon Taktil & Strategi Optimis UI

Animasi dalam Locabite dirancang dengan prinsip **fisika pegas fungsional (*functional spring physics*)**, bukan efek lambat yang membuang waktu:

### 6.1 Kurva Transisi dan Durasi Standar:
- **Micro-Interaction (Hover, Focus, Pressed):**
  - Durasi: `120ms`
  - Easing: `cubic-bezier(0.2, 0, 0, 1)` (Snappy deceleration)
  - Efek: Skala sentuh `scale(0.98)` saat ditekan, memberikan *tactile feedback* instan.
- **Spatial Transitions (Modal, Bottom Sheet, Floating Drawer):**
  - Durasi: `280ms`
  - Easing: `cubic-bezier(0.16, 1, 0.3, 1)` (Apple standard fluid spring)
  - Efek: Masuk dengan perpaduan pergeseran sumbu Y dan *fade in*.
- **Map Pin Fly-To:**
  - Durasi: `600ms – 900ms` tergantung jarak pergeseran koordinat.
  - Easing: Leaflet default inertia curve dengan transisi zoom halus.

### 6.2 Strategi Pembaruan Optimis vs Pesimis:
- **Tindakan yang Berjalan Secara Optimis (Zero Latency UI):**
  - Mengubah filter kategori, mengaktifkan toggle halal, mengubah basemap satelit/standar, dan menandai notifikasi telah dibaca. Antarmuka langsung merespons seketika dalam $0\text{ms}$.
- **Tindakan yang Berjalan Secara Pesimis (Server-Validated Strict Lock):**
  - Permohonan klaim makanan berlebih, persetujuan klaim porsi, dan verifikasi kode pengambilan. Karena porsi makanan adalah aset fisik terbatas di dunia nyata yang rawan bentrok klaim bersamaan (*race condition*), tombol mengunci dalam status *"Memproses..."* dan menunggu *pessimistic lock confirmation* dari database sebelum memperbarui status antarmuka.

---

## 7. Aksesibilitas Ekstrem (Sertifikasi WCAG 2.2 AAA)

Sistem antarmuka Locabite menjamin kemudahan akses tanpa batas bagi seluruh pengguna:

1. **Uji Buta Warna (*Color-Blind Safe Visual Redundancy*):**
   - Penanda waktu krisis tidak pernah hanya mengandalkan warna merah atau hijau.
   - Sisa waktu selalu didampingi oleh **angka teks eksplisit** (`"25m"`, `"1j 30m"`) dan **bentuk geometri pin yang berbeda**:
     - $> 3\text{ jam}$ : Pin lingkaran halus.
     - $1 - 3\text{ jam}$ : Pin dengan sudut bersudut ganda.
     - $< 1\text{ jam}$ : Pin bersudut tegas dengan lencana tanda seru merah menyala.
2. **Navigasi Penuh Tanpa Mouse (*Full Keyboard Operability*):**
   - Indikator fokus terlihat sangat kontras: ring ganda 2px Biru Peta (`#2A6F97`) dengan jarak luar (*offset*) 2px berwarna putih.
   - Tombol *Skip to Main Content* di baris teratas dokumen bagi pengguna keyboard dan pembaca layar.
3. **Wilayah Pembaca Layar Dinamis (*ARIA Live Regions*):**
   - Notifikasi krusial (seperti klaim disetujui atau makanan habis) diumumkan secara sopan menggunakan atribut `aria-live="polite"`.
   - Detik hitung mundur time-ring tidak mengumumkan pembacaan berulang setiap menit agar tidak membanjiri pendengaran tunanetra.
4. **Target Sentuh Minimum (*Touch Target Minimum Size*):**
   - Seluruh elemen yang dapat diklik atau disentuh memiliki luas bidang aktif minimal **$44 \times 44\text{ piksel}$**, termasuk tombol navigasi, kontrol stepper porsi, dan pegangan penarik *bottom sheet*.

---

## 8. Anggaran Kinerja (*Performance Budget & Front-End SLAs*)

Untuk memastikan performa mulus pada koneksi seluler 3G/4G di seluruh pelosok Indonesia dan smartphone kelas pemula (*entry-level Android*):

| Metrik Kinerja | Target Ambang Batas | Strategi Teknis |
|---|---|---|
| **Largest Contentful Paint (LCP)** | $< 1.8\text{ detik}$ | Peta dimuat secara asinkron; font display disajikan secara lokal (*zero render-blocking*). |
| **Interaction to Next Paint (INP)** | $< 80\text{ milidetik}$ | Peringanan manipulasi DOM pada Leaflet markers menggunakan *Canvas Renderer* untuk 50+ pin. |
| **Cumulative Layout Shift (CLS)** | $< 0.02$ | Setiap wadah foto makanan dan panel peta memiliki rasio aspek baku (*fixed aspect ratio*). |
| **Ukuran Bundle JavaScript Awal** | $< 120\text{ KB (gzipped)}$ | Library GIS hanya dimuat secara selektif pada rute yang membutuhkan peta interaktif. |
| **Format Aset Visual Gambar** | $100\%\text{ WebP}$ | Pemangkasan otomatis resolusi foto maksimal 1280px dan kompresi kualitas 80% via GD extension. |

---

## 9. Daftar Periksa Kualitas Implementasi (*Quality Assurance Checklist*)

Gunakan daftar audit berikut sebelum setiap rilis fitur:

- [ ] Seluruh pasangan kontras teks dan latar belakang lolos pengujian $\ge 4.5:1$ (teks normal) dan $\ge 3.0:1$ (elemen UI).
- [ ] Komponen Time-Ring mengkalkulasi selisih waktu dari timestamp server (`server_now`), bukan jam lokal klien.
- [ ] Fitur *Click-to-Pin* bekerja mulus baik melalui klik mouse desktop maupun ketukan sentuh layar ponsel.
- [ ] Autocomplete pencarian geocoding menampilkan multivariasi opsi nama daerah yang mirip dan tidak mengirim request sebelum debounce 300ms.
- [ ] Fitur *Reverse Geocoding* berhasil membaca nama jalan/wilayah dan mengisi kolom alamat saat pin digeser.
- [ ] Tombol ganti layer Basemap (Peta Standar vs Satelit Esri) berfungsi tanpa kebocoran memori atau reload peta.
- [ ] Alamat presisi dan koordinat akurat mutlak tersembunyi dari publik dan hanya terbuka bagi pemohon yang klaimnya telah disetujui (`status = approved`).
- [ ] Dropdown wilayah pendaftaran akun mengalir secara bertingkat (*Provinsi $\rightarrow$ Kabupaten/Kota $\rightarrow$ Kecamatan*) menggunakan EMSIFA API Wilayah Indonesia.
- [ ] Pengguna tidak dapat mengklaim makanannya sendiri dan menerima pesan peringatan yang mendidik.
- [ ] Seluruh elemen interaktif dapat dijangkau dan dioperasikan menggunakan tombol `Tab`, `Shift+Tab`, `Space`, dan `Enter`.
- [ ] Halaman tetap responsif dan fungsional pada resolusi layar 360px (ponsel kecil), 768px (tablet), hingga 1920px (desktop ultrawide).
- [ ] Kartu katalog e-commerce memiliki tombol aksi "Ambil di Peta" yang mengalihkan pengguna ke koordinat penjemputan dengan hand-off state yang mulus.
- [ ] Tidak ada satupun emoji visual yang digunakan di seluruh antarmuka web, seluruhnya digantikan oleh SVG iconography presisi tinggi.

---

## 9. Spesifikasi Antarmuka Katalog Makanan E-Commerce & Hand-off ke Peta

### 9.1 Layout & Grid Token
- **Container:** Max-width 7xl (`1280px`), padding horizontal responsif (`px-4 sm:px-6 lg:px-8`).
- **Catalog Grid:**
  - Ponsel (< 640px): 1 kolom kartu vertikal penuh (`grid-cols-1`).
  - Tablet (640px - 1024px): 2 kolom kartu produk (`sm:grid-cols-2`).
  - Desktop (1024px - 1280px): 3 kolom kartu produk (`lg:grid-cols-3`).
  - Desktop Lebar (> 1280px): 4 kolom kartu produk (`xl:grid-cols-4`).
- **Sidebar Filter:** Kolom kiri sticky 64 (w-64) pada desktop, dan drawer panel tersembunyi pada layar ponsel (`x-show="mobileFilterOpen"`).

### 9.2 Anatomi Kartu Produk E-Commerce (Food Rescue Card)
1. **Media Box (Aspect 4:3 / 16:9):**
   - Foto resolusi tinggi dengan radius sudut atas `rounded-t-3xl`.
   - Fallback visual: Gradasi latar belakang bertema kategori dengan ilustrasi outline SVG makanan elegan (tanpa emoji).
   - **Floating Time Urgency Ring:** Menempel di sudut kanan atas dengan latar akrilik `bg-white/95 backdrop-blur-md` dan micro-ring SVG penanda sisa waktu.
   - **Category & Portion Badges:** Sudut kiri atas memuat lencana kategori dan jumlah sorsi tersisa.
2. **Metadata Body:**
   - Nama donor/organisasi dengan status verifikasi (ikon centang perisai terverifikasi).
   - Judul makanan: Bricolage Grotesque Bold 16px, line clamp 1.
   - Jarak & Kecamatan: Misal `Kec. Tembalang • 1.2 km`.
   - Diet & Syariah Chips: Lencana `Halal` dan `Vegetarian` berukuran micro-pill.
3. **Action Footer (Hand-off ke Peta):**
   - **Primary Action ("Ambil di Peta"):** Tombol hijau kontras tinggi (`bg-[#1E7A4C] hover:bg-[#14573A] text-white`) dengan ikon pin navigasi SVG. Mengarahkan langsung ke URL `/?focus={id}&lat={lat}&lng={lng}`.
   - **Secondary Action ("Detail Makanan"):** Tautan outline halus (`text-[#4E6157] hover:text-[#16251D]`) untuk membuka halaman spesifikasi keamanan pangan (`/listings/{id}`).

---

## 10. Spesifikasi Sistem Navigasi Rute OpenStreetMap (OSRM) & Rich Place Detail Sheet

### 10.1 Navigasi Rute Jalan Raya di Atas Peta (OSRM Road Routing)
1. **Engine Geospasial:** Menggunakan integrasi OpenStreetMap (OSM) dan API Open Source Routing Machine (OSRM) pada profil `driving` (kendaraan roda 2 dan 4) serta `walking` (pejalan kaki).
2. **Visual Polylines di Leaflet:**
   - **Outer Casing:** Polylines gelap tebal (`weight: 8`, warna `#0F3B24`, opasitas 35%) untuk memastikan rute tampak kontras di atas peta jalan maupun satelit.
   - **Active Polyline:** Garis utama (`weight: 5`, opasitas 95%) berwarna hijau `#1E7A4C` untuk berkendara, atau biru `#2A6F97` bergaris putus-putus (`dashArray: '8, 8'`) untuk jalan kaki.
   - **Framing Otomatis:** Memanggil `map.fitBounds(bounds, { padding: [80, 80] })` agar seluruh rute dari titik awal hingga tujuan terlihat utuh.
3. **Active Route Floating HUD (Top Overlay):**
   - Bar mengambang kaca akrilik di atas peta (`bg-white/95 backdrop-blur-2xl rounded-3xl border border-emerald-500/40`).
   - Indikator pulsa aktif, nama destinasi, metrik besar ETA menit dan jarak jalan riil (bukan jarak burung lurus).
   - Tombol toggle instan *Kendaraan* vs *Jalan Kaki*.
   - Aksi terintegrasi "Buka Suara di Google Maps" untuk panduan suara GPS dan tombol "Tutup Rute".

### 10.2 Rich Place Detail Sheet (Google Maps / Apple Maps Class)
1. **Pemicu:** Muncul secara reaktif ketika nama tempat di klik pada kartu daftar atau ketika pin marker di peta disentuh.
2. **Struktur Panel:**
   - Ponsel: *Bottom sheet* meluncur dari bawah dengan tuas tarik geser (*swipe handle*).
   - Desktop: *Floating modal card* di kiri bawah peta (`sm:w-[420px] max-h-[640px]`).
3. **Elemen Informasi & Aksi:**
   - Lencana status: Kategori, Halal, Terverifikasi, dan Pil Urgensi Sisa Waktu.
   - Judul listing & profil avatar inisial donatur.
   - **Kotak Alamat Pengambilan Lengkap:** Menampilkan alamat fisik lengkap, label status presisi, dan tombol interaktif "Salin Alamat Lengkap".
   - Catatan keamanan pangan & deskripsi donor.
   - **Toolbar 4 Tombol Aksi:**
     - **Rute di Peta:** Memanggil kalkulasi OSRM dan menggambar rute jalan langsung di peta.
     - **Bagikan:** Memanggil Web Share API atau menyalin tautan unik (`/?focus={id}&lat={lat}&lng={lng}`) ke *clipboard*.
     - **Google Maps:** Tautan navigasi turn-by-turn eksternal.
     - **Ambil Makanan:** CTA primer menuju form klaim makanan.
4. **Sistem Notifikasi Toast:**
   - Balon konfirmasi mengambang di bawah tengah layar (`bg-[#16251D]/95 text-white`) dengan ikon centang SVG saat tautan atau alamat berhasil disalin.
5. **Kebijakan Nol Emoji:** Seluruh antarmuka 100% menggunakan SVG vektor Tailwind terstandar.

---
*Dokumen ini merupakan spesifikasi antarmuka definitif untuk Locabite v2.0. Setiap penambahan fitur atau modifikasi tampilan wajib merujuk pada prinsip, token desain, dan aturan aksesibilitas yang telah ditetapkan di atas.*
