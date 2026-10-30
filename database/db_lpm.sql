-- ========================================================
-- SISTEM LAYANAN PENGADUAN MASYARAKAT (LPM)
-- File Skema Database MySQL & Data Awal (Seeder)
-- Nama Database: db_lpm
-- ========================================================

CREATE DATABASE IF NOT EXISTS `db_lpm` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `db_lpm`;

-- --------------------------------------------------------
-- 1. Tabel users
-- Role: 'admin', 'anggota'
-- Status Aktif: 1 (Aktif), 0 (Nonaktif)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `log_status`;
DROP TABLE IF EXISTS `tanggapan`;
DROP TABLE IF EXISTS `pengaduan`;
DROP TABLE IF EXISTS `kategori_pengaduan`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `no_hp` VARCHAR(50) NULL DEFAULT NULL,
  `alamat` TEXT NULL DEFAULT NULL,
  `role` ENUM('admin', 'anggota') NOT NULL DEFAULT 'anggota',
  `status_aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_users_role` (`role`),
  INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Tabel kategori_pengaduan
-- --------------------------------------------------------
CREATE TABLE `kategori_pengaduan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Tabel pengaduan
-- Status: 'menunggu', 'diproses', 'selesai', 'ditolak'
-- Format kode tiket: LPM-YYYYMMDD-XXXX
-- --------------------------------------------------------
CREATE TABLE `pengaduan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_tiket` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `kategori_id` BIGINT UNSIGNED NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `isi_pengaduan` TEXT NOT NULL,
  `lokasi` VARCHAR(255) NULL DEFAULT NULL,
  `foto` VARCHAR(255) NULL DEFAULT NULL,
  `status` ENUM('menunggu', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'menunggu',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_pengaduan_status` (`status`),
  INDEX `idx_pengaduan_kategori` (`kategori_id`),
  INDEX `idx_pengaduan_created_at` (`created_at`),
  INDEX `idx_pengaduan_user` (`user_id`),
  CONSTRAINT `fk_pengaduan_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pengaduan_kategori` FOREIGN KEY (`kategori_id`) 
    REFERENCES `kategori_pengaduan` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Tabel tanggapan
-- Menampung tanggapan tertulis resmi dari Admin
-- --------------------------------------------------------
CREATE TABLE `tanggapan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pengaduan_id` BIGINT UNSIGNED NOT NULL,
  `admin_id` BIGINT UNSIGNED NOT NULL,
  `isi_tanggapan` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_tanggapan_pengaduan` (`pengaduan_id`),
  CONSTRAINT `fk_tanggapan_pengaduan` FOREIGN KEY (`pengaduan_id`) 
    REFERENCES `pengaduan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tanggapan_admin` FOREIGN KEY (`admin_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Tabel log_status
-- Mencatat riwayat perubahan status pengaduan
-- --------------------------------------------------------
CREATE TABLE `log_status` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pengaduan_id` BIGINT UNSIGNED NOT NULL,
  `status_lama` VARCHAR(50) NOT NULL,
  `status_baru` VARCHAR(50) NOT NULL,
  `diubah_oleh` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_log_pengaduan` (`pengaduan_id`),
  CONSTRAINT `fk_log_pengaduan` FOREIGN KEY (`pengaduan_id`) 
    REFERENCES `pengaduan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_log_diubah_oleh` FOREIGN KEY (`diubah_oleh`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- DATA AWAL (SEEDER)
-- Akun Admin:
--   Email: admin@lpm.go.id | Password: admin123
-- Akun Anggota:
--   Email: budi@gmail.com   | Password: anggota123
--   Email: siti@gmail.com   | Password: anggota123
-- ========================================================

-- Insert Akun Users
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `no_hp`, `alamat`, `role`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Administrator Utama', 'admin@lpm.go.id', '$2y$10$EYvmq2TUlFPBeG40wB0iIudV2/JjcVtPWTDkjHMZQSeMNrfBsEfVa', '081234567890', 'Gedung Pusat Layanan Kota Terpadu Lt. 3', 'admin', 1, NOW(), NOW()),
(2, 'Budi Santoso', 'budi@gmail.com', '$2y$10$eTgYWBTZOSr39w1AZ369O.EX3Tc5CsqQukFIz8AEhrmSoWYi9PN5C', '081298765432', 'Jl. Merdeka No. 12, Kelurahan Sukamaju', 'anggota', 1, NOW(), NOW()),
(3, 'Siti Rahmawati', 'siti@gmail.com', '$2y$10$eTgYWBTZOSr39w1AZ369O.EX3Tc5CsqQukFIz8AEhrmSoWYi9PN5C', '081377889900', 'Jl. Sudirman No. 45, Kompleks Asri', 'anggota', 1, NOW(), NOW());

-- Insert Kategori Pengaduan
INSERT INTO `kategori_pengaduan` (`id`, `nama_kategori`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Infrastruktur', 'Kerusakan jalan, jembatan, penerangan jalan umum (PJU), dan fasilitas umum lainnya', NOW(), NOW()),
(2, 'Kebersihan', 'Penumpukan sampah liar, saluran drainase mampet, dan kebersihan lingkungan publik', NOW(), NOW()),
(3, 'Pelayanan Publik', 'Keluhan terkait prosedur perizinan, administrasi kependudukan, atau pelayanan aparat', NOW(), NOW()),
(4, 'Keamanan', 'Gangguan ketertiban umum, aksi premanisme, balap liar, atau kerawanan lingkungan', NOW(), NOW()),
(5, 'Lingkungan Hidup', 'Pencemaran udara, limbah cair industri, penebangan pohon sembarangan', NOW(), NOW());

-- Insert Contoh Pengaduan
INSERT INTO `pengaduan` (`id`, `kode_tiket`, `user_id`, `kategori_id`, `judul`, `isi_pengaduan`, `lokasi`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(1, 'LPM-20261015-0001', 2, 1, 'Jalan Berlubang di Depan Pasar Minggu', 'Terdapat lubang berdiameter sekitar 80 cm dan kedalaman 15 cm di jalur utama depan Pasar Minggu arah selatan. Sangat membahayakan pengendara motor terutama saat hujan tergenang air.', 'Jl. Pasar Minggu Raya No. 45, Jakarta Selatan', NULL, 'diproses', NOW() - INTERVAL 2 DAY, NOW()),
(2, 'LPM-20261015-0002', 2, 2, 'Tumpukan Sampah Liar Belum Diangkut', 'Sudah 5 hari tumpukan sampah di sudut pertigaan Jalan Mawar tidak diangkut petugas kebersihan sehingga menimbulkan bau menyengat dan mengundang lalat ke pemukiman warga sekitar.', 'Pertigaan Jl. Mawar RT 04/RW 02', NULL, 'selesai', NOW() - INTERVAL 4 DAY, NOW()),
(3, 'LPM-20261015-0003', 3, 4, 'Lampu Penerangan Jalan Umum Mati Total', 'Sekitar 4 tiang lampu penerangan jalan dari nomor PJU 12 hingga 15 padam total sejak seminggu lalu. Jalan menjadi sangat gelap dan rawan tindak kejahatan pada malam hari.', 'Jl. Flamboyan Timur Blok C', NULL, 'menunggu', NOW() - INTERVAL 1 HOUR, NOW()),
(4, 'LPM-20261015-0004', 3, 3, 'Permohonan KTP Digital Belum Diaktivasi', 'Saya sudah mengajukan aktivasi IKD sejak 2 minggu lalu namun notifikasi verifikasi belum masuk ke aplikasi kependudukan.', 'Kantor Kelurahan Sukamaju', NULL, 'ditolak', NOW() - INTERVAL 6 DAY, NOW());

-- Insert Tanggapan Admin
INSERT INTO `tanggapan` (`id`, `pengaduan_id`, `admin_id`, `isi_tanggapan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Laporan telah diteruskan ke Dinas Pekerjaan Umum dan Penataan Ruang (PUPR). Tim pemeliharaan jalan dijadwalkan turun ke lokasi pada hari Kamis untuk penambalan aspal darurat.', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY),
(2, 2, 1, 'Dinas Lingkungan Hidup telah mengerahkan armada truk pengangkut sampah ke lokasi pada pukul 09.30 WIB tadi pagi. Area telah dibersihkan dan disemprot disinfektan.', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY),
(3, 4, 1, 'Mohon maaf, pengaduan terkait aktivasi IKD secara mandiri memerlukan kehadiran pemohon ke loket Disdukcapil untuk pencocokan biometrik sidik jari sesuai regulasi Permendagri No. 72.', NOW() - INTERVAL 5 DAY, NOW() - INTERVAL 5 DAY);

-- Insert Log Status
INSERT INTO `log_status` (`id`, `pengaduan_id`, `status_lama`, `status_baru`, `diubah_oleh`, `created_at`) VALUES
(1, 1, 'menunggu', 'diproses', 1, NOW() - INTERVAL 1 DAY),
(2, 2, 'menunggu', 'diproses', 1, NOW() - INTERVAL 3 DAY),
(3, 2, 'diproses', 'selesai', 1, NOW() - INTERVAL 1 DAY),
(4, 4, 'menunggu', 'ditolak', 1, NOW() - INTERVAL 5 DAY);
