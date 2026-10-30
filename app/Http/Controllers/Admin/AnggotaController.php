<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    /**
     * Tampilkan daftar anggota masyarakat
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'anggota')->withCount('pengaduan');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('no_hp', 'like', "%{$q}%")
                    ->orWhere('alamat', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status === 'aktif' ? 1 : 0);
        }

        $anggotas = $query->latest()->paginate(10)->withQueryString();

        return view('admin.anggota.index', compact('anggotas'));
    }

    /**
     * Aktifkan atau Nonaktifkan akun anggota
     */
    public function toggleStatus($id)
    {
        $user = User::where('role', 'anggota')->findOrFail($id);

        $newStatus = $user->status_aktif ? 0 : 1;
        $user->update(['status_aktif' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan kembali' : 'dinonaktifkan';

        return back()->with('success', "Akun anggota atas nama '{$user->nama}' berhasil {$statusText}.");
    }
}
