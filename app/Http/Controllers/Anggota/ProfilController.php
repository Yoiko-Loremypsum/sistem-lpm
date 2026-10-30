<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    /**
     * Tampilkan form profil dan ubah kata sandi
     */
    public function edit()
    {
        $user = Auth::user();
        return view('anggota.profil', compact('user'));
    }

    /**
     * Perbarui data profil anggota
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
        ]);

        $user->update([
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi anggota
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password_sekarang' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password_sekarang.required' => 'Kata sandi saat ini wajib diisi.',
            'password_baru.required' => 'Kata sandi baru wajib diisi.',
            'password_baru.min' => 'Kata sandi baru minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
        ]);

        if (!Hash::check($validated['password_sekarang'], $user->password)) {
            return back()->withErrors([
                'password_sekarang' => 'Kata sandi saat ini tidak sesuai dengan data kami.',
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password_baru']),
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah. Gunakan kata sandi baru untuk login selanjutnya.');
    }
}
