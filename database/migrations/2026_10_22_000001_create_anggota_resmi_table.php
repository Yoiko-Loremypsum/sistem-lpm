<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tabel anggota_resmi
 *
 * Menyimpan data resmi mahasiswa dan dosen dari sistem akademik UMI.
 * Tabel ini digunakan sebagai referensi verifikasi saat proses pendaftaran akun.
 * Pengguna hanya bisa mendaftar jika NIM/NIDN-nya terdapat di tabel ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_resmi', function (Blueprint $table) {
            $table->id();

            // NIM untuk mahasiswa, NIDN untuk dosen — wajib unik
            $table->string('nim_nidn')->unique()->comment('NIM untuk mahasiswa / NIDN untuk dosen');

            $table->string('nama')->comment('Nama lengkap sesuai data akademik');

            // Menentukan tipe anggota untuk keperluan tampilan dan validasi
            $table->enum('jenis', ['mahasiswa', 'dosen'])->comment('Jenis keanggotaan civitas');

            // Email institusi (@umi.ac.id) sebagai identitas resmi dan tujuan OTP
            $table->string('email_institusi')->unique()->comment('Email resmi UMI untuk OTP dan komunikasi');

            // Program studi bisa kosong untuk dosen lintas prodi
            $table->string('prodi')->nullable()->comment('Program studi (nullable untuk dosen)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_resmi');
    }
};
