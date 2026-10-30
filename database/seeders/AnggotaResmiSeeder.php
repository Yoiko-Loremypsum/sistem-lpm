<?php

namespace Database\Seeders;

use App\Models\AnggotaResmi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Seeder: AnggotaResmiSeeder
 *
 * Membaca file CSV di database/data/anggota_resmi.csv lalu memasukkan
 * atau memperbarui data ke tabel anggota_resmi menggunakan updateOrCreate
 * berdasarkan nim_nidn agar aman dijalankan berulang kali.
 *
 * Kolom CSV yang diharapkan:
 * nim_nidn, nama, jenis, email_institusi, prodi
 *
 * Cara menjalankan:
 *   php artisan db:seed --class=AnggotaResmiSeeder
 */
class AnggotaResmiSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('data/anggota_resmi.csv');

        // Pastikan file CSV tersedia sebelum diproses
        if (! file_exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan: {$csvPath}");
            $this->command->line('Letakkan file CSV di: database/data/anggota_resmi.csv');
            return;
        }

        $file    = fopen($csvPath, 'r');
        $headers = fgetcsv($file); // Baca baris pertama sebagai header

        if (! $headers) {
            $this->command->error('File CSV kosong atau format tidak valid.');
            fclose($file);
            return;
        }

        // Normalisasi nama header (trim spasi, lowercase)
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        $berhasil = 0;
        $gagal    = 0;
        $baris    = 1; // mulai dari baris ke-2 (setelah header)

        while (($row = fgetcsv($file)) !== false) {
            $baris++;

            // Lewati baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            // Pastikan jumlah kolom sesuai dengan header
            if (count($row) !== count($headers)) {
                $this->command->warn("Baris {$baris}: jumlah kolom tidak sesuai, dilewati.");
                $gagal++;
                continue;
            }

            $data = array_combine($headers, $row);

            // Validasi kolom wajib
            if (empty(trim($data['nim_nidn'] ?? '')) || empty(trim($data['nama'] ?? ''))) {
                $this->command->warn("Baris {$baris}: nim_nidn atau nama kosong, dilewati.");
                $gagal++;
                continue;
            }

            // Validasi enum jenis
            $jenis = strtolower(trim($data['jenis'] ?? ''));
            if (! in_array($jenis, ['mahasiswa', 'dosen'])) {
                $this->command->warn("Baris {$baris}: kolom 'jenis' tidak valid ('{$jenis}'), dilewati.");
                $gagal++;
                continue;
            }

            try {
                AnggotaResmi::updateOrCreate(
                    // Kunci unik: cari berdasarkan nim_nidn
                    ['nim_nidn' => trim($data['nim_nidn'])],
                    // Data yang akan diisi atau diperbarui
                    [
                        'nama'             => trim($data['nama']),
                        'jenis'            => $jenis,
                        'email_institusi'  => strtolower(trim($data['email_institusi'] ?? '')),
                        'prodi'            => ! empty(trim($data['prodi'] ?? ''))
                                                ? trim($data['prodi'])
                                                : null,
                    ]
                );
                $berhasil++;
            } catch (\Exception $e) {
                $this->command->warn("Baris {$baris}: gagal menyimpan ({$e->getMessage()}).");
                Log::error('AnggotaResmiSeeder error', [
                    'baris' => $baris,
                    'data'  => $data,
                    'error' => $e->getMessage(),
                ]);
                $gagal++;
            }
        }

        fclose($file);

        $this->command->info("Seeder selesai: {$berhasil} berhasil, {$gagal} dilewati.");
    }
}
