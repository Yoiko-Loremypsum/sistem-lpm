<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Controller: PersetujuanAkunController (Admin)
 *
 * Mengelola antrian akun anggota yang menunggu persetujuan admin.
 * Admin dapat menyetujui atau menolak akun yang baru mendaftar.
 */
class PersetujuanAkunController extends Controller
{
    /**
     * Tampilkan daftar akun berdasarkan status.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'menunggu_verifikasi');

        $akun = User::where('role', 'anggota')
            ->where('status_akun', $status)
            ->with('anggotaResmi')
            ->latest()
            ->paginate(15);

        $jumlahMenunggu = User::where('role', 'anggota')
            ->where('status_akun', 'menunggu_verifikasi')
            ->count();

        return view('admin.persetujuan-akun.index', compact('akun', 'status', 'jumlahMenunggu'));
    }

    /**
     * Setujui akun → ubah status_akun menjadi 'aktif'.
     */
    public function setujui(Request $request, int $id)
    {
        $user = User::where('role', 'anggota')
            ->where('status_akun', 'menunggu_verifikasi')
            ->findOrFail($id);

        $user->status_akun = 'aktif';
        $user->save();

        // Kirim notifikasi email ke pengguna
        try {
            Mail::raw(
                "Halo {$user->nama},\n\n"
                . "Akun Sistem LPM UMI Anda (username: {$user->username}) telah disetujui oleh administrator.\n"
                . "Anda sekarang dapat masuk ke sistem menggunakan NIM/NIDN dan kata sandi Anda.\n\n"
                . "URL: " . config('app.url') . "\n\n"
                . "Salam,\nTim Sistem LPM Universitas Methodist Indonesia",
                fn($m) => $m
                    ->to($user->email)
                    ->subject('[Sistem LPM UMI] Akun Anda Telah Disetujui')
            );
        } catch (\Exception) {
            // Gagal kirim email tidak menggagalkan persetujuan
        }

        return redirect()->route('admin.persetujuan-akun.index')
            ->with('success', "Akun {$user->nama} ({$user->username}) berhasil disetujui.");
    }

    /**
     * Tolak akun → ubah status_akun menjadi 'ditolak'.
     */
    public function tolak(Request $request, int $id)
    {
        $request->validate(
            ['alasan' => ['nullable', 'string', 'max:500']],
            ['alasan.max' => 'Alasan maksimal 500 karakter.']
        );

        $user = User::where('role', 'anggota')
            ->where('status_akun', 'menunggu_verifikasi')
            ->findOrFail($id);

        $user->status_akun = 'ditolak';
        $user->save();

        $alasan = $request->alasan ?? 'Tidak ada alasan yang diberikan.';

        try {
            Mail::raw(
                "Halo {$user->nama},\n\n"
                . "Pendaftaran akun Sistem LPM UMI Anda (username: {$user->username}) tidak dapat disetujui.\n\n"
                . "Alasan: {$alasan}\n\n"
                . "Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator.\n\n"
                . "Salam,\nTim Sistem LPM Universitas Methodist Indonesia",
                fn($m) => $m
                    ->to($user->email)
                    ->subject('[Sistem LPM UMI] Pendaftaran Akun Tidak Disetujui')
            );
        } catch (\Exception) {
            // Gagal kirim email tidak menggagalkan penolakan
        }

        return redirect()->route('admin.persetujuan-akun.index')
            ->with('success', "Akun {$user->nama} berhasil ditolak.");
    }
}
