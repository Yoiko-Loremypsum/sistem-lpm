<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';

    protected $fillable = [
        'kode_tiket',
        'user_id',
        'kategori_id',
        'judul',
        'isi_pengaduan',
        'lokasi',
        'foto',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPengaduan::class, 'kategori_id');
    }

    public function tanggapan()
    {
        return $this->hasMany(Tanggapan::class, 'pengaduan_id')->orderBy('created_at', 'asc');
    }

    public function logStatus()
    {
        return $this->hasMany(LogStatus::class, 'pengaduan_id')->orderBy('created_at', 'asc');
    }

    // Status formatting helpers
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'badge-menunggu',
            'diproses' => 'badge-diproses',
            'selesai' => 'badge-selesai',
            'ditolak' => 'badge-ditolak',
            default => 'badge-default',
        };
    }
}
