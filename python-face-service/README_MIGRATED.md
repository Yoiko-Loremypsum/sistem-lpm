# Layanan Python (Deprecated / Tidak Dipakai Lagi)

Layanan Python ini sebelumnya digunakan untuk:
1. Endpoint `/embed` (InsightFace ArcFace)
2. Endpoint `/compare` (Cosine Similarity)

### Status Migrasi:
- **Telah dimigrasikan ke face-api.js (di browser klien)**.
- Seluruh deteksi wajah dan ekstraksi 128-float descriptor sekarang dilakukan langsung di peramban (browser) pengguna menggunakan `public/js/face-service.js` dan model di `public/models/`.
- Perbandingan biometrik wajah dilakukan secara aman di sisi server menggunakan PHP Euclidean Distance (`App\Services\FaceRecognitionService`).
- **Layanan Python ini tidak lagi dipanggil oleh Laravel** dan tidak perlu dijalankan saat proses deployment/hosting (cocok untuk cPanel/Shared Hosting PHP + MySQL standar).
- Folder ini dipertahankan sebagai arsip sesuai permintaan pengguna dan dapat dihapus sewaktu-waktu jika sudah tidak diperlukan.
