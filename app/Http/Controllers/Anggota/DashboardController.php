<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $stats = [
            'total' => Pengaduan::where('user_id', $userId)->count(),
            'menunggu' => Pengaduan::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'diproses' => Pengaduan::where('user_id', $userId)->where('status', 'diproses')->count(),
            'selesai' => Pengaduan::where('user_id', $userId)->where('status', 'selesai')->count(),
            'ditolak' => Pengaduan::where('user_id', $userId)->where('status', 'ditolak')->count(),
        ];

        // Hitung persentase selesai
        $stats['persentase_selesai'] = $stats['total'] > 0 
            ? round(($stats['selesai'] / $stats['total']) * 100) 
            : 0;

        $pengaduanTerbaru = Pengaduan::with('kategori')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('anggota.dashboard', compact('stats', 'pengaduanTerbaru'));
    }
}
