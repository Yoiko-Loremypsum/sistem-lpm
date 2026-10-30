<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Menambah kolom auth & biometrik ke tabel users
 *
 * Kolom yang ditambahkan:
 * - username      : NIM/NIDN, dipakai sebagai kredensial login
 * - anggota_resmi_id : foreign key ke tabel anggota_resmi
 * - status_akun   : alur persetujuan admin sebelum akun bisa dipakai
 * - face_embedding : vektor wajah terenkripsi (tidak pernah ditampilkan)
 * - face_terdaftar : apakah wajah sudah didaftarkan
 * - gagal_login_wajah : counter kegagalan verifikasi wajah
 *
 * Kolom lama yang dipertahankan: role, status_aktif, nama, email, password, dll.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Username = NIM/NIDN, diisi otomatis saat registrasi
            $table->string('username')->unique()->nullable()->after('id')
                  ->comment('Alias NIM/NIDN, digunakan sebagai username login');

            // Relasi ke data resmi; nullable agar admin bisa tetap dibuat manual
            $table->foreignId('anggota_resmi_id')
                  ->nullable()
                  ->after('username')
                  ->constrained('anggota_resmi')
                  ->nullOnDelete()
                  ->comment('Referensi ke tabel anggota_resmi');

            // Alur persetujuan: baru daftar → menunggu_verifikasi → aktif / ditolak
            $table->enum('status_akun', ['menunggu_verifikasi', 'aktif', 'ditolak'])
                  ->default('menunggu_verifikasi')
                  ->after('anggota_resmi_id')
                  ->comment('Status persetujuan akun oleh admin');

            // Embedding wajah disimpan dalam bentuk terenkripsi (Crypt::encrypt)
            // Kolom ini TIDAK BOLEH dikembalikan dalam response API atau log
            $table->longText('face_embedding')
                  ->nullable()
                  ->after('status_akun')
                  ->comment('Vektor embedding wajah terenkripsi — jangan expose ke luar');

            // Flag apakah wajah sudah didaftarkan
            $table->boolean('face_terdaftar')
                  ->default(false)
                  ->after('face_embedding')
                  ->comment('True jika face_embedding sudah tersimpan');

            // Counter untuk lock-out setelah 3 kali gagal verifikasi wajah
            $table->integer('gagal_login_wajah')
                  ->default(0)
                  ->after('face_terdaftar')
                  ->comment('Jumlah kegagalan verifikasi wajah berturut-turut');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu sebelum kolom
            $table->dropForeign(['anggota_resmi_id']);
            $table->dropUnique(['username']);

            $table->dropColumn([
                'username',
                'anggota_resmi_id',
                'status_akun',
                'face_embedding',
                'face_terdaftar',
                'gagal_login_wajah',
            ]);
        });
    }
};
