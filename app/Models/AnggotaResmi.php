<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: AnggotaResmi
 *
 * Representasi data resmi civitas UMI (mahasiswa & dosen).
 * Digunakan sebagai referensi pada proses pendaftaran akun:
 * pengguna hanya bisa mendaftar jika NIM/NIDN-nya ada di tabel ini.
 */
class AnggotaResmi extends Model
{
    protected $table = 'anggota_resmi';

    protected $fillable = [
        'nim_nidn',
        'nama',
        'jenis',
        'email_institusi',
        'prodi',
    ];

    /**
     * Relasi: satu anggota resmi bisa memiliki satu akun user.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'anggota_resmi_id');
    }

    /**
     * Kembalikan label jenis yang mudah dibaca.
     */
    public function getLabelJenisAttribute(): string
    {
        return match ($this->jenis) {
            'mahasiswa' => 'Mahasiswa',
            'dosen'     => 'Dosen',
            default     => ucfirst($this->jenis),
        };
    }

    /**
     * Kembalikan label NIM atau NIDN sesuai jenis.
     */
    public function getLabelNimNidnAttribute(): string
    {
        return $this->jenis === 'dosen' ? 'NIDN' : 'NIM';
    }

    /**
     * Cek apakah anggota resmi ini sudah punya akun terdaftar.
     */
    public function sudahPunyaAkun(): bool
    {
        return $this->user()->exists();
    }
}
