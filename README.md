# SISTEM LAYANAN PENGADUAN MASYARAKAT (LPM)

Aplikasi web modern berbasis Laravel 12 dan MySQL untuk pengelolaan pengaduan masyarakat secara transparan, akuntabel, dan responsif. Mengadopsi desain antarmuka minimalis monokrom dengan sentuhan efek kaca (*glassmorphism*).

---

## 1. Fitur Utama

### A. Fitur Anggota (Masyarakat)
- **Registrasi & Login:** Warga dapat mendaftarkan akun baru dan masuk ke sistem.
- **Ajukan Pengaduan:** Form pengaduan dengan kode tiket otomatis berformat `LPM-YYYYMMDD-XXXX`, kategori, isi laporan, lokasi kejadian, dan lampiran foto (maks 2 MB).
- **Pengaduan Saya:** Memantau daftar laporan pengaduan, status penanganan, pencarian, dan filter kategori.
- **Detail Pengaduan:** Menampilkan uraian lengkap, foto bukti kejadian, tanggapan resmi dari pihak admin, serta lini masa riwayat status (*audit log*).
- **Profil & Keamanan:** Pengubahan identitas diri (nama, no. HP, alamat) serta pembaruan kata sandi.

### B. Fitur Administrator
- **Dashboard Statistik:**
  - Metrik kartu monokrom: total pengaduan, menunggu, diproses, selesai, dan total warga.
  - Grafik tren pengaduan bulanan menggunakan garis tipis dengan penanda nilai tertinggi.
  - Kartu progres lingkaran (*ring progress*) tingkat penyelesaian/resolusi laporan.
  - Tabel pengaduan terkini yang membutuhkan tindak lanjut cepat.
- **Kelola Pengaduan:**
  - Pencarian fleksibel berdasarkan judul, nomor tiket, atau nama pelapor.
  - Pemfilteran berdasarkan status (`menunggu`, `diproses`, `selesai`, `ditolak`) dan kategori.
  - Pengurutan tanggal (terbaru / terlama).
  - Mengubah status pengaduan dengan pencatatan otomatis ke tabel `log_status`.
  - Memberikan tanggapan tertulis resmi kepada pelapor.
- **Kelola Kategori:** Penambahan, pembaruan, dan penghapusan kategori pengaduan (dengan proteksi integritas data).
- **Kelola Anggota:** Melihat daftar masyarakat terdaftar beserta tombol penonaktifan atau pengaktifan kembali status akun.
- **Laporan & Ekspor:**
  - Rekapitulasi pengaduan dengan filter rentang tanggal, status, dan kategori.
  - Cetak laporan resmi berformat PDF/Print lengkap dengan kop surat kedinasan dan lembar tanda tangan pengesahan.
  - Ekspor data laporan ke format Excel/CSV dengan *encoding* UTF-8 BOM.

---

## 2. Struktur Direktori Proyek

```
sistem-lpm/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php              # Autentikasi (login, register, logout)
│   │   │   ├── Anggota/
│   │   │   │   ├── DashboardController.php     # Dashboard warga
│   │   │   │   ├── PengaduanController.php     # CRUD & pengajuan pengaduan
│   │   │   │   └── ProfilController.php        # Profil & kata sandi warga
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php     # Dashboard & analitik admin
│   │   │       ├── PengaduanController.php     # Manajemen pengaduan & tanggapan
│   │   │       ├── KategoriController.php      # Kelola kategori pengaduan
│   │   │       ├── AnggotaController.php       # Kelola status akun warga
│   │   │       └── LaporanController.php       # Filter, cetak & ekspor CSV
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php             # Proteksi hak akses administrator
│   │       └── AnggotaMiddleware.php           # Proteksi hak akses masyarakat
│   └── Models/
│       ├── User.php                            # Model pengguna (Admin/Anggota)
│       ├── KategoriPengaduan.php               # Model kategori
│       ├── Pengaduan.php                       # Model pengaduan masyarakat
│       ├── Tanggapan.php                       # Model tanggapan resmi admin
│       └── LogStatus.php                       # Model jejak audit riwayat status
├── database/
│   └── db_lpm.sql                              # File skema database & data awal (seeder)
├── public/
│   ├── css/
│   │   └── lpm.css                             # Design system monokrom & glassmorphism
│   ├── js/
│   │   └── lpm.js                              # Interaktivitas UI & upload preview
│   └── storage/ -> storage/app/public          # Tautan penyimpanan file foto
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php                   # Layout utama (sidebar & topbar)
│       │   └── auth.blade.php                  # Layout login & registrasi
│       ├── auth/
│       │   ├── login.blade.php                 # Tampilan masuk
│       │   └── register.blade.php              # Tampilan pendaftaran
│       ├── anggota/
│       │   ├── dashboard.blade.php             # Dashboard masyarakat
│       │   ├── profil.blade.php                # Form profil & ganti sandi
│       │   └── pengaduan/
│       │       ├── create.blade.php            # Form pengaduan baru
│       │       ├── index.blade.php             # Daftar pengaduan saya
│       │       └── show.blade.php              # Detail pengaduan & tanggapan
│       └── admin/
│           ├── dashboard.blade.php             # Dashboard admin & Chart.js
│           ├── pengaduan/
│           │   ├── index.blade.php             # Tabel manajemen pengaduan
│           │   └── show.blade.php              # Detail, status & form respon
│           ├── kategori/
│           │   └── index.blade.php             # Kelola kategori pengaduan
│           ├── anggota/
│           │   └── index.blade.php             # Kelola warga & toggle status
│           └── laporan/
│               ├── index.blade.php             # Filter & pratinjau laporan
│               └── print.blade.php             # Template cetak PDF resmi
└── routes/
    └── web.php                                 # Definisi seluruh rute sistem
```

---

## 3. Akun Pengguna Contoh (Seeder)

Untuk kemudahan pengujian, database telah dilengkapi dengan akun contoh:

| Role | Nama Pengguna | Alamat Email | Kata Sandi | Status |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | Administrator Utama | `admin@lpm.go.id` | `admin123` | Aktif |
| **Anggota (Warga)** | Budi Santoso | `budi@gmail.com` | `anggota123` | Aktif |
| **Anggota (Warga)** | Siti Rahmawati | `siti@gmail.com` | `anggota123` | Aktif |

> *Catatan: Pada halaman login telah disediakan tombol "Demo Admin" dan "Demo Warga" untuk mengisi kredensial secara instan dalam 1 klik.*

---

## 4. Cara Instalasi & Menjalankan Proyek

1. **Pastikan Web Server & Database Aktif:**
   Jalankan MySQL melalui XAMPP, Laragon, atau layanan MySQL lokal pada *port* 3306.

2. **Konfigurasi Database:**
   File `database/db_lpm.sql` berisi seluruh perintah pembuatan database `db_lpm`, tabel-tabel, indeks, kunci asing (*foreign key*), dan data awal. Jika ingin mengimpor ulang manual:
   ```bash
   mysql -u root -p < database/db_lpm.sql
   ```

3. **Pengaturan Berkas `.env`:**
   Pastikan pengaturan koneksi di `.env` telah sesuai:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_lpm
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Tautan Berkas Unggahan (*Storage Link*):**
   ```bash
   php artisan storage:link
   ```

5. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui peramban web di: `http://localhost:8000`
