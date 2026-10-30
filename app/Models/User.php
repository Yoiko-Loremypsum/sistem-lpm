<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

/**
 * Model: User
 *
 * Mewakili pengguna sistem LPM (admin dan anggota civitas UMI).
 *
 * Catatan keamanan:
 * - face_embedding TIDAK PERNAH dikembalikan dalam response JSON atau log.
 * - Gunakan getFaceEmbeddingDecrypted() untuk membaca, bukan akses langsung.
 * - Kolom face_embedding di-guard dari mass assignment untuk mencegah overwrite.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'no_hp',
        'alamat',
        'role',
        'status_aktif',
        'anggota_resmi_id',
        'status_akun',
        'face_terdaftar',
        'gagal_login_wajah',
        'remember_token',
    ];

    /**
     * Kolom yang disembunyikan dari serialisasi (toArray, toJson).
     * face_embedding wajib disembunyikan agar tidak bocor ke response.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'face_embedding', // WAJIB: embedding wajah tidak boleh terekspos
    ];

    protected function casts(): array
    {
        return [
            'password'           => 'hashed',
            'status_aktif'       => 'boolean',
            'face_terdaftar'     => 'boolean',
            'gagal_login_wajah'  => 'integer',
            'email_verified_at'  => 'datetime',
        ];
    }

    // =========================================================
    // Relasi
    // =========================================================

    /**
     * Relasi ke data resmi anggota (anggota_resmi).
     */
    public function anggotaResmi()
    {
        return $this->belongsTo(AnggotaResmi::class, 'anggota_resmi_id');
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'user_id');
    }

    public function tanggapan()
    {
        return $this->hasMany(Tanggapan::class, 'admin_id');
    }

    public function logStatus()
    {
        return $this->hasMany(LogStatus::class, 'diubah_oleh');
    }

    // =========================================================
    // Accessor & Helper
    // =========================================================

    /**
     * Kompatibilitas kolom 'name' yang diharapkan Laravel (notifikasi, dll.).
     */
    public function getNameAttribute(): string
    {
        return $this->nama ?? '';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAnggota(): bool
    {
        return $this->role === 'anggota';
    }

    public function isActive(): bool
    {
        return (bool) $this->status_aktif;
    }

    /**
     * Cek apakah akun sudah disetujui admin (status_akun = 'aktif').
     */
    public function isAkunAktif(): bool
    {
        // Admin selalu dianggap aktif meskipun tidak ada status_akun
        if ($this->isAdmin()) {
            return true;
        }

        return $this->status_akun === 'aktif';
    }

    /**
     * Ambil label status akun yang mudah dibaca.
     */
    public function getLabelStatusAkunAttribute(): string
    {
        return match ($this->status_akun) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'aktif'               => 'Aktif',
            'ditolak'             => 'Ditolak',
            default               => 'Tidak Diketahui',
        };
    }

    // =========================================================
    // Manajemen Embedding Wajah (Enkripsi / Dekripsi)
    // =========================================================

    /**
     * Simpan embedding wajah dalam bentuk terenkripsi.
     * Input: array float dari Python service.
     *
     * Catatan: kolom face_embedding tidak boleh di-set langsung via mass assignment.
     */
    public function simpanFaceEmbedding(array $embedding): void
    {
        // Enkripsi menggunakan APP_KEY Laravel
        $this->face_embedding    = Crypt::encryptString(json_encode(array_values($embedding)));
        $this->face_terdaftar    = true;
        $this->gagal_login_wajah = 0;
        $this->save();
    }

    /** Alias untuk descriptor wajah face-api.js (128 float) */
    public function simpanFaceDescriptor(array $descriptor): void
    {
        $this->simpanFaceEmbedding($descriptor);
    }

    /**
     * Ambil embedding / descriptor wajah yang sudah didekripsi.
     * Kembalikan null jika belum terdaftar atau gagal dekripsi.
     *
     * JANGAN panggil fungsi ini di luar konteks verifikasi server-side.
     */
    public function getFaceEmbeddingDecrypted(): ?array
    {
        if (! $this->face_terdaftar || ! $this->face_embedding) {
            return null;
        }

        try {
            // Ambil langsung dari kolom (bypass hidden guard)
            $raw = $this->attributes['face_embedding'] ?? null;
            if (! $raw) {
                return null;
            }

            return json_decode(Crypt::decryptString($raw), true);
        } catch (\Exception $e) {
            // Catat error tanpa expose embedding
            \Log::error('Gagal mendekripsi face_embedding untuk user_id: ' . $this->id);
            return null;
        }
    }

    /** Alias untuk descriptor wajah face-api.js */
    public function getFaceDescriptor(): ?array
    {
        return $this->getFaceEmbeddingDecrypted();
    }

    /**
     * Hapus data wajah (hak pengguna atas data biometrik — UU PDP).
     */
    public function hapusDataWajah(): void
    {
        $this->face_embedding    = null;
        $this->face_terdaftar    = false;
        $this->gagal_login_wajah = 0;
        $this->save();
    }
}
