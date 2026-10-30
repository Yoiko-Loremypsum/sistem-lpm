<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogStatus extends Model
{
    use HasFactory;

    protected $table = 'log_status';

    // Disables updated_at since table schema only requires created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'pengaduan_id',
        'status_lama',
        'status_baru',
        'diubah_oleh',
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    public function pengubah()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
