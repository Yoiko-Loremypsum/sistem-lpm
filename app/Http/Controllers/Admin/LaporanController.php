<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengaduan;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Halaman filter dan preview laporan pengaduan
     */
    public function index(Request $request)
    {
        $kategoris = KategoriPengaduan::orderBy('nama_kategori')->get();

        $query = Pengaduan::with(['user', 'kategori', 'tanggapan']);

        // Filter rentang tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $pengaduans = $query->latest()->get();

        // Ringkasan metrik laporan
        $summary = [
            'total' => $pengaduans->count(),
            'menunggu' => $pengaduans->where('status', 'menunggu')->count(),
            'diproses' => $pengaduans->where('status', 'diproses')->count(),
            'selesai' => $pengaduans->where('status', 'selesai')->count(),
            'ditolak' => $pengaduans->where('status', 'ditolak')->count(),
        ];

        return view('admin.laporan.index', compact('pengaduans', 'kategoris', 'summary'));
    }

    /**
     * Tampilan cetak PDF / Print resmi
     */
    public function exportPrint(Request $request)
    {
        $query = Pengaduan::with(['user', 'kategori', 'tanggapan']);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $pengaduans = $query->latest()->get();

        $summary = [
            'total' => $pengaduans->count(),
            'menunggu' => $pengaduans->where('status', 'menunggu')->count(),
            'diproses' => $pengaduans->where('status', 'diproses')->count(),
            'selesai' => $pengaduans->where('status', 'selesai')->count(),
            'ditolak' => $pengaduans->where('status', 'ditolak')->count(),
        ];

        $filterInfo = [
            'tanggal_mulai' => $request->tanggal_mulai ?: 'Semua Tanggal',
            'tanggal_selesai' => $request->tanggal_selesai ?: 'Hari Ini',
            'status' => $request->status ? ucfirst($request->status) : 'Semua Status',
        ];

        return view('admin.laporan.print', compact('pengaduans', 'summary', 'filterInfo'));
    }

    /**
     * Ekspor data laporan ke format CSV / Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Pengaduan::with(['user', 'kategori', 'tanggapan']);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $pengaduans = $query->latest()->get();

        $filename = 'Laporan_Pengaduan_LPM_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($pengaduans) {
            $output = fopen('php://output', 'w');
            
            // Tambahkan UTF-8 BOM untuk kompatibilitas Microsoft Excel
            fputs($output, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($output, [
                'No',
                'Kode Tiket',
                'Tanggal Lapor',
                'Nama Pelapor',
                'Email Pelapor',
                'No. HP Pelapor',
                'Kategori',
                'Lokasi Kejadian',
                'Status',
                'Judul Pengaduan',
                'Isi Pengaduan',
                'Jumlah Tanggapan'
            ], ';');

            foreach ($pengaduans as $index => $p) {
                fputcsv($output, [
                    $index + 1,
                    $p->kode_tiket,
                    $p->created_at->format('d/m/Y H:i'),
                    $p->user ? $p->user->nama : '-',
                    $p->user ? $p->user->email : '-',
                    $p->user ? $p->user->no_hp : '-',
                    $p->kategori ? $p->kategori->nama_kategori : '-',
                    $p->lokasi ?: '-',
                    $p->status_label,
                    $p->judul,
                    str_replace(["\r", "\n"], ' ', $p->isi_pengaduan),
                    $p->tanggapan->count()
                ], ';');
            }

            fclose($output);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
