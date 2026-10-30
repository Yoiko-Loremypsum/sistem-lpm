# Python Face Service — Sistem LPM UMI

Layanan mikro Python yang menyediakan kemampuan pengenalan wajah menggunakan **InsightFace** (model ArcFace `buffalo_l`) untuk Sistem LPM Universitas Methodist Indonesia.

## Persyaratan

- Python 3.10 atau lebih baru
- Koneksi internet saat pertama kali dijalankan (unduh model ~300 MB)
- Windows 10/11 atau Linux

## Instalasi

```bash
# Masuk ke direktori layanan
cd python-face-service

# (Disarankan) Buat virtual environment
python -m venv venv

# Aktifkan virtual environment
# Windows:
venv\Scripts\activate
# Linux/macOS:
source venv/bin/activate

# Install dependensi
pip install -r requirements.txt
```

> **Catatan GPU**: Jika memiliki GPU NVIDIA, ganti `onnxruntime` dengan `onnxruntime-gpu` di `requirements.txt` untuk performa lebih cepat.

## Menjalankan Layanan

```bash
python app.py
```

Layanan akan berjalan di `http://127.0.0.1:5001` secara default.

**Saat pertama kali dijalankan**, InsightFace akan mengunduh model `buffalo_l` (~300 MB). Pastikan koneksi internet tersedia.

## Konfigurasi .env Laravel

Tambahkan baris berikut ke file `.env` proyek Laravel:

```env
FACE_SERVICE_URL=http://127.0.0.1:5001
FACE_SERVICE_PORT=5001
FACE_THRESHOLD=0.40
```

## Endpoint API

### `GET /health`
Mengecek status layanan.

```json
{ "status": "ok", "model": "buffalo_l (InsightFace ArcFace)", "siap": true }
```

### `POST /embed`
Membuat vektor embedding dari gambar wajah.

**Request:**
```json
{ "image_base64": "<string base64 JPEG>" }
```

**Response sukses:**
```json
{ "embedding": [0.123, -0.456, ...] }
```

**Response gagal (wajah tidak terdeteksi):**
```json
{ "embedding": null, "pesan": "Wajah tidak terdeteksi" }
```

### `POST /compare`
Membandingkan dua embedding dan menentukan apakah cocok.

**Request:**
```json
{
  "embedding1": [0.123, -0.456, ...],
  "embedding2": [0.124, -0.450, ...]
}
```

**Response:**
```json
{
  "match": true,
  "similarity": 0.9123,
  "distance": 0.0877,
  "threshold": 0.40
}
```

## Keamanan

- Layanan ini **hanya boleh diakses dari localhost** (`127.0.0.1`). Jangan expose port ke internet.
- Embedding wajah **tidak pernah di-log** oleh layanan ini.
- Laravel mengenkripsi embedding sebelum menyimpan ke database menggunakan `Crypt::encryptString()`.

## Menjalankan sebagai Background Service (Windows)

```bash
# Menggunakan NSSM (Non-Sucking Service Manager)
nssm install LpmFaceService "C:\Python310\python.exe" "C:\path\ke\sistem-lpm\python-face-service\app.py"
nssm start LpmFaceService
```

Atau gunakan Task Scheduler untuk memulai otomatis saat startup.
