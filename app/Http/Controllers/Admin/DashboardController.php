<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengaduan;
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPengaduan = Pengaduan::count();
        $menunggu = Pengaduan::where('status', 'menunggu')->count();
        $diproses = Pengaduan::where('status', 'diproses')->count();
        $selesai = Pengaduan::where('status', 'selesai')->count();
        $ditolak = Pengaduan::where('status', 'ditolak')->count();
        $totalAnggota = User::where('role', 'anggota')->count();
        $totalKategori = KategoriPengaduan::count();

        // Persentase penyelesaian
        $persentaseSelesai = $totalPengaduan > 0 
            ? round(($selesai / $totalPengaduan) * 100, 1) 
            : 0;

        // Data Tren Bulanan untuk 6 bulan terakhir atau tahun berjalan
        $bulanNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $chartLabels = [];
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthNum = (int) $date->format('n');
            $year = $date->format('Y');
            
            $chartLabels[] = $bulanNames[$monthNum] . ' ' . substr($year, 2);

            $count = Pengaduan::whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->count();

            $chartData[] = $count;
        }

        // Pengaduan terbaru
        $pengaduanTerbaru = Pengaduan::with(['user', 'kategori'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalPengaduan',
            'menunggu',
            'diproses',
            'selesai',
            'ditolak',
            'totalAnggota',
            'totalKategori',
            'persentaseSelesai',
            'chartLabels',
            'chartData',
            'pengaduanTerbaru'
        ));
    }
}
