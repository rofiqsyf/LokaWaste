# design.md — Desain UI/UX Locabite

**Project:** Website Pencegahan Food Waste dan Distribusi Makanan Berlebih Berbasis Web dan Geolokasi
**Stack UI:** Laravel 13 (Blade) + Tailwind CSS + Alpine.js + Leaflet.js (OpenStreetMap)
**Versi Dokumen:** 1.2 (hasil audit UI/UX: kontras terukur, aksesibilitas marker, privasi lokasi, status antarmuka, dinamika)

---

## Catatan Revisi 1.2 (Hasil Audit)
Audit memeriksa kontras warna dengan rumus WCAG (dihitung, bukan ditaksir) dan menelusuri alur layar demi layar.

| # | Temuan | Perbaikan |
|---|---|---|
| 1 | Kunyit `#E3A008` hanya **2,26:1** di atas putih (gagal untuk teks maupun grafis); teks putih di atasnya juga gagal | Diganti Kunyit Tua `#B45309` (5,02:1) dan pasangan latar lembut `#FBEFC9` dengan teks `#7A4A00` (6,52:1) |
| 2 | Garis `#D5E0D9` hanya **1,36:1**: batas kolom isian nyaris tak terlihat | Token baru `line-strong` `#74877B` (3,82:1) untuk batas input dan kontrol |
| 3 | Marker peta hanya dibedakan warna hijau/kuning/merah: tidak ramah buta warna merah-hijau | Ditambah chip waktu di bawah pin dan penanda bentuk (bagian 3.3) |
| 4 | Komponen time ring tidak lengkap (properti `label`, `color` tidak didefinisikan), memakai jam perangkat, dan `stroke="var()"` tidak berfungsi pada atribut SVG | Komponen lengkap, koreksi `server_now`, warna lewat `style` (3.1) |
| 5 | Bottom sheet: gestur tarik bentrok dengan geser peta, tidak dapat dipakai keyboard | Gestur hanya pada pegangan, tombol Perluas/Ciutkan (4.1) |
| 6 | Tombol mengambang "Bagikan" dan item tengah navigasi bawah menggandakan satu aksi | Satu posisi: tengah navigasi bawah (mobile) |
| 7 | "Menu Galang" ambigu (daftar publik atau kelola?) | Daftar publik di navigasi; kelola di Dashboard, tab "Galang saya" |
| 8 | Pin galang "di luar kecamatan ditolak" tidak dapat ditegakkan tanpa poligon batas | Menjadi peringatan; keputusan di tinjauan admin |
| 9 | Alamat tampil publik di detail listing | Lokasi perkiraan sampai klaim disetujui (4.2) |
| 10 | Tidak ada status memuat, kosong, galat, offline | Bagian 11 |
| 11 | Antarmuka terasa statis: tak ada penyegaran data | Bagian 12 |
| 12 | Font dari server pihak ketiga; pencarian alamat per ketikan melanggar kebijakan Nominatim | Font di-host sendiri; pencarian saat Enter atau tombol cari (bagian 14, 10) |
| 13 | Form berbagi terlalu panjang untuk situasi terburu-buru | Form target 30 detik, "Bagikan lagi" (3.5) |
| 14 | Informasi keamanan pangan tidak terstruktur | Bagian 13 dan atribut listing |
| 15 | Estetika belum pernah dirender atau diuji pengguna | Bagian 15 mencatat apa yang belum tervalidasi |

---

## 1. Ringkasan Desain

**Subjek:** platform yang menghubungkan pengguna yang punya makanan berlebih dengan pengguna terdekat yang membutuhkannya, dengan relawan wilayah untuk penyaluran ke panti, ponpes, dan tunawisma.
**Audiens:** pemilik restoran/toko roti, warga yang punya makanan lebih, mahasiswa, pengelola komunitas, dan calon relawan. Semua memakai **satu jenis akun**: pengguna yang sama bisa membagikan makanan hari ini dan mengambil makanan besok. Banyak pengguna akan membuka aplikasi dari ponsel, sering dalam keadaan terburu-buru karena makanan punya batas waktu.
**Tugas utama desain:** membantu orang menjawab tiga pertanyaan dalam hitungan detik: *Ada makanan apa di dekat saya? Berapa lama lagi bisa diambil? Bagaimana cara mengambilnya?* Untuk pengguna yang ingin berbagi, pertanyaannya: *Bagaimana cara membagikan makanan ini sebelum waktunya habis?*

### Konsep: "Waktu adalah informasi utama"
Pembeda utama aplikasi ini dari direktori biasa adalah **batas waktu layak ambil**. Karena itu:

- Satu elemen yang dibuat memorable: **cincin hitung mundur (time ring)** pada setiap kartu dan marker peta. Cincin menyusut dan berubah warna seiring waktu berjalan.
- Elemen lain dibuat tenang dan disiplin agar cincin dan peta menjadi pusat perhatian.
- **Peta adalah halaman utama**, bukan pelengkap. Daftar mengikuti peta, bukan sebaliknya.
- **Satu akun, dua aksi.** Antarmuka tidak meminta pengguna memilih "mau jadi donatur atau penerima". Dua aksi, **Bagikan makanan** dan **Cari makanan**, selalu tersedia berdampingan.
- **Relawan adalah lapisan tambahan**, bukan akun terpisah. Fitur relawan (Galang, klaim atas nama galang) muncul di tempat yang sama setelah pengajuan disetujui, tanpa perlu berganti akun atau tampilan.

---

## 2. Token Desain

### 2.1 Warna
Palet diambil dari dunia pangan Indonesia, tetapi sengaja menghindari krem/terakota yang terlalu umum.

| Token | Nama | Hex | Penggunaan |
|---|---|---|---|
| `--color-bg` | Embun | `#F2F6F3` | Latar halaman |
| `--color-surface` | Putih Nasi | `#FFFFFF` | Kartu, panel, modal |
| `--color-ink` | Arang Hijau | `#16251D` | Teks utama |
| `--color-ink-soft` | Daun Kering | `#4E6157` | Teks sekunder |
| `--color-primary` | Hijau Daun | `#1E7A4C` | Tombol utama, tautan, status tersedia |
| `--color-primary-dark` | Hijau Hutan | `#14573A` | Hover/aktif tombol utama |
| `--color-time-ok` | Hijau Segar Tua | `#23865A` | Time ring: sisa > 3 jam |
| `--color-time-warn` | Kunyit Tua | `#B45309` | Time ring: sisa 1 sampai 3 jam; ikon dan teks peringatan |
| `--color-warn-soft` | Kunyit Muda | `#FBEFC9` | Latar lencana peringatan |
| `--color-warn-ink` | Cokelat Kunyit | `#7A4A00` | Teks di atas `warn-soft` |
| `--color-time-urgent` | Cabai | `#C93A2E` | Time ring: sisa < 1 jam, galat, kedaluwarsa |
| `--color-map` | Biru Peta | `#2A6F97` | Lokasi pengguna, rute, kontrol peta, galang |
| `--color-line` | Garis Embun | `#D5E0D9` | Pemisah dekoratif saja (bukan batas kontrol) |
| `--color-line-strong` | Garis Tegas | `#74877B` | Batas input, tombol sekunder, kontrol interaktif |

**Kontras terukur** (rumus WCAG 2.x; ambang 4,5:1 untuk teks, 3:1 untuk grafis dan batas kontrol):
| Pasangan | Rasio | Hasil |
|---|---|---|
| Arang Hijau di atas Embun | 14,62 | Lulus |
| Daun Kering di atas Embun / Putih | 6,07 / 6,62 | Lulus |
| Putih di atas Hijau Daun (tombol) | 5,33 | Lulus |
| Hijau Daun di atas Embun (tautan) | 4,88 | Lulus |
| Putih di atas Hijau Hutan | 8,56 | Lulus |
| Hijau Segar Tua di atas Putih / Embun | 4,54 / 4,16 | Lulus (grafis) |
| Kunyit Tua di atas Putih / Embun | 5,02 / 4,60 | Lulus |
| Cokelat Kunyit di atas Kunyit Muda | 6,52 | Lulus |
| Cabai di atas Putih / Embun | 5,09 / 4,66 | Lulus |
| Putih di atas Cabai (tombol bahaya) | 5,09 | Lulus |
| Biru Peta di atas Putih / Embun | 5,50 / 5,04 | Lulus |
| Garis Tegas di atas Putih / Embun | 3,82 / 3,50 | Lulus (batas kontrol) |
| ~~Kunyit lama `#E3A008` di atas Putih~~ | 2,26 | **Gagal**, diganti |
| ~~Garis Embun sebagai batas input~~ | 1,36 | **Gagal**, diganti |

**Aturan pemakaian:**
- Teks putih tidak pernah diletakkan di atas Kunyit; gunakan Cokelat Kunyit di atas Kunyit Muda.
- Status tidak boleh hanya dibedakan lewat warna; selalu sertakan teks atau ikon ("Sisa 45 menit").
- Hijau, kunyit, dan cabai dapat tampak mirip bagi pengguna buta warna merah-hijau; karena itu setiap penanda waktu memiliki **teks dan bentuk** pendamping.
- Angka di atas dihitung, bukan diuji pada perangkat. Verifikasi di layar ponsel dengan kecerahan rendah dan di bawah sinar matahari tetap perlu (bagian 15).

### 2.2 Tipografi
| Peran | Keluarga | Alasan |
|---|---|---|
| Judul dan angka waktu | **Bricolage Grotesque** (600, 700) | Karakter kuat dan ramah, cocok untuk judul dan angka hitung mundur |
| Isi dan antarmuka | **Atkinson Hyperlegible** (400, 700) | Dirancang untuk keterbacaan tinggi, membantu pengguna dengan penglihatan terbatas |

Fallback: `system-ui, sans-serif`.

**Skala tipografi (rasio ±1.25):**
| Level | Ukuran | Line-height | Pemakaian |
|---|---|---|---|
| Display | 40 / 2.5rem | 1.1 | Judul beranda (desktop) |
| H1 | 32 / 2rem | 1.15 | Judul halaman |
| H2 | 24 / 1.5rem | 1.25 | Judul seksi |
| H3 | 20 / 1.25rem | 1.3 | Judul kartu |
| Body | 16 / 1rem | 1.55 | Teks isi |
| Small | 14 / 0.875rem | 1.45 | Keterangan, metadata |

Panjang baris teks isi dibatasi maksimal 70 karakter. Gunakan *sentence case* untuk semua label dan tombol.

### 2.3 Spasi, Radius, Bayangan
- **Spasi:** kelipatan 4 px (4, 8, 12, 16, 24, 32, 48).
- **Radius:** bertingkat sesuai fungsi, bukan satu nilai untuk semua.
  - Input dan tombol: `10px`
  - Kartu listing: `16px`
  - Panel/bottom sheet: `24px` pada sisi atas
  - Lencana status: penuh (pill)
- **Bayangan:** hanya dua level. `shadow-card` tipis untuk kartu, `shadow-sheet` lebih tegas untuk panel di atas peta. Elemen lain memakai border `--color-line`.

### 2.4 Konfigurasi Tailwind (acuan)
```js
// tailwind.config.js
export default {
  theme: {
    extend: {
      colors: {
        bg: '#F2F6F3', ink: '#16251D', 'ink-soft': '#4E6157',
        primary: { DEFAULT: '#1E7A4C', dark: '#14573A' },
        time: { ok: '#23865A', warn: '#B45309', urgent: '#C93A2E' },
        warn: { soft: '#FBEFC9', ink: '#7A4A00' },
        map: '#2A6F97', line: { DEFAULT: '#D5E0D9', strong: '#74877B' },
      },
      fontFamily: {
        display: ['"Bricolage Grotesque"', 'system-ui', 'sans-serif'],
        body: ['"Atkinson Hyperlegible"', 'system-ui', 'sans-serif'],
      },
      borderRadius: { field: '10px', card: '16px', sheet: '24px' },
    },
  },
}
```

---

## 3. Komponen Inti

### 3.1 Time Ring (komponen signature)
Cincin SVG yang menunjukkan sisa waktu ambil. **Warna ditentukan oleh sisa waktu absolut**, sedangkan **panjang busur** menunjukkan proporsi dari jendela ambil listing tersebut.

| Sisa waktu | Warna | Label |
|---|---|---|
| > 3 jam | Hijau Segar Tua | "Sisa 4 jam" |
| 1 sampai 3 jam | Kunyit Tua | "Sisa 2 jam" |
| < 1 jam | Cabai | "Sisa 25 menit" |
| Habis | Abu (`--color-ink-soft`) | "Sudah berakhir" |

- Angka sisa waktu ditulis di tengah cincin dengan Bricolage Grotesque; **angka itulah informasi utama**, cincin hanya penguat.
- Busur diberi nilai minimum 6% agar cincin tidak tampak kosong saat waktu hampir habis.
- Pembaruan tiap menit (bukan tiap detik) dan segera saat tab kembali aktif.
- Sisa waktu dihitung dari `expires_at - server_now`, **bukan** dari jam perangkat, sehingga jam ponsel yang keliru tidak menipu pengguna. Server tetap menjadi penentu: klaim pada listing yang sudah habis ditolak server.
- Saat habis, komponen memancarkan event `listing:expired` agar kartu menonaktifkan tombol klaim dan memberi tahu pengguna.
- Animasi hanya berupa transisi warna saat berganti tingkat; nonaktif bila `prefers-reduced-motion`.
- Pembaca layar membaca label lengkap sekali (mis. "Sisa 2 jam 10 menit, batas pukul 20.30 WIB"), tidak diumumkan ulang tiap menit.

```js
// resources/js/time-ring.js
export default (expiresAt, serverNow, totalMinutes, tzLabel) => ({
  offset: new Date(serverNow) - Date.now(),   // koreksi selisih jam perangkat
  remainingMs: 0,
  timer: null,

  init() {
    this.tick();
    this.timer = setInterval(() => this.tick(), 60_000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) this.tick(); });
  },
  destroy() { clearInterval(this.timer); },

  tick() {
    const was = this.remainingMs;
    this.remainingMs = Math.max(0, new Date(expiresAt) - (Date.now() + this.offset));
    if (was > 0 && this.remainingMs === 0) this.$dispatch('listing:expired');
  },

  get minutes()  { return Math.ceil(this.remainingMs / 60_000); },
  get expired()  { return this.remainingMs === 0; },
  get percent()  { return this.expired ? 0 : Math.max(6, Math.min(100, (this.minutes / totalMinutes) * 100)); },
  get color()    {
    if (this.expired)      return 'var(--color-ink-soft)';
    if (this.minutes < 60)  return 'var(--color-time-urgent)';
    if (this.minutes <= 180) return 'var(--color-time-warn)';
    return 'var(--color-time-ok)';
  },
  get short()    {
    if (this.expired) return '0';
    const h = Math.floor(this.minutes / 60), m = this.minutes % 60;
    return h >= 1 ? (m ? `${h}j${m}` : `${h}j`) : `${m}m`;
  },
  get label()    {
    if (this.expired) return 'Waktu ambil sudah berakhir';
    const h = Math.floor(this.minutes / 60), m = this.minutes % 60;
    return `Sisa ${h ? h + ' jam ' : ''}${m ? m + ' menit' : ''}, batas pukul ${this.deadline} ${tzLabel}`.trim();
  },
  get deadline() {
    return new Date(expiresAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: this.$el.dataset.tz });
  },
});
// resources/js/app.js: Alpine.data('timeRing', timeRing)
```

```html
<!-- resources/views/components/time-ring.blade.php -->
<div x-data="timeRing('{{ $listing->expires_at->toIso8601String() }}', '{{ now()->toIso8601String() }}', {{ $listing->windowMinutes() }}, '{{ $listing->tzLabel() }}')"
     data-tz="{{ $listing->timezone() }}"
     class="relative h-14 w-14" role="img" :aria-label="label">
  <svg viewBox="0 0 36 36" class="h-full w-full -rotate-90" aria-hidden="true">
    <circle cx="18" cy="18" r="15.9" fill="none" stroke-width="3" style="stroke: var(--color-line)"/>
    <circle cx="18" cy="18" r="15.9" fill="none" stroke-width="3" stroke-linecap="round"
            :style="{ stroke: color }" :stroke-dasharray="`${percent} 100`"/>
  </svg>
  <span class="absolute inset-0 grid place-items-center font-display text-sm font-bold text-ink"
        x-text="short" aria-hidden="true"></span>
</div>
```
Catatan teknis: warna harus diberikan lewat `style`, bukan atribut `stroke="var(...)"`, karena variabel CSS tidak diproses pada atribut presentasi SVG. Cincin dasar memakai `--color-line` karena bersifat dekoratif; informasi sebenarnya ada pada angka dan label.

### 3.2 Kartu Listing
Struktur (kiri ke kanan pada mobile, foto di atas pada layar sempit):

```
+----------------------------------------------+
| [Foto]   Nasi kotak ayam bakar        (Ring) |
|          Warung Bu Sari                      |
|          12 porsi • 1,2 km • Halal           |
|          [ Lihat detail ]                    |
+----------------------------------------------+
```
- Judul memakai H3, nama pemberi memakai teks sekunder.
- Metadata dipisah spasi dan ikon kecil, bukan titik tengah berulang.
- Lencana "Terverifikasi" muncul hanya bila `is_verified = true`; lencana "Relawan" muncul bila pemberi adalah relawan.
- Listing yang ditujukan ke galang menampilkan label kecil "Untuk galang: [nama galang]" dan keterangan "Prioritas relawan sampai 18.30".

### 3.3 Marker Peta
Marker tidak boleh bergantung pada warna saja. Setiap marker listing terdiri dari pin dan **chip waktu** di bawahnya.

| Tingkat | Warna pin | Chip waktu | Penanda bentuk |
|---|---|---|---|
| > 3 jam | Hijau Segar Tua | "3j+" | Pin polos |
| 1 sampai 3 jam | Kunyit Tua | "2j" | Pin dengan titik ganda di sudut |
| < 1 jam | Cabai | "40m" | Pin dengan tepi ganda dan tanda seru |

- Pin berisi ikon kategori (nasi, roti, buah, dst.); ikon berukuran minimal 16 px.
- **Pemberi individu** ditampilkan sebagai **lingkaran area** (sesuai koordinat publik yang sengaja digeser), bukan pin presisi. Pemberi bisnis/lembaga terverifikasi yang memilih menampilkan lokasi tepat memakai pin presisi. Pengguna diberi tahu: "Lokasi perkiraan. Titik tepat dibuka setelah klaim disetujui."
- Klaster marker berwarna Biru Peta dengan jumlah di tengah.
- **Marker galang makanan** (lapisan opsional): persegi bersudut tumpul dengan ikon rumah, Biru Peta, tanpa chip waktu. Galang penerima manfaat tunawisma tampil sebagai area.
- Lokasi pengguna: titik biru dengan lingkaran radius pencarian semi transparan.
- Marker terpilih membesar dan kartu terkait di daftar tersorot; popup menampilkan foto kecil, judul, sisa waktu (teks), dan tombol "Lihat detail".
- Marker dapat difokuskan dengan keyboard (elemen `button`) dan memiliki nama aksesibel: "Nasi kotak ayam bakar, sisa 40 menit, 1,2 km".

### 3.4 Tombol
| Jenis | Gaya | Contoh label |
|---|---|---|
| Primer | Isi Hijau Daun, teks putih | Ajukan klaim |
| Sekunder | Border Hijau Daun, teks Hijau Daun | Lihat rute |
| Bahaya | Isi Cabai, teks putih | Batalkan listing |
| Teks | Tanpa border, garis bawah saat hover | Lapor masalah |

Ukuran sentuh minimal 44 x 44 px. Label berupa kata kerja spesifik ("Ajukan klaim", "Simpan listing"), bukan "Submit".

### 3.5 Formulir
- Label selalu terlihat di atas input (tidak hanya placeholder).
- Batas input memakai `--color-line-strong` (kontras 3,82:1), bukan garis dekoratif.
- Pesan galat tampil di bawah input dan menjelaskan cara memperbaiki. Saat kirim gagal, fokus pindah ke ringkasan galat di atas formulir, lalu tiap galat menaut ke kolomnya.
- Data yang sudah diisi tidak hilang bila pengiriman gagal.

**Form "Bagikan makanan" (target selesai dalam 30 detik untuk kasus umum):**
1. **Foto**: tombol kamera langsung (`capture`), opsional tetapi dianjurkan; unggah dalam latar belakang.
2. **Nama makanan**: saran otomatis dari riwayat pengguna.
3. **Porsi**: stepper (+/-) dengan tombol 44 px.
4. **Batas ambil**: chip cepat "+1 jam", "+2 jam", "+4 jam", "Pilih jam". Zona waktu ditampilkan (WIB/WITA/WIT).
5. **Jenis makanan**: Dimasak, Kemasan, Bahan mentah. Jika "Dimasak", muncul "Dimasak pukul" (nilai awal: sekarang) dan peringatan bila jendela ambil terlalu panjang.
6. **Alergen**: chip pilihan (telur, susu, kacang, gluten, seafood, kedelai) plus "Tidak tahu".
7. **Titik ambil**: nilai awal dari lokasi tersimpan atau GPS; pin dapat digeser. Kecamatan terisi otomatis sebagai saran dan dapat diubah.
8. **Pernyataan**: kotak centang "Saya menyatakan makanan ini layak dan halal" (bila relevan), dengan bahasa yang jelas bahwa ini pernyataan pemberi.
9. Bagian **Opsi lanjutan** (terlipat): deskripsi, catatan penyimpanan, maksimal porsi per klaim, tujukan ke galang.

- Tombol **"Bagikan lagi"** pada riwayat menyalin listing sebelumnya; hanya waktu yang perlu diisi ulang.
- Draf diisi otomatis tersimpan di perangkat sehingga tidak hilang saat sinyal terputus.

**Pemilih lokasi:** peta mini + tombol "Pakai lokasi saya" + kolom alamat. Pencarian alamat berjalan saat **Enter atau tombol Cari ditekan**, bukan per ketikan (kebijakan penggunaan Nominatim melarang pencarian otomatis per karakter). Di bawah peta mini tampil keterangan privasi dengan ilustrasi lingkaran area: "Pengambil hanya melihat lokasi perkiraan sampai Anda menyetujui klaim."

### 3.6 Lencana Status
| Status | Teks | Warna |
|---|---|---|
| available | Tersedia | Hijau Daun |
| reserved | Dipesan | Biru Peta |
| completed | Selesai | Abu |
| expired | Kedaluwarsa | Cabai (outline) |
| cancelled | Dibatalkan | Abu (outline) |
| pending (klaim) | Menunggu persetujuan | Kunyit Muda, teks Cokelat Kunyit, ikon jam |
| campaign pending_review | Menunggu tinjauan | Kunyit Muda, teks Cokelat Kunyit |
| campaign published | Terbit | Hijau Daun |
| campaign closed | Ditutup | Abu |
| campaign rejected / taken_down | Ditolak / Diturunkan | Cabai (outline) |
| area pending / approved / revoked | Menunggu / Disetujui / Dicabut | Kunyit Muda / Hijau Daun / Cabai (outline) |
| klaim picked_up | Sudah diambil | Hijau Daun |
| klaim cancelled | Dibatalkan | Abu (outline) |
| klaim expired | Kedaluwarsa | Abu (outline) |
| klaim no_show | Tidak diambil | Cabai (outline) |
| campaign paused | Dijeda | Kunyit Muda, teks Cokelat Kunyit |

Setiap lencana memakai ikon kecil dan teks sehingga tidak bergantung pada warna.

### 3.7 Lencana Relawan
- Pill dengan ikon tangan, border dan teks Hijau Hutan, latar putih. Bentuk berbeda dari lencana status agar tidak tertukar.
- Teks ringkas: "Relawan, Kota Semarang". Jika lebih dari satu kecamatan, tampil "2 kecamatan" dan dapat dibuka untuk melihat daftarnya.
- Tampil di profil publik, kartu listing, dan artikel galang. Tidak pernah tampil bila belum ada kecamatan berstatus disetujui.

### 3.8 Kartu Galang Makanan
```
+----------------------------------------------+
| [Foto sampul]                                |
| [Panti asuhan]  Kec. Tembalang               |
| Makan malam untuk 40 anak panti              |
| [=========>      ] 340 dari 600 porsi        |
| Sisa 12 hari  •  oleh Rina (Relawan)         |
| [ Baca galang ]                              |
+----------------------------------------------+
```
- Label jenis penerima manfaat (panti asuhan, ponpes, panti jompo, tunawisma, lainnya) berupa pill netral.
- Bilah progres memakai Hijau Daun; selalu disertai angka agar tidak bergantung pada warna.
- Kartu galang memakai radius kartu yang sama dengan listing, tetapi **tanpa time ring**, karena galang berjangka hari, bukan jam. Dengan begitu, time ring tetap identik dengan "makanan yang segera habis".

### 3.9 Pemilih Wilayah Berjenjang
Dipakai pada pengajuan relawan dan form galang.
```
Kabupaten/kota   [ Cari: Kota Semarang        v ]
Kecamatan        [ Cari kecamatan...           ]
                 [x] Tembalang   [x] Banyumanik
                 [ ] Gajahmungkur [ ] Candisari
Terpilih: 2 kecamatan                 [Pilih semua]
```
- Kecamatan baru bisa dipilih setelah kabupaten/kota dipilih; mengganti kabupaten/kota mengosongkan pilihan kecamatan dan memberi tahu pengguna.
- Daftar kecamatan memakai kotak centang (pilihan jamak) dan dapat dicari.
- Kecamatan yang sudah disetujui atau menunggu ditandai dan tidak dapat dipilih ulang.

---

## 4. Tata Letak Halaman

### 4.1 Beranda / Peta Utama (halaman terpenting)

**Mobile (utama):**
```
+------------------------------+
| Locabite          [Masuk]  |
| [ Cari alamat atau area  ]   |
| [Makanan | Galang]            |
| [1 km][3 km][5 km] [Filter]  |
|                              |
|        P E T A               |
|     (marker + lokasi saya)   |
|                              |
|+----------------------------+|
|| ---- (bottom sheet) ----   ||
|| Nasi kotak ayam   (Ring)   ||
|| Roti sisa hari ini (Ring)  ||
|| Sup jagung        (Ring)   ||
|+----------------------------+|
+------------------------------+
```
- Bottom sheet dapat ditarik: tertutup (peta penuh), setengah (daftar singkat), penuh (daftar lengkap).
- Daftar diurutkan menurut yang paling cepat habis, lalu jarak.

**Desktop:**
```
+-----------------------------------------------------+
| Locabite   [Cari alamat...]   [Filter]   [Masuk]  |
+---------------------+-------------------------------+
| Daftar listing      |                               |
| (scroll)            |          P E T A              |
| [kartu]             |                               |
| [kartu]             |                               |
| [kartu]             |                               |
+---------------------+-------------------------------+
```
Perataan: konten kiri (left aligned), kecuali kondisi kosong yang berada di tengah.

**Dua aksi utama** selalu terlihat: tombol primer **Bagikan makanan** (di kanan atas pada desktop; pada mobile menjadi item tengah navigasi bawah, tanpa tombol mengambang kedua) dan fokus pencarian **Cari makanan** pada bilah atas. Tamu yang menekan salah satunya diarahkan masuk dulu, lalu dikembalikan ke aksi semula.

**Sakelar lapisan** "Makanan | Galang" mengganti isi peta dan daftar: listing yang bisa diambil sekarang, atau galang makanan terdekat. Sakelar ini berada di peta; item "Galang" di navigasi bawah membuka daftar galang publik, sedangkan pengelolaan galang berada di Dashboard, tab "Galang saya".

**Bottom sheet (mobile):**
- Tiga posisi: tertutup, setengah, penuh.
- Gestur tarik hanya pada **pegangan** setinggi 44 px; area daftar menggulir biasa dan area peta tetap dapat digeser, sehingga dua gestur tidak bentrok.
- Tombol "Perluas" dan "Ciutkan" tersedia untuk keyboard dan pembaca layar; panel memiliki `role="region"` dan label.
- Memilih marker menaikkan sheet ke posisi setengah dan menyorot kartu terkait tanpa memindahkan fokus keyboard.
- Posisi sheet tidak mengubah isi; daftar dan peta selalu sinkron.

**Hero:** tidak memakai banner besar. Halaman langsung menampilkan peta dengan satu kalimat pengantar di atas bilah pencarian: "Makanan layak yang bisa diambil di dekatmu." Penjelasan cara kerja diletakkan di bawah peta untuk pengunjung baru.

### 4.2 Detail Listing
```
+------------------------------------------+
| [Foto besar]                             |
| Nasi kotak ayam bakar          (Ring)    |
| Warung Bu Sari • Terverifikasi           |
| Sisa 8 dari 12 porsi                     |
|                                          |
| Diambil sebelum 20.30 hari ini           |
| Area: Kec. Tembalang (lokasi perkiraan)  |
| [ Mini peta + Lihat rute ]               |
|                                          |
| Deskripsi, bahan, catatan alergen        |
|                                          |
| [ Ajukan klaim ]       Lapor masalah     |
| [ Ambil untuk galang saya ] (relawan)    |
+------------------------------------------+
```
Tombol klaim bersifat tetap (sticky) di bawah layar pada mobile.

**Informasi pangan** ditampilkan terstruktur di bawah judul: jenis (dimasak pukul 17.30, kemasan, bahan mentah), alergen, catatan penyimpanan, dan "Pemberi menyatakan halal" bila diisi. Peringatan keamanan pangan satu kalimat tampil di dekat tombol klaim, bukan sebagai jendela yang menghalangi.

**Privasi lokasi:** peta mini menampilkan **lingkaran area**. Alamat lengkap, titik tepat, tombol "Lihat rute", dan kode ambil baru muncul di panel "Detail pengambilan" setelah klaim disetujui. Untuk pemberi individu, panel menyertakan anjuran bertemu di tempat yang aman dan terbuka.

Perilaku tombol menurut siapa yang melihat:
- **Pemilik listing:** tombol klaim diganti panel "Ini listing Anda" berisi tombol Ubah, Batalkan, dan daftar klaim masuk.
- **Pengguna lain:** tombol "Ajukan klaim".
- **Relawan di kecamatan listing:** tambahan tombol sekunder "Ambil untuk galang saya" jika memiliki galang terbit di kecamatan tersebut.
- **Listing berprioritas galang:** selama masa prioritas, pengguna non-relawan melihat pesan "Diprioritaskan untuk relawan galang sampai 18.30. Anda bisa mengklaim setelahnya." dan tombol klaim dinonaktifkan dengan alasan yang jelas.

### 4.3 Dashboard Pengguna (satu halaman, dua sisi)
```
+--------------------------------------------------+
| Halo, Rofiq                                      |
| [ Bagikan makanan ]   [ Cari makanan ]           |
|                                                  |
| Ringkasan | Yang saya bagikan | Yang saya ambil  |
|           | (Galang saya)* | * hanya relawan     |
+--------------------------------------------------+
```
- **Ringkasan:** porsi dibagikan, porsi diambil, dan klaim yang menunggu keputusan saya.
- **Yang saya bagikan:** tabel listing dengan status dan tindakan cepat (Ubah, Batalkan). Klaim masuk yang menunggu persetujuan diletakkan paling atas karena sensitif waktu.
- **Yang saya ambil:** klaim aktif sebagai kartu besar berisi **kode ambil**, alamat, dan sisa waktu; riwayat di bawahnya dengan tombol "Beri ulasan" setelah selesai.
- **Galang saya** hanya muncul bila pengguna relawan.
- Bagi pengguna yang belum relawan, tampil satu kartu undangan yang dapat ditutup: "Ingin membantu lebih jauh? Ajukan diri sebagai relawan di kecamatanmu." Kartu tidak muncul lagi setelah ditutup.

### 4.4 Profil Publik
- Nama, foto, organisasi/usaha (bila ada), lencana "Terverifikasi" dan "Relawan".
- Jumlah porsi yang pernah dibagikan, ulasan yang diterima, dan galang yang diampu (bila relawan).
- Alamat dan telepon **tidak** ditampilkan di profil publik.

### 4.5 Panel Admin
- Tata letak sidebar + konten.
- Modul: Pengguna, Pengajuan relawan, Galang makanan, Listing, Laporan, Kategori, Wilayah, Statistik.
- **Antrean tinjauan** di bagian atas dashboard admin: pengajuan relawan dan galang yang menunggu, diurutkan dari yang terlama.
- Pengajuan relawan ditampilkan sebagai satu kartu per pengajuan dengan daftar kecamatan; admin menyetujui atau menolak **per kecamatan** lewat kotak centang, lalu menekan "Simpan keputusan" dengan catatan opsional.
- Tinjauan galang menampilkan pratinjau persis seperti halaman publik, tombol "Terbitkan", "Minta revisi" (wajib catatan), dan "Tolak".
- Tabel dengan pencarian, filter status, dan paginasi.

### 4.6 Halaman Lain
Masuk, Daftar (tanpa pemilihan peran), Pengaturan profil, Notifikasi, Halaman 403/404/500.

### 4.7 Ajukan Relawan dan Wilayah Saya
```
Jadi relawan
Bantu menyalurkan makanan di wilayahmu. Pengajuan ditinjau admin.

1. Pilih kabupaten/kota   [ Kota Semarang      v ]
2. Pilih kecamatan        [x] Tembalang [x] Banyumanik ...
3. Tulis alasan singkat   [ .................... ]

[ Kirim pengajuan ]
```
Nomor langkah dipakai karena ini memang urutan pengisian.

**Wilayah saya** (daftar kecamatan dengan status):
```
Kota Semarang
  Tembalang      Disetujui     [Mundur]
  Banyumanik     Menunggu
Kab. Demak
  Mranggen       Ditolak       Catatan admin: ...
[ + Tambah wilayah ]
```
- Satu pengajuan hanya untuk satu kabupaten/kota. Untuk kabupaten/kota lain, tekan "Tambah wilayah" dan isi pengajuan baru.
- Tombol "Mundur" meminta konfirmasi dan menjelaskan akibatnya (galang terbit di kecamatan itu akan dijeda).

### 4.8 Galang Makanan (halaman publik)
**Daftar galang:** filter kabupaten/kota, kecamatan, dan jenis penerima manfaat; urut berdasarkan terbaru atau paling butuh (progres terendah). Kartu galang mengikuti bagian 3.8.

**Detail galang:**
```
+------------------------------------------+
| [Foto sampul]                            |
| [Panti asuhan]  Kec. Tembalang           |
| Makan malam untuk 40 anak panti          |
| Dibuat oleh Rina  [Relawan]              |
| [=========>      ] 340 dari 600 porsi    |
|                                          |
| Artikel (cerita, kebutuhan, jadwal)      |
|                                          |
| [ Mini peta + titik penyaluran ]         |
|                                          |
| Pembaruan terbaru                        |
|  - 14 Nov: 60 porsi tersalurkan [foto]   |
|                                          |
| [ Bagikan makanan ke galang ini ]        |
|        Lapor masalah                     |
+------------------------------------------+
```
- Tombol utama **Bagikan makanan ke galang ini** membuka form listing dengan galang sudah terpilih dan lokasi titik penyaluran ditampilkan sebagai tujuan.
- Alamat lengkap penerima manfaat hanya ditampilkan sesuai yang dicantumkan relawan; untuk tunawisma, gunakan titik area penyaluran, bukan alamat pribadi.
- Galang berstatus ditutup tetap dapat dibaca, dengan banner "Galang ini sudah ditutup" dan ringkasan hasil.

### 4.9 Kelola Galang (relawan)
- **Daftar galang saya** dengan status (draf, menunggu tinjauan, terbit, ditutup, ditolak) dan tindakan sesuai status.
- **Form galang:** jenis penerima manfaat, nama penerima, alamat dan pin di peta (kecamatan dipilih dari wilayah yang disetujui; bila pin jauh dari pusat kecamatan muncul **peringatan** "Pin ini jauh dari pusat Kec. X. Pastikan kecamatannya benar." Pengiriman tetap dapat dilakukan dan admin melihat penanda peringatan saat meninjau; untuk penerima manfaat tunawisma tersedia opsi "Tampilkan sebagai area"), kebutuhan harian, target porsi, periode, sampul, dan isi artikel.
- Tombol: **Simpan draf** dan **Kirim untuk tinjauan**. Setelah dikirim, halaman menampilkan banner status dan catatan admin bila diminta revisi.
- **Posting pembaruan** (teks + foto) tersedia saat galang terbit.
- **Tutup galang** meminta konfirmasi dan menampilkan ringkasan porsi yang terkumpul.

---

## 5. Alur Pengguna Utama

### 5.1 Pengguna mengambil makanan
```
Buka peta -> izinkan lokasi -> lihat marker terdekat
-> pilih listing -> cek sisa waktu & alamat -> Ajukan klaim
-> menunggu persetujuan -> notifikasi disetujui + kode ambil
-> datang ke lokasi -> tunjukkan kode -> pemberi verifikasi -> selesai
```

### 5.2 Pengguna membagikan makanan
```
Masuk -> Bagikan makanan -> isi detail & foto
-> tentukan titik ambil di peta -> atur batas waktu -> (opsional) tujukan ke galang
-> Simpan listing -> terima notifikasi klaim -> Setujui
-> verifikasi kode saat serah terima
```

### 5.3 Pengguna menjadi relawan
```
Dashboard -> kartu "Jadi relawan" (atau menu Relawan)
-> pilih kabupaten/kota -> pilih kecamatan -> tulis alasan -> Kirim pengajuan
-> admin meninjau per kecamatan -> notifikasi keputusan
-> kecamatan disetujui: lencana Relawan aktif, menu Galang muncul
-> (kapan saja) Tambah wilayah / Mundur dari kecamatan
```

### 5.4 Relawan membuat galang makanan
```
Dashboard -> Galang saya -> Buat galang -> isi artikel & kebutuhan
-> letakkan pin di kecamatan yang diampu -> Simpan draf
-> Kirim untuk tinjauan -> admin menerbitkan (atau minta revisi)
-> galang tampil publik & di peta -> pengguna membagikan makanan ke galang
-> relawan klaim atas nama galang -> ambil makanan -> progres bertambah
-> posting pembaruan -> tutup galang
```

### 5.5 Admin meninjau
```
Dashboard admin -> antrean tinjauan
-> pengajuan relawan: centang kecamatan yang disetujui -> Simpan keputusan
-> galang: pratinjau -> Terbitkan / Minta revisi / Tolak
```

---

## 6. Konten dan Nada Bahasa

Prinsip: bahasa Indonesia sehari-hari, jelas, tanpa istilah teknis.

| Konteks | Teks yang digunakan |
|---|---|
| Tombol klaim | Ajukan klaim |
| Klaim berhasil | Klaim terkirim. Tunggu persetujuan pemberi. |
| Klaim disetujui | Klaim disetujui. Tunjukkan kode ini saat mengambil. |
| Peta kosong | Belum ada makanan dalam 3 km. Coba perluas radius ke 5 km. |
| Izin lokasi ditolak | Lokasi tidak aktif. Cari alamat atau geser pin di peta. |
| Gagal unggah foto | Foto lebih dari 2 MB. Pilih foto yang lebih kecil. |
| Listing berakhir | Waktu ambil sudah berakhir. Listing ini tidak bisa diklaim lagi. |
| Peringatan keamanan pangan | Periksa kondisi makanan sebelum dikonsumsi. Pemberi bertanggung jawab atas kelayakannya. |
| Klaim listing sendiri | Ini listing Anda. Kelola dari halaman "Yang saya bagikan". |
| Prioritas galang | Diprioritaskan untuk relawan galang sampai 18.30. Anda bisa mengklaim setelahnya. |
| Pengajuan relawan terkirim | Pengajuan terkirim. Admin akan meninjau tiap kecamatan yang Anda pilih. |
| Disetujui sebagian | Disetujui untuk 1 dari 2 kecamatan. Lihat catatan admin di Wilayah saya. |
| Pin jauh dari kecamatan | Pin ini jauh dari pusat Kec. Tembalang. Pastikan kecamatannya benar. |
| Galang dikirim | Galang dikirim untuk ditinjau. Anda akan diberi tahu saat terbit. |
| Galang diminta revisi | Admin meminta perubahan. Baca catatan, ubah, lalu kirim ulang. |
| Pengguna belum relawan membuka Galang | Fitur ini untuk relawan. Ajukan diri sebagai relawan di kecamatanmu. |
| Klaim dibatalkan | Klaim dibatalkan. Porsi dikembalikan ke listing. |
| Klaim tidak diambil | Klaim berakhir karena makanan tidak diambil sampai batas waktu. |
| Akun dibatasi | Anda belum dapat mengklaim sampai 28 Nov. Anda tetap dapat membagikan makanan. |
| Email belum terverifikasi | Verifikasi email dulu untuk membagikan atau mengklaim makanan. Kirim ulang tautan? |
| Kode ambil salah | Kode tidak cocok. Sisa 3 percobaan. |
| Kode terkunci | Terlalu banyak percobaan. Coba lagi dalam 15 menit. |
| Listing habis saat dilihat | Waktu ambil baru saja berakhir. Daftar sudah diperbarui. |
| Peta gagal dimuat | Peta tidak dapat dimuat. Gunakan daftar di bawah. |
| Data gagal diperbarui | Data gagal diperbarui. Menampilkan data 3 menit lalu. Coba lagi |
| Lokasi perkiraan | Lokasi perkiraan. Titik tepat dibuka setelah klaim disetujui. |

Satu aksi memakai satu nama di seluruh alur ("Klaim" tidak berganti menjadi "Pesan" di tempat lain). Pesan galat tidak meminta maaf berlebihan dan selalu menjelaskan solusi.

---

## 7. Aksesibilitas

- Kontras diukur dengan rumus WCAG (tabel di bagian 2.1): teks minimal 4,5:1, grafis dan batas kontrol minimal 3:1.
- Tautan lompat "Ke konten utama" dan "Ke daftar listing" di awal halaman.
- Pembaruan penting (klaim disetujui, listing habis) diumumkan lewat wilayah `aria-live="polite"`; hitung mundur tidak diumumkan ulang tiap menit.
- Halaman tetap dapat dipakai pada pembesaran 200% tanpa hilangnya fungsi.
- Fokus keyboard terlihat jelas (outline 2 px Biru Peta dengan offset).
- Semua gambar memiliki teks alternatif; foto makanan memakai judul listing.
- Peta memiliki **alternatif daftar**: seluruh informasi di marker juga tersedia di daftar yang dapat dibaca pembaca layar.
- Status tidak hanya diwakili warna; selalu ada teks.
- Menghormati `prefers-reduced-motion`: matikan transisi cincin dan animasi bottom sheet.
- Target sentuh minimal 44 px.
- Atribut `lang="id"` pada elemen `html`.

---

## 8. Responsivitas

| Breakpoint | Lebar | Penyesuaian |
|---|---|---|
| Mobile | < 640 px | Peta penuh + bottom sheet, navigasi bawah (Peta, Bagikan, Aktivitas, Galang, Profil) |
| Tablet | 640 sampai 1023 px | Daftar di samping peta (rasio 40:60), filter dalam drawer |
| Desktop | >= 1024 px | Daftar kiri tetap, peta kanan, navigasi atas |

Pendekatan *mobile first*: tulis gaya dasar untuk mobile, tambahkan `md:` dan `lg:` untuk layar lebih besar.

---

## 9. Gerak dan Interaksi

Gerak dipakai seperlunya dan hanya untuk menjawab aksi pengguna.

- Bottom sheet naik/turun mengikuti gestur tarik.
- Marker yang dipilih membesar sedikit dan kartu terkait tersorot di daftar.
- Cincin waktu bertransisi saat berganti warna.
- Toast singkat setelah aksi ("Klaim terkirim"), hilang otomatis setelah 4 detik.
- Tidak ada animasi masuk pada setiap seksi dan tidak ada efek hover berlebihan pada setiap kartu.

---

## 10. Integrasi Peta (Leaflet)

| Aspek | Keputusan |
|---|---|
| Ubin peta | OpenStreetMap standar untuk demo kuliah dengan atribusi dan caching browser. Untuk produksi dengan lalu lintas nyata, gunakan penyedia ubin berlisensi atau host sendiri (server ubin publik OSM tidak ditujukan untuk beban tinggi) |
| Pusat awal | Lokasi pengguna; jika tidak ada, pusat kota yang dipilih (misalnya Semarang) |
| Zoom awal | 14 (sekitar 1 sampai 3 km) |
| Klaster | Leaflet.markercluster |
| Kontrol | Zoom, "Lokasi saya", pemilih radius |
| Pembaruan data | Muat ulang saat peta berhenti digeser (`moveend`, *debounce* 400 ms) dan otomatis tiap 60 detik pada tab aktif (lihat bagian 12) |
| Pencarian alamat | Dijalankan saat Enter atau tombol Cari; hasil di-cache; bukan per ketikan |
| Koordinat | Hanya koordinat publik yang dimuat ke klien; titik tepat tidak pernah dikirim ke peta publik |
| Performa | Batasi 100 marker per permintaan, kirim hanya kolom yang diperlukan |

Kerangka inisialisasi:
```js
// resources/js/map.js
import L from 'leaflet';
import 'leaflet.markercluster';

export function initMap(el, { lat, lng }) {
  const map = L.map(el).setView([lat, lng], 14);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map);
  return map;
}
```

---

## 11. Status Antarmuka (Memuat, Kosong, Galat, Offline)
Setiap layar memiliki empat keadaan di luar "data tampil normal". Keadaan ini dirancang, bukan dibiarkan default.

| Layar | Memuat | Kosong | Galat | Offline atau izin ditolak |
|---|---|---|---|---|
| Peta dan daftar | Peta abu dengan 3 kartu kerangka (tanpa animasi bila *reduced motion*) | "Belum ada makanan dalam 3 km." + tombol "Perluas ke 5 km" dan "Bagikan makanan" | Banner "Data gagal diperbarui. Menampilkan data 3 menit lalu." + tombol Coba lagi; data lama tetap tampil dengan cap waktu | Offline: banner, data terakhir tampil, tombol klaim nonaktif. Izin lokasi ditolak: panel "Cari alamat atau geser pin" |
| Detail listing | Kerangka foto dan teks | Tidak berlaku | 404: "Listing ini sudah tidak tersedia." Listing habis saat dilihat: banner dan tombol nonaktif | Offline: konten tersimpan tampil, klaim nonaktif |
| Klaim | Tombol berubah "Mengirim..." dan dikunci | Tidak berlaku | Pesan sesuai penyebab: porsi habis, batas 3 klaim, akun dibatasi, listing berakhir | Offline: "Tidak ada koneksi. Klaim belum terkirim." |
| Dashboard | Kerangka tabel | "Belum ada yang dibagikan. Buat listing pertama dalam 30 detik." | Banner dengan Coba lagi | Data terakhir tampil |
| Daftar galang | Kerangka kartu | "Belum ada galang di wilayah ini." + tautan "Jadi relawan" | Banner dengan Coba lagi | Data terakhir tampil |
| Form | Tidak berlaku | Tidak berlaku | Ringkasan galat, data tidak hilang | Draf tersimpan di perangkat, kirim ulang saat online |

Aturan umum: jangan pernah menampilkan layar kosong tanpa penjelasan dan langkah berikutnya; jangan menampilkan galat teknis mentah; selalu pertahankan data terakhir yang valid dengan cap waktu.

---

## 12. Dinamika dan Pembaruan Real-time
Antarmuka tidak boleh terasa statis, tetapi pembaruan juga tidak boleh membuat konten melompat.

- **Penyegaran data:** peta dan daftar memuat ulang saat peta berhenti digeser dan tiap 60 detik hanya pada tab aktif. Penyegaran dijeda saat tab tersembunyi; saat error, selang diperpanjang bertahap (60, 120, 240 detik). Gunakan ETag agar respons tidak berubah tidak mengirim ulang data.
- **Tanpa lompatan:** saat pengguna sedang membaca atau menggulir daftar, urutan tidak diubah otomatis. Muncul banner "3 pembaruan. Segarkan" yang dapat ditekan.
- **Listing habis:** kartu dan marker memudar singkat lalu dihapus; bila sedang dibuka, banner di detail.
- **Aksi klaim tidak optimistik:** karena porsi terbatas dan bisa direbut pengguna lain, tombol menampilkan "Mengirim..." dan menunggu hasil server. Aksi yang aman (menandai notifikasi dibaca) boleh optimistik.
- **Notifikasi:** lencana jumlah belum dibaca diperbarui tiap 60 detik; pembaruan seketika memakai Laravel Reverb sebagai peningkatan opsional.
- **Status klaim:** halaman "Yang saya ambil" menyegarkan status dan menampilkan toast saat klaim disetujui atau ditolak.
- **Keadaan dapat dibagikan:** pusat peta, zoom, radius, dan filter disimpan di URL sehingga tautan dapat dikirim.

---

## 13. Kepercayaan dan Keselamatan
- **Sinyal kepercayaan:** lencana Terverifikasi dan Relawan, jumlah pengambilan sukses, dan ulasan ditampilkan di kartu dan profil.
- **Informasi pangan terstruktur:** jenis, waktu dimasak, alergen, penyimpanan; halal ditulis sebagai "Pemberi menyatakan halal", bukan sertifikasi.
- **Tanggung jawab jelas tetapi tidak menghalangi:** satu kalimat peringatan tampil di dekat tombol klaim; tidak ada modal berulang.
- **Pemberi individu:** lokasi perkiraan, anjuran bertemu di tempat terbuka, kode ambil sebagai bukti serah terima, dan tombol Lapor yang selalu terlihat.
- **Pengambil:** tampilan "Detail pengambilan" menyatakan siapa pemberi, jam batas, dan cara membatalkan.
- **Moderasi:** laporan memiliki alasan terstruktur (makanan tidak layak, listing palsu, galang palsu, pelecehan, lainnya).

---

## 14. Kinerja Front-end
- **Font di-host sendiri** (misalnya melalui paket Fontsource), subset aksara Latin, hanya bobot yang dipakai (Bricolage Grotesque 600 dan 700; Atkinson Hyperlegible 400 dan 700), `font-display: swap`. Tidak memuat Google Fonts dari server pihak ketiga (performa dan privasi pengguna).
- Leaflet dan markercluster dimuat dengan *dynamic import* hanya pada halaman peta.
- Gambar WebP, `loading="lazy"`, dimensi tetap agar layout tidak bergeser; thumbnail 320 px untuk kartu.
- Target (diukur dengan Lighthouse, bukan jaminan): LCP < 2,5 detik, CLS < 0,1, INP < 200 ms pada profil 4G dan ponsel kelas menengah; JS awal < 150 KB gzip.
- Kartu tanpa foto tetap lengkap dan berfungsi, untuk koneksi lambat.

---

## 15. Validasi Desain (Belum Dilakukan)
Dokumen ini adalah rancangan. **Belum dirender sebagai antarmuka dan belum diuji pengguna**, sehingga penilaian "nyaman" dan "estetis" belum terbukti. Langkah validasi:

1. Bangun prototipe resolusi tinggi untuk tiga layar: peta, detail listing, form Bagikan makanan.
2. Uji 3 sampai 5 calon pengguna dengan tugas: (a) menemukan dan mengklaim makanan terdekat, (b) membagikan makanan dalam kurang dari 60 detik, (c) membaca status klaim dan menunjukkan kode ambil, (d) mengajukan diri sebagai relawan, (e) membaca sebuah galang.
3. Catat tingkat keberhasilan, waktu tugas, kesalahan, dan skor SUS (target >= 68).
4. Simulasikan buta warna (protanopia dan deuteranopia) pada marker dan time ring.
5. Uji dengan pembaca layar (TalkBack atau VoiceOver), pembesaran 200%, dan ponsel Android kelas bawah.
6. Periksa warna di layar nyata pada kecerahan rendah dan di luar ruangan.

---

## 16. Daftar Periksa Kualitas UI

- [ ] Peta dan daftar menampilkan data yang sama
- [ ] Time ring benar untuk tiga rentang waktu dan kondisi habis
- [ ] Izin lokasi ditolak tetap dapat dipakai (alamat atau pin manual)
- [ ] Semua form menampilkan galat yang jelas
- [ ] Dapat dioperasikan dengan keyboard
- [ ] Tampilan 360 px, 768 px, dan 1280 px tidak rusak
- [ ] Kontras teks lolos pemeriksaan
- [ ] Atribusi OpenStreetMap tampil
- [ ] Semua pasangan warna lolos tabel kontras bagian 2.1 (periksa ulang jika token berubah)
- [ ] Marker dapat dibedakan tanpa warna (chip waktu dan bentuk)
- [ ] Sisa waktu dihitung dari `server_now`, bukan jam perangkat
- [ ] Setiap layar memiliki keadaan memuat, kosong, galat, dan offline (bagian 11)
- [ ] Bottom sheet dapat dipakai keyboard dan tidak bentrok dengan geser peta
- [ ] Alamat dan titik tepat tidak tampil sebelum klaim disetujui
- [ ] Pencarian alamat tidak berjalan per ketikan
- [ ] Font di-host sendiri; tidak ada permintaan ke server font pihak ketiga
- [ ] Form Bagikan makanan dapat diselesaikan dalam 30 detik pada uji pengguna
- [ ] Pernyataan keamanan pangan tampil di detail listing dan form bagikan makanan
- [ ] Satu akun dapat membagikan dan mengambil tanpa berganti mode
- [ ] Pengguna tidak dapat mengklaim listing sendiri dan melihat pesan yang jelas
- [ ] Pemilih wilayah berjenjang berfungsi dan mengosongkan kecamatan saat kabupaten/kota diganti
- [ ] Lencana Relawan hanya tampil bila ada kecamatan disetujui
- [ ] Galang tidak tampil publik sebelum diterbitkan admin
- [ ] Pin galang yang jauh dari pusat kecamatan menampilkan peringatan dan penanda untuk admin

---

## 17. Catatan Desain

Pilihan yang disengaja agar tampilan tidak seperti templat umum:

1. Palet hijau-embun dengan aksen kunyit dan cabai berfungsi sebagai **kode waktu**, bukan dekorasi.
2. Peta menjadi halaman utama; penjelasan fitur ada di bawahnya.
3. Radius sudut bertingkat sesuai fungsi, bayangan hanya dua level.
4. Tidak ada label huruf kapital berjarak, tidak ada angka bernomor kecuali pada alur yang memang berurutan (langkah pengajuan relawan).
5. Time ring khusus untuk makanan yang segera habis; galang berjangka hari memakai bilah progres supaya dua jenis kebutuhan waktu tidak tercampur.
6. Peran relawan ditampilkan sebagai lencana dan menu tambahan, bukan sebagai mode atau akun terpisah.
7. Warna tidak pernah menjadi satu-satunya pembawa informasi; seluruh kontras dihitung dan dicatat.
8. Privasi adalah bagian dari tampilan: lokasi perkiraan, bukan tepat, sampai ada persetujuan.
