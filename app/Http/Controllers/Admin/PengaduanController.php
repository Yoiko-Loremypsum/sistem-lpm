<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengaduan;
use App\Models\LogStatus;
use App\Models\Pengaduan;
use App\Models\Tanggapan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    /**
     * Tampilkan seluruh data pengaduan dengan filter, pencarian, dan pengurutan
     */
    public function index(Request $request)
    {
        $query = Pengaduan::with(['user', 'kategori', 'tanggapan']);

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['menunggu', 'diproses', 'selesai', 'ditolak'])) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Pencarian teks (judul, kode tiket, isi, nama pelapor)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('kode_tiket', 'like', "%{$q}%")
                    ->orWhere('lokasi', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('nama', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        // Pengurutan
        $sort = $request->input('sort', 'terbaru');
        if ($sort === 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $pengaduans = $query->paginate(12)->withQueryString();
        $kategoris = KategoriPengaduan::orderBy('nama_kategori')->get();

        return view('admin.pengaduan.index', compact('pengaduans', 'kategoris'));
    }

    /**
     * Tampilkan detail pengaduan, form tanggapan, dan log riwayat status
     */
    public function show($id)
    {
        $pengaduan = Pengaduan::with(['user', 'kategori', 'tanggapan.admin', 'logStatus.pengubah'])
            ->findOrFail($id);

        return view('admin.pengaduan.show', compact('pengaduan'));
    }

    /**
     * Ubah status pengaduan (Menunggu -> Diproses -> Selesai / Ditolak)
     */
    public function updateStatus(Request $request, $id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        // Validasi sisi server: tolak jika pengaduan sudah berstatus selesai
        if ($pengaduan->status === 'selesai') {
            return back()->with('error', 'Pengaduan yang sudah selesai tidak dapat diubah lagi.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,diproses,selesai,ditolak'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'status.required' => 'Pilih status baru pengaduan.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        $statusLama = $pengaduan->status;
        $statusBaru = $validated['status'];

        if ($statusLama === $statusBaru) {
            return back()->with('info', "Status pengaduan saat ini sudah '{$pengaduan->status_label}'.");
        }

        $pengaduan->update([
            'status' => $statusBaru,
        ]);

        // Catat ke log status
        LogStatus::create([
            'pengaduan_id' => $pengaduan->id,
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
            'diubah_oleh' => Auth::id(),
        ]);

        // Jika ada catatan saat mengubah status, simpan juga sebagai tanggapan admin otomatis
        if (!empty($validated['catatan'])) {
            Tanggapan::create([
                'pengaduan_id' => $pengaduan->id,
                'admin_id' => Auth::id(),
                'isi_tanggapan' => "[Pembaruan Status ke " . ucfirst($statusBaru) . "] " . $validated['catatan'],
            ]);
        }

        return back()->with('success', "Status pengaduan {$pengaduan->kode_tiket} berhasil diubah menjadi '" . ucfirst($statusBaru) . "'.");
    }

    /**
     * Berikan tanggapan tertulis resmi pada pengaduan
     */
    public function storeTanggapan(Request $request, $id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        // Validasi sisi server: tolak jika pengaduan sudah berstatus selesai
        if ($pengaduan->status === 'selesai') {
            return back()->with('error', 'Pengaduan yang sudah selesai tidak dapat diubah lagi.');
        }

        $validated = $request->validate([
            'isi_tanggapan' => ['required', 'string', 'min:3'],
            'ubah_status' => ['nullable', 'in:menunggu,diproses,selesai,ditolak'],
        ], [
            'isi_tanggapan.required' => 'Isi tanggapan tertulis tidak boleh kosong.',
            'isi_tanggapan.min' => 'Isi tanggapan minimal 3 karakter.',
        ]);

        Tanggapan::create([
            'pengaduan_id' => $pengaduan->id,
            'admin_id' => Auth::id(),
            'isi_tanggapan' => $validated['isi_tanggapan'],
        ]);

        // Jika admin juga memilih memperbarui status secara bersamaan
        if (!empty($validated['ubah_status']) && $validated['ubah_status'] !== $pengaduan->status) {
            $statusLama = $pengaduan->status;
            $statusBaru = $validated['ubah_status'];

            $pengaduan->update(['status' => $statusBaru]);

            LogStatus::create([
                'pengaduan_id' => $pengaduan->id,
                'status_lama' => $statusLama,
                'status_baru' => $statusBaru,
                'diubah_oleh' => Auth::id(),
            ]);
        }

        return back()->with('success', 'Tanggapan tertulis resmi berhasil dikirim ke pelapor.');
    }
}
