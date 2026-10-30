# 📖 Panduan Sinkronisasi Data & Cara Kerja Scan Wajah
**Sistem Layanan Pengaduan Masyarakat (LPM) — Universitas Methodist Indonesia**

Dokumen catatan ini dibuat untuk memudahkan pengelolaan data civitas akademika, akun administrator, serta memahami cara kerja pemindaian wajah (Face Recognition) dengan bahasa yang sederhana.

---

## 1. Format & Ekstensi File yang Digunakan

| Komponen | File & Lokasi | Ekstensi | Fungsi & Keterangan |
| :--- | :--- | :--- | :--- |
| **Data Anggota Resmi** | `database/data/anggota_resmi.csv` | `.csv` *(Comma Separated Values)* | Daftar mahasiswa & dosen resmi UMI. Bisa dibuka/diedit langsung lewat Microsoft Excel, Notepad, atau VS Code. |
| **Data Administrator** | `database/seeders/AdminSeeder.php` | `.php` | Berisi daftar akun admin yang diberi hak akses mengelola sistem pengaduan dan persetujuan akun. |
| **Layanan Scan Wajah** | `python-face-service/app.py` | `.py` *(Python)* | Server kecerdasan buatan (AI) yang mendeteksi wajah dan menghitung pola unik wajah. |
| **Model Otak AI Wajah** | `~/.insightface/models/buffalo_l/` | `.onnx` *(Open Neural Network Exchange)* | Berkas model deep learning (ArcFace) yang mengenali fitur wajah manusia dengan akurasi tinggi. |

---

## 2. Bagaimana File .CSV Terintegrasi dengan Database MySQL?

Banyak yang bertanya: *bagaimana bisa file teks biasa seperti `.csv` bisa "nyambung" dan masuk ke database MySQL?*

Jawabannya adalah melalui jembatan yang bernama **`AnggotaResmiSeeder.php`** (lokasi: `database/seeders/AnggotaResmiSeeder.php`).

Berikut alur kerja integrasinya secara rinci:

```
[ File anggota_resmi.csv ]
         │
         ▼  (1) Dibuka & Dibaca baris per baris oleh PHP (fgetcsv)
[ AnggotaResmiSeeder.php ]
         │
         ▼  (2) Dicocokkan judul kolomnya (nim_nidn, nama, jenis, dll)
[ Model AnggotaResmi ]
         │
         ▼  (3) Dijalankan query SQL INSERT / UPDATE (updateOrCreate)
[ Tabel 'anggota_resmi' di MySQL ]
```

### Tahapan yang Terjadi di Balik Layar:

1. **Pembacaan Berkas (`fopen` & `fgetcsv`)**:  
   Saat Anda mengetik perintah `php artisan db:seed --class=AnggotaResmiSeeder`, Laravel membuka file `database/data/anggota_resmi.csv`.
2. **Pemetaan Header**:  
   Baris pertama (`nim_nidn,nama,jenis,email_institusi,prodi`) dibaca sebagai nama kolom (kunci array).
3. **Pembersihan & Validasi Tiap Baris**:  
   Setiap baris data mahasiswa/dosen dicek:
   - Apakah kolom `nim_nidn` atau `nama` kosong? Jika kosong, baris dilewati.
   - Apakah `jenis` berisi `'mahasiswa'` atau `'dosen'`? Jika tidak valid, dilewati.
4. **Penyimpanan Cerdas (`updateOrCreate`)**:  
   Data dimasukkan ke tabel database `anggota_resmi` menggunakan perintah:
   ```php
   AnggotaResmi::updateOrCreate(
       ['nim_nidn' => $data['nim_nidn']], // Cek apakah NIM/NIDN ini sudah ada di tabel?
       [
           'nama'            => $data['nama'],
           'jenis'           => $data['jenis'],
           'email_institusi' => $data['email_institusi'],
           'prodi'           => $data['prodi'],
       ]
   );
   ```
   - **Jika NIM belum ada** ➔ Sistem menjalankan perintah SQL `INSERT` (menambah data baru).
   - **Jika NIM sudah ada** ➔ Sistem menjalankan perintah SQL `UPDATE` (memperbarui nama/email jika ada perubahan di CSV).
5. **Koneksi saat Pendaftaran Mahasiswa (`/daftar`)**:  
   Saat pengguna mengetik NIM di web pendaftaran, sistem Laravel langsung mencari ke tabel `anggota_resmi` di MySQL ini. Jika ditemukan dan belum punya akun, data namanya akan otomatis muncul di Langkah 2!

---

## 3. Cara Menambah Data Baru & Sinkronisasi ke Database

Ketika Anda mengetik atau mengedit data di file, data tersebut **belum otomatis masuk ke database MySQL**. Anda perlu menjalankan satu perintah terminal singkat untuk "memasukkan / menyinkronkan" data tersebut (proses ini disebut **Seeding**).

### A. Jika Anda Menambah Data Mahasiswa / Dosen di `anggota_resmi.csv`:
1. Buka file `database/data/anggota_resmi.csv`.
2. Tambahkan baris baru di bawahnya mengikuti format kolom:
   ```csv
   nim_nidn,nama,jenis,email_institusi,prodi
   123456789,Nama Lengkap,mahasiswa,email@student.umi.ac.id,Teknik Informatika
   ```
3. Buka Terminal di VS Code / command prompt, lalu ketik perintah berikut:
   ```bash
   php artisan db:seed --class=AnggotaResmiSeeder
   ```
   *(Perintah ini aman dijalankan berulang-kali karena menggunakan sistem `updateOrCreate`, sehingga data lama tidak akan duplikat).*

---

### B. Jika Anda Menambah / Mengubah Akun Admin di `AdminSeeder.php`:
1. Buka file `database/seeders/AdminSeeder.php`.
2. Tambahkan blok array baru di dalam `$admins`:
   ```php
   [
       'nama'         => 'Nama Admin Baru, S.Kom.',
       'username'     => 'admin3',              // Username login
       'email'        => 'adminbaru@gmail.com',  // Email login
       'password'     => Hash::make('admin123'), // Kata sandi akun
       'role'         => 'admin',
       'status_aktif' => 1,
       'status_akun'  => 'aktif',
       'no_hp'        => '081234567899',
       'alamat'       => 'Biro Fikom UMI',
   ],
   ```
3. Buka Terminal, lalu ketik perintah berikut:
   ```bash
   php artisan db:seed --class=AdminSeeder
   ```

---

### C. Mau Sinkronkan Keduanya Sekaligus?
Cukup ketik perintah utama:
```bash
php artisan db:seed
```
Sistem akan otomatis memperbarui data anggota resmi sekaligus data admin.

---

## 3. Bagaimana Cara Kerja Scan Wajah? (Penjelasan Sederhana)

Banyak yang mengira sistem menyimpan foto wajah kita di database. Sebenarnya **bukan foto yang disimpan**, melainkan **rumus matematika**. Berikut alur kerjanya:

```
[Kamera Laptop] ──► Ambil Foto ──► [AI Python InsightFace] ──► Diubah jadi 512 Angka ──► [Database (Terenkripsi)]
```

1. **Langkah 1: Mengambil Foto**
   - Kamera laptop Anda mengambil satu jepretan foto saat Anda menekan tombol kamera.
2. **Langkah 2: Diterjemahkan oleh Otak AI (Python)**
   - Foto dikirim ke mikroservis Python (`app.py`).
   - AI InsightFace memindai lekukan wajah (jarak mata, hidung, garis rahang) dan mengubahnya menjadi **512 deret angka desimal unik** (disebut *Facial Embedding*).
3. **Langkah 3: Pengamanan & Enkripsi (UU PDP)**
   - Sistem Laravel mengacak/mengenkripsi 512 deret angka tersebut sehingga tidak bisa dibajak orang lain.
   - **Foto asli Anda langsung dibuang/dihapus** dari memori dan tidak disimpan di database.
4. **Langkah 4: Saat Login (Verifikasi Wajah)**
   - Ketika Anda login di Tahap 2, kamera mengambil foto baru.
   - AI kembali menghitung 512 deret angka dari foto baru tersebut.
   - Angka baru dibandingkan dengan angka lama yang tersimpan di database:
     - Jika tingkat kemiripannya di atas ambang batas (contoh: kecocokan > 60%), pintu login terbuka!
     - Jika salah 3 kali, sistem otomatis menawarkan verifikasi darurat lewat kode OTP email.

---

## 4. Dua Jendela Terminal yang Wajib Aktif Saat Menggunakan Web

Agar semua fitur (web dan kamera wajah) berfungsi lancar, pastikan dua perintah ini berjalan di terminal masing-masing:

| No | Terminal | Perintah | Fungsi |
| :-- | :--- | :--- | :--- |
| 1 | **Terminal 1** | `php artisan serve` | Menjalankan web Sistem LPM di `http://127.0.0.1:8000` |
| 2 | **Terminal 2** | `cd python-face-service` lalu `.venv\Scripts\python app.py` *(atau klik ganda file `start.bat`)* | Menjalankan otak AI verifikasi wajah di `http://127.0.0.1:5001` |
