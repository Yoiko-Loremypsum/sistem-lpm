<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengaduan;
use App\Models\LogStatus;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PengaduanController extends Controller
{
    /**
     * Tampilkan riwayat pengaduan milik anggota yang login
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Pengaduan::with(['kategori', 'tanggapan'])
            ->where('user_id', $userId);

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['menunggu', 'diproses', 'selesai', 'ditolak'])) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Pencarian judul atau kode tiket
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('kode_tiket', 'like', "%{$q}%")
                    ->orWhere('lokasi', 'like', "%{$q}%");
            });
        }

        $pengaduans = $query->latest()->paginate(9)->withQueryString();
        $kategoris = KategoriPengaduan::orderBy('nama_kategori')->get();

        return view('anggota.pengaduan.index', compact('pengaduans', 'kategoris'));
    }

    /**
     * Tampilkan form pembuatan pengaduan baru
     */
    public function create()
    {
        $kategoris = KategoriPengaduan::orderBy('nama_kategori')->get();
        return view('anggota.pengaduan.create', compact('kategoris'));
    }

    /**
     * Simpan pengaduan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:kategori_pengaduan,id'],
            'isi_pengaduan' => ['required', 'string', 'min:10'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'judul.required' => 'Judul pengaduan wajib diisi.',
            'kategori_id.required' => 'Silakan pilih kategori pengaduan.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'isi_pengaduan.required' => 'Isi laporan pengaduan wajib diuraikan.',
            'isi_pengaduan.min' => 'Isi pengaduan minimal 10 karakter.',
            'foto.image' => 'File lampiran harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal adalah 2 MB.',
        ]);

        // Generate Kode Tiket unik: LPM-YYYYMMDD-XXXX
        $today = date('Ymd');
        $prefix = "LPM-{$today}-";
        $latestRecord = Pengaduan::where('kode_tiket', 'like', "{$prefix}%")
            ->orderBy('kode_tiket', 'desc')
            ->first();

        if ($latestRecord) {
            $latestNum = (int) substr($latestRecord->kode_tiket, -4);
            $nextNum = str_pad($latestNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        $kodeTiket = $prefix . $nextNum;

        // Upload foto jika disertakan
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'lpm_' . date('Ymd_His') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $fotoPath = $file->storeAs('pengaduan', $filename, 'public');
        }

        $pengaduan = Pengaduan::create([
            'kode_tiket' => $kodeTiket,
            'user_id' => Auth::id(),
            'kategori_id' => $validated['kategori_id'],
            'judul' => $validated['judul'],
            'isi_pengaduan' => $validated['isi_pengaduan'],
            'lokasi' => $validated['lokasi'] ?? null,
            'foto' => $fotoPath,
            'status' => 'menunggu',
        ]);

        // Catat log status awal
        LogStatus::create([
            'pengaduan_id' => $pengaduan->id,
            'status_lama' => 'baru',
            'status_baru' => 'menunggu',
            'diubah_oleh' => Auth::id(),
        ]);

        return redirect()->route('anggota.pengaduan.show', $pengaduan->id)
            ->with('success', "Pengaduan berhasil diajukan dengan Kode Tiket {$kodeTiket}. Admin akan segera meninjau laporan Anda.");
    }

    /**
     * Tampilkan detail pengaduan beserta tanggapan dan log status
     */
    public function show($id)
    {
        $pengaduan = Pengaduan::with(['kategori', 'tanggapan.admin', 'logStatus.pengubah', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('anggota.pengaduan.show', compact('pengaduan'));
    }
}
